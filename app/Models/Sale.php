<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    protected $fillable = [
        'receipt_number',
        'cashier_id',
        'total_amount',
        'discount_amount',
        'amount_paid',
        'change_amount',
        'payment_method',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'total_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_amount' => 'decimal:2',
        ];
    }

    /**
     * A sale belongs to a cashier (user).
     */
    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cashier_id');
    }

    /**
     * A sale can have many sale items.
     */
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class, 'sale_id');
    }
}
