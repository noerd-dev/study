<?php

namespace Nywerk\Study\Database\Seeders;

use Illuminate\Database\Seeder;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

class StudyTestDataSeeder extends Seeder
{
    public function run(): void
    {
        $materials = [
            [
                'title' => 'Introduction to Business Informatics',
                'author' => 'Prof. Dr. James Miller',
                'page_count' => 420,
                'publication_year' => 2023,
            ],
            [
                'title' => 'Fundamentals of Business Administration',
                'author' => 'Prof. Dr. Anna Smith',
                'page_count' => 380,
                'publication_year' => 2022,
            ],
            [
                'title' => 'Mathematics for Business Studies',
                'author' => 'Prof. Dr. Charles Weber',
                'page_count' => 560,
                'publication_year' => 2021,
            ],
            [
                'title' => 'Statistics and Probability Theory',
                'author' => 'Prof. Dr. Mary Fisher',
                'page_count' => 340,
                'publication_year' => 2023,
            ],
            [
                'title' => 'Law for Business Informatics',
                'author' => 'Prof. Dr. Thomas Brown',
                'page_count' => 290,
                'publication_year' => 2024,
            ],
            [
                'title' => 'Project Management and Agility',
                'author' => 'Prof. Dr. Laura Hoffman',
                'page_count' => 310,
                'publication_year' => 2024,
            ],
        ];

        foreach ($materials as $materialData) {
            $studyMaterial = StudyMaterial::create(array_merge($materialData, [
                'tenant_id' => 1,
            ]));

            $summaryCount = rand(3, 6);
            $summaries = [];

            for ($i = 1; $i <= $summaryCount; $i++) {
                $summaries[] = Summary::factory()->create([
                    'tenant_id' => 1,
                    'study_material_id' => $studyMaterial->id,
                    'title' => 'Chapter ' . $i,
                ]);
            }

            $flashcardCount = rand(5, 10);
            for ($j = 0; $j < $flashcardCount; $j++) {
                $summaryId = null;

                // Roughly half of flashcards are linked to a summary
                if ($j % 2 === 0 && count($summaries) > 0) {
                    $summaryId = $summaries[array_rand($summaries)]->id;
                }

                Flashcard::factory()->create([
                    'tenant_id' => 1,
                    'study_material_id' => $studyMaterial->id,
                    'summary_id' => $summaryId,
                ]);
            }
        }
    }
}
