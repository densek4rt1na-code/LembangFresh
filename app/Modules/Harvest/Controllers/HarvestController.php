<?php

namespace App\Modules\Harvest\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Harvest\Models\Harvest;
use App\Modules\Harvest\Requests\StoreHarvestRequest;
use App\Modules\Harvest\Requests\UpdateHarvestRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class HarvestController extends Controller
{
    public function index(Request $request)
    {
        $query = Harvest::with('farmer')->latest();

        // If petani, default filter to their own or allow toggle
        if (Auth::user()->isPetani() && !$request->has('all')) {
            $query->where('farmer_id', Auth::id());
        }

        if ($request->filled('search')) {
            $query->where('commodity', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $harvests = $query->paginate(8)->withQueryString();

        return view('harvest::index', compact('harvests'));
    }

    public function create()
    {
        return view('harvest::create');
    }

    public function store(StoreHarvestRequest $request)
    {
        $data = $request->validated();
        $data['farmer_id'] = Auth::id();
        $data['marketing_channel'] = Harvest::determineMarketingChannel($data['grade']);

        if ($request->hasFile('photo')) {
            $data['photo_path'] = $request->file('photo')->store('harvests', 'public');
        }

        $data['status'] = 'tersedia';

        Harvest::create($data);

        return redirect()->route('harvests.index')
            ->with('success', 'Data hasil panen ' . $data['commodity'] . ' berhasil ditambahkan!');
    }

    public function show(Harvest $harvest)
    {
        $harvest->load('farmer');
        return view('harvest::show', compact('harvest'));
    }

    public function edit(Harvest $harvest)
    {
        // Petani only can edit their own
        if (Auth::user()->isPetani() && $harvest->farmer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk mengedit data hasil panen ini.');
        }

        return view('harvest::edit', compact('harvest'));
    }

    public function update(UpdateHarvestRequest $request, Harvest $harvest)
    {
        if (Auth::user()->isPetani() && $harvest->farmer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk mengubah data ini.');
        }

        $data = $request->validated();
        $data['marketing_channel'] = Harvest::determineMarketingChannel($data['grade']);

        if ($request->hasFile('photo')) {
            // Hapus file foto lama jika ada
            if ($harvest->photo_path && Storage::disk('public')->exists($harvest->photo_path)) {
                Storage::disk('public')->delete($harvest->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('harvests', 'public');
        }

        $harvest->update($data);

        return redirect()->route('harvests.index')
            ->with('success', 'Data hasil panen ' . $harvest->commodity . ' berhasil diperbarui!');
    }

    public function destroy(Harvest $harvest)
    {
        if (Auth::user()->isPetani() && $harvest->farmer_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak untuk menghapus data ini.');
        }

        $commodityName = $harvest->commodity;

        // Hapus file foto dari disk storage
        if ($harvest->photo_path && Storage::disk('public')->exists($harvest->photo_path)) {
            Storage::disk('public')->delete($harvest->photo_path);
        }

        $harvest->delete();

        return redirect()->route('harvests.index')
            ->with('success', 'Data hasil panen ' . $commodityName . ' dan file foto terkait berhasil dihapus dari storage!');
    }
}

