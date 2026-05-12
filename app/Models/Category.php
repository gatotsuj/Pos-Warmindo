<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use BelongsToTenant;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
    ];

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'slug';
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($category) {
            if (empty($category->slug)) {
                $tenantId = $category->tenant_id ?? \App\Support\CurrentTenant::id();
                $category->slug = static::generateUniqueSlug($category->name, null, $tenantId);
            }
        });

        static::updating(function ($category) {
            // Regenerate slug if name changed
            if ($category->isDirty('name')) {
                $category->slug = static::generateUniqueSlug($category->name, $category->id);
            }
        });
    }

    /**
     * Generate a unique slug from the given name.
     *
     * @param string $name
     * @param int|null $excludeId
     * @param int|null $tenantIdForCreate tenant id when creating (seeding) without CurrentTenant
     * @return string
     */
    protected static function generateUniqueSlug(string $name, ?int $excludeId = null, ?int $tenantIdForCreate = null): string
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        $tenantId = $tenantIdForCreate
            ?? ($excludeId
                ? static::withoutGlobalScopes()->where('id', $excludeId)->value('tenant_id')
                : \App\Support\CurrentTenant::id());

        while (static::withoutGlobalScopes()
            ->where('tenant_id', $tenantId)
            ->where('slug', $slug)
            ->when($excludeId, fn ($query) => $query->where('id', '!=', $excludeId))
            ->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Scope a query to filter categories by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearch($query, string $search)
    {
        return $query->where('name', 'like', '%' . $search . '%');
    }

    /**
     * Get the products for the category.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
