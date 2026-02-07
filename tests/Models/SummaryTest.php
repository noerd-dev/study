<?php

declare(strict_types=1);

namespace Nywerk\Study\Tests\Models;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\Tenant;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

uses(\Tests\TestCase::class, RefreshDatabase::class);

it('can create a summary', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);

    $summary = Summary::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
        'title' => 'Chapter 1: Introduction',
        'content' => 'This is the summary of chapter 1.',
    ]);

    $this->assertDatabaseHas('study_summaries', [
        'title' => 'Chapter 1: Introduction',
        'content' => 'This is the summary of chapter 1.',
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

    $summary = Summary::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->assertEquals('Clean Code', $summary->studyMaterial->title);
});

it('has tenant relationship', function (): void {
    $tenant = Tenant::factory()->create(['name' => 'Test Tenant']);
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);
    $summary = Summary::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->assertEquals('Test Tenant', $summary->tenant->name);
});

it('is deleted when study material is deleted', function (): void {
    $tenant = Tenant::factory()->create();
    $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $tenant->id]);

    Summary::factory()->create([
        'tenant_id' => $tenant->id,
        'study_material_id' => $studyMaterial->id,
    ]);

    $this->assertCount(1, Summary::all());

    $studyMaterial->delete();

    $this->assertCount(0, Summary::all());
});
