<?php

use Livewire\Component;
use Noerd\Facades\Noerd;
use Noerd\Traits\NoerdList;
use Nywerk\Study\Models\StudyMaterial;

new class extends Component {
    use NoerdList;

    public function listAction(mixed $modelId = null, array $relations = []): void
    {
        Noerd::modal('study::study-material-detail', ['modelId' => $modelId, 'relations' => $relations]);
    }

    public function with(): array
    {
        $rows = $this->listQuery(StudyMaterial::class)
            ->paginate($this->perPage);

        return [
            'listConfig' => $this->buildList($rows),
        ];
    }

    public function rendering()
    {
        if ((int) request()->studyMaterialId) {
            $this->listAction(request()->studyMaterialId);
        }

        if (request()->create) {
            $this->listAction();
        }
    }
}; ?>

<x-noerd::page :disableModal="$disableModal">
    <x-noerd::list />
</x-noerd::page>
