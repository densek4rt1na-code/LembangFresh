@extends('layouts.app')

@section('title', 'Kelola Hasil Panen Hortikultura - LembangFresh')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fas fa-seedling text-success me-2"></i> Pengelolaan Hasil Panen Hortikultura</h3>
        <p class="text-muted small m-0">Daftar pasokan hortikultura segar dari petani Lembang beserta tingkatan grade mutu dan jalur pemasaran.</p>
    </div>
    <div>
        <a href="{{ route('harvests.create') }}" class="btn btn-forest shadow-sm fw-semibold">
            <i class="fas fa-plus-circle me-1"></i> Tambah Hasil Panen Baru
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <form action="{{ route('harvests.index') }}" method="GET" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                <input type="text" name="search" class="form-control bg-light"
                       placeholder="Cari nama komoditas..." value="{{ request('search') }}">
            </div>
        </div>

        <div class="col-md-3">
            <select name="grade" class="form-select bg-light">
                <option value="">Semua Grade (A, B, C)</option>
                <option value="A" {{ request('grade') === 'A' ? 'selected' : '' }}>Grade A (Pasar Normal)</option>
                <option value="B" {{ request('grade') === 'B' ? 'selected' : '' }}>Grade B (Pasar Alternatif)</option>
                <option value="C" {{ request('grade') === 'C' ? 'selected' : '' }}>Grade C (Jalur Pengolahan)</option>
            </select>
        </div>

        <div class="col-md-3">
            <select name="status" class="form-select bg-light">
                <option value="">Semua Status Stok</option>
                <option value="tersedia" {{ request('status') === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                <option value="sebagian_terjual" {{ request('status') === 'sebagian_terjual' ? 'selected' : '' }}>Sebagian Terjual</option>
                <option value="habis" {{ request('status') === 'habis' ? 'selected' : '' }}>Habis</option>
            </select>
        </div>

        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-outline-success fw-semibold">
                <i class="fas fa-filter me-1"></i> Filter
            </button>
        </div>
    </form>
</div>

<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-3" style="width: 70px;">Foto</th>
                    <th scope="col">Komoditas & Varietas</th>
                    <th scope="col">Petani Pemilik</th>
                    <th scope="col">Tingkat Mutu & Jalur</th>
                    <th scope="col">Stok Tersedia</th>
                    <th scope="col">Harga / kg</th>
                    <th scope="col">Status</th>
                    <th scope="col" class="text-end pe-3" style="min-width: 170px;">Aksi</th>
                </tr>
            </thead>
            <tbody>

                @forelse($harvests as $item)
                    <tr>
                        <td class="ps-3">
                            @if($item->photo_path && file_exists(public_path('storage/' . $item->photo_path)))
                                <img src="{{ asset('storage/' . $item->photo_path) }}"
                                     alt="{{ $item->commodity }}"
                                     class="rounded-3 object-fit-cover shadow-sm border"
                                     style="width: 55px; height: 55px;">
                            @else
                                <div class="bg-light rounded-3 d-flex align-items-center justify-content-center text-muted border"
                                     style="width: 55px; height: 55px;">
                                    <i class="fas fa-image"></i>
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $item->commodity }}</div>
                            <div class="small text-muted">{{ $item->variety ?? '-' }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $item->farmer->name ?? 'Petani' }}</div>
                            <div class="text-muted" style="font-size: 0.75rem;">
                                <i class="fas fa-map-marker-alt text-danger me-1"></i> {{ $item->farmer->address ?? 'Lembang' }}
                            </div>
                        </td>
                        <td>
                            <x-status-badge :status="$item->grade" type="grade" class="mb-1 d-inline-block" /><br>
                            <span class="text-muted small" style="font-size: 0.8rem;">
                                <i class="fas fa-route text-success me-1"></i> {{ $item->marketing_channel }}
                            </span>
                        </td>
                        <td>
                            <span class="fw-bold">{{ number_format($item->quantity) }}</span> <span class="text-muted small">kg</span>
                        </td>
                        <td>
                            <span class="fw-bold text-success">Rp {{ number_format($item->price_per_kg, 0, ',', '.') }}</span>
                        </td>
                        <td>
                            <x-status-badge :status="$item->status" />
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group" role="group">
                                <a href="{{ route('harvests.show', $item->id) }}" class="btn btn-sm btn-outline-info" title="Lihat Detail">
                                    <i class="fas fa-eye"></i>
                                </a>

                                @if(auth()->user()->isAdmin() || auth()->id() === $item->farmer_id)
                                    <a href="{{ route('harvests.edit', $item->id) }}" class="btn btn-sm btn-outline-warning" title="Ubah Data">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    <form action="{{ route('harvests.destroy', $item->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus data hasil panen {{ $item->commodity }}? Berkas foto pada storage juga akan dihapus.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Hapus Data">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="fas fa-seedling fa-3x mb-3 text-secondary opacity-50"></i>
                            <div class="fw-semibold">Belum ada data hasil panen yang terdaftar</div>
                            <small>Klik tombol "Tambah Hasil Panen Baru" untuk menambahkan komoditas.</small>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($harvests->hasPages())
        <div class="card-footer bg-white border-0 py-3">
            {{ $harvests->links() }}
        </div>
    @endif
</div>
@endsection

