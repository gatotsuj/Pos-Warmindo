<?php

namespace App\Repositories\Eloquent;

use App\Models\Product;
use App\Repositories\Contracts\ProductRepositoryInterface;
use App\Support\CurrentTenant;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class ProductRepository extends BaseRepository implements ProductRepositoryInterface
{
    public function __construct(Product $model)
    {
        parent::__construct($model);
    }

    public function paginateFiltered(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->with('category');

        if (!empty($filters['search'])) {
            $query->search($filters['search']);
        }

        if (!empty($filters['category'])) {
            $query->byCategory($filters['category']);
        }

        if (isset($filters['stock'])) {
            if ($filters['stock'] === 'low') {
                $query->lowStock();
            } elseif ($filters['stock'] === 'out') {
                $query->where('stock', 0);
            }
        }

        if (isset($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->active();
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        return $query->latest()->paginate($perPage);
    }

    public function getActiveInStock(array $filters = []): Collection
    {
        if (empty($filters['search']) && empty($filters['category'])) {
            $tenantId = CurrentTenant::id() ?? 1;
            $cacheKey = "tenant_{$tenantId}_active_products";
            return Cache::store('redis')->remember($cacheKey, 600, function () {
                return $this->model->newQuery()
                    ->with('category')
                    ->active()
                    ->where('stock', '>', 0)
                    ->get();
            });
        }

        $query = $this->model->newQuery()
            ->with('category')
            ->active()
            ->where('stock', '>', 0);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category_id', $filters['category']);
        }

        return $query->get();
    }

    public function getLowStock(int $limit = 5, int $threshold = 10): Collection
    {
        return $this->model->newQuery()->lowStock($threshold)->take($limit)->get();
    }

    public function allOrderedByName(string $direction = 'asc'): Collection
    {
        return $this->model->newQuery()->orderBy('name', $direction)->get();
    }

    public static function flushCache(int $tenantId): void
    {
        Cache::store('redis')->forget("tenant_{$tenantId}_active_products");
    }
}
