<?php

namespace Nywerk\Study\Traits;

use Nywerk\Study\Models\StudyMaterial;

trait StudyMaterialFilterTrait
{
    protected function getStudyMaterialsListFilter(): array
    {
        $filter['label'] = __('study_label_study_material');
        $filter['column'] = 'study_material_id';
        $filter['type'] = 'Picklist';
        $filter['options'] = [
            null => __('study_label_all_study_materials'),
        ];

        $options = StudyMaterial::orderBy('title', 'asc')->get()
            ->map(fn ($studyMaterial) => [
                'id' => $studyMaterial->id,
                'name' => $studyMaterial->title,
            ])
            ->pluck('name', 'id')
            ->toArray();

        foreach ($options as $key => $option) {
            $filter['options'][$key] = $option;
        }

        return $filter;
    }
}
