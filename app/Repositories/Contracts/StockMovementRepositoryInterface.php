<?php

namespace App\Repositories\Contracts;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Pagination\LengthAwarePaginator;

interface StockMovementRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Paginate stock movements with filters: product_id, type, date.
     */
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator;

    /**
     * Record a new stock movement and update the product's stock.
     */
    public function recordMovement(Product $product, int $quantity, string $type, string $reference, ?string $notes = null): StockMovement;
}
