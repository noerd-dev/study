<?php

namespace Nywerk\Study\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nywerk\Study\Models\Flashcard;

class FlashcardFactory extends Factory
{
    protected $model = Flashcard::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'study_material_id' => null,
            'question' => $this->faker->sentence() . '?',
            'answer' => $this->faker->paragraph(),
            'created_date' => $this->faker->date(),
        ];
    }
}
