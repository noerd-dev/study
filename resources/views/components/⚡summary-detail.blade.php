<?php

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Noerd\Traits\NoerdDetail;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdDetail;

    #[Url(as: 'summaryId', keep: false, except: '')]
    public $modelId = null;

    public const DETAIL_CLASS = Summary::class;

    public array $relations = [];

    public function mount(mixed $model = null): void
    {
        $this->initDetail($model);

        if (($this->relations['study_material_id'] ?? null) && ! isset($this->detailData['study_material_id'])) {
            $this->detailData['study_material_id'] = $this->relations['study_material_id'];
        }

        if ($this->detailData['study_material_id'] ?? null) {
            $this->relationTitles['study_material_id'] = StudyMaterial::find($this->detailData['study_material_id'])?->title;
        }
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

        $this->detailData['tenant_id'] = auth()->user()->selected_tenant_id;
        $summary = Summary::updateOrCreate(['id' => $this->modelId], $this->detailData);

        $this->showSuccessIndicator = true;

        if ($summary->wasRecentlyCreated) {
            $this->modelId = $summary->id;
        }
    }

    public function delete(): void
    {
        Summary::find($this->modelId)->delete();
        $this->closeModalProcess($this->getListComponent());
    }
}; ?>

<x-noerd::page :disableModal="$disableModal">
    <x-slot:header>
        <x-noerd::modal-title>{{ __('study_summary') }}</x-noerd::modal-title>
    </x-slot:header>

    <x-noerd::tab-content :layout="$pageLayout" />

    <x-slot:footer>
        <x-noerd::delete-save-bar :showDelete="isset($modelId)"/>
    </x-slot:footer>
</x-noerd::page>
