<?php

declare(strict_types=1);

use Illuminate\Foundation\Testing\RefreshDatabase;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    $this->user = createNoerdUserForApp('STUDY');
    $this->tenantId = $this->user->selected_tenant_id;

    $this->actingAs($this->user, 'noerd');
});

describe('flashcard detail', function (): void {
    it('validates required fields', function (): void {
        Livewire::test('study::flashcard-detail')
            ->call('store')
            ->assertHasErrors(['detailData.question'])
            ->assertHasErrors(['detailData.study_material_id']);
    });

    it('successfully stores the data', function (): void {
        $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);

        Livewire::test('study::flashcard-detail')
            ->set('detailData', validDetailPayload(Flashcard::class, [
                'tenant_id' => $this->tenantId,
                'question' => 'What is Laravel?',
                'study_material_id' => $studyMaterial->id,
            ]))
            ->call('store')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('study_flashcards', [
            'question' => 'What is Laravel?',
            'study_material_id' => $studyMaterial->id,
        ]);
    });


    it('preselects study material from session on mount', function (): void {
        $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);

        session(['listFilters' => ['study_material_id' => $studyMaterial->id]]);

        Livewire::test('study::flashcard-detail')
            ->assertSet('detailData.study_material_id', $studyMaterial->id)
            ->assertSet('relationTitles.study_material_id', $studyMaterial->title)
            ->assertHasNoErrors();
    });

    it('sets study_material_id from relations on mount', function (): void {
        $studyMaterial = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);

        Livewire::test('study::flashcard-detail', ['relations' => ['study_material_id' => $studyMaterial->id]])
            ->assertSet('detailData.study_material_id', $studyMaterial->id)
            ->assertHasNoErrors();
    });
});

describe('flashcards list', function (): void {
    it('applies study material filter to query results', function (): void {
        $studyMaterial1 = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);
        $studyMaterial2 = StudyMaterial::factory()->create(['tenant_id' => $this->tenantId]);

        Flashcard::factory()->create([
            'study_material_id' => $studyMaterial1->id,
            'tenant_id' => $this->tenantId,
        ]);
        Flashcard::factory()->create([
            'study_material_id' => $studyMaterial2->id,
            'tenant_id' => $this->tenantId,
        ]);

        session(['listFilters' => ['study_material_id' => $studyMaterial1->id]]);

        $component = Livewire::test('study::flashcards-list');

        $rows = $component->instance()->listData()['rows'];

        expect($rows)->toHaveCount(1);
        expect($rows->first()->study_material_id)->toBe($studyMaterial1->id);
    });
});
