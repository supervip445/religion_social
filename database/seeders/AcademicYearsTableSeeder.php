<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\User;
use Illuminate\Database\Seeder;

class AcademicYearsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $creatorId = User::query()->orderBy('id')->value('id');

        if (!$creatorId) {
            $this->command?->warn('AcademicYearsTableSeeder skipped: no users available for created_by.');
            return;
        }

        $firstYear = 2025;
        $lastYear = 2099;

        for ($year = $firstYear; $year <= $lastYear; $year++) {
            $nextYear = $year + 1;
            $name = sprintf('%d-%d', $year, $nextYear);
            $code = sprintf('AY%d-%02d', $year, $nextYear % 100);

            AcademicYear::updateOrCreate(
                ['code' => $code],
                [
                    'name' => $name,
                    'start_date' => sprintf('%d-07-01', $year),
                    'end_date' => sprintf('%d-06-30', $nextYear),
                    'is_active' => $year === $firstYear,
                    'description' => null,
                    'created_by' => $creatorId,
                ]
            );
        }
    }
}

