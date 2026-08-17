<?php

namespace App\Repositories\Eloquent;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function paginateWithProductsCount(?string $search = null, int $perPage = 5): LengthAwarePaginator
    {
        $query = $this->model->newQuery()->withCount('products');

        if (!empty($search)) {
            $query->search($search);
        }

        return $query->latest()->paginate($perPage);
    }

    public function hasProducts(Category|int|string $category): bool
    {
        $model = $category instanceof Category ? $category : $this->findOrFail($category);
        return $model->products()->exists();
    }

    public function allOrderedByName(string $direction = 'asc'): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()->orderBy('name', $direction)->get();
    }

    public function getActiveInStockOrderedByName(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->model->newQuery()
            ->whereHas('products', function ($query) {
                $query->active()->where('stock', '>', 0);
            })
            ->orderBy('name')
            ->get();
    }
}
