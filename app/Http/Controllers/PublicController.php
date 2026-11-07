<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Demand;
use App\Models\Order;
use App\Modules\Harvest\Models\Harvest;
use App\Modules\Logistics\Models\LogisticsShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    // Haversine distance in km
    public static function calculateDistance(?float $lat1, ?float $lng1, ?float $lat2, ?float $lng2): ?float
    {
        if ($lat1 === null || $lng1 === null || $lat2 === null || $lng2 === null) {
            return null;
        }

        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return round($earthRadius * $c, 1);
    }

    public function index(Request $request)
    {
        $query = Harvest::with('farmer')->where('status', '!=', 'habis');

        if ($request->filled('search')) {
            $query->where('commodity', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('max_price')) {
            $query->where('price_per_kg', '<=', (int) $request->max_price);
        }

        $harvests = $query->latest()->get();

        $currentUser = Auth::user();

        // Calculate GIS distance for each harvest to user's location
        $harvests->map(function ($item) use ($currentUser) {
            if ($currentUser && $currentUser->lat && $currentUser->lng && $item->farmer) {
                $item->distance_km = self::calculateDistance(
                    $currentUser->lat,
                    $currentUser->lng,
                    $item->farmer->lat,
                    $item->farmer->lng
                );
            } else {
                $item->distance_km = null;
            }
            return $item;
        });

        if ($request->boolean('sort_distance') && $currentUser) {
            $harvests = $harvests->sortBy('distance_km')->values();
        }

        // All active farmers for map markers
        $farmerMarkers = User::where('role', 'petani')
            ->whereNotNull('lat')
            ->whereNotNull('lng')
            ->get();

        return view('welcome', compact('harvests', 'farmerMarkers'));
    }

    public function showProduct($id)
    {
        $harvest = Harvest::with('farmer')->findOrFail($id);
        $currentUser = Auth::user();

        $distance = null;
        if ($currentUser && $currentUser->lat && $currentUser->lng && $harvest->farmer) {
            $distance = self::calculateDistance(
                $currentUser->lat,
                $currentUser->lng,
                $harvest->farmer->lat,
                $harvest->farmer->lng
            );
        }

        // Calculate delivery options based on LembangFresh document:
        // 1. Ambil Sendiri: Rp 0
        // 2. Diantar Petani: jika farmer delivery_available dan distance <= radius
        // 3. Mitra Pengangkut: Rp 50.000 + (jarak * Rp 5.000)
        $deliveryOptions = [
            'ambil_sendiri' => [
                'name' => 'Ambil Sendiri ke Kebun',
                'fee' => 0,
                'available' => true,
                'note' => 'Cocok jika Anda memiliki armada sendiri atau lokasi dekat.',
            ],
        ];

        $farmer = $harvest->farmer;
        $farmerCanDeliver = $farmer->delivery_available && ($distance === null || $distance <= $farmer->delivery_radius);

        $deliveryOptions['antar_petani'] = [
            'name' => 'Diantar Langsung Petani (' . $farmer->name . ')',
            'fee' => $farmer->delivery_fee,
            'available' => $farmerCanDeliver,
            'note' => $farmerCanDeliver
                ? 'Petani bersedia mengantar dalam radius ' . $farmer->delivery_radius . ' km.'
                : 'Lokasi Anda berada di luar radius pengantaran petani (' . $farmer->delivery_radius . ' km) atau layanan sedang nonaktif.',
        ];

        $hasActiveMitra = User::where('role', 'mitra')->where('is_active', true)->exists();
        $distForMitra = $distance ?? 5.0; // default 5 km jika belum set koordinat
        $mitraFee = LogisticsShipment::calculateMitraFee($distForMitra);

        $deliveryOptions['mitra_pengangkut'] = [
            'name' => 'Diantar Mitra Pengangkut (Pickup Lokal Lembang)',
            'fee' => $mitraFee,
            'available' => $hasActiveMitra,
            'note' => 'Solusi pengangkutan bila petani tidak mengantar. Tarif dasar Rp 50.000 + Rp 5.000/km.',
        ];

        return view('product-detail', compact('harvest', 'distance', 'deliveryOptions'));
    }

    public function orderProduct(Request $request, $id)
    {
        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu untuk memesan.');
        }

        $harvest = Harvest::with('farmer')->findOrFail($id);

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1|max:' . $harvest->quantity,
            'delivery_method' => 'required|in:ambil_sendiri,antar_petani,mitra_pengangkut',
        ], [
            'quantity.required' => 'Jumlah pesanan wajib diisi.',
            'quantity.max' => 'Jumlah pesanan melebihi stok yang tersedia (' . $harvest->quantity . ' kg).',
            'delivery_method.required' => 'Pilih metode pengiriman.',
        ]);

        $dist = self::calculateDistance($user->lat, $user->lng, $harvest->farmer->lat, $harvest->farmer->lng) ?? 5.0;

        $shippingCost = 0;
        if ($validated['delivery_method'] === 'antar_petani') {
            $shippingCost = $harvest->farmer->delivery_fee;
        } elseif ($validated['delivery_method'] === 'mitra_pengangkut') {
            $shippingCost = LogisticsShipment::calculateMitraFee($dist);
        }

        $totalPrice = ($validated['quantity'] * $harvest->price_per_kg) + $shippingCost;

        $order = Order::create([
            'order_number' => 'ORD-LF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
            'buyer_id' => $user->id,
            'harvest_id' => $harvest->id,
            'quantity' => $validated['quantity'],
            'unit_price' => $harvest->price_per_kg,
            'delivery_method' => $validated['delivery_method'],
            'shipping_cost' => $shippingCost,
            'total_price' => $totalPrice,
            'status' => 'dibuat',
        ]);

        // Kurangi stok hasil panen
        $newQty = $harvest->quantity - $validated['quantity'];
        $harvest->update([
            'quantity' => $newQty,
            'status' => $newQty <= 0 ? 'habis' : 'sebagian_terjual',
        ]);

        // Integrasi Otomatis Modul Logistik LembangFresh (Bab 3 & Bab 10 SDD)
        if ($validated['delivery_method'] === 'mitra_pengangkut') {
            $activeMitra = User::where('role', 'mitra')->first();
            LogisticsShipment::create([
                'tracking_code' => 'TRK-LF-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4)),
                'mitra_id' => $activeMitra ? $activeMitra->id : null,
                'sender_name' => $harvest->farmer->name . ' (' . ($harvest->farmer->address ?? 'Kec. Lembang') . ')',
                'receiver_name' => $user->name . ' (' . ($user->address ?? 'Kec. Lembang') . ')',
                'commodity_info' => $harvest->commodity . ' (' . $validated['quantity'] . ' kg)',
                'weight_kg' => $validated['quantity'],
                'distance_km' => $dist,
                'shipping_method' => 'mitra_pengangkut',
                'shipping_cost' => $shippingCost,
                'status' => 'menunggu',
                'notes' => 'Pesanan distribusi ' . $order->order_number,
            ]);
        }

        return redirect()->route('dashboard')->with('success', 'Pesanan ' . $order->order_number . ' berhasil dibuat! Menunggu konfirmasi petani.');
    }

    public function gisView()
    {
        $farmers = User::where('role', 'petani')->get();
        $buyers = User::where('role', 'pembeli')->get();
        $mitras = User::where('role', 'mitra')->get();
        $harvests = Harvest::with('farmer')->where('status', '!=', 'habis')->get();

        return view('gis', compact('farmers', 'buyers', 'mitras', 'harvests'));
    }

    public function matchingView()
    {
        $harvests = Harvest::with('farmer')->where('status', '!=', 'habis')->get();
        $demands = Demand::with('buyer')->where('status', 'terbuka')->get();

        // Perform Supply-Demand Matching Engine (Bab 3.3 & Bab 13 SDD LembangFresh)
        $matchResults = [];

        foreach ($demands as $demand) {
            $matchingHarvests = $harvests->filter(function ($h) use ($demand) {
                return strtolower($h->commodity) === strtolower($demand->commodity)
                    && $h->grade === $demand->grade
                    && $h->price_per_kg <= $demand->max_price_per_kg;
            });

            $enhancedMatches = $matchingHarvests->map(function ($h) use ($demand) {
                $dist = null;
                if ($demand->buyer && $demand->buyer->lat && $demand->buyer->lng && $h->farmer && $h->farmer->lat && $h->farmer->lng) {
                    $dist = self::calculateDistance($demand->buyer->lat, $demand->buyer->lng, $h->farmer->lat, $h->farmer->lng);
                }

                $absorbed = min($h->quantity, $demand->quantity);
                $remaining = max(0, $h->quantity - $demand->quantity);
                $isFullyMatched = $h->quantity >= $demand->quantity;

                $farmerCanDeliver = $h->farmer && $h->farmer->delivery_available && ($dist === null || $dist <= $h->farmer->delivery_radius);

                $h->gis_distance = $dist;
                $h->absorbed_qty = $absorbed;
                $h->remaining_stock = $remaining;
                $h->is_fully_matched = $isFullyMatched;
                $h->farmer_can_deliver = $farmerCanDeliver;
                return $h;
            });

            $matchResults[] = [
                'demand' => $demand,
                'matches' => $enhancedMatches,
            ];
        }

        return view('matching', compact('matchResults'));
    }

    public function storeDemand(Request $request)
    {
        $user = Auth::user();
        if (!$user->isPembeli() && !$user->isAdmin()) {
            abort(403);
        }

        $validated = $request->validate([
            'commodity' => 'required|string|max:100',
            'quantity' => 'required|integer|min:1',
            'grade' => 'required|in:A,B,C',
            'max_price_per_kg' => 'required|integer|min:500',
            'notes' => 'nullable|string|max:500',
        ]);

        $validated['buyer_id'] = $user->id;
        $validated['status'] = 'terbuka';

        Demand::create($validated);

        return back()->with('success', 'Permintaan kebutuhan komoditas berhasil ditambahkan!');
    }
}

