<?php

namespace App\Services;

use App\Models\Flashcard;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use InvalidArgumentException;

class GameService
{
    public function __construct(
        protected LearningActivityService $learningActivityService,
        protected XpService $xpService,
        protected DailyGoalService $dailyGoalService,
        protected GuestProgressService $guestProgressService
    ) {}

    /**
     * Minimal pair datasets for tricky Mandarin sounds.
     */
    protected const MINIMAL_PAIRS_DATA = [
        [
            'type' => 'initial',
            'group' => 'j_q_x',
            'title' => 'q vs j (Âm mặt lưỡi)',
            'hint' => 'q bật hơi mạnh, j không bật hơi',
            'pinyin_link' => '/pinyin?tab=initials',
            'link_text' => 'Luyện thanh mẫu j / q / x',
            'pairs' => [
                ['audio_pinyin' => 'qī', 'audio_text' => '七', 'meaning' => 'số 7', 'options' => ['qī', 'jī', 'xī', 'tī'], 'correct' => 'qī'],
                ['audio_pinyin' => 'jī', 'audio_text' => '鸡', 'meaning' => 'con gà', 'options' => ['qī', 'jī', 'xī', 'dī'], 'correct' => 'jī'],
                ['audio_pinyin' => 'qián', 'audio_text' => '钱', 'meaning' => 'tiền', 'options' => ['qián', 'jián', 'xián', 'tián'], 'correct' => 'qián'],
                ['audio_pinyin' => 'qù', 'audio_text' => '去', 'meaning' => 'đi', 'options' => ['qù', 'jù', 'xù', 'chù'], 'correct' => 'qù'],
                ['audio_pinyin' => 'jù', 'audio_text' => '句', 'meaning' => 'câu', 'options' => ['jù', 'qù', 'xù', 'zhù'], 'correct' => 'jù'],
            ],
        ],
        [
            'type' => 'initial',
            'group' => 'zh_z',
            'title' => 'zh vs z (Uốn lưỡi vs Thẳng lưỡi)',
            'hint' => 'zh uốn đầu lưỡi chạm ngạc cứng, z đầu lưỡi thẳng chạm mặt sau răng',
            'pinyin_link' => '/pinyin?tab=initials',
            'link_text' => 'Luyện thanh mẫu zh / ch / sh',
            'pairs' => [
                ['audio_pinyin' => 'zhī', 'audio_text' => '知', 'meaning' => 'biết', 'options' => ['zhī', 'zī', 'chī', 'cī'], 'correct' => 'zhī'],
                ['audio_pinyin' => 'zī', 'audio_text' => '资', 'meaning' => 'tư chất / tài nguyên', 'options' => ['zī', 'zhī', 'sī', 'cī'], 'correct' => 'zī'],
                ['audio_pinyin' => 'zhōng', 'audio_text' => '中', 'meaning' => 'trung tâm / giữa', 'options' => ['zhōng', 'zōng', 'chōng', 'sōng'], 'correct' => 'zhōng'],
                ['audio_pinyin' => 'zhù', 'audio_text' => '住', 'meaning' => 'trú / sống', 'options' => ['zhù', 'zù', 'chù', 'shù'], 'correct' => 'zhù'],
            ],
        ],
        [
            'type' => 'initial',
            'group' => 'ch_c',
            'title' => 'ch vs c (Bật hơi: Uốn lưỡi vs Thẳng lưỡi)',
            'hint' => 'ch bật hơi và uốn lưỡi, c bật hơi và lưỡi thẳng tự nhiên',
            'pinyin_link' => '/pinyin?tab=initials',
            'link_text' => 'Luyện nhóm âm đầu lưỡi c / ch',
            'pairs' => [
                ['audio_pinyin' => 'chī', 'audio_text' => '吃', 'meaning' => 'ăn', 'options' => ['chī', 'cī', 'zhī', 'qī'], 'correct' => 'chī'],
                ['audio_pinyin' => 'chū', 'audio_text' => '出', 'meaning' => 'ra ngoài', 'options' => ['chū', 'cū', 'zhū', 'shū'], 'correct' => 'chū'],
                ['audio_pinyin' => 'cài', 'audio_text' => '菜', 'meaning' => 'món ăn / rau', 'options' => ['cài', 'chài', 'zài', 'tài'], 'correct' => 'cài'],
            ],
        ],
        [
            'type' => 'initial',
            'group' => 'sh_s',
            'title' => 'sh vs s (Xát âm uốn lưỡi vs thẳng lưỡi)',
            'hint' => 'sh uốn lưỡi ma sát nhẹ, s răng khép lưỡi phẳng',
            'pinyin_link' => '/pinyin?tab=initials',
            'link_text' => 'Luyện thanh mẫu sh / s',
            'pairs' => [
                ['audio_pinyin' => 'shān', 'audio_text' => '山', 'meaning' => 'núi', 'options' => ['shān', 'sān', 'chān', 'fān'], 'correct' => 'shān'],
                ['audio_pinyin' => 'sān', 'audio_text' => '三', 'meaning' => 'số 3', 'options' => ['sān', 'shān', 'cān', 'zān'], 'correct' => 'sān'],
                ['audio_pinyin' => 'shī', 'audio_text' => '师', 'meaning' => 'thầy / chuyên gia', 'options' => ['shī', 'sī', 'chī', 'xī'], 'correct' => 'shī'],
                ['audio_pinyin' => 'shì', 'audio_text' => '是', 'meaning' => 'là / phải', 'options' => ['shì', 'sì', 'zhì', 'cì'], 'correct' => 'shì'],
                ['audio_pinyin' => 'sì', 'audio_text' => '四', 'meaning' => 'số 4', 'options' => ['sì', 'shì', 'cì', 'zì'], 'correct' => 'sì'],
            ],
        ],
        [
            'type' => 'initial',
            'group' => 'b_p',
            'title' => 'b vs p (Hai môi: Không bật hơi vs Bật hơi)',
            'hint' => 'b ngậm môi mở nhẹ không hơi, p mím chặt môi bật hơi cực mạnh',
            'pinyin_link' => '/pinyin?tab=initials',
            'link_text' => 'Luyện âm hai môi b / p',
            'pairs' => [
                ['audio_pinyin' => 'bā', 'audio_text' => '八', 'meaning' => 'số 8', 'options' => ['bā', 'pā', 'dā', 'mā'], 'correct' => 'bā'],
                ['audio_pinyin' => 'pā', 'audio_text' => '趴', 'meaning' => 'nằm sấp', 'options' => ['pā', 'bā', 'tā', 'kā'], 'correct' => 'pā'],
                ['audio_pinyin' => 'bǐ', 'audio_text' => '笔', 'meaning' => 'cây bút', 'options' => ['bǐ', 'pǐ', 'dǐ', 'mǐ'], 'correct' => 'bǐ'],
                ['audio_pinyin' => 'pào', 'audio_text' => '泡', 'meaning' => 'ngâm / bọt nước', 'options' => ['pào', 'bào', 'tào', 'dào'], 'correct' => 'pào'],
            ],
        ],
        [
            'type' => 'final',
            'group' => 'in_ing',
            'title' => 'in vs ing (Mũi trước n vs Mũi sau ng)',
            'hint' => 'in đầu lưỡi chạm lợi trên, ing cuống lưỡi chạm ngạc mềm ngân trong khoang mũi',
            'pinyin_link' => '/pinyin?tab=finals',
            'link_text' => 'Luyện vận mẫu in / ing',
            'pairs' => [
                ['audio_pinyin' => 'xīn', 'audio_text' => '心', 'meaning' => 'trái tim / tấm lòng', 'options' => ['xīn', 'xīng', 'xēn', 'xān'], 'correct' => 'xīn'],
                ['audio_pinyin' => 'xīng', 'audio_text' => '星', 'meaning' => 'ngôi sao', 'options' => ['xīng', 'xīn', 'xiāng', 'xēng'], 'correct' => 'xīng'],
                ['audio_pinyin' => 'jīn', 'audio_text' => '今', 'meaning' => 'nay / hiện tại', 'options' => ['jīn', 'jīng', 'jiān', 'jēn'], 'correct' => 'jīn'],
                ['audio_pinyin' => 'jīng', 'audio_text' => '经', 'meaning' => 'kinh qua / trải nghiệm', 'options' => ['jīng', 'jīn', 'jiāng', 'jèng'], 'correct' => 'jīng'],
            ],
        ],
        [
            'type' => 'tone',
            'group' => 'tone1_tone4',
            'title' => 'Thanh 1 (55) vs Thanh 4 (51)',
            'hint' => 'Thanh 1 âm vực cao bằng phẳng ngân dài, Thanh 4 giáng mạnh dứt khoát',
            'pinyin_link' => '/pinyin?tab=tones',
            'link_text' => 'Luyện quy tắc thanh điệu 1 & 4',
            'pairs' => [
                ['audio_pinyin' => 'mā', 'audio_text' => '妈', 'meaning' => 'mẹ (Thanh 1)', 'options' => ['mā', 'má', 'mǎ', 'mà'], 'correct' => 'mā'],
                ['audio_pinyin' => 'mà', 'audio_text' => '骂', 'meaning' => 'mắng (Thanh 4)', 'options' => ['mà', 'má', 'mǎ', 'mā'], 'correct' => 'mà'],
                ['audio_pinyin' => 'shī', 'audio_text' => '师', 'meaning' => 'thầy (Thanh 1)', 'options' => ['shī', 'shí', 'shǐ', 'shì'], 'correct' => 'shī'],
                ['audio_pinyin' => 'shì', 'audio_text' => '是', 'meaning' => 'là / đúng (Thanh 4)', 'options' => ['shì', 'shī', 'shí', 'shǐ'], 'correct' => 'shì'],
            ],
        ],
    ];

    /**
     * Create Fast Match session with encrypted token.
     *
     * @return array{token: string, cards: array, total_pairs: int, difficulty: string, mode: string, time_limit: ?int}
     */
    public function createFastMatchSession(
        string $mode = 'practice',
        string $difficulty = 'medium',
        ?int $hskLevel = null,
        ?int $lessonId = null,
        ?User $user = null
    ): array {
        $pairCount = match ($difficulty) {
            'easy' => 4,
            'challenge' => 6,
            default => 5, // 'medium'
        };

        $query = Flashcard::query()->where('is_active', true);
        if ($lessonId) {
            $query->where('lesson_id', $lessonId);
        } elseif ($hskLevel) {
            $query->where('hsk_level', $hskLevel);
        }

        $flashcards = $query->inRandomOrder()->take($pairCount)->get();

        // Fallback to any active flashcards if filter returned too few
        if ($flashcards->count() < $pairCount) {
            $flashcards = Flashcard::query()
                ->where('is_active', true)
                ->inRandomOrder()
                ->take($pairCount)
                ->get();
        }

        // If database has very few flashcards (e.g. fresh environment or test)
        if ($flashcards->isEmpty()) {
            $fallbackData = [
                ['id' => 1, 'hanzi' => '你好', 'pinyin' => 'nǐ hǎo', 'meaning' => 'xin chào'],
                ['id' => 2, 'hanzi' => '谢谢', 'pinyin' => 'xièxie', 'meaning' => 'cảm ơn'],
                ['id' => 3, 'hanzi' => '再见', 'pinyin' => 'zàijiàn', 'meaning' => 'tạm biệt'],
                ['id' => 4, 'hanzi' => '朋友', 'pinyin' => 'péngyou', 'meaning' => 'bạn bè'],
                ['id' => 5, 'hanzi' => '老师', 'pinyin' => 'lǎoshī', 'meaning' => 'thầy cô'],
                ['id' => 6, 'hanzi' => '中国', 'pinyin' => 'Zhōngguó', 'meaning' => 'Trung Quốc'],
            ];
            $selected = array_slice($fallbackData, 0, $pairCount);
            $items = collect($selected)->map(fn ($item) => (object) $item);
        } else {
            $items = $flashcards;
        }

        $cardToPair = [];
        $pairDetails = [];
        $clientCards = [];

        foreach ($items as $idx => $card) {
            $pairId = 'p_' . $idx . '_' . Str::random(6);
            $cardAId = 'c_a_' . Str::random(8);
            $cardBId = 'c_b_' . Str::random(8);

            $cardToPair[$cardAId] = $pairId;
            $cardToPair[$cardBId] = $pairId;

            $pairDetails[$pairId] = [
                'id' => $card->id,
                'hanzi' => $card->hanzi,
                'pinyin' => $card->pinyin,
                'meaning' => $card->meaning,
            ];

            $pairHash = substr(hash('sha256', $pairId), 0, 10);

            // Card A is Hanzi
            $clientCards[] = [
                'id' => $cardAId,
                'pair_hash' => $pairHash,
                'type' => 'hanzi',
                'content' => $card->hanzi,
                'subtext' => null,
            ];

            // Card B is Meaning or Tone Pinyin depending on mode
            if ($mode === 'hanzi_pinyin') {
                $clientCards[] = [
                    'id' => $cardBId,
                    'pair_hash' => $pairHash,
                    'type' => 'pinyin',
                    'content' => $card->pinyin,
                    'subtext' => null,
                ];
            } else {
                $clientCards[] = [
                    'id' => $cardBId,
                    'pair_hash' => $pairHash,
                    'type' => 'meaning',
                    'content' => $card->meaning,
                    'subtext' => null,
                ];
            }
        }

        shuffle($clientCards);

        $timeLimit = match ($mode) {
            'time_challenge' => match ($difficulty) {
                'easy' => 45,
                'challenge' => 75,
                default => 60,
            },
            default => null,
        };

        $sessionPayload = [
            'session_id' => (string) Str::uuid(),
            'game_type' => 'fast_match',
            'mode' => $mode,
            'difficulty' => $difficulty,
            'card_to_pair' => $cardToPair,
            'pair_details' => $pairDetails,
            'total_pairs' => count($items),
            'time_limit' => $timeLimit,
            'created_at' => Carbon::now()->timestamp,
            'user_id' => $user?->id,
        ];

        $token = Crypt::encryptString(json_encode($sessionPayload));

        return [
            'token' => $token,
            'cards' => $clientCards,
            'total_pairs' => count($items),
            'difficulty' => $difficulty,
            'mode' => $mode,
            'time_limit' => $timeLimit,
        ];
    }

    /**
     * Decrypt and verify Fast Match session, calculate score, accuracy, medal, and award XP.
     *
     * @param array<array{c1: string, c2: string}> $moves
     * @return array
     */
    public function verifyFastMatchSession(
        string $token,
        array $moves,
        int $durationSeconds,
        ?User $user = null,
        ?string $guestUuid = null,
        ?string $claimToken = null
    ): array {
        try {
            $payload = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException $e) {
            throw new InvalidArgumentException('Mã phiên chơi không hợp lệ hoặc đã bị thay đổi.');
        }

        if (!isset($payload['game_type']) || $payload['game_type'] !== 'fast_match') {
            throw new InvalidArgumentException('Phiên chơi không đúng loại.');
        }

        // Check expiration (max 20 minutes)
        if (Carbon::now()->timestamp - ($payload['created_at'] ?? 0) > 1200) {
            throw new InvalidArgumentException('Phiên chơi đã hết hạn.');
        }

        $cardToPair = $payload['card_to_pair'] ?? [];
        $pairDetails = $payload['pair_details'] ?? [];
        $totalPairs = (int) ($payload['total_pairs'] ?? 0);
        $timeLimit = $payload['time_limit'] ?? null;

        $solvedPairs = [];
        $missedWords = [];
        $correctCount = 0;
        $wrongCount = 0;
        $currentCombo = 0;
        $maxCombo = 0;

        foreach ($moves as $move) {
            $c1 = $move['c1'] ?? null;
            $c2 = $move['c2'] ?? null;

            if (!$c1 || !$c2 || !isset($cardToPair[$c1], $cardToPair[$c2])) {
                continue;
            }

            if ($cardToPair[$c1] === $cardToPair[$c2]) {
                $pairId = $cardToPair[$c1];
                if (!isset($solvedPairs[$pairId])) {
                    $solvedPairs[$pairId] = true;
                    $correctCount++;
                    $currentCombo++;
                    $maxCombo = max($maxCombo, $currentCombo);
                }
            } else {
                // Wrong match attempt
                $wrongCount++;
                $currentCombo = 0; // Combo resets to 0

                // Capture missed word details
                $p1 = $cardToPair[$c1];
                $p2 = $cardToPair[$c2];
                if (isset($pairDetails[$p1])) {
                    $missedWords[$p1] = $pairDetails[$p1];
                }
                if (isset($pairDetails[$p2])) {
                    $missedWords[$p2] = $pairDetails[$p2];
                }
            }
        }

        $solvedCount = count($solvedPairs);
        $totalAttempts = $correctCount + $wrongCount;
        $accuracy = $totalAttempts > 0 ? (int) round(($correctCount / $totalAttempts) * 100) : 0;
        $safeDuration = max(1, min(600, $durationSeconds));

        // Score formula: (Correct * 100) + (Max combo * 20) + (Remaining seconds * 5)
        $speedBonus = 0;
        if ($timeLimit !== null && $solvedCount === $totalPairs) {
            $remaining = max(0, $timeLimit - $safeDuration);
            $speedBonus = $remaining * 5;
        } elseif ($solvedCount === $totalPairs) {
            // Practice mode speed bonus
            $speedBonus = max(0, (60 - $safeDuration)) * 2;
        }

        $score = ($correctCount * 100) + ($maxCombo * 20) + $speedBonus;

        // Determine Medal
        $medal = 'none';
        if ($solvedCount === $totalPairs && $totalPairs > 0) {
            if ($accuracy >= 90) {
                $medal = 'gold';
            } elseif ($accuracy >= 75) {
                $medal = 'silver';
            } else {
                $medal = 'bronze';
            }
        }

        $xpEarned = 0;
        $activityResult = null;
        $guestProgressData = null;

        if ($user) {
            $idempotencyKey = 'fm_' . ($payload['session_id'] ?? Str::uuid());
            $activityResult = $this->learningActivityService->logActivity($user, 'fast_match_completed', [
                'source_type' => 'game',
                'idempotency_key' => $idempotencyKey,
                'meta' => [
                    'game_type' => 'fast_match',
                    'difficulty' => $payload['difficulty'],
                    'mode' => $payload['mode'],
                    'score' => $score,
                    'accuracy' => $accuracy,
                    'max_combo' => $maxCombo,
                    'duration_seconds' => $safeDuration,
                    'missed_count' => count($missedWords),
                ],
            ]);
            $xpEarned = $activityResult['xp']['earned'];
        } else {
            // Guest progress tracking
            $resolvedGuestUuid = null;
            if ($claimToken) {
                $tokenPayload = $this->guestProgressService->verifySignedToken($claimToken);
                if ($tokenPayload && ! empty($tokenPayload['guest_uuid'])) {
                    $resolvedGuestUuid = $tokenPayload['guest_uuid'];
                }
            }
            if (! $resolvedGuestUuid && $guestUuid) {
                $resolvedGuestUuid = $guestUuid;
            }

            $guestProgressRecord = $this->guestProgressService->resolveOrCreateProgress($resolvedGuestUuid);
            $idempotencyKey = 'fm_' . ($payload['session_id'] ?? Str::uuid());

            $guestResult = $this->guestProgressService->recordActivity(
                guestUuid: $guestProgressRecord->guest_uuid,
                activityType: 'fast_match_completed',
                data: [
                    'source_type' => 'game',
                    'idempotency_key' => $idempotencyKey,
                    'meta' => [
                        'game_type' => 'fast_match',
                        'difficulty' => $payload['difficulty'],
                        'mode' => $payload['mode'],
                        'score' => $score,
                        'accuracy' => $accuracy,
                        'max_combo' => $maxCombo,
                        'duration_seconds' => $safeDuration,
                        'missed_count' => count($missedWords),
                    ],
                ]
            );

            $xpEarned = $guestResult['xp_earned'];
            $guestProgressData = [
                'guest_uuid' => $guestResult['guest_uuid'],
                'xp_earned' => $guestResult['xp_earned'],
                'total_xp' => $guestResult['total_xp'],
                'activities_count' => $guestResult['activities_count'],
                'claim_token' => $guestResult['claim_token'],
            ];
        }

        return [
            'success' => true,
            'score' => $score,
            'accuracy' => $accuracy,
            'max_combo' => $maxCombo,
            'duration_seconds' => $safeDuration,
            'solved_pairs' => $solvedCount,
            'total_pairs' => $totalPairs,
            'medal' => $medal,
            'xp_earned' => $xpEarned,
            'total_xp' => $user ? ($activityResult['xp']['total'] ?? null) : ($guestProgressData['total_xp'] ?? null),
            'streak' => $activityResult['streak']['current'] ?? null,
            'daily_goal' => $activityResult['daily_goal'] ?? null,
            'claim_token' => $guestProgressData['claim_token'] ?? null,
            'guest_progress' => $guestProgressData,
            'missed_words' => array_values($missedWords),
        ];
    }

    /**
     * Create Audio Pop Quiz session with encrypted token.
     *
     * @return array{token: string, questions: array, total_questions: int, mode: string}
     */
    public function createAudioQuizSession(
        string $mode = 'vocabulary',
        ?int $hskLevel = null,
        ?User $user = null
    ): array {
        $questions = [];
        $answersKey = [];

        if ($mode === 'minimal_pairs') {
            // Pick from minimal pairs dataset
            $groups = self::MINIMAL_PAIRS_DATA;
            shuffle($groups);
            $selectedGroups = array_slice($groups, 0, 5);

            foreach ($selectedGroups as $idx => $grp) {
                $subPairs = $grp['pairs'];
                shuffle($subPairs);
                $target = $subPairs[0];

                $qId = 'aq_mp_' . $idx . '_' . Str::random(6);
                $options = $target['options'];
                shuffle($options);

                $questions[] = [
                    'id' => $qId,
                    'type' => 'minimal_pairs',
                    'prompt' => 'Nghe âm tiết và chọn cách viết Pinyin đúng:',
                    'audio_text' => $target['audio_text'],
                    'audio_pinyin' => $target['audio_pinyin'],
                    'options' => $options,
                    'hint' => $grp['hint'],
                    'group_title' => $grp['title'],
                    'pinyin_link' => $grp['pinyin_link'],
                    'link_text' => $grp['link_text'],
                ];

                $answersKey[$qId] = [
                    'correct' => $target['correct'],
                    'audio_text' => $target['audio_text'],
                    'audio_pinyin' => $target['audio_pinyin'],
                    'meaning' => $target['meaning'],
                    'hint' => $grp['hint'],
                    'group_title' => $grp['title'],
                    'pinyin_link' => $grp['pinyin_link'],
                    'link_text' => $grp['link_text'],
                ];
            }
        } elseif ($mode === 'tone_recognition') {
            // Syllable tone recognition: e.g. mā, má, mǎ, mà
            $baseTones = [
                ['base' => 'ma', 'char' => '妈', 'tones' => ['mā', 'má', 'mǎ', 'mà'], 'correct_idx' => 0, 'meaning' => 'mẹ'],
                ['base' => 'ba', 'char' => '八', 'tones' => ['bā', 'bá', 'bǎ', 'bà'], 'correct_idx' => 0, 'meaning' => 'số 8'],
                ['base' => 'shi', 'char' => '是', 'tones' => ['shī', 'shí', 'shǐ', 'shì'], 'correct_idx' => 3, 'meaning' => 'là'],
                ['base' => 'hao', 'char' => '好', 'tones' => ['hāo', 'háo', 'hǎo', 'hào'], 'correct_idx' => 2, 'meaning' => 'tốt / khoẻ'],
                ['base' => 'xie', 'char' => '谢', 'tones' => ['xiē', 'xié', 'xiě', 'xiè'], 'correct_idx' => 3, 'meaning' => 'cảm ơn'],
                ['base' => 'guo', 'char' => '国', 'tones' => ['guō', 'guó', 'guǒ', 'guò'], 'correct_idx' => 1, 'meaning' => 'nước / quốc gia'],
            ];
            shuffle($baseTones);
            $selected = array_slice($baseTones, 0, 5);

            foreach ($selected as $idx => $item) {
                $qId = 'aq_tr_' . $idx . '_' . Str::random(6);
                $correct = $item['tones'][$item['correct_idx']];

                $questions[] = [
                    'id' => $qId,
                    'type' => 'tone_recognition',
                    'prompt' => 'Nghe và nhận diện thanh điệu đúng:',
                    'audio_text' => $item['char'],
                    'audio_pinyin' => $correct,
                    'options' => $item['tones'],
                    'hint' => 'Thanh 1 (ngang cao), Thanh 2 (lên), Thanh 3 (xuống-lên), Thanh 4 (dứt khoát)',
                    'group_title' => 'Thanh điệu tiếng Trung',
                    'pinyin_link' => '/pinyin?tab=tones',
                    'link_text' => 'Luyện 4 thanh điệu chuẩn',
                ];

                $answersKey[$qId] = [
                    'correct' => $correct,
                    'audio_text' => $item['char'],
                    'audio_pinyin' => $correct,
                    'meaning' => $item['meaning'],
                    'hint' => 'Thanh điệu chuẩn của chữ ' . $item['char'] . ' là: ' . $correct,
                    'pinyin_link' => '/pinyin?tab=tones',
                    'link_text' => 'Luyện 4 thanh điệu chuẩn',
                ];
            }
        } else {
            // Mode: vocabulary
            $query = Flashcard::query()->where('is_active', true);
            if ($hskLevel) {
                $query->where('hsk_level', $hskLevel);
            }
            $cards = $query->inRandomOrder()->take(20)->get();

            if ($cards->count() < 5) {
                $cards = Flashcard::query()->where('is_active', true)->inRandomOrder()->take(20)->get();
            }

            if ($cards->isEmpty()) {
                $cards = collect([
                    (object) ['id' => 1, 'hanzi' => '你好', 'pinyin' => 'nǐ hǎo', 'meaning' => 'xin chào'],
                    (object) ['id' => 2, 'hanzi' => '谢谢', 'pinyin' => 'xièxie', 'meaning' => 'cảm ơn'],
                    (object) ['id' => 3, 'hanzi' => '再见', 'pinyin' => 'zàijiàn', 'meaning' => 'tạm biệt'],
                    (object) ['id' => 4, 'hanzi' => '朋友', 'pinyin' => 'péngyou', 'meaning' => 'bạn bè'],
                    (object) ['id' => 5, 'hanzi' => '老师', 'pinyin' => 'lǎoshī', 'meaning' => 'thầy cô'],
                ]);
            }

            $questionCards = $cards->slice(0, 5)->values();
            $allMeanings = $cards->pluck('meaning')->unique()->values()->all();

            foreach ($questionCards as $idx => $card) {
                $qId = 'aq_voc_' . $idx . '_' . Str::random(6);

                // Build 4 options: 1 correct meaning + 3 distractors
                $distractors = array_values(array_filter($allMeanings, fn ($m) => $m !== $card->meaning));
                shuffle($distractors);
                $options = array_slice($distractors, 0, 3);
                $options[] = $card->meaning;
                shuffle($options);

                $questions[] = [
                    'id' => $qId,
                    'type' => 'vocabulary',
                    'prompt' => 'Nghe phát âm và chọn ý nghĩa tiếng Việt đúng:',
                    'audio_text' => $card->hanzi,
                    'audio_pinyin' => $card->pinyin,
                    'options' => $options,
                    'hint' => 'Hãy chú ý thanh điệu và phát âm.',
                    'pinyin_link' => '/flashcards',
                    'link_text' => 'Ôn tập thẻ từ vựng',
                ];

                $answersKey[$qId] = [
                    'correct' => $card->meaning,
                    'audio_text' => $card->hanzi,
                    'audio_pinyin' => $card->pinyin,
                    'meaning' => $card->meaning,
                    'hint' => "{$card->hanzi} ({$card->pinyin}): {$card->meaning}",
                    'pinyin_link' => '/flashcards',
                    'link_text' => 'Ôn tập thẻ từ vựng',
                ];
            }
        }

        $sessionPayload = [
            'session_id' => (string) Str::uuid(),
            'game_type' => 'audio_quiz',
            'mode' => $mode,
            'answers_key' => $answersKey,
            'total_questions' => count($questions),
            'created_at' => Carbon::now()->timestamp,
            'user_id' => $user?->id,
        ];

        $token = Crypt::encryptString(json_encode($sessionPayload));

        return [
            'token' => $token,
            'questions' => $questions,
            'total_questions' => count($questions),
            'mode' => $mode,
        ];
    }

    /**
     * Decrypt and verify Audio Quiz session, calculate speed score, accuracy, medal, and award XP.
     *
     * @param array<string, array{selected: string, response_ms?: int}>|array<string, string> $answers
     * @return array
     */
    public function verifyAudioQuizSession(
        string $token,
        array $answers,
        int $durationSeconds,
        ?User $user = null,
        ?string $guestUuid = null,
        ?string $claimToken = null
    ): array {
        try {
            $payload = json_decode(Crypt::decryptString($token), true);
        } catch (DecryptException $e) {
            throw new InvalidArgumentException('Mã phiên chơi không hợp lệ hoặc đã bị thay đổi.');
        }

        if (!isset($payload['game_type']) || $payload['game_type'] !== 'audio_quiz') {
            throw new InvalidArgumentException('Phiên chơi không đúng loại.');
        }

        if (Carbon::now()->timestamp - ($payload['created_at'] ?? 0) > 1200) {
            throw new InvalidArgumentException('Phiên chơi đã hết hạn.');
        }

        $answersKey = $payload['answers_key'] ?? [];
        $totalQuestions = (int) ($payload['total_questions'] ?? count($answersKey));

        $correctCount = 0;
        $totalScore = 0;
        $missedItems = [];

        foreach ($answersKey as $qId => $keyData) {
            $userSubmission = $answers[$qId] ?? null;
            $selected = is_array($userSubmission) ? ($userSubmission['selected'] ?? null) : $userSubmission;
            $responseMs = is_array($userSubmission) ? ($userSubmission['response_ms'] ?? 3000) : 3000;

            if ($selected !== null && $selected === $keyData['correct']) {
                $correctCount++;

                // Speed score calculation
                if ($responseMs < 2500) {
                    $speedScore = 100;
                } elseif ($responseMs <= 5000) {
                    $speedScore = 80;
                } else {
                    $speedScore = 60;
                }
                $totalScore += $speedScore;
            } else {
                // Wrong or omitted answer
                $missedItems[] = [
                    'id' => $qId,
                    'hanzi' => $keyData['audio_text'],
                    'pinyin' => $keyData['audio_pinyin'],
                    'meaning' => $keyData['meaning'],
                    'correct' => $keyData['correct'],
                    'selected' => $selected,
                    'hint' => $keyData['hint'] ?? null,
                    'pinyin_link' => $keyData['pinyin_link'] ?? null,
                    'link_text' => $keyData['link_text'] ?? null,
                ];
            }
        }

        $accuracy = $totalQuestions > 0 ? (int) round(($correctCount / $totalQuestions) * 100) : 0;
        $safeDuration = max(1, min(600, $durationSeconds));

        // Medal calculation
        $medal = 'none';
        if ($totalQuestions > 0) {
            if ($accuracy >= 90) {
                $medal = 'gold';
            } elseif ($accuracy >= 70) {
                $medal = 'silver';
            } else {
                $medal = 'bronze';
            }
        }

        $xpEarned = 0;
        $activityResult = null;
        $guestProgressData = null;

        if ($user) {
            $idempotencyKey = 'aq_' . ($payload['session_id'] ?? Str::uuid());
            $activityResult = $this->learningActivityService->logActivity($user, 'audio_quiz_completed', [
                'source_type' => 'game',
                'idempotency_key' => $idempotencyKey,
                'meta' => [
                    'game_type' => 'audio_quiz',
                    'mode' => $payload['mode'],
                    'score' => $totalScore,
                    'accuracy' => $accuracy,
                    'duration_seconds' => $safeDuration,
                    'missed_count' => count($missedItems),
                ],
            ]);
            $xpEarned = $activityResult['xp']['earned'];
        } else {
            // Guest progress tracking
            $resolvedGuestUuid = null;
            if ($claimToken) {
                $tokenPayload = $this->guestProgressService->verifySignedToken($claimToken);
                if ($tokenPayload && ! empty($tokenPayload['guest_uuid'])) {
                    $resolvedGuestUuid = $tokenPayload['guest_uuid'];
                }
            }
            if (! $resolvedGuestUuid && $guestUuid) {
                $resolvedGuestUuid = $guestUuid;
            }

            $guestProgressRecord = $this->guestProgressService->resolveOrCreateProgress($resolvedGuestUuid);
            $idempotencyKey = 'aq_' . ($payload['session_id'] ?? Str::uuid());

            $guestResult = $this->guestProgressService->recordActivity(
                guestUuid: $guestProgressRecord->guest_uuid,
                activityType: 'audio_quiz_completed',
                data: [
                    'source_type' => 'game',
                    'idempotency_key' => $idempotencyKey,
                    'meta' => [
                        'game_type' => 'audio_quiz',
                        'mode' => $payload['mode'],
                        'score' => $totalScore,
                        'accuracy' => $accuracy,
                        'duration_seconds' => $safeDuration,
                        'missed_count' => count($missedItems),
                    ],
                ]
            );

            $xpEarned = $guestResult['xp_earned'];
            $guestProgressData = [
                'guest_uuid' => $guestResult['guest_uuid'],
                'xp_earned' => $guestResult['xp_earned'],
                'total_xp' => $guestResult['total_xp'],
                'activities_count' => $guestResult['activities_count'],
                'claim_token' => $guestResult['claim_token'],
            ];
        }

        return [
            'success' => true,
            'score' => $totalScore,
            'accuracy' => $accuracy,
            'correct_count' => $correctCount,
            'total_questions' => $totalQuestions,
            'duration_seconds' => $safeDuration,
            'medal' => $medal,
            'xp_earned' => $xpEarned,
            'total_xp' => $user ? ($activityResult['xp']['total'] ?? null) : ($guestProgressData['total_xp'] ?? null),
            'streak' => $activityResult['streak']['current'] ?? null,
            'daily_goal' => $activityResult['daily_goal'] ?? null,
            'claim_token' => $guestProgressData['claim_token'] ?? null,
            'guest_progress' => $guestProgressData,
            'missed_words' => $missedItems,
        ];
    }
}
