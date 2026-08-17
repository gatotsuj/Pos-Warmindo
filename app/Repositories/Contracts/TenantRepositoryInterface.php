<?php

namespace App\Repositories\Contracts;

use App\Models\Tenant;
use Illuminate\Pagination\LengthAwarePaginator;

interface TenantRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate tenants with filters: event_id, search term.
     */
    public function paginateFiltered(array $filters, int $perPage = 15): LengthAwarePaginator;

    /**
     * Check if the tenant has associated users.
     */
    public function hasUsers(Tenant|int|string $tenant): bool;

    /**
     * Get IDs of filtered tenants.
     */
    public function getFilteredTenantIds(array $filters): \Illuminate\Support\Collection;
}
