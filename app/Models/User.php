<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Modules\Harvest\Models\Harvest;
use App\Modules\Logistics\Models\LogisticsShipment;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'lat',
        'lng',
        'is_verified',
        'delivery_available',
        'delivery_radius',
        'delivery_fee',
        'vehicle_desc',
        'vehicle_capacity',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'delivery_available' => 'boolean',
            'is_active' => 'boolean',
            'lat' => 'double',
            'lng' => 'double',
            'delivery_radius' => 'double',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPetani(): bool
    {
        return $this->role === 'petani';
    }

    public function isPembeli(): bool
    {
        return $this->role === 'pembeli';
    }

    public function isMitra(): bool
    {
        return $this->role === 'mitra';
    }

    public function hasRole(string|array $roles): bool
    {
        if (is_array($roles)) {
            return in_array($this->role, $roles);
        }
        return $this->role === $roles;
    }

    public function harvests()
    {
        return $this->hasMany(Harvest::class, 'farmer_id');
    }

    public function shipments()
    {
        return $this->hasMany(LogisticsShipment::class, 'mitra_id');
    }

    public function demands()
    {
        return $this->hasMany(Demand::class, 'buyer_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'buyer_id');
    }
}
