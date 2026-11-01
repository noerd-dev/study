<?php

use Livewire\Component;
use Noerd\Facades\Noerd;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdList;

    public $listModel = Summary::class;
    public ?string $detailRoute = 'study.summary.detail';
    public $detailComponent = 'study::summary-detail';

    public ?int $studyMaterialId = null;

    protected array $listWith = ['studyMaterial'];

    protected array $listScopes = ['study_material_id' => 'studyMaterialId'];

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        Noerd::modalFor('study.summary.detail', 'study::summary-detail', ['modelId' => $modelId, 'relations' => $this->studyMaterialId ? ['study_material_id' => $this->studyMaterialId] : $relations]);
    }

    public function listData(): array
    {
        $rows = $this->paginateList($this->listQuery($this->listModel));

        foreach ($rows as $row) {
            $row->studyMaterial = $row->studyMaterial?->title;
        }

        return $this->buildList($rows);
    }
}; ?>

<x-noerd::page>
    <x-noerd::list />
</x-noerd::page>
