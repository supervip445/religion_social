<?php

namespace Database\Seeders;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubjectsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creatorId = User::query()->orderBy('id')->value('id');

        if (!$creatorId) {
            $this->command?->warn('SubjectsTableSeeder skipped: no users available for created_by.');
            return;
        }

        $subjects = [
            'သင်ပုန်းကြီး',
            'ကစ္စည်းသဒ္ဒါကြီး',
            'အခြေပြုသဒ္ဒါကြီး',
            'ပါဠိသိက္ခာ',
            'ကိုယ်ကျင့်အဘိဓမ္မာ',
            'အဘိဓမ္မာ',
            'အဘိဓမ္မတ္ထသင်္ဂဟ',
            'အခြေပြုသင်္ဂြိုဟ်အဘိဓမ္မာ',
            'ဋီကာ',
            'ဝိနည်း',
            'ကင်္ခါ',
            'ပဌာန်း',
            'ဒုကမာတိကာ',
            'တိကမာတိကာ',
            'ဓာတုကထာ',
            'အောက်ယမိုက် ငါးကျမ်း',
            'အထက်ယမိုက် ငါးကျမ်း',
            'အဘိဓာန်',
            'ဆန်း',
            'အလင်္ကာ',
            'ဇာတက (ဇာတ်တော်ကြီး)',
            'ဝိနယပိဋက',
            'အဘိဓမ္မာပိဋက',
            'သုတ္တန္တ ပိဋက',
            'လောကနီတိ',
            'ဓမ္မပဒ',
            'သမ္မတဆယ်ဆောင်တွဲ',
            'ဗုဒ္ဓဝင်',
            'ပါရာဇိကဏ်ပါဠိတော်',
            'ပါစိတ္တိယပါဠိတော်',
            'မဟာဝါပါဠိတော်',
            'စူဠဝါပါဠိတော်',
            'ပရိဝါပါဠိတော်',
            'အင်္ဂုတ္ထိုရ် ပါဠိတော်',
            'ဓမ္မပဒ ပါဠိတော်',
            'ဘိက္ခူပါတိမောက်',
            'ဘိက္ခူနီပါတိမောက်',
            'မင်းကွန်းဆရာတော် တရားတော်များ',
            'သီတဂူဆရာတော် တရားတော်များ',
            'ပါချုပ်ဆရာတော် တရားတော်များ',
            'မိမိ တရားတော်များ',
            'မိုးကုတ်ဆရာတော် တရားတော်များ',
            'လယ်တီဆရာတော် တရားတော်များ',
            'သဲအင်းဂူဆရာတော် တရားတော်များ',
            'ဓမ္မကထိကဆရာတော်များ၏ တရားတော်များ',
            'အင်္ဂလိပ်စာ',
            'သင်္ချာ',
            'ပထဝီ',
            'သမိုင်း',
            'သိပ္ပံ',
            'ပန်းချီ',
            'ဂီတ',
            'ကွန်ပျူတာ',
            'English Subject',
            'Math Subject',
            'Science Subject',
            'Social Studies Subject',
            'History Subject',
            'Geography Subject',
            'Economics Subject',
            'Political Science Subject',
            'Philosophy Subject',
            'Religion Subject',
            'Art Subject',
            'Music Subject',
            'Dance Subject',
            'Theater Subject',
            'Film Subject',
            'Television Subject',
            'Radio Subject',
            'Internet Subject',
            'Computer Science Subject',
            'Information Technology Subject',
            'Business Subject',
            'Accounting Subject',
            'Finance Subject',
            'Economics Subject',
            'Political Science Subject',
            'Philosophy Subject',
            'Religion Subject',
            'Buddhist Philosophy Subject',
            'Buddhist Ethics Subject',
            'Buddhist Social Science Subject',
            'Buddhist History Subject',
            'Buddhist Geography Subject',
            'Buddhist Economics Subject',
            'Buddhist Political Science Subject',
            'Buddhist Philosophy Subject',
            'Buddhist Ethics Subject',
            'Buddhist Social Science Subject',
        ];

        foreach ($subjects as $index => $name) {
            $code = $this->buildSubjectCode($name, $index + 1);

            Subject::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'description' => null,
                    'credit_hours' => 3,
                    'is_active' => true,
                    'created_by' => $creatorId,
                ]
            );
        }
    }

    private function buildSubjectCode(string $name, int $position): string
    {
        $base = strtoupper(Str::substr(Str::slug($name, ''), 0, 3));
        $base = str_pad($base ?: 'SUB', 3, 'X');

        return sprintf('%s%03d', $base, $position);
    }
}

