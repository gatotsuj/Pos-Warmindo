<?php

// app/Models/Product.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use \App\Models\Concerns\BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'name',
        'sku',
        'description',
        'price',
        'stock',
        'image',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'is_active' => 'boolean',
    ];

    // ==================== RELATIONSHIPS ====================

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    // ==================== ACCESSORS ====================

    /**
     * Get formatted price in Rupiah
     */
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get image URL or default placeholder
     */
    public function getImageUrlAttribute(): string
    {
        if ($this->image && Storage::disk('public')->exists($this->image)) {
            return Storage::url($this->image);
        }

        // Return placeholder image
        return '<https://via.placeholder.com/200x200?text=No+Image>';
    }

    // ==================== SCOPES ====================

    /**
     * Scope: Only active products
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Search by name or SKU
     */
    public function scopeSearch($query, ?string $term)
    {
        return $query->when($term, function ($q) use ($term) {
            $q->where(function ($query) use ($term) {
                $query->where('name', 'like', "%{$term}%")
                      ->orWhere('sku', 'like', "%{$term}%");
            });
        });
    }

    /**
     * Scope: Low stock products
     */
    public function scopeLowStock($query, int $threshold = 10)
    {
        return $query->where('stock', '<=', $threshold);
    }

    /**
     * Scope: Filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->when($categoryId, function ($q) use ($categoryId) {
            $q->where('category_id', $categoryId);
        });
    }

    /**
     * Scope: Available for sale (active AND has stock)
     */
    public function scopeAvailable($query)
    {
        return $query->active()->where('stock', '>', 0);
    }

    // ==================== METHODS ====================

    /**
     * Decrease stock
     */
    public function decreaseStock(int $quantity): bool
    {
        if ($this->stock < $quantity) {
            return false;
        }

        $this->decrement('stock', $quantity);
        return true;
    }

    /**
     * Increase stock
     */
    public function increaseStock(int $quantity): void
    {
        $this->increment('stock', $quantity);
    }

    /**
     * Check if product has enough stock
     */
    public function hasEnoughStock(int $quantity): bool
    {
        return $this->stock >= $quantity;
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock(int $threshold = 10): bool
    {
        return $this->stock <= $threshold;
    }

    /**
     * Check if product is out of stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }
}
