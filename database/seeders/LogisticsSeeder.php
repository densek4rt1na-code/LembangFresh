<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Modules\Logistics\Models\LogisticsShipment;
use App\Models\User;

class LogisticsSeeder extends Seeder
{
    public function run(): void
    {
        $mitraBudi = User::where('email', 'budi@lembangfresh.test')->first();
        $budiId = $mitraBudi ? $mitraBudi->id : 5;

        $items = [
            [
                'tracking_code' => 'LF-LOG-20261101-WTL1',
                'mitra_id' => $budiId,
                'sender_name' => 'Kebun Pak Agus (Cikidang RT 02)',
                'receiver_name' => 'UMKM Salad Fresh (Jl. Panorama)',
                'commodity_info' => 'Wortel Grade B (300 kg)',
                'weight_kg' => 300,
                'distance_km' => 3.5,
                'shipping_method' => 'mitra_pengangkut',
                'shipping_cost' => LogisticsShipment::calculateMitraFee(3.5), // 50000 + 4 * 5000 = 70000
                'status' => 'selesai',
                'document_path' => 'logistics/surat_jalan_001.png',
                'notes' => 'Surat jalan resmi telah ditandatangani oleh penerima di lokasi toko.',
            ],
            [
                'tracking_code' => 'LF-LOG-20261102-TMT2',
                'mitra_id' => $budiId,
                'sender_name' => 'Kebun Pak Agus (Cikidang RT 02)',
                'receiver_name' => 'Hotel Lembang Asri Resort',
                'commodity_info' => 'Tomat Cherry Grade A (150 kg)',
                'weight_kg' => 150,
                'distance_km' => 4.8,
                'shipping_method' => 'mitra_pengangkut',
                'shipping_cost' => LogisticsShipment::calculateMitraFee(4.8), // 50000 + 5 * 5000 = 75000
                'status' => 'dikirim',
                'document_path' => 'logistics/surat_jalan_002.png',
                'notes' => 'Armada pickup dalam perjalanan dari Cikidang menuju hotel.',
            ],
            [
                'tracking_code' => 'LF-LOG-20261103-KTG3',
                'mitra_id' => null, // diantar petani sendiri
                'sender_name' => 'Kebun Bu Siti (Cibodas RT 03)',
                'receiver_name' => 'Kios Sayur Berkah (Pasar Panorama)',
                'commodity_info' => 'Kentang Granola Grade A (600 kg)',
                'weight_kg' => 600,
                'distance_km' => 2.1,
                'shipping_method' => 'antar_petani',
                'shipping_cost' => 20000,
                'status' => 'diambil',
                'document_path' => 'logistics/surat_jalan_003.png',
                'notes' => 'Pengantaran langsung oleh pihak petani menggunakan tossa roda tiga.',
            ],
            [
                'tracking_code' => 'LF-LOG-20261104-SLD4',
                'mitra_id' => $budiId,
                'sender_name' => 'Kebun Bu Siti (Cibodas RT 03)',
                'receiver_name' => 'Pabrik Olahan Sayur Lembang Jaya',
                'commodity_info' => 'Selada Grade C (200 kg)',
                'weight_kg' => 200,
                'distance_km' => 5.5,
                'shipping_method' => 'mitra_pengangkut',
                'shipping_cost' => LogisticsShipment::calculateMitraFee(5.5), // 50000 + 6 * 5000 = 80000
                'status' => 'diproses',
                'document_path' => 'logistics/surat_jalan_004.png',
                'notes' => 'Muatan dijadwalkan pickup sore hari pukul 16.00 WIB.',
            ],
            [
                'tracking_code' => 'LF-LOG-20261105-BKL5',
                'mitra_id' => null, // ambil sendiri
                'sender_name' => 'Kelompok Tani Cikahuripan Lembang',
                'receiver_name' => 'Resto Alam Sunda Lembang',
                'commodity_info' => 'Brokoli Hijau Grade A (100 kg)',
                'weight_kg' => 100,
                'distance_km' => 1.2,
                'shipping_method' => 'ambil_sendiri',
                'shipping_cost' => 0,
                'status' => 'selesai',
                'document_path' => 'logistics/surat_jalan_005.png',
                'notes' => 'Pembeli mengambil sendiri langsung ke kebun menggunakan pick-up pribadi.',
            ],
        ];

        foreach ($items as $item) {
            LogisticsShipment::create($item);
        }
    }
}

