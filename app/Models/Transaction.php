<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    protected $fillable = [
        'invoice_number',
        'user_id',
        'total_amount',
        'payment_method',
        'paid_amount',
        'change_amount',
        'status',
    ];

    protected $casts = [
        'total_amount' => 'float',
        'paid_amount' => 'float',
        'change_amount' => 'float',
    ];

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class);
    }
}
