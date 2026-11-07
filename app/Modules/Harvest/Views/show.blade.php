@extends('layouts.app')

@section('title', 'Detail Data Hasil Panen - LembangFresh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold text-dark m-0">{{ $harvest->commodity }}</h3>
                    <div class="text-muted small">ID Panen: #{{ $harvest->id }} | Didaftarkan: {{ $harvest->created_at->format('d M Y H:i') }}</div>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Daftar
                    </a>
                    @if(auth()->user()->isAdmin() || auth()->id() === $harvest->farmer_id)
                        <a href="{{ route('harvests.edit', $harvest->id) }}" class="btn btn-warning btn-sm fw-semibold">
                            <i class="fas fa-edit me-1"></i> Edit Data
                        </a>
                    @endif
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-5">
                    <div class="card border-0 bg-light rounded-3 overflow-hidden text-center">
                        @if($harvest->photo_path && file_exists(public_path('storage/' . $harvest->photo_path)))
                            <img src="{{ asset('storage/' . $harvest->photo_path) }}" 
                                 alt="{{ $harvest->commodity }}" class="img-fluid w-100 object-fit-cover shadow-sm">
                            <div class="p-2 bg-white small text-muted border-top">
                                <i class="fas fa-file-image text-primary me-1"></i> Path: <code>storage/app/public/{{ $harvest->photo_path }}</code>
                            </div>
                        @else
                            <div class="p-5 text-muted">
                                <i class="fas fa-image fa-3x mb-2 opacity-50"></i>
                                <div>Foto belum diunggah</div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="col-md-7">
                    <table class="table table-bordered align-middle">
                        <tbody>
                            <tr>
                                <th class="bg-light" style="width: 35%;">Nama Komoditas</th>
                                <td class="fw-bold text-dark">{{ $harvest->commodity }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Varietas</th>
                                <td>{{ $harvest->variety ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Grade Mutu Kualitas</th>
                                <td>
                                    <x-status-badge :status="$harvest->grade" type="grade" />
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Jalur Pemasaran</th>
                                <td class="text-forest fw-semibold">
                                    <i class="fas fa-route me-1"></i> {{ $harvest->marketing_channel }}
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Jumlah Sisa Stok</th>
                                <td class="fs-5 fw-bold text-dark">{{ number_format($harvest->quantity) }} kg</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Harga per kg</th>
                                <td class="fs-5 fw-bold text-success">Rp {{ number_format($harvest->price_per_kg, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Status Ketersediaan</th>
                                <td>
                                    <x-status-badge :status="$harvest->status" />
                                </td>
                            </tr>
                            <tr>
                                <th class="bg-light">Tanggal Panen</th>
                                <td>{{ \Carbon\Carbon::parse($harvest->harvest_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Siap Didistribusikan</th>
                                <td>{{ \Carbon\Carbon::parse($harvest->ready_date)->format('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light">Petani Pemilik</th>
                                <td>
                                    <div class="fw-semibold">{{ $harvest->farmer->name ?? 'Petani' }}</div>
                                    <div class="small text-muted"><i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $harvest->farmer->address ?? '-' }}</div>
                                    <div class="small text-muted"><i class="fas fa-phone text-success me-1"></i> {{ $harvest->farmer->phone ?? '-' }}</div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    @if($harvest->description)
                        <div class="card p-3 bg-light border-0 rounded-3 mt-3">
                            <h6 class="fw-bold text-dark mb-1">Catatan Tambahan:</h6>
                            <p class="small text-muted mb-0">{{ $harvest->description }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

