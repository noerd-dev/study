<?php

use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Noerd\Traits\NoerdDetail;
use Nywerk\Study\Models\Flashcard;
use Nywerk\Study\Models\StudyMaterial;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdDetail;

    #[Url(as: 'flashcardId', keep: false, except: '')]
    public $modelId = null;

    public const DETAIL_CLASS = Flashcard::class;

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

        if ($this->detailData['summary_id'] ?? null) {
            $this->relationTitles['summary_id'] = Summary::find($this->detailData['summary_id'])?->title;
        }

        $this->preselect('study_material_id');

        if (! isset($this->detailData['created_date'])) {
            $this->detailData['created_date'] = now()->format('Y-m-d');
        }
    }

    #[On('studyMaterialSelected')]
    public function studyMaterialSelected($studyMaterialId): void
    {
        $studyMaterial = StudyMaterial::find($studyMaterialId);
        $this->detailData['study_material_id'] = $studyMaterial->id;
        $this->relationTitles['study_material_id'] = $studyMaterial->title;
    }

    #[On('summarySelected')]
    public function summarySelected($summaryId): void
    {
        $summary = Summary::find($summaryId);
        $this->detailData['summary_id'] = $summary->id;
        $this->relationTitles['summary_id'] = $summary->title;
    }

    public function store(): void
    {
        $this->validate([
            'detailData.question' => ['required', 'string'],
            'detailData.study_material_id' => ['required', 'exists:study_materials,id'],
        ]);

        $this->detailData['tenant_id'] = auth()->user()->selected_tenant_id;
        $flashcard = Flashcard::updateOrCreate(['id' => $this->modelId], $this->detailData);

        $this->showSuccessIndicator = true;

        if ($flashcard->wasRecentlyCreated) {
            $this->modelId = $flashcard->id;
        }
    }

    public function delete(): void
    {
        Flashcard::find($this->modelId)->delete();
        $this->closeModalProcess($this->getListComponent());
    }
}; ?>

<x-noerd::page :disableModal="$disableModal">
    <x-slot:header>
        <x-noerd::modal-title>{{ __('study_flashcard') }}</x-noerd::modal-title>
    </x-slot:header>

    <x-noerd::tab-content :layout="$pageLayout" />

    <x-slot:footer>
        <x-noerd::delete-save-bar :showDelete="isset($modelId)"/>
    </x-slot:footer>
</x-noerd::page>
