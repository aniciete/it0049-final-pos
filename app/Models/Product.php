<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    public const LOW_STOCK_THRESHOLD = 5;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'price',
        'stock_quantity',
        'image',
    ];

    /**
     * Sales transactions in which this product was sold.
     *
     * @return HasMany<Sale, $this>
     */
    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }

    /**
     * Scope query to active products that have available inventory.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeAvailableForSale(Builder $query): Builder
    {
        return $query->where('stock_quantity', '>', 0)->orderBy('name')->orderBy('id');
    }

    /**
     * Scope query to active products at or below the low-stock threshold.
     *
     * @param  Builder<Product>  $query
     * @return Builder<Product>
     */
    public function scopeLowStock(Builder $query, int $threshold = self::LOW_STOCK_THRESHOLD): Builder
    {
        return $query->where('stock_quantity', '<=', $threshold);
    }

    /**
     * Determine whether this product has an existing image stored on the public disk.
     */
    public function hasImage(): bool
    {
        return ! empty($this->image) && Storage::disk('public')->exists($this->image);
    }

    /**
     * Resolve the public image URL for this product, falling back to the placeholder asset.
     */
    public function imageUrl(): string
    {
        if ($this->hasImage()) {
            return is_file(public_path('storage/'.$this->image))
                ? '/storage/'.ltrim($this->image, '/')
                : route('media.show', ['folder' => 'products', 'filename' => basename($this->image)], false);
        }

        return '/assets/images/product-placeholder.svg';
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'stock_quantity' => 'integer',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }
}
