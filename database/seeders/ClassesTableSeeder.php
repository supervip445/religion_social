<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\User;
use Illuminate\Database\Seeder;

class ClassesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creatorId = User::query()->orderBy('id')->value('id');
        $academicYearId = AcademicYear::query()
                ->where('is_active', true)
                ->orderBy('start_date')
                ->value('id')
            ?? AcademicYear::query()->orderBy('start_date')->value('id');

        if (!$creatorId || !$academicYearId) {
            $this->command?->warn('ClassesTableSeeder skipped: missing prerequisite users or academic years.');
            return;
        }

        $grades = $this->gradeDefinitions();

        foreach ($grades as $grade) {
            $code = $this->buildCode($grade['label']);

            SchoolClass::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $grade['label'],
                    'grade_level' => $grade['level'],
                    'section' => null,
                    'capacity' => 30,
                    'is_active' => true,
                    'academic_year_id' => $academicYearId,
                    'class_teacher_id' => null,
                    'created_by' => $creatorId,
                ]
            );
        }
    }

    private function gradeDefinitions(): array
    {
        $grades = [
            ['label' => 'စာစုတန်း', 'level' => 0],
            ['label' => 'အခြေပြုတန်း', 'level' => 1],
            ['label' => 'မူလတန်း', 'level' => 2],
            ['label' => 'ပထမငယ်တန်း', 'level' => 3],
            ['label' => 'ပထမလတ်တန်း', 'level' => 4],
            ['label' => 'ပထမကြီးတန်း', 'level' => 5],
            ['label' => 'ဓမ္မာစရိယတန်း', 'level' => 6],
            ['label' => 'သာမဏေကျော်ပထမဆင့်', 'level' => 7],
            ['label' => 'သာမဏေကျော်ဒုတိယဆင့်', 'level' => 8],
            ['label' => 'သာမဏေကျော်တတိယဆင့်', 'level' => 9],
            ['label' => 'Diploma in Buddhist Studies (First Semester)', 'level' => 10],
            ['label' => 'Diploma in Buddhist Studies (Second Semester)', 'level' => 11],
            ['label' => 'Diploma in Buddhist Studies (Third Semester)', 'level' => 12],
            ['label' => 'Diploma in Buddhist Studies (Fourth Semester)', 'level' => 13],
            ['label' => 'BA in Buddhist Studies (First Semester)', 'level' => 14],
            ['label' => 'BA in Buddhist Studies (Second Semester)', 'level' => 15],
            ['label' => 'BA in Buddhist Studies (Third Semester)', 'level' => 16],
            ['label' => 'BA in Buddhist Studies (Fourth Semester)', 'level' => 17],
            ['label' => 'MPhil in Buddhist Studies (Fifth Semester)', 'level' => 18],
            ['label' => 'MPhil in Buddhist Studies (Sixth Semester)', 'level' => 19],
            ['label' => 'PhD in Buddhist Studies (First Semester)', 'level' => 20],
            ['label' => 'PhD in Buddhist Studies (Second Semester)', 'level' => 21],
            ['label' => 'PhD in Buddhist Studies (Third Semester)', 'level' => 22],
            ['label' => 'PhD in Buddhist Studies (Fourth Semester)', 'level' => 23],
            ['label' => 'MA in Buddhist Studies (First Semester)', 'level' => 24],
            ['label' => 'MA in Buddhist Studies (Second Semester)', 'level' => 25],
            ['label' => 'MA in Buddhist Studies (Third Semester)', 'level' => 26],
            ['label' => 'MA in Buddhist Studies (Fourth Semester)', 'level' => 27],
            ['label' => 'English Speaking', 'level' => 28],
            ['label' => 'English Writing', 'level' => 29],
            ['label' => 'English Reading', 'level' => 30],
            ['label' => 'English Listening', 'level' => 31],
            ['label' => 'English Grammar', 'level' => 32],
            ['label' => 'English Vocabulary', 'level' => 33],
            ['label' => 'English Pronunciation', 'level' => 34],
            ['label' => 'Computer Basic', 'level' => 35],
            ['label' => 'Computer Programming', 'level' => 36],
            ['label' => 'Computer Networking', 'level' => 37],
            ['label' => 'Computer Security', 'level' => 38],
            ['label' => 'Computer Hardware', 'level' => 39],
            ['label' => 'Computer Software', 'level' => 40],
            ['label' => 'Computer Maintenance', 'level' => 41],
            ['label' => 'Computer Troubleshooting', 'level' => 42],
            ['label' => 'Computer Optimization', 'level' => 43],
            ['label' => 'Programming Basic', 'level' => 44],
            ['label' => 'Programming Intermediate', 'level' => 45],
            ['label' => 'Programming Advanced', 'level' => 46],
            ['label' => 'Programming Expert', 'level' => 47],
            ['label' => 'Programming Master', 'level' => 48],
            ['label' => 'HTML', 'level' => 49],
            ['label' => 'CSS', 'level' => 50],
            ['label' => 'JavaScript', 'level' => 51],
            ['label' => 'PHP', 'level' => 52],
            ['label' => 'MySQL', 'level' => 53],
            ['label' => 'Laravel', 'level' => 54],
            ['label' => 'React', 'level' => 55],
            ['label' => 'Node.js', 'level' => 56],
            ['label' => 'Express.js', 'level' => 57],
            ['label' => 'MongoDB', 'level' => 58],
            ['label' => 'PostgreSQL', 'level' => 59],
            ['label' => 'SQLite', 'level' => 60],
            ['label' => 'Oracle', 'level' => 61],
            ['label' => 'Microsoft SQL Server', 'level' => 62],
            ['label' => 'Microsoft Access', 'level' => 63],
            ['label' => 'Microsoft Excel', 'level' => 64],
            ['label' => 'Microsoft Word', 'level' => 65],
            ['label' => 'Microsoft PowerPoint', 'level' => 66],
            ['label' => 'Microsoft Visio', 'level' => 67],
            ['label' => 'Microsoft Project', 'level' => 68],
            ['label' => 'Microsoft Outlook', 'level' => 69],
            ['label' => 'Microsoft OneDrive', 'level' => 70],
            ['label' => 'Microsoft Teams', 'level' => 71],
            ['label' => 'Microsoft Azure', 'level' => 72],
            ['label' => 'Microsoft Graph', 'level' => 73],
            ['label' => 'Microsoft Power BI', 'level' => 74],
            
        ];

        for ($level = 1; $level <= 23; $level++) {
            $grades[] = [
                'label' => sprintf('G-%d', $level),
                'level' => $level,
            ];
        }

        return $grades;
    }

    private function buildCode(string $label): string
    {
        return strtoupper(str_replace('-', '', $label));
    }
}

