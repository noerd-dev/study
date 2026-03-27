<?php

use Livewire\Component;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\Summary;

new class extends Component {
    use NoerdList;

    public ?int $studyMaterialId = null;

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        $this->dispatch(
            event: 'noerdModal',
            modalComponent: 'summary-detail',
            source: $this->getComponentName(),
            arguments: ['modelId' => $modelId, 'relations' => $this->studyMaterialId ? ['study_material_id' => $this->studyMaterialId] : $relations],
        );
    }

    public function with()
    {
        $rows = $this->listQuery(Summary::class)
            ->with('studyMaterial')
            ->when($this->studyMaterialId, function ($query): void {
                $query->where('study_material_id', $this->studyMaterialId);
            })
            ->paginate($this->perPage);

        foreach ($rows as $row) {
            $row->studyMaterial = $row->studyMaterial?->title;
        }

        return [
            'listConfig' => $this->buildList($rows),
        ];
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

<x-noerd::page :disableModal="$disableModal">
    <x-noerd::list />
</x-noerd::page>
