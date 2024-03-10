<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use PHPUnit\Event\RuntimeException;

class ReportTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pathToPapJsonFile = resource_path('json/CustomPapsmearTemplate.json');
        $PapJsonContent = file_get_contents($pathToPapJsonFile);

        $pathToPathologyJsonFile = resource_path('json/CustomPathologyTemplate.json');
        $PathologyJsonContent = file_get_contents($pathToPathologyJsonFile);

        // Ensure the file was read successfully
        if ($PapJsonContent === false || $PathologyJsonContent === false) {
            throw new RuntimeException("Failed to read the JSON file.");
        }
        $rows = [
            [
                'test_title' => 'PAP.SMEAR',
                'note' => 'A single negative pap smear has a limited value in cervical cancer screening.',
                'data' => $PapJsonContent
            ],
            [
                'test_title' => 'Pathology',
                'note' => '',
                'data' => $PathologyJsonContent
            ],
        ];

        DB::table('report_templates')->insert($rows);
    }
}
