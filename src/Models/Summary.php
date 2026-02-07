<?php

namespace Nywerk\Study\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Noerd\Models\Tenant;
use Noerd\Traits\BelongsToTenant;
use Noerd\Traits\HasListScopes;
use Nywerk\Study\Database\Factories\SummaryFactory;

class Summary extends Model
{
    use BelongsToTenant;
    use HasFactory;
    use HasListScopes;

    protected $table = 'study_summaries';

    protected $fillable = [
        'tenant_id',
        'study_material_id',
        'title',
        'content',
    ];

    protected array $searchable = [
        'title',
        'content',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function studyMaterial(): BelongsTo
    {
        return $this->belongsTo(StudyMaterial::class);
    }

    protected static function newFactory(): Factory
    {
        return SummaryFactory::new();
    }

    protected function casts(): array
    {
        return [];
    }
}
