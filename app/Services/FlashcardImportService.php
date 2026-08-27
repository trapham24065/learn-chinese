<?php

namespace App\Services;

use App\Models\Flashcard;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FlashcardImportService
{
    /**
     * Import flashcards from a file path or raw CSV/TSV text.
     */
    public function import(
        ?string $filePath = null,
        ?string $rawContent = null,
        ?int $defaultHskLevel = null,
        ?int $defaultLessonId = null,
        string $duplicateMode = 'update' // 'update' | 'skip' | 'create'
    ): array {
        $content = '';

        if ($filePath) {
            if (Storage::disk('local')->exists($filePath)) {
                $content = Storage::disk('local')->get($filePath);
            } elseif (file_exists($filePath)) {
                $content = file_get_contents($filePath);
            }
        } elseif ($rawContent) {
            $content = $rawContent;
        }

        if (empty(trim($content))) {
            return [
                'total'          => 0,
                'created'        => 0,
                'updated'        => 0,
                'skipped'        => 0,
                'errors'         => 0,
                'error_messages' => ['Không tìm thấy dữ liệu để import.'],
            ];
        }

        // Remove UTF-8 BOM if present
        $bom = pack('H*', 'EFBBBF');
        $content = preg_replace("/^$bom/", '', $content);

        // Normalize newlines
        $content = str_replace(["\r\n", "\r"], "\n", $content);
        $lines = explode("\n", trim($content));

        if (empty($lines)) {
            return [
                'total'          => 0,
                'created'        => 0,
                'updated'        => 0,
                'skipped'        => 0,
                'errors'         => 0,
                'error_messages' => ['Tệp rỗng hoặc không có dòng dữ liệu hợp lệ.'],
            ];
        }

        // Detect delimiter from the first line
        $firstLine = $lines[0];
        $delimiter = $this->detectDelimiter($firstLine);

        // Check if first line is a header row
        $firstRow = str_getcsv($firstLine, $delimiter);
        $headerMap = $this->detectHeaderMap($firstRow);

        $startIndex = 0;
        if (!empty($headerMap)) {
            $startIndex = 1;
        } else {
            $headerMap = [
                'hanzi'           => 0,
                'pinyin'          => 1,
                'meaning'         => 2,
                'example'         => 3,
                'example_pinyin'  => 4,
                'example_meaning' => 5,
                'hsk_level'       => 6,
                'tags'            => 7,
            ];
        }

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $errors = 0;
        $errorMessages = [];
        $totalRows = count($lines) - $startIndex;

        DB::beginTransaction();

        try {
            for ($i = $startIndex; $i < count($lines); $i++) {
                $line = trim($lines[$i]);
                if ($line === '') {
                    continue;
                }

                $row = str_getcsv($line, $delimiter);
                if (empty($row) || (count($row) === 1 && trim($row[0]) === '')) {
                    continue;
                }

                $hanzi          = $this->extractCol($row, $headerMap, 'hanzi');
                $pinyin         = $this->extractCol($row, $headerMap, 'pinyin');
                $meaning        = $this->extractCol($row, $headerMap, 'meaning');
                $example        = $this->extractCol($row, $headerMap, 'example');
                $examplePinyin  = $this->extractCol($row, $headerMap, 'example_pinyin');
                $exampleMeaning = $this->extractCol($row, $headerMap, 'example_meaning');
                $hskLevelRaw    = $this->extractCol($row, $headerMap, 'hsk_level');
                $tagsRaw        = $this->extractCol($row, $headerMap, 'tags');

                // Validation: Hanzi is mandatory
                if (empty($hanzi)) {
                    $errors++;
                    $errorMessages[] = "Dòng " . ($i + 1) . ": Thiếu chữ Hán (Hanzi).";
                    continue;
                }

                if (empty($meaning)) {
                    $meaning = $hanzi;
                }
                if (empty($pinyin)) {
                    $pinyin = '-';
                }

                // Parse HSK level (1..6)
                $hskLevel = null;
                if (!empty($hskLevelRaw)) {
                    if (preg_match('/[1-6]/', $hskLevelRaw, $matches)) {
                        $hskLevel = (int) $matches[0];
                    }
                }
                if ($hskLevel === null && $defaultHskLevel) {
                    $hskLevel = $defaultHskLevel;
                }

                // Parse Tags
                $tags = null;
                if (!empty($tagsRaw)) {
                    $parsedTags = preg_split('/[,;|]/', $tagsRaw);
                    $cleanedTags = array_values(array_filter(array_map('trim', $parsedTags)));
                    if (!empty($cleanedTags)) {
                        $tags = $cleanedTags;
                    }
                }

                $payload = [
                    'hanzi'           => $hanzi,
                    'pinyin'          => $pinyin,
                    'meaning'         => $meaning,
                    'example'         => $example ?: null,
                    'example_pinyin'  => $examplePinyin ?: null,
                    'example_meaning' => $exampleMeaning ?: null,
                    'hsk_level'       => $hskLevel,
                    'lesson_id'       => $defaultLessonId ?: null,
                    'tags'            => $tags,
                    'is_active'       => true,
                ];

                $existingQuery = Flashcard::where('hanzi', $hanzi);
                if ($hskLevel) {
                    $existingQuery->where(function ($q) use ($hskLevel) {
                        $q->where('hsk_level', $hskLevel)->orWhereNull('hsk_level');
                    });
                }
                $existing = $existingQuery->first();

                if ($existing) {
                    if ($duplicateMode === 'skip') {
                        $skipped++;
                        continue;
                    } elseif ($duplicateMode === 'update') {
                        $updateData = array_filter($payload, fn ($val) => $val !== null);
                        $existing->update($updateData);
                        $updated++;
                    } else {
                        Flashcard::create($payload);
                        $created++;
                    }
                } else {
                    Flashcard::create($payload);
                    $created++;
                }
            }

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            return [
                'total'          => $totalRows,
                'created'        => 0,
                'updated'        => 0,
                'skipped'        => 0,
                'errors'         => $totalRows,
                'error_messages' => ['Lỗi cơ sở dữ liệu: ' . $e->getMessage()],
            ];
        }

        return [
            'total'          => $created + $updated + $skipped + $errors,
            'created'        => $created,
            'updated'        => $updated,
            'skipped'        => $skipped,
            'errors'         => $errors,
            'error_messages' => array_slice($errorMessages, 0, 10),
        ];
    }

    private function detectDelimiter(string $line): string
    {
        $commaCount     = substr_count($line, ',');
        $tabCount       = substr_count($line, "\t");
        $semicolonCount = substr_count($line, ';');

        if ($tabCount > $commaCount && $tabCount > $semicolonCount) {
            return "\t";
        }
        if ($semicolonCount > $commaCount && $semicolonCount > $tabCount) {
            return ';';
        }
        return ',';
    }

    private function detectHeaderMap(array $headers): array
    {
        $map = [];
        $isHeader = false;

        foreach ($headers as $index => $rawCol) {
            $col = mb_strtolower(trim($rawCol));

            if (in_array($col, ['hanzi', 'chữ hán', 'chu han', 'chuhan', 'chinese', 'từ vựng', 'tu vung'])) {
                $map['hanzi'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['pinyin', 'phiên âm', 'phien am', 'bính âm', 'binh am'])) {
                $map['pinyin'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['meaning', 'nghĩa', 'nghia', 'dịch nghĩa', 'dich nghia', 'tiếng việt', 'tieng viet', 'vietnamese'])) {
                $map['meaning'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['example', 'ví dụ', 'vi du', 'câu ví dụ', 'cau vi du'])) {
                $map['example'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['example_pinyin', 'pinyin ví dụ', 'pinyin vi du', 'phiên âm ví dụ', 'phien am vi du'])) {
                $map['example_pinyin'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['example_meaning', 'dịch ví dụ', 'dich vi du', 'nghĩa ví dụ', 'nghia vi du'])) {
                $map['example_meaning'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['hsk_level', 'hsk', 'cấp hsk', 'cap hsk', 'level', 'cấp độ'])) {
                $map['hsk_level'] = $index;
                $isHeader = true;
            } elseif (in_array($col, ['tags', 'tag', 'thẻ', 'the', 'chủ đề', 'chu de'])) {
                $map['tags'] = $index;
                $isHeader = true;
            }
        }

        return $isHeader ? $map : [];
    }

    private function extractCol(array $row, array $headerMap, string $key): ?string
    {
        if (!isset($headerMap[$key])) {
            return null;
        }

        $index = $headerMap[$key];
        if (!isset($row[$index])) {
            return null;
        }

        $val = trim($row[$index]);
        return $val === '' ? null : $val;
    }

    public static function getSampleCsv(): string
    {
        return "hanzi,pinyin,meaning,example,example_pinyin,example_meaning,hsk_level,tags\r\n" .
            "你好,nǐ hǎo,xin chào,你好吗？,Nǐ hǎo ma?,Bạn khỏe không?,1,\"chào hỏi, cơ bản\"\r\n" .
            "谢谢,xièxie,cảm ơn,非常感谢你！,Fēicháng gǎnxiè nǐ!,Rất cảm ơn bạn!,1,\"lịch sự, giao tiếp\"\r\n" .
            "再见,zàijiàn,tạm biệt,明天再见！,Míngtiān zàijiàn!,Ngày mai gặp lại!,1,\"chào hỏi\"\r\n" .
            "学习,xuéxí,học tập,我喜欢学习汉语。,Wǒ xǐhuān xuéxí hànyǔ.,Tôi thích học tiếng Hán.,2,\"học tập, động từ\"\r\n";
    }
}
