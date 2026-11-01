<?php

namespace Nywerk\Study\Traits;

use Noerd\Support\ListFilter;
use Nywerk\Study\Models\StudyMaterial;

trait StudyMaterialFilterTrait
{
    protected function getStudyMaterialsListFilter(): ListFilter
    {
        return ListFilter::model(__('Study Material'), 'study_material_id', StudyMaterial::class, 'title');
    }
}
