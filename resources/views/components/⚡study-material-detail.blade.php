<?php

use Livewire\Attributes\Url;
use Livewire\Component;
use Noerd\Traits\NoerdDetail;
use Nywerk\Study\Models\StudyMaterial;

new class extends Component {
    use NoerdDetail;

    #[Url(as: 'studyMaterialId', keep: false, except: '')]
    public $modelId = null;

    public const DETAIL_CLASS = StudyMaterial::class;

    public function mount(): void
    {
        $this->initDetail();

        $this->setPreselect('study_material_id', $this->modelId);
    }

    public function store(): void
    {
        $this->validateFromLayout();

        $this->detailData['tenant_id'] = auth()->user()->selected_tenant_id;
        $studyMaterial = StudyMaterial::updateOrCreate(['id' => $this->modelId], $this->detailData);

        $this->showSuccessIndicator = true;

        if ($studyMaterial->wasRecentlyCreated) {
            $this->modelId = $studyMaterial->id;
        }
    }

    public function delete(): void
    {
        StudyMaterial::find($this->modelId)->delete();
        $this->closeModalProcess($this->getListComponent());
    }
}; ?>

<x-noerd::page :disableModal="$disableModal">
    <x-slot:header>
        <x-noerd::modal-title>{{ __('study_study_material') }}</x-noerd::modal-title>
    </x-slot:header>

    <x-noerd::tab-content :layout="$pageLayout" :modelId="$modelId" />

    <x-slot:footer>
        <x-noerd::delete-save-bar :showDelete="isset($modelId)"/>
    </x-slot:footer>
</x-noerd::page>
