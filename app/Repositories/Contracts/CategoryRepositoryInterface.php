<?php

namespace App\Repositories\Contracts;

use App\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate categories with product counts, optionally filtering by search term.
     */
    public function paginateWithProductsCount(?string $search = null, int $perPage = 5): LengthAwarePaginator;

    /**
     * Check if the given category has associated products.
     */
    public function hasProducts(Category|int|string $category): bool;

    /**
     * Get all categories ordered by name.
     */
    public function allOrderedByName(string $direction = 'asc'): \Illuminate\Database\Eloquent\Collection;

    /**
     * Get categories that have active and in-stock products, ordered by name.
     */
    public function getActiveInStockOrderedByName(): \Illuminate\Database\Eloquent\Collection;
}
