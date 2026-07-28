<?php

use Livewire\Component;
use Noerd\Facades\Noerd;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdList;

    public $listModel = Summary::class;
    public $detailComponent = 'study::summary-detail';

    public ?int $studyMaterialId = null;

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        Noerd::modal('study::summary-detail', ['modelId' => $modelId, 'relations' => $this->studyMaterialId ? ['study_material_id' => $this->studyMaterialId] : $relations]);
    }

    public function listData(): array
    {
        $rows = $this->listQuery($this->listModel)
            ->with('studyMaterial')
            ->when($this->studyMaterialId, function ($query): void {
                $query->where('study_material_id', $this->studyMaterialId);
            })
            ->paginate($this->perPage);

        foreach ($rows as $row) {
            $row->studyMaterial = $row->studyMaterial?->title;
        }

        return $this->buildList($rows);
    }

    public function rendering()
    {
        if ((int) request()->summaryId) {
            $this->listAction(request()->summaryId);
        }

        if (request()->create) {
            $this->listAction();
        }
    }
}; ?>

<x-noerd::page>
    <x-noerd::list />
</x-noerd::page>
