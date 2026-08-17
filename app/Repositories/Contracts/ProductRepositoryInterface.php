<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate products with optional filters: search, category, stock, and status.
     */
    public function paginateFiltered(array $filters, int $perPage = 12): LengthAwarePaginator;

    /**
     * Get active products that are in stock, optionally filtered by search and category.
     */
    public function getActiveInStock(array $filters = []): Collection;

    /**
     * Get low stock products.
     */
    public function getLowStock(int $limit = 5, int $threshold = 10): Collection;

    /**
     * Get all products ordered by name.
     */
    public function allOrderedByName(string $direction = 'asc'): Collection;
}
