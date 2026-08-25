<?php

namespace App\Console\Commands;

use App\Models\Flashcard;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Stichoza\GoogleTranslate\GoogleTranslate;

class ImportHskData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:import-hsk {--level= : Specific HSK level to import (1-6)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import 5000 HSK words and automatically translate meanings to Vietnamese';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Bắt đầu tải và đồng bộ 5000 từ vựng HSK...");
        
        $tr = new GoogleTranslate('vi');
        $tr->setSource('en');

        $levelsToImport = $this->option('level') ? [$this->option('level')] : [1, 2, 3, 4, 5, 6];

        foreach ($levelsToImport as $level) {
            $this->info("\nĐang xử lý HSK Cấp độ $level...");
            
            $url = "https://raw.githubusercontent.com/clem109/hsk-vocabulary/master/hsk-vocab-json/hsk-level-{$level}.json";
            
            try {
                $response = Http::timeout(30)->get($url);
                
                if (!$response->successful()) {
                    $this->error("Không thể tải file JSON từ Github cho HSK $level");
                    continue;
                }

                $words = $response->json();
                
                if (!is_array($words)) {
                    $this->error("Dữ liệu lỗi cho HSK $level");
                    continue;
                }

                $bar = $this->output->createProgressBar(count($words));
                $bar->start();

                foreach ($words as $word) {
                    $hanzi = $word['hanzi'] ?? '';
                    $pinyin = $word['pinyin'] ?? '';
                    $englishMeanings = implode(', ', $word['translations'] ?? []);
                    
                    if (empty($hanzi)) continue;
                    
                    // Skip if word already exists to avoid duplicates
                    if (!Flashcard::where('hanzi', $hanzi)->exists()) {
                        try {
                            $vietnamese = $tr->translate($englishMeanings);
                            
                            Flashcard::create([
                                'hanzi' => $hanzi,
                                'pinyin' => $pinyin,
                                'meaning' => $vietnamese,
                                'hsk_level' => $level,
                                'is_active' => true,
                            ]);
                        } catch (\Exception $e) {
                            // If translation fails (e.g. rate limit), use english as fallback
                            Flashcard::create([
                                'hanzi' => $hanzi,
                                'pinyin' => $pinyin,
                                'meaning' => $englishMeanings,
                                'hsk_level' => $level,
                                'is_active' => true,
                            ]);
                        }
                    }
                    
                    $bar->advance();
                }
                
                $bar->finish();
                $this->newLine();
                $this->info("Hoàn tất nhập dữ liệu HSK $level!");
                
            } catch (\Exception $e) {
                $this->error("Lỗi khi kết nối: " . $e->getMessage());
            }
        }

        $this->info("\nTuyệt vời! Đã bơm toàn bộ từ vựng HSK vào Database thành công.");
    }
}
