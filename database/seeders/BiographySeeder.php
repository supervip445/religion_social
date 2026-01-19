<?php

namespace Database\Seeders;

use App\Models\Biography;
use Illuminate\Database\Seeder;

class BiographySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $biographies = [
            [
                'name' => 'အရှင်နန္ဒမာလာ',
                'birth_year' => '၁၃၇၆ ခုနှစ် (၂၀၁၄ ခရစ်နှစ်)',
                'sangha_entry_year' => '၁၃၉၂ ခုနှစ် (၂၀၃၀ ခရစ်နှစ်)',
                'disciples' => 'ရဟန်း ၁၅ ပါး၊ ရဟန်းငယ် ၁၀ ပါး',
                'teaching_monastery' => 'ပါရာယန ဓမ္မစင်တာ ကျောင်းတိုက်',
                'sangha_dhamma' => 'သီလ (ကျင့်ဝတ်)၊ သမာဓိ (စူးစိုက်မှု)၊ ပညာ (ဉာဏ်) ကျင့်ကြံမှုမှတစ်ဆင့် သာသနာတော် တိုးတက်ပြန့်ပွားပါစေ။ သတ္တဝါအားလုံး ပျော်ရွှင်ပြီး ဒ္ခမှ လွတ်မြောက်ပါစေ။',
            ],
            [
                'name' => 'အရှင်ဥပ္ပလ',
                'birth_year' => '၁၃၆၅ ခုနှစ် (၂၀၀၃ ခရစ်နှစ်)',
                'sangha_entry_year' => '၁၃၈၀ ခုနှစ် (၂၀၁၈ ခရစ်နှစ်)',
                'disciples' => 'ရဟန်း ၈ ပါး၊ ရဟန်းငယ် ၅ ပါး',
                'teaching_monastery' => 'ပါရာယန ဓမ္မစင်တာ ကျောင်းတိုက်',
                'sangha_dhamma' => 'တရားထိုင်ခြင်းကို သွန်သင်ပြီး ငြိမ်းချမ်းမှုနှင့် နားလည်မှုကို ရှာဖွေသူအားလုံးထံသို့ ဓမ္မကို ဖြန့်ဝေရန် ရည်ရွယ်ထားပါသည်။',
            ],
            [
                'name' => 'အရှင်သီလ',
                'birth_year' => '၁၃၇၀ ခုနှစ် (၂၀၀၈ ခရစ်နှစ်)',
                'sangha_entry_year' => '၁၃၈၅ ခုနှစ် (၂၀၂၃ ခရစ်နှစ်)',
                'disciples' => 'ရဟန်း ၅ ပါး၊ ရဟန်းငယ် ၃ ပါး',
                'teaching_monastery' => 'ပါရာယန ဓမ္မစင်တာ ကျောင်းတိုက်',
                'sangha_dhamma' => 'ဗုဒ္ဓ၏ စစ်မှန်သော သွန်သင်ချက်များကို ထိန်းသိမ်းပြီး သွန်သင်ရန် ရည်ရွယ်ထားပါသည်။',
            ],
        ];

        foreach ($biographies as $biography) {
            Biography::create([
                'name' => $biography['name'],
                'birth_year' => $biography['birth_year'],
                'sangha_entry_year' => $biography['sangha_entry_year'],
                'disciples' => $biography['disciples'],
                'teaching_monastery' => $biography['teaching_monastery'],
                'sangha_dhamma' => $biography['sangha_dhamma'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}

