<?php

namespace App\Repositories\Contracts;

use App\Models\Event;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface EventRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate events with tenant counts.
     */
    public function paginateWithTenantsCount(int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if the event has associated tenants.
     */
    public function hasTenants(Event|int|string $event): bool;

    /**
     * Get all active events ordered by name.
     */
    public function getActiveOrderedByName(): Collection;

    /**
     * Get all active events, plus a specific event, ordered by name.
     */
    public function getActiveOrSpecificOrderedByName(int $eventId): Collection;

    /**
     * Get all events ordered by name.
     */
    public function allOrderedByName(string $direction = 'asc'): Collection;

    /**
     * Get all events ordered by starts_at desc, then name.
     */
    public function allOrderedByStartsAtAndName(): Collection;
}
