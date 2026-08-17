<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate tenant users, optionally filtering by search and role, excluding superadmin.
     */
    public function paginateTenantUsers(int $tenantId, array $filters, int $perPage = 10): LengthAwarePaginator;

    /**
     * Check if a user belongs to the given tenant and is not a superadmin.
     */
    public function isTenantUser(User|int|string $user, int $tenantId): bool;
}
