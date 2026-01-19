<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dhammaCategory = Category::where('slug', 'dhamma-talks')->first();
        $eventsCategory = Category::where('slug', 'events')->first();
        $newsCategory = Category::where('slug', 'news')->first();
        $announcementsCategory = Category::where('slug', 'announcements')->first();

        $posts = [
            [
                'title' => 'ပါရာယန ဓမ္မစင်တာသို့ ကြိုဆိုပါသည်',
                'content' => 'ကျွန်ုပ်တို့ကျောင်းတိုက်သို့ ကြိုဆိုပါသည်။ ကျွန်ုပ်တို့သည် ဗုဒ္ဓ၏သွန်သင်ချက်များကို ဖြန့်ဝေရန်နှင့် တရားထိုင်ခြင်းနှင့် သင်ယူခြင်းအတွက် ငြိမ်းချမ်းသောနေရာတစ်ခု ပေးအပ်ရန် ရည်ရွယ်ထားပါသည်။ ကျွန်ုပ်တို့၏စင်တာသည် တရားထိုင်ခြင်း၊ ဓမ္မဟောပြောခြင်းနှင့် လူမှုရေးလုပ်ငန်းများအပါအဝင် အစီအစဉ်မျိုးစုံကို ပေးဆောင်ပါသည်။',
                'category_id' => $announcementsCategory?->id,
                'status' => 'published',
                'slug' => 'welcome-to-မဟာဝိဇိတာရာမတိုက်-dhamma-center',
            ],
            [
                'title' => 'အပတ်စဉ် တရားထိုင်ချိန်ဇယား',
                'content' => 'အပတ်စဉ် ပုံမှန်တရားထိုင်ခြင်းအစီအစဉ်များတွင် ကျွန်ုပ်တို့နှင့်အတူ ပါဝင်ရန် ဖိတ်ခေါ်ပါသည်။ နံနက်ပိုင်း တရားထိုင်ချိန်မှာ နံနက် ၆:၀၀ မှ ၇:၀၀ အထိ၊ ညနေပိုင်း တရားထိုင်ချိန်မှာ ညနေ ၆:၀၀ မှ ၇:၀၀ အထိ ဖြစ်ပါသည်။ အတွေ့အကြုံအဆင့်မရွေး အားလုံးကို ကြိုဆိုပါသည်။',
                'category_id' => $eventsCategory?->id,
                'status' => 'published',
                'slug' => 'weekly-meditation-schedule',
            ],
            [
                'title' => 'အရိယာသစ္စာလေးပါးကို နားလည်ခြင်း',
                'content' => 'အရိယာသစ္စာလေးပါးသည် ဗုဒ္ဓဘာသာ၏ အခြေခံသွန်သင်ချက်ဖြစ်ပါသည်။ ၎င်းတို့သည် ဒုက္ခ၏သဘောတရား၊ ၎င်း၏အကြောင်းရင်း၊ ၎င်း၏ချုပ်ငြိမ်းခြင်းနှင့် ချုပ်ငြိမ်းရာသို့ ရောက်စေသောလမ်းကို ရှင်းလင်းပြသထားပါသည်။ ဗုဒ္ဓဘာသာလမ်းကို လိုက်စားသူများအတွက် ဤသစ္စာများကို နားလည်ခြင်းသည် အလွန်အရေးကြီးပါသည်။',
                'category_id' => $dhammaCategory?->id,
                'status' => 'published',
                'slug' => 'understanding-the-four-noble-truths',
            ],
            [
                'title' => 'နှစ်သစ်ကူး ဆုတောင်းပွဲ',
                'content' => 'နှစ်စဉ် နှစ်သစ်ကူး ဆုတောင်းပွဲတွင် ပါဝင်ရန် ဒါယကာဒါယိကာမများအားလုံးကို ဖိတ်ခေါ်ပါသည်။ ပွဲတွင် ပရိတ်ရွတ်ဖတ်ခြင်း၊ ရဟန်းတော်များထံမှ ဆုတောင်းခြင်းနှင့် အထူးဓမ္မဟောပြောခြင်းတို့ ပါဝင်ပါမည်။ အစားအစာနှင့် သောက်စရာများကို ပေးဆောင်ပါမည်။',
                'category_id' => $eventsCategory?->id,
                'status' => 'published',
                'slug' => 'new-year-blessing-ceremony',
            ],
            [
                'title' => 'ကျောင်းတိုက် ပြန်လည်မွမ်းမံမှု အဆင့်အတန်း',
                'content' => 'ကျောင်းတိုက် ပြန်လည်မွမ်းမံမှု စီမံကိန်းသည် ကောင်းစွာ တိုးတက်နေကြောင်း ဝမ်းမြောက်စွာ အကြောင်းကြားပါသည်။ တရားထိုင်ခန်းသစ်သည် ပြီးစီးရန် နီးကပ်နေပြီး လာမည့်လတွင် ပြည်သူများအတွက် ဖွင့်လှစ်နိုင်မည်ဟု မျှော်လင့်ထားပါသည်။ ဤအရာကို ဖြစ်မြောက်စေသော လှူဒါန်းမှုအားလုံးအတွက် ကျေးဇူးတင်ပါသည်။',
                'category_id' => $newsCategory?->id,
                'status' => 'published',
                'slug' => 'monastery-renovation-update',
            ],
            [
                'title' => 'မေတ္တာဘာဝနာ ကျင့်ကြံခြင်း',
                'content' => 'မေတ္တာ သို့မဟုတ် မေတ္တာဘာဝနာသည် သတ္တဝါအားလုံးအတွက် မရည်ရွယ်သော မေတ္တာနှင့် ကရုဏာကို ပြုစုပျိုးထောင်သော ကျင့်ကြံမှုတစ်ခုဖြစ်ပါသည်။ ဤကျင့်ကြံမှုသည် အပြုသဘောစိတ်ခံစားမှုများကို ဖွံ့ဖြိုးစေပြီး ဒေါသနှင့် မုန်းတီးမှုကဲ့သို့သော အပျက်သဘောစိတ်အခြေအနေများကို လျော့ပါးစေပါသည်။',
                'category_id' => $dhammaCategory?->id,
                'status' => 'draft',
                'slug' => 'the-practice-of-metta-loving-kindness',
            ],
        ];

        foreach ($posts as $post) {
            Post::create([
                'title' => $post['title'],
                'content' => $post['content'],
                'category_id' => $post['category_id'],
                'status' => $post['status'],
                'slug' => $post['slug'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

