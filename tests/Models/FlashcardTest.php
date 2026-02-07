<?php

declare(strict_types=1);

namespace Nywerk\Study\Tests\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\Tenant;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;

uses(\Tests\TestCase::class, RefreshDatabase::class);

it('can create a flashcard', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);

    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
        'question' => 'What is SOLID?',
        'answer' => 'SOLID is an acronym for five design principles.',
        'created_date' => '2025-01-10',
    ]);

    $this->assertDatabaseHas('study_flashcards', [
        'question' => 'What is SOLID?',
        'answer' => 'SOLID is an acronym for five design principles.',
        'study_material_id' => $studyMaterial->id,
        'tenant_id' => $tenant->id,
    ]);
});

it('belongs to a study material', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $tenant->id,
        'title' => 'Clean Code',
    ]);

    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->assertEquals('Clean Code', $flashcard->studyMaterial->title);
});

it('has tenant relationship', function (): void {
    $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);
    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->assertEquals('Test Tenant', $flashcard->tenant->name);
});

it('casts created_date to date', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);

    $flashcard = Flashcard::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
        'created_date' => '2025-01-10',
    ]);

    $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $flashcard->created_date);
});

it('is deleted when study material is deleted', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);

    Flashcard::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->assertCount(1, Flashcard::all());

    $studyMaterial->delete();

    $this->assertCount(0, Flashcard::all());
});
