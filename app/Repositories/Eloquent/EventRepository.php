<?php

namespace App\Repositories\Eloquent;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    public function paginateWithTenantsCount(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->withCount('tenants')
            ->orderByDesc('starts_at')
            ->orderBy('name')
            ->paginate($perPage);
    }

    public function hasTenants(Event|int|string $event): bool
    {
        $model = $event instanceof Event ? $event : $this->findOrFail($event);
        return $model->tenants()->exists();
    }

    public function getActiveOrderedByName(): Collection
    {
        return $this->model->newQuery()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    public function getActiveOrSpecificOrderedByName(int $eventId): Collection
    {
        return $this->model->newQuery()
            ->where(function ($q) use ($eventId) {
                $q->where('is_active', true)
                  ->orWhere('id', $eventId);
            })
            ->orderBy('name')
            ->get();
    }

    public function allOrderedByName(string $direction = 'asc'): Collection
    {
        return $this->model->newQuery()->orderBy('name', $direction)->get();
    }

    public function allOrderedByStartsAtAndName(): Collection
    {
        return $this->model->newQuery()
            ->orderByDesc('starts_at')
            ->orderBy('name')
            ->get();
    }
}
