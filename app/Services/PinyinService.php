<?php

namespace App\Services;

class PinyinService
{
    protected ?array $initials = null;
    protected ?array $finals = null;
    protected ?array $syllables = null;
    protected ?array $toneRules = null;
    protected ?array $matrix = null;

    public function getInitials(): array
    {
        if ($this->initials === null) {
            $this->initials = require resource_path('data/pinyin/initials.php');
        }
        return $this->initials;
    }

    public function getFinals(): array
    {
        if ($this->finals === null) {
            $this->finals = require resource_path('data/pinyin/finals.php');
        }
        return $this->finals;
    }

    public function getSyllables(): array
    {
        if ($this->syllables === null) {
            $this->syllables = require resource_path('data/pinyin/syllables.php');
        }
        return $this->syllables;
    }

    public function getToneRules(): array
    {
        if ($this->toneRules === null) {
            $this->toneRules = require resource_path('data/pinyin/tone_rules.php');
        }
        return $this->toneRules;
    }

    public function getSyllable(string $syllable): ?array
    {
        $syllables = $this->getSyllables();
        $key = strtolower(trim($syllable));
        return $syllables[$key] ?? null;
    }

    /**
     * Get the syllable matrix data:
     * - 'initials': list of initials with codes and labels (including zero-initial / '')
     * - 'finals': list of finals with codes and labels
     * - 'cells': 2D lookup map [$initial][$final] => [ 'syllable' => ..., 'tones' => [...] ]
     * - 'total_syllables': count of valid syllables
     */
    public function getMatrix(): array
    {
        if ($this->matrix !== null) {
            return $this->matrix;
        }

        $initialsData = $this->getInitials();
        $finalsData = $this->getFinals();
        $syllables = $this->getSyllables();

        // Flatten initials
        $initialsList = [];
        // Zero initial (vowels beginning syllables without initial consonant)
        $initialsList[] = [
            'initial' => '',
            'name' => 'Ø (Không phụ âm)',
            'ipa' => '-',
            'description' => 'Các âm tiết bắt đầu trực tiếp bằng nguyên âm',
        ];
        foreach ($initialsData['list'] as $initCode => $item) {
            $initialsList[] = $item;
        }

        // Flatten finals
        $finalsList = [];
        foreach ($finalsData['list'] as $finalCode => $item) {
            $finalsList[] = $item;
        }

        // Build lookup cells
        $cells = [];
        foreach ($syllables as $sylKey => $data) {
            $init = $data['initial'] ?? '';
            $final = $data['final'] ?? '';
            $cells[$init][$final] = [
                'syllable' => $sylKey,
                'tones' => $data['tones'] ?? [],
            ];
        }

        $this->matrix = [
            'initials' => $initialsList,
            'finals' => $finalsList,
            'cells' => $cells,
            'total_syllables' => count($syllables),
        ];

        return $this->matrix;
    }

    /**
     * Search syllables, characters, or meanings.
     */
    public function search(string $query): array
    {
        $query = mb_strtolower(trim($query));
        if ($query === '') {
            return [];
        }

        $syllables = $this->getSyllables();
        $results = [];

        foreach ($syllables as $sylKey => $data) {
            $matched = false;

            // Direct syllable match
            if (str_contains($sylKey, $query)) {
                $matched = true;
            }

            // Check tones, characters, meanings
            if (!$matched) {
                foreach ($data['tones'] as $t) {
                    if (
                        str_contains(mb_strtolower($t['pinyin']), $query) ||
                        str_contains($t['char'], $query) ||
                        str_contains(mb_strtolower($t['meaning']), $query)
                    ) {
                        $matched = true;
                        break;
                    }
                }
            }

            if ($matched) {
                $results[] = $data;
            }
        }

        return $results;
    }
}
