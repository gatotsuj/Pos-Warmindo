<?php

namespace App\Repositories\Eloquent;

use App\Models\Tenant;
use App\Repositories\Contracts\TenantRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class TenantRepository extends BaseRepository implements TenantRepositoryInterface
{
    public function __construct(Tenant $model)
    {
        parent::__construct($model);
    }

    public function paginateFiltered(array $filters, int $perPage = 15): LengthAwarePaginator
    {
        $query = $this->model->newQuery()
            ->with('event')
            ->withCount('users');

        if (!empty($filters['event_id'])) {
            $query->where('event_id', (int)$filters['event_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('name')->paginate($perPage);
    }

    public function hasUsers(Tenant|int|string $tenant): bool
    {
        $model = $tenant instanceof Tenant ? $tenant : $this->findOrFail($tenant);
        return $model->users()->exists();
    }

    public function getFilteredTenantIds(array $filters): \Illuminate\Support\Collection
    {
        $query = $this->model->newQuery();

        if (!empty($filters['event_id'])) {
            $query->where('event_id', (int)$filters['event_id']);
        }

        if (!empty($filters['search'])) {
            $query->where('name', 'like', '%' . $filters['search'] . '%');
        }

        return $query->pluck('id');
    }
}
