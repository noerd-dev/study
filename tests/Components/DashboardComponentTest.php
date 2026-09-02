<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Noerd\Models\Tenant;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;

uses(Tests\TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

it('counts only the records of the selected tenant', function (): void {
    $user = $this->withStudyModule();
    $tenantId = $user->selected_tenant_id;

    $this->actingAs($user);

    $material = StudyMaterial::factory()->create(['tenant_id' => $tenantId]);
    Summary::factory()->count(2)->create([
        'tenant_id' => $tenantId,
        'study_material_id' => $material->id,
    ]);
    Flashcard::factory()->count(3)->create([
        'tenant_id' => $tenantId,
        'study_material_id' => $material->id,
    ]);

    // A second tenant's material must not inflate anybody's dashboard.
    $otherTenant = Tenant::factory()->create();
    $otherMaterial = StudyMaterial::factory()->create(['tenant_id' => $otherTenant->id]);
    Summary::factory()->create([
        'tenant_id' => $otherTenant->id,
        'study_material_id' => $otherMaterial->id,
    ]);
    Flashcard::factory()->create([
        'tenant_id' => $otherTenant->id,
        'study_material_id' => $otherMaterial->id,
    ]);

    $component = Livewire::test('study::dashboard');

    expect($component->viewData('studyMaterialsCount'))->toBe(1)
        ->and($component->viewData('summariesCount'))->toBe(2)
        ->and($component->viewData('flashcardsCount'))->toBe(3);
});
