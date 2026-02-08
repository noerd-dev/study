<?php

namespace Nywerk\Study\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Nywerk\Study\Models\Summary;

class SummaryFactory extends Factory
{
    protected $model = Summary::class;

    public function definition(): array
    {
        return [
            'tenant_id' => 1,
            'study_material_id' => null,
            'title' => 'Chapter ' . $this->faker->numberBetween(1, 20),
            'content' => $this->faker->paragraphs(3, true),
        ];
    }
}
