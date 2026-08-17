<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Models\StockMovement;
use App\Repositories\Contracts\StockMovementRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class StockMovementRepository extends BaseRepository implements StockMovementRepositoryInterface
{
    public function __construct(StockMovement $model)
    {
        parent::__construct($model);
    }

    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with(['product', 'user'])->latest();

        if (!empty($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['date'])) {
            $query->whereDate('created_at', $filters['date']);
        }

        return $query->paginate($perPage);
    }

    public function recordMovement(Product $product, int $quantity, string $type, string $reference, ?string $notes = null): StockMovement
    {
        return DB::transaction(function () use ($product, $quantity, $type, $reference, $notes) {
            $stockBefore = $product->stock;

            if ($type === 'in' || $type === 'void') {
                $product->increment('stock', $quantity);
            } elseif ($type === 'out') {
                $product->decrement('stock', $quantity);
            }

            $stockAfter = $product->fresh()->stock;

            return $this->create([
                'product_id'   => $product->id,
                'user_id'      => auth()->id(),
                'type'         => $type,
                'quantity'     => $quantity,
                'stock_before' => $stockBefore,
                'stock_after'  => $stockAfter,
                'reference'    => $reference,
                'notes'        => $notes,
            ]);
        });
    }
}
