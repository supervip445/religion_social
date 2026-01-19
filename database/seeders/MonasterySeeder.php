<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Monastery;

class MonasterySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monasteryDataJson = '{
          "title": "ချမ်းမြသာယာရပ်ကွက် မဟာဝိဇိတာရာမတိုက် အတွင်း ကျောင်းအမည် စာရင်းများ",
          "subtitle": "စီစစ်ပြီး ကျောင်းစာရင်း ၂-၈-၂၀၂၀",
          "monasteries": [
            {"id": 1, "name": "ကနန်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 2, "name": "ကမ္ဘားတဲကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 3, "name": "ကန္နီကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 4, "name": "ကုဋေအဝေရာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 5, "name": "ကေတုမတီတောင်ငူကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 6, "name": "ကေတုမတီရွှေလရောင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 7, "name": "ကံတော်မင်္ဂလာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 8, "name": "ကံသာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 9, "name": "ကံစွယ်ကျောင်း (မျက်ပါးရပ်)", "monks": 0, "novices": 0, "total": 0},
            {"id": 10, "name": "ကောင်းမြတ်ရတနာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 11, "name": "ကျောက်ဆည်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 12, "name": "ကျောက်တိုင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 13, "name": "ကြံတိုင်းအောင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 14, "name": "ခေမာသီဝံကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 15, "name": "ချမ်းမြေ့သုခကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 16, "name": "ချမ်းမြေ့အေးကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 17, "name": "ချမ်းအေးသာဇံကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 18, "name": "ချမ်းသာသုခကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 19, "name": "ချမ်းအေးသာဇံကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 20, "name": "ခြောက်အိမ်တန်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 21, "name": "ဂုဏ်မေတ္တာရောင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 22, "name": "ငွေခြင်္သေ့မိုးကောင်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 23, "name": "စန်းပဒေသာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 24, "name": "စိန်ခြယ်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 25, "name": "စိတ္တသုခပျော်ဘွယ်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 26, "name": "ဇေယျသိဒ္ဓိအလယ်ကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 27, "name": "ဇေယျာသီရိကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 28, "name": "ဇေယျသုခကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 29, "name": "ဇော်မင်္ဂလာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 30, "name": "ညောင်ကန်သာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 31, "name": "ညောင်ရမ်းကျောင်း (ခေမာရာမ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 32, "name": "တိပိဋကဂန္ဓာရုံကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 33, "name": "တောင်သာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 34, "name": "ဒယူးသဟာယကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 35, "name": "ဓမ္မရက္ခိတကျောင်း (မန်းလည်-၂)", "monks": 0, "novices": 0, "total": 0},
            {"id": 36, "name": "ဓမ္မဝီရပေကုန်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 37, "name": "ဓမ္မအလင်းရောင်ကျောင်း (၂) (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 38, "name": "ဓမ္မာလင်္ကာရမိုးညှင်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 39, "name": "ဓမ္မသိရောမဏိကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 40, "name": "ဓမ္မသုခရွှေလူးကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 41, "name": "ဓမ္မရံသီကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 42, "name": "နယ်ဦးကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 43, "name": "နေမင်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 44, "name": "နေပြည်တော်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 45, "name": "နောင်ပိုအောင်ကျောင်း (ပြည်သာအေး - တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 46, "name": "ပစ်တိုင်ထောင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 47, "name": "ပညာရံသီကျောင်း (ကန်ရတနာ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 48, "name": "ပညာဝေပုလ္လကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 49, "name": "ပဉ္စဂုဏကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 50, "name": "ပရဟိတကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 51, "name": "ပင်းယကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 52, "name": "ပုပ္ပါးကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 53, "name": "ပုလဲရတနာအောင်သိဒ္ဓိကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 54, "name": "ပုသိမ်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 55, "name": "ဖက်ပင်အိုင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 56, "name": "ဖောင်ပြင်ဆွတ်နန်းကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 57, "name": "ဖြုံဘူးကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 58, "name": "မအူပင်တာတပေါကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 59, "name": "မလိခဝါဆိုကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 60, "name": "မရှိခဏကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 61, "name": "မဏိရတနာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 62, "name": "မင်္ဂလာသိဒ္ဓိကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 63, "name": "မင်္ဂလာမဉ္ဇူကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 64, "name": "မတ္တရာမြို့မကျောင်း  (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 65, "name": "မေဓာဝီကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 66, "name": "မိတ္ထီလာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 67, "name": "မုံရွှာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 68, "name": "မောက္ကတော်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 69, "name": "မော်လိုက်တပ်ဦးကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 70, "name": "မှန်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 71, "name": "မာန်လည်ကျောင်း (၁)", "monks": 0, "novices": 0, "total": 0},
            {"id": 72, "name": "မြကန်သာကျောင်း (စစ်ကိုင်း)", "monks": 0, "novices": 0, "total": 0},
            {"id": 73, "name": "မြင်းခြံကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 74, "name": "မြတ်ဆုမွန်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 75, "name": "ရတနာမာန်အောင်ကျောင်း (စန္ဒနာရုံ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 76, "name": "ရတနာမြိုင်ကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 77, "name": "ရတနာကြည်ညွန့်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 78, "name": "ရမည်းသင်းကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 79, "name": "ရွှေပြည်သာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 80, "name": "ရွှေကယားကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 81, "name": "ရွှေကျော်ထွန်းကျောင်း (ပင်ဘော)", "monks": 0, "novices": 0, "total": 0},
            {"id": 82, "name": "ရွှေထီးကမ္ဘားတဲကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 83, "name": "ရွှေဟင်္သာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 84, "name": "ရွှေလရောင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 85, "name": "ရွှေသစ္စာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 86, "name": "ဝင်းရတနာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 87, "name": "ဝိဝေကရံသီကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 88, "name": "ဝေဠုဝန်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 89, "name": "သဒ္ဓမ္မဂုဏ်ရည်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 90, "name": "သဒ္ဓမ္မသစ္စာမြရတနာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 91, "name": "သစ္စာအောင်မြေကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 92, "name": "သစ္စာဂုဏ်ရည်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 93, "name": "သဟပုညကာရီကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 94, "name": "သာသနမာမကကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 95, "name": "သာသနာသုံးဆူကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 96, "name": "သာသနာ့ဂုဏ်ရည် (ဇောတိကာရုံ) ကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 97, "name": "သာသနာလင်္ကာရ", "monks": 0, "novices": 0, "total": 0},
            {"id": 98, "name": "သာသနာ့ဝန်ဆောင်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 99, "name": "သာသနာ့ဝေပုလ္လ(ရှမ်းကျောင်း-တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 100, "name": "သ္ပမဉ္ဇူကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 101, "name": "သီရိမင်္လာကျောင်း(တောင်ပေါ် - တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 102, "name": "သီရိမန္တလာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 103, "name": "သီဝံချမ်းမြေ့ကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 104, "name": "အနန္တဂုဏ်ရည်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 105, "name": "အာလောကာရုံကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 106, "name": "အေးဝမ်းကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 107, "name": "အောင်လံတော်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 108, "name": "အောင်မြေသီရိကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 109, "name": "အောင်မြေရတနာကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0},
            {"id": 110, "name": "အောင်မြေဘုံသာကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 111, "name": "အောင်သုခဝါဆိုကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 112, "name": "အောင်ချမ်းသာမေတ္တာရိပ်ကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 113, "name": "ဣစ္ဆာသယကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 114, "name": "ဥတ္တမပုရိသဒီပနီကျောင်း (တ)", "monks": 0, "novices": 0, "total": 0}
          ],
          "buildings": [
            {"id": 1, "name": "မန်းမြို့ဦးကျောင်းတိုက်", "monasteryName": "မန်းမြို့ဦးကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 2, "name": "မန္တလာရာမကျောင်းတိုက်", "monasteryName": "မန္တလာရာမကျောင်း", "monks": 0, "novices": 0, "total": 0},
            {"id": 3, "name": "မဟာစည်ရိပ်သာကျောင်းတိုက်", "monasteryName": "", "monks": 0, "novices": 0, "total": 0},
            {"id": 4, "name": "မိုးကုတ်ရိပ်သာကျောင်းတိုက် (တောင်)", "monasteryName": "", "monks": 0, "novices": 0, "total": 0},
            {"id": 5, "name": "မိုးကုတ်ရိပ်သာကျောင်းတိုက် (မြောက်)", "monasteryName": "", "monks": 0, "novices": 0, "total": 0}
          ]
        }';

        $data = json_decode($monasteryDataJson, true);

        // Seed monasteries
        foreach ($data['monasteries'] as $monastery) {
            Monastery::create([
                'name' => $monastery['name'],
                'type' => 'monastery',
                'monastery_name' => null,
                'monks' => $monastery['monks'],
                'novices' => $monastery['novices'],
                'total' => $monastery['total'],
                'order' => $monastery['id'],
            ]);
        }

        // Seed buildings
        foreach ($data['buildings'] as $building) {
            Monastery::create([
                'name' => $building['name'],
                'type' => 'building',
                'monastery_name' => $building['monasteryName'] ?: null,
                'monks' => $building['monks'],
                'novices' => $building['novices'],
                'total' => $building['total'],
                'order' => $building['id'] + 1000, // Offset to keep buildings separate
            ]);
        }
    }
}

