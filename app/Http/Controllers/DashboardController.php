<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Demand;
use App\Models\Order;
use App\Modules\Harvest\Models\Harvest;
use App\Modules\Logistics\Models\LogisticsShipment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // Admin Stats
        $adminStats = [
            'total_farmers' => User::where('role', 'petani')->count(),
            'total_buyers' => User::where('role', 'pembeli')->count(),
            'total_mitra' => User::where('role', 'mitra')->count(),
            'total_harvests' => Harvest::count(),
            'total_orders' => Order::count(),
            'total_shipments' => LogisticsShipment::count(),
        ];

        // All users for admin management and GIS
        $allUsers = User::where('role', '!=', 'admin')->get();

        // Farmer's data
        $myHarvests = $user->isPetani()
            ? Harvest::where('farmer_id', $user->id)->latest()->take(5)->get()
            : collect();

        // Matching demands for this farmer's commodities
        $matchingDemands = collect();
        if ($user->isPetani()) {
            $myCommodities = Harvest::where('farmer_id', $user->id)->pluck('commodity')->unique();
            $matchingDemands = Demand::with('buyer')
                ->whereIn('commodity', $myCommodities)
                ->where('status', 'terbuka')
                ->get();
        }

        // Buyer's demands & orders
        $myDemands = $user->isPembeli()
            ? Demand::where('buyer_id', $user->id)->latest()->get()
            : collect();

        $myOrders = $user->isPembeli()
            ? Order::with(['harvest.farmer', 'mitra'])->where('buyer_id', $user->id)->latest()->get()
            : collect();

        // Farmer's received orders
        $receivedOrders = $user->isPetani()
            ? Order::with(['buyer', 'harvest'])->whereHas('harvest', function ($q) use ($user) {
                $q->where('farmer_id', $user->id);
            })->latest()->get()
            : collect();

        // Admin all orders
        $allOrders = $user->isAdmin()
            ? Order::with(['buyer', 'harvest.farmer', 'mitra'])->latest()->take(10)->get()
            : collect();

        // Mitra's shipments
        $myShipments = $user->isMitra()
            ? LogisticsShipment::where('mitra_id', $user->id)->orWhereNull('mitra_id')->latest()->take(5)->get()
            : collect();

        return view('dashboard', compact(
            'adminStats',
            'allUsers',
            'myHarvests',
            'matchingDemands',
            'myDemands',
            'myOrders',
            'receivedOrders',
            'allOrders',
            'myShipments'
        ));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = Order::with('harvest')->findOrFail($id);
        $user = Auth::user();
        $toStatus = $request->input('status');

        $allowed = false;

        if ($user->isAdmin()) {
            $allowed = true;
        } elseif ($user->isPetani() && $order->harvest->farmer_id === $user->id) {
            if ($toStatus === 'diproses' && $order->status === 'dibuat') $allowed = true;
            if ($toStatus === 'ditolak' && $order->status === 'dibuat') {
                $allowed = true;
                // Kembalikan stok panen
                $order->harvest->increment('quantity', $order->quantity);
            }
            if ($toStatus === 'dikirim' && $order->status === 'diproses') $allowed = true;
        } elseif ($user->isMitra()) {
            if ($toStatus === 'diambil' && $order->delivery_method === 'mitra_pengangkut' && $order->status === 'diproses') {
                $allowed = true;
                $order->mitra_id = $user->id;
            }
            if ($toStatus === 'dikirim' && $order->status === 'diambil' && $order->mitra_id === $user->id) {
                $allowed = true;
            }
        } elseif ($user->isPembeli() && $order->buyer_id === $user->id) {
            if ($toStatus === 'selesai' && $order->status === 'dikirim') $allowed = true;
        }

        if (!$allowed) {
            return back()->with('error', 'Anda tidak memiliki hak untuk mengubah status pesanan ke tahap tersebut.');
        }

        $order->status = $toStatus;
        $order->save();

        return back()->with('success', 'Status pesanan ' . $order->order_number . ' berhasil diubah menjadi: ' . ucfirst(str_replace('_', ' ', $toStatus)) . '.');
    }

    public function updateFarmerDelivery(Request $request)
    {
        $user = Auth::user();
        if (!$user->isPetani()) {
            abort(403);
        }

        $validated = $request->validate([
            'delivery_available' => 'nullable|boolean',
            'delivery_radius' => 'required|numeric|min:0.5|max:50',
            'delivery_fee' => 'required|integer|min:0',
        ]);

        $user->update([
            'delivery_available' => $request->has('delivery_available'),
            'delivery_radius' => $validated['delivery_radius'],
            'delivery_fee' => $validated['delivery_fee'],
        ]);

        return back()->with('success', 'Pengaturan pengantaran petani berhasil disimpan!');
    }

    public function toggleMitraAvailability()
    {
        $user = Auth::user();
        if (!$user->isMitra()) {
            abort(403);
        }

        $user->update([
            'is_active' => !$user->is_active,
        ]);

        $statusStr = $user->is_active ? 'TERSEDIA' : 'TIDAK TERSEDIA';
        return back()->with('success', "Status ketersediaan armada mitra diperbarui menjadi {$statusStr}.");
    }

    public function toggleVerification($id)
    {
        if (!Auth::user()->isAdmin()) {
            abort(403);
        }

        $targetUser = User::findOrFail($id);
        $targetUser->update([
            'is_verified' => !$targetUser->is_verified,
        ]);

        $status = $targetUser->is_verified ? 'diverifikasi' : 'dicabut status verifikasinya';
        return back()->with('success', "Pengguna {$targetUser->name} berhasil {$status}.");
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'lat' => 'nullable|numeric',
            'lng' => 'nullable|numeric',
            'vehicle_desc' => 'nullable|string|max:100',
            'vehicle_capacity' => 'nullable|integer|min:0',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profil dan koordinat GIS Anda berhasil diperbarui!');
    }
}

