<?php

namespace App\Modules\Logistics\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class LogisticsShipment extends Model
{
    use HasFactory;

    protected $table = 'logistics_shipments';

    protected $fillable = [
        'tracking_code',
        'mitra_id',
        'sender_name',
        'receiver_name',
        'commodity_info',
        'weight_kg',
        'distance_km',
        'shipping_method',
        'shipping_cost',
        'status',
        'document_path',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'integer',
            'distance_km' => 'double',
            'shipping_cost' => 'integer',
        ];
    }

    public function mitra()
    {
        return $this->belongsTo(User::class, 'mitra_id');
    }

    public static function calculateMitraFee(float $distanceKm): int
    {

        return 50000 + (int) (ceil($distanceKm) * 5000);
    }
}

