<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $primaryKey = 'product_id';

    protected $fillable = [
        'category_id',
        'product_name',
        'barcode',
        'unit',
        'cost_price',
        'selling_price',
        'current_stock',
        'minimum_stock',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'cost_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'current_stock' => 'integer',
            'minimum_stock' => 'integer',
        ];
    }

    /**
     * A product belongs to a category.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(
            Category::class,
            'category_id',
            'category_id'
        );
    }

    /**
     * A product can appear in many sale items.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(
            SaleItem::class,
            'product_id',
            'product_id'
        );
    }

    /**
     * A product can have many stock transactions.
     */
    public function stockTransactions(): HasMany
    {
        return $this->hasMany(
            StockTransaction::class,
            'product_id',
            'product_id'
        );
    }

    /**
     * Check if product is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->minimum_stock;
    }

    /**
     * Scope to get low stock products.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock')
            ->where('status', 'active');
    }
}
