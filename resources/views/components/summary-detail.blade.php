<?php

use Livewire\Attributes\On;
use Livewire\Component;
use Noerd\Traits\NoerdDetail;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdDetail;

    public ?string $detailPrimary = 'summaryId';

    public $detailModel = Summary::class;

    public array $relations = [];

    public function mount(): void
    {
        $this->initDetail();

        if (($this->relations['study_material_id'] ?? null) && ! isset($this->detailData['study_material_id'])) {
            $this->detailData['study_material_id'] = $this->relations['study_material_id'];
        }

        if ($this->detailData['study_material_id'] ?? null) {
            $this->relationTitles['study_material_id'] = StudyMaterial::find($this->detailData['study_material_id'])?->title;
        }

        $this->preselect('study_material_id');
    }

    #[On('studyMaterialSelected')]
    public function studyMaterialSelected($studyMaterialId): void
    {
        $studyMaterial = StudyMaterial::find($studyMaterialId);
        $this->detailData['study_material_id'] = $studyMaterial->id;
        $this->relationTitles['study_material_id'] = $studyMaterial->title;
    }

    public function store(): void
    {
        $this->validate([
            'detailData.title' => ['required', 'string', 'max:255'],
            'detailData.study_material_id' => ['required', 'exists:study_materials,id'],
        ]);

        $summary = Summary::updateOrCreate(['id' => $this->modelId], $this->detailData);

        $this->storeProcess($summary);
    }

}; ?>

<x-noerd::page>
    <x-slot:header>
        <x-noerd::modal-title>{{ __('Summary') }}</x-noerd::modal-title>
    </x-slot:header>

    <x-noerd::tab-content :layout="$pageLayout" />

    <x-slot:footer>
        <x-noerd::delete-save-bar :showDelete="isset($modelId)"/>
    </x-slot:footer>
</x-noerd::page>
