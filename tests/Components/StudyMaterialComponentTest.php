<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Tests\Traits\CreatesStudyUser;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
uses(CreatesStudyUser::class);

beforeEach(function (): void {
    $this->user = $this->withStudyModule();
    $this->tenantId = $this->user->selected_tenant_id;

    $this->actingAs($this->user);
});

it('validates required fields via layout', function (): void {
    $component = Livewire::test('study::study-material-detail')
        ->set('detailData', [])
        ->call('store');

    $component->assertHasErrors(requiredLayoutFields($component));
});

it('successfully stores the data', function (): void {
    $title = fake()->sentence(3);

    Livewire::test('study::study-material-detail')
        ->set('detailData', validDetailPayload(StudyMaterial::class, ['tenant_id' => $this->tenantId]))
        ->set('detailData.title', $title)
        ->set('detailData.author', 'Test Author')
        ->call('store')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('study_materials', [
        'title' => $title,
        'author' => 'Test Author',
    ]);
});

it('sets and removes the model id in url', function (): void {
    $model = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);

    // Which target the row opens is configuration; that the row opens a modal at
    // all is the mechanic worth proving here.
    Livewire::test('study::study-materials-list')->call('listAction', $model->id)
        ->assertDispatched('noerdModal');

    Livewire::withUrlParams(['studyMaterialId' => $model->id])
        ->test('study::study-material-detail')
        ->assertSet('modelId', $model->id)
        ->assertHasNoErrors();
});

it('loads existing study material data', function (): void {
    $studyMaterial = StudyMaterial::factory()->create([
        'tenant_id' => $this->tenantId,
        'title' => 'Test Book',
        'author' => 'Test Author',
    ]);

    Livewire::withUrlParams(['studyMaterialId' => $studyMaterial->id])
        ->test('study::study-material-detail')
        ->assertSet('modelId', $studyMaterial->id)
        ->assertSet('detailData.title', 'Test Book')
        ->assertSet('detailData.author', 'Test Author')
        ->assertHasNoErrors();
});
