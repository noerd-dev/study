<?php

use Livewire\Component;
use Noerd\Traits\NoerdDetail;
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

        $this->preselect('study_material_id');
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
