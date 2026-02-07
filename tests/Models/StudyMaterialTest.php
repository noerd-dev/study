<?php

declare(strict_types=1);

namespace Nywerk\Study\Tests\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\Tenant;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

uses(\Tests\TestCase::class, RefreshDatabase::class);

it('can create a study material', function (): void {
    $tenant = Tenant::factory()->create();

    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $tenant->id,
        'title' => 'Clean Code',
        'author' => 'Robert C. Martin',
        'page_count' => 464,
        'publication_year' => 2008,
    ]);

    $this->assertDatabaseHas('study_materials', [
        'title' => 'Clean Code',
        'author' => 'Robert C. Martin',
        'page_count' => 464,
        'publication_year' => 2008,
        'tenant_id' => $tenant->id,
    ]);
});

it('has summaries relationship', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    Summary::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
        'title' => 'Chapter 1',
    ]);

    $this->assertCount(1, $studyMaterial->summaries);
    $this->assertEquals('Chapter 1', $studyMaterial->summaries->first()->title);
});

it('has flashcards relationship', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    Flashcard::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
        'question' => 'What is clean code?',
    ]);

    $this->assertCount(1, $studyMaterial->flashcards);
    $this->assertEquals('What is clean code?', $studyMaterial->flashcards->first()->question);
});

it('has tenant relationship', function (): void {
    $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $tenant->id,
    ]);

    $this->assertEquals('Test Tenant', $studyMaterial->tenant->name);
});

it('casts page_count and publication_year to integer', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $tenant->id,
        'page_count' => '500',
        'publication_year' => '2020',
    ]);

    $this->assertIsInt($studyMaterial->page_count);
    $this->assertIsInt($studyMaterial->publication_year);
});
