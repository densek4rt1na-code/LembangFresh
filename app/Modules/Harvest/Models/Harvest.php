<?php

namespace App\Modules\Harvest\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Harvest extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_id',
        'commodity',
        'variety',
        'quantity',
        'price_per_kg',
        'grade',
        'marketing_channel',
        'harvest_date',
        'ready_date',
        'photo_path',
        'description',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'harvest_date' => 'date',
            'ready_date' => 'date',
            'quantity' => 'integer',
            'price_per_kg' => 'integer',
        ];
    }

    public function farmer()
    {
        return $this->belongsTo(User::class, 'farmer_id');
    }

    public static function determineMarketingChannel(string $grade): string
    {
        return match ($grade) {
            'A' => 'Pasar Normal (Supermarket, Hotel, Restoran)',
            'B' => 'Pasar Alternatif (UMKM, Rumah Makan, Katering)',
            'C' => 'Jalur Pengolahan (Industri Olahan Makanan)',
            default => 'Pasar Normal',
        };
    }
}

