<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{

    public function run(): void
    {

        $primaryUsers = [
            [
                'name' => 'Admin LembangFresh',
                'email' => 'admin@lembangfresh.test',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081122334455',
                'address' => 'Jl. Raya Lembang No. 100, Kec. Lembang',
                'lat' => -6.8120,
                'lng' => 107.6180,
                'is_verified' => true,
                'delivery_available' => false,
                'delivery_radius' => 0,
                'delivery_fee' => 0,
                'vehicle_desc' => null,
                'vehicle_capacity' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Pak Agus Susanto (Petani Hortikultura)',
                'email' => 'agus@lembangfresh.test',
                'password' => Hash::make('petani123'),
                'role' => 'petani',
                'phone' => '081234567891',
                'address' => 'Desa Cikidang RT 02/05, Kec. Lembang',
                'lat' => -6.7950,
                'lng' => 107.6350,
                'is_verified' => true,
                'delivery_available' => true,
                'delivery_radius' => 5.0,
                'delivery_fee' => 25000,
                'vehicle_desc' => 'Tossa Roda Tiga',
                'vehicle_capacity' => 200,
                'is_active' => true,
            ],
            [
                'name' => 'Ibu Siti Aminah (Petani Sayur Segar)',
                'email' => 'siti@lembangfresh.test',
                'password' => Hash::make('petani123'),
                'role' => 'petani',
                'phone' => '081234567892',
                'address' => 'Desa Cibodas RT 03/02, Kec. Lembang',
                'lat' => -6.8300,
                'lng' => 107.6100,
                'is_verified' => true,
                'delivery_available' => false,
                'delivery_radius' => 3.0,
                'delivery_fee' => 20000,
                'vehicle_desc' => null,
                'vehicle_capacity' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'UMKM Salad Fresh Lembang',
                'email' => 'salad@lembangfresh.test',
                'password' => Hash::make('pembeli123'),
                'role' => 'pembeli',
                'phone' => '081334455667',
                'address' => 'Jl. Panorama No. 45, Kec. Lembang',
                'lat' => -6.8110,
                'lng' => 107.6170,
                'is_verified' => true,
                'delivery_available' => false,
                'delivery_radius' => 0,
                'delivery_fee' => 0,
                'vehicle_desc' => null,
                'vehicle_capacity' => 0,
                'is_active' => true,
            ],
            [
                'name' => 'Pak Budi Santoso (Mitra Logistik)',
                'email' => 'budi@lembangfresh.test',
                'password' => Hash::make('mitra123'),
                'role' => 'mitra',
                'phone' => '081445566778',
                'address' => 'Jl. Tangkuban Perahu No. 12, Kec. Lembang',
                'lat' => -6.8050,
                'lng' => 107.6200,
                'is_verified' => true,
                'delivery_available' => false,
                'delivery_radius' => 15.0,
                'delivery_fee' => 50000,
                'vehicle_desc' => 'Pickup Mitsubishi L300 (D 1234 XY)',
                'vehicle_capacity' => 800,
                'is_active' => true,
            ],
        ];

        foreach ($primaryUsers as $userData) {
            User::updateOrCreate(['email' => $userData['email']], $userData);
        }

        $faker = Faker::create('id_ID');
        $dummyRoles = ['petani', 'pembeli', 'mitra', 'petani', 'pembeli'];
        $lembangVillages = ['Cikahuripan', 'Kayuambon', 'Jayagiri', 'Gudangkahuripan', 'Wangunsari'];

        for ($i = 0; $i < 5; $i++) {
            $role = $dummyRoles[$i];
            $village = $lembangVillages[$i];

            User::create([
                'name' => $faker->name . ' (' . ucfirst($role) . ')',
                'email' => 'dummy' . ($i + 1) . '_' . strtolower($faker->userName) . '@lembangfresh.test',
                'password' => Hash::make('password'),
                'role' => $role,
                'phone' => $faker->phoneNumber,
                'address' => 'Desa ' . $village . ', Kec. Lembang, Kab. Bandung Barat',
                'lat' => -6.8120 + ($faker->randomFloat(4, -0.03, 0.03)),
                'lng' => 107.6180 + ($faker->randomFloat(4, -0.03, 0.03)),
                'is_verified' => $faker->boolean(80),
                'delivery_available' => $role === 'petani' ? $faker->boolean(60) : false,
                'delivery_radius' => $faker->randomElement([4.0, 5.0, 7.5, 10.0]),
                'delivery_fee' => $faker->randomElement([20000, 25000, 30000]),
                'vehicle_desc' => $role === 'mitra' ? 'Pickup Gran Max D ' . rand(1000, 9999) . ' ABC' : null,
                'vehicle_capacity' => $role === 'mitra' ? rand(500, 1000) : 0,
                'is_active' => true,
            ]);
        }
    }
}

