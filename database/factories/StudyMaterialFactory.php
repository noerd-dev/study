<?php

namespace Nywerk\Study\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nywerk\Study\Models\StudyMaterial;

class StudyMaterialFactory extends Factory
{
    protected $model = StudyMaterial::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name(),
            'page_count' => $this->faker->numberBetween(100, 800),
            'publication_year' => $this->faker->numberBetween(1990, 2025),
        ];
    }
}
