<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Customer extends Authenticatable implements JWTSubject
{
    use HasFactory;

    public function isPerson()
    {
        return $this->role === 'client'; // Ajusta esto según tu lógica
    }
    protected $fillable = [
        'name',
        'surname',
        'dni',
        'ruc',
        'customer_type_id',
        'reason',
        'address',
        'image',
        'email',
        'password',
        'phone'
    ];

    public function customerType():BelongsTo
    {
        return $this->belongsTo(CustomerType::class);
    }

    public function sales():HasMany
    {
        return $this->hasMany(Sale::class);
    }

    public function comments():HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
