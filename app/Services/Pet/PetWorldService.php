<?php
namespace App\Services\Pet;

use App\Models\Flashcard;
use App\Models\PetWorldObject;
use App\Models\UserPet;

class PetWorldService
{
    public static function classify(string $hanzi): array
    {
        $map = [
            '山' => ['nature', '⛰️'], '水' => ['nature', '💧'], '树' => ['nature', '🌳'],
            '花' => ['nature', '🌸'], '草' => ['nature', '🌿'], '风' => ['nature', '🌬️'],
            '雨' => ['nature', '🌧️'], '雪' => ['nature', '❄️'], '云' => ['nature', '☁️'],
            '月' => ['nature', '🌙'], '日' => ['nature', '☀️'], '星' => ['nature', '⭐'],
            '河' => ['nature', '🏞️'], '海' => ['nature', '🌊'], '火' => ['nature', '🔥'],
            '石' => ['nature', '🪨'], '土' => ['nature', '🌍'], '天' => ['nature', '🌤️'],
            '地' => ['nature', '🌏'], '园' => ['nature', '🌺'], '田' => ['nature', '🌾'],
            '林' => ['nature', '🌲'], '森' => ['nature', '🌳'], '竹' => ['nature', '🎋'],
            '桃' => ['nature', '🍑'], '梅' => ['nature', '🌸'],

            '猫' => ['animal', '🐱'], '狗' => ['animal', '🐶'], '鸟' => ['animal', '🐦'],
            '鱼' => ['animal', '🐟'], '马' => ['animal', '🐴'], '牛' => ['animal', '🐄'],
            '羊' => ['animal', '🐑'], '猪' => ['animal', '🐷'], '鸡' => ['animal', '🐔'],
            '虎' => ['animal', '🐯'], '龙' => ['animal', '🐉'], '兔' => ['animal', '🐰'],
            '蛇' => ['animal', '🐍'], '猴' => ['animal', '🐒'], '熊' => ['animal', '🐻'],
            '象' => ['animal', '🐘'], '鸭' => ['animal', '🦆'], '鹅' => ['animal', '🦢'],

            '苹果' => ['food', '🍎'], '香蕉' => ['food', '🍌'], '米' => ['food', '🍚'],
            '饭' => ['food', '🍛'], '面' => ['food', '🍜'], '茶' => ['food', '🍵'],
            '水果' => ['food', '🍑'], '鸡蛋' => ['food', '🥚'], '肉' => ['food', '🥩'],
            '菜' => ['food', '🥬'], '糖' => ['food', '🍬'], '饺子' => ['food', '🥟'],
            '包子' => ['food', '🫔'], '豆腐' => ['food', '🧊'], '汤' => ['food', '🍲'],

            '妈妈' => ['family', '👩'], '爸爸' => ['family', '👨'], '爷爷' => ['family', '👴'],
            '奶奶' => ['family', '👵'], '哥哥' => ['family', '👦'], '姐姐' => ['family', '👧'],
            '弟弟' => ['family', '🧒'], '妹妹' => ['family', '👶'], '家' => ['home', '🏠'],
            '儿子' => ['family', '👦'], '女儿' => ['family', '👧'], '丈夫' => ['family', '🤵'],
            '妻子' => ['family', '👰'], '孩子' => ['family', '🧒'], '朋友' => ['family', '🤝'],

            '书' => ['home', '📚'], '桌' => ['home', '🪑'], '椅' => ['home', '🪑'],
            '床' => ['home', '🛏️'], '门' => ['home', '🚪'], '窗' => ['home', '🪟'],
            '灯' => ['home', '💡'], '电视' => ['home', '📺'], '手机' => ['home', '📱'],
            '电脑' => ['home', '💻'], '钱' => ['home', '💰'], '包' => ['home', '👜'],
            '衣服' => ['home', '👗'], '鞋' => ['home', '👟'], '帽' => ['home', '🧢'],

            '学校' => ['school', '🏫'], '老师' => ['school', '👩‍🏫'], '学生' => ['school', '📖'],
            '笔' => ['school', '✏️'], '纸' => ['school', '📄'], '黑板' => ['school', '🖊️'],
            '课' => ['school', '📝'], '作业' => ['school', '📋'], '考试' => ['school', '📝'],

            '车' => ['transport', '🚗'], '飞机' => ['transport', '✈️'], '火车' => ['transport', '🚂'],
            '船' => ['transport', '⛵'], '路' => ['transport', '🛣️'], '城市' => ['transport', '🏙️'],
        ];

        if (isset($map[$hanzi])) {
            return ['type' => $map[$hanzi][0], 'emoji' => $map[$hanzi][1]];
        }

        return ['type' => 'other', 'emoji' => '✨'];
    }

    public function addWordToWorld(UserPet $userPet, Flashcard $flashcard): ?PetWorldObject
    {
        $hanzi     = $flashcard->hanzi ?? '';
        if (empty($hanzi)) return null;

        $objectKey = "pet_{$userPet->id}_word_{$hanzi}";

        if (PetWorldObject::where('object_key', $objectKey)->exists()) {
            return null;
        }

        $classified = self::classify($hanzi);

        $existingCount = PetWorldObject::where('user_pet_id', $userPet->id)->count();
        $posX = (($existingCount * 17) % 80) + 10;
        $posY = (($existingCount * 23) % 70) + 15;
        $size = $classified['type'] === 'other' ? 2 : 3;

        return PetWorldObject::create([
            'user_pet_id' => $userPet->id,
            'flashcard_id' => $flashcard->id,
            'hanzi'       => $hanzi,
            'object_type' => $classified['type'],
            'emoji'       => $classified['emoji'],
            'object_key'  => $objectKey,
            'pos_x'       => $posX,
            'pos_y'       => $posY,
            'size'        => $size,
            'is_visible'  => true,
            'metadata'    => [
                'pinyin'  => $flashcard->pinyin,
                'meaning' => $flashcard->meaning,
            ],
        ]);
    }

    public function getWorldObjects(UserPet $userPet): array
    {
        $objects = PetWorldObject::where('user_pet_id', $userPet->id)
            ->where('is_visible', true)
            ->orderBy('created_at')
            ->get();

        return $objects->map(fn($o) => [
            'id'      => $o->id,
            'hanzi'   => $o->hanzi,
            'emoji'   => $o->emoji,
            'type'    => $o->object_type,
            'pos_x'   => $o->pos_x,
            'pos_y'   => $o->pos_y,
            'size'    => $o->size,
            'pinyin'  => $o->metadata['pinyin']  ?? '',
            'meaning' => $o->metadata['meaning'] ?? '',
        ])->values()->toArray();
    }

    public function generateDreamSentence(UserPet $userPet): ?string
    {
        $objects = PetWorldObject::where('user_pet_id', $userPet->id)
            ->where('is_visible', true)
            ->inRandomOrder()
            ->limit(10)
            ->get();

        if ($objects->count() < 3) {
            return null;
        }

        $animals  = $objects->where('object_type', 'animal')->pluck('hanzi')->values();
        $nature   = $objects->where('object_type', 'nature')->pluck('hanzi')->values();
        $food     = $objects->where('object_type', 'food')->pluck('hanzi')->values();
        $home     = $objects->where('object_type', 'home')->pluck('hanzi')->values();
        $family   = $objects->where('object_type', 'family')->pluck('hanzi')->values();

        $patterns = [];
        if ($animals->isNotEmpty() && $nature->isNotEmpty()) {
            $patterns[] = "昨晚我梦见了{$animals->random()}和{$nature->random()}……";
        }
        if ($animals->isNotEmpty() && $food->isNotEmpty()) {
            $patterns[] = "梦里有一只{$animals->random()}在吃{$food->random()}……";
        }
        if ($family->isNotEmpty() && $home->isNotEmpty()) {
            $patterns[] = "我梦见了在{$home->random()}里有{$family->random()}……";
        }
        if ($nature->count() >= 2) {
            $n1 = $nature->random(); $nature2 = $nature->reject(fn($v) => $v === $n1)->first() ?? $n1;
            $patterns[] = "梦里有{$n1}，还有{$nature2}……";
        }

        if (empty($patterns)) {
            $first = $objects->first();
            $second = $objects->skip(1)->first();
            $patterns[] = "昨晚我梦见了{$first->hanzi}和{$second->hanzi}……";
        }

        return $patterns[array_rand($patterns)];
    }
}
