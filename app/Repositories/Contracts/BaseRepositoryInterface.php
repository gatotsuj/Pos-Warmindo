<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;

interface BaseRepositoryInterface
{
    public function all(array $columns = ['*']): Collection;
    
    public function find(int|string $id, array $columns = ['*']): ?Model;
    
    public function findOrFail(int|string $id, array $columns = ['*']): Model;
    
    public function create(array $details): Model;
    
    public function update(Model|int|string $modelOrId, array $details): bool;
    
    public function delete(Model|int|string $modelOrId): bool;
}
