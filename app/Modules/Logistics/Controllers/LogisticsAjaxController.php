<?php

namespace App\Modules\Logistics\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Logistics\Models\LogisticsShipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LogisticsAjaxController extends Controller
{
    public function index()
    {
        $mitras = User::where('role', 'mitra')->get();
        return view('logistics::index', compact('mitras'));
    }

    public function getData(Request $request)
    {
        $query = LogisticsShipment::with('mitra')->latest();

        if (Auth::user()->isMitra()) {
            $query->where(function ($q) {
                $q->where('mitra_id', Auth::id())
                  ->orWhereNull('mitra_id');
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('tracking_code', 'like', "%{$search}%")
                  ->orWhere('sender_name', 'like', "%{$search}%")
                  ->orWhere('receiver_name', 'like', "%{$search}%")
                  ->orWhere('commodity_info', 'like', "%{$search}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $shipments = $query->get()->map(function ($shipment) {
            $shipment->document_url = $shipment->document_path 
                ? asset('storage/' . $shipment->document_path) 
                : null;
            return $shipment;
        });

        return response()->json([
            'success' => true,
            'data' => $shipments,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_name' => 'required|string|max:150',
            'receiver_name' => 'required|string|max:150',
            'commodity_info' => 'required|string|max:150',
            'weight_kg' => 'required|integer|min:1',
            'distance_km' => 'required|numeric|min:0.1',
            'shipping_method' => 'required|in:ambil_sendiri,antar_petani,mitra_pengangkut',
            'status' => 'required|in:menunggu,diproses,diambil,dikirim,sampai_tujuan,selesai',
            'document_file' => 'required|file|mimes:jpeg,png,jpg,pdf,webp|max:3072',
            'notes' => 'nullable|string|max:1000',
            'mitra_id' => 'nullable|exists:users,id',
        ], [
            'sender_name.required' => 'Nama pengirim / kebun wajib diisi.',
            'receiver_name.required' => 'Nama penerima / pembeli wajib diisi.',
            'commodity_info.required' => 'Informasi komoditas wajib diisi.',
            'weight_kg.required' => 'Berat muatan wajib diisi.',
            'distance_km.required' => 'Jarak GIS wajib diisi.',
            'document_file.required' => 'Berkas surat jalan / bukti serah terima wajib diunggah.',
            'document_file.mimes' => 'Berkas harus berformat JPEG, PNG, JPG, WEBP, atau PDF.',
            'document_file.max' => 'Ukuran berkas maksimal 3 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        
        // Buat kode pelacakan otomatis
        $data['tracking_code'] = 'LF-LOG-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Hitung ongkir sesuai aturan dokumen LembangFresh
        if ($data['shipping_method'] === 'ambil_sendiri') {
            $data['shipping_cost'] = 0;
        } elseif ($data['shipping_method'] === 'antar_petani') {
            $data['shipping_cost'] = 25000;
        } else {
            $data['shipping_cost'] = LogisticsShipment::calculateMitraFee((float) $data['distance_km']);
        }

        // Upload berkas ke storage/app/public/logistics
        if ($request->hasFile('document_file')) {
            $data['document_path'] = $request->file('document_file')->store('logistics', 'public');
        }

        if (Auth::user()->isMitra() && empty($data['mitra_id'])) {
            $data['mitra_id'] = Auth::id();
        }

        $shipment = LogisticsShipment::create($data);
        $shipment->load('mitra');
        $shipment->document_url = $shipment->document_path ? asset('storage/' . $shipment->document_path) : null;

        return response()->json([
            'success' => true,
            'message' => 'Pengiriman ' . $shipment->tracking_code . ' berhasil dibuat via AJAX!',
            'data' => $shipment,
        ], 201);
    }

    public function show($id)
    {
        $shipment = LogisticsShipment::with('mitra')->findOrFail($id);
        $shipment->document_url = $shipment->document_path ? asset('storage/' . $shipment->document_path) : null;

        return response()->json([
            'success' => true,
            'data' => $shipment,
        ]);
    }

    public function update(Request $request, $id)
    {
        $shipment = LogisticsShipment::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'sender_name' => 'required|string|max:150',
            'receiver_name' => 'required|string|max:150',
            'commodity_info' => 'required|string|max:150',
            'weight_kg' => 'required|integer|min:1',
            'distance_km' => 'required|numeric|min:0.1',
            'shipping_method' => 'required|in:ambil_sendiri,antar_petani,mitra_pengangkut',
            'status' => 'required|in:menunggu,diproses,diambil,dikirim,sampai_tujuan,selesai',
            'document_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,webp|max:3072',
            'notes' => 'nullable|string|max:1000',
            'mitra_id' => 'nullable|exists:users,id',
        ], [
            'sender_name.required' => 'Nama pengirim / kebun wajib diisi.',
            'receiver_name.required' => 'Nama penerima / pembeli wajib diisi.',
            'commodity_info.required' => 'Informasi komoditas wajib diisi.',
            'document_file.mimes' => 'Berkas harus berformat JPEG, PNG, JPG, WEBP, atau PDF.',
            'document_file.max' => 'Ukuran berkas maksimal 3 MB.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Hitung ulang ongkos kirim
        if ($data['shipping_method'] === 'ambil_sendiri') {
            $data['shipping_cost'] = 0;
        } elseif ($data['shipping_method'] === 'antar_petani') {
            $data['shipping_cost'] = 25000;
        } else {
            $data['shipping_cost'] = LogisticsShipment::calculateMitraFee((float) $data['distance_km']);
        }

        // Jika ada unggah berkas baru
        if ($request->hasFile('document_file')) {
            if ($shipment->document_path && Storage::disk('public')->exists($shipment->document_path)) {
                Storage::disk('public')->delete($shipment->document_path);
            }
            $data['document_path'] = $request->file('document_file')->store('logistics', 'public');
        }

        $shipment->update($data);
        $shipment->load('mitra');
        $shipment->document_url = $shipment->document_path ? asset('storage/' . $shipment->document_path) : null;

        return response()->json([
            'success' => true,
            'message' => 'Data pengiriman ' . $shipment->tracking_code . ' berhasil diperbarui via AJAX!',
            'data' => $shipment,
        ]);
    }

    public function destroy($id)
    {
        $shipment = LogisticsShipment::findOrFail($id);
        $code = $shipment->tracking_code;

        // Hapus file dari storage
        if ($shipment->document_path && Storage::disk('public')->exists($shipment->document_path)) {
            Storage::disk('public')->delete($shipment->document_path);
        }

        $shipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Data pengiriman ' . $code . ' beserta berkas berhasil dihapus via AJAX!',
        ]);
    }

    public function downloadDocument($id)
    {
        $shipment = LogisticsShipment::findOrFail($id);

        if (!$shipment->document_path || !Storage::disk('public')->exists($shipment->document_path)) {
            return back()->with('error', 'Berkas pengiriman tidak ditemukan di storage.');
        }

        return Storage::disk('public')->download($shipment->document_path);
    }
}

