<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sale extends Model
{
    use HasFactory;
    protected $fillable = [
        'total_sale',
        'total_quantity',
        'customer_id',
        'inventory_id',
        'payment_method_id'
    ];

    public function inventory():BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    public function paymentMethod():BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function customer():BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function orders():HasMany
    {
        return $this->hasMany(Order::class);
    }
    

}
