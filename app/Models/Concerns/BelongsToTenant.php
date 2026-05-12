<?php

namespace App\Models\Concerns;

use App\Support\CurrentTenant;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

trait BelongsToTenant
{
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (CurrentTenant::has()) {
                $builder->where(
                    $builder->getModel()->getTable().'.tenant_id',
                    CurrentTenant::id()
                );
            }
        });

        static::creating(function (Model $model) {
            if (CurrentTenant::has() && empty($model->tenant_id)) {
                $model->tenant_id = CurrentTenant::id();
            }
        });
    }
}
