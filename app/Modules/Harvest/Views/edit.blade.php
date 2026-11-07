@extends('layouts.app')

@section('title', 'Ubah Data Hasil Panen - LembangFresh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold text-dark m-0"><i class="fas fa-edit text-warning me-2"></i> Ubah Data Hasil Panen</h3>
                    <p class="text-muted small m-0">Memperbarui informasi komoditas, stok, atau mengganti berkas foto</p>
                </div>
                <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Form Edit dengan Multipart Form Data -->
            <form action="{{ route('harvests.update', $harvest->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="commodity" class="form-label fw-semibold">Nama Komoditas Hortikultura: <span class="text-danger">*</span></label>
                        <input type="text" name="commodity" id="commodity" 
                               class="form-control @error('commodity') is-invalid @enderror" 
                               value="{{ old('commodity', $harvest->commodity) }}" required>
                        @error('commodity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="variety" class="form-label fw-semibold">Varietas / Jenis Khusus:</label>
                        <input type="text" name="variety" id="variety" 
                               class="form-control @error('variety') is-invalid @enderror" 
                               value="{{ old('variety', $harvest->variety) }}">
                        @error('variety')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-semibold">Jumlah Sisa Panen (kg): <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" id="quantity" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   value="{{ old('quantity', $harvest->quantity) }}" min="1" required>
                            <span class="input-group-text">kg</span>
                        </div>
                        @error('quantity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="price_per_kg" class="form-label fw-semibold">Harga Jual per kg (Rp): <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" name="price_per_kg" id="price_per_kg" 
                                   class="form-control @error('price_per_kg') is-invalid @enderror" 
                                   value="{{ old('price_per_kg', $harvest->price_per_kg) }}" min="500" required>
                        </div>
                        @error('price_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="grade" class="form-label fw-semibold">Grade Mutu Kualitas: <span class="text-danger">*</span></label>
                        <select name="grade" id="gradeSelect" class="form-select @error('grade') is-invalid @enderror" required>
                            <option value="A" {{ old('grade', $harvest->grade) === 'A' ? 'selected' : '' }}>Grade A (Pasar Normal)</option>
                            <option value="B" {{ old('grade', $harvest->grade) === 'B' ? 'selected' : '' }}>Grade B (Pasar Alternatif)</option>
                            <option value="C" {{ old('grade', $harvest->grade) === 'C' ? 'selected' : '' }}>Grade C (Pengolahan)</option>
                        </select>
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Jalur Pemasaran Dinamis -->
                <div class="p-3 mb-3 rounded-3 border bg-light">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-route text-success fs-4 me-2"></i>
                        <div>
                            <span class="small text-muted fw-semibold">Rekomendasi Jalur Pemasaran:</span>
                            <div class="fw-bold text-forest fs-6" id="recommendationTitle">{{ $harvest->marketing_channel }}</div>
                            <div class="small text-muted" id="recommendationDesc">Disesuaikan otomatis dengan grade mutu.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="harvest_date" class="form-label fw-semibold">Tanggal Panen: <span class="text-danger">*</span></label>
                        <input type="date" name="harvest_date" id="harvest_date" 
                               class="form-control @error('harvest_date') is-invalid @enderror" 
                               value="{{ old('harvest_date', $harvest->harvest_date->format('Y-m-d')) }}" required>
                        @error('harvest_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="ready_date" class="form-label fw-semibold">Tanggal Siap Dikirim: <span class="text-danger">*</span></label>
                        <input type="date" name="ready_date" id="ready_date" 
                               class="form-control @error('ready_date') is-invalid @enderror" 
                               value="{{ old('ready_date', $harvest->ready_date->format('Y-m-d')) }}" required>
                        @error('ready_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="status" class="form-label fw-semibold">Status Ketersediaan: <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-select @error('status') is-invalid @enderror" required>
                            <option value="tersedia" {{ old('status', $harvest->status) === 'tersedia' ? 'selected' : '' }}>Tersedia</option>
                            <option value="sebagian_terjual" {{ old('status', $harvest->status) === 'sebagian_terjual' ? 'selected' : '' }}>Sebagian Terjual</option>
                            <option value="habis" {{ old('status', $harvest->status) === 'habis' ? 'selected' : '' }}>Habis</option>
                        </select>
                        @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Input Upload File Foto Pengganti -->
                <div class="mb-3">
                    <label for="photo" class="form-label fw-semibold">Ganti Foto Komoditas (Opsional):</label>
                    <input type="file" name="photo" id="photo" 
                           class="form-control @error('photo') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImg(event)">
                    <div class="form-text small">Kosongkan jika tidak ingin mengubah foto. Format: JPG, PNG, WEBP (Maks: 2 MB).</div>
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- Pratinjau Foto Saat Ini & Foto Baru -->
                    <div class="row g-2 mt-2">
                        @if($harvest->photo_path && file_exists(public_path('storage/' . $harvest->photo_path)))
                            <div class="col-auto">
                                <div class="small fw-semibold text-muted mb-1">Foto Saat Ini di Storage:</div>
                                <img src="{{ asset('storage/' . $harvest->photo_path) }}" alt="{{ $harvest->commodity }}" 
                                     class="img-thumbnail rounded-3" style="max-height: 120px;">
                            </div>
                        @endif
                        <div class="col-auto d-none" id="imgPreviewWrapper">
                            <div class="small fw-semibold text-primary mb-1">Pratinjau Foto Baru:</div>
                            <img id="imgPreview" src="#" alt="Pratinjau Baru" class="img-thumbnail rounded-3 border-primary" style="max-height: 120px;">
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Deskripsi / Catatan Tambahan:</label>
                    <textarea name="description" id="description" rows="3" 
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $harvest->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-warning px-4 shadow-sm fw-semibold">
                        <i class="fas fa-check-circle me-1"></i> Perbarui Data Panen
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const recommendations = {
        'A': 'Pasar Normal (Supermarket, Hotel, Restoran)',
        'B': 'Pasar Alternatif (UMKM, Rumah Makan, Katering)',
        'C': 'Jalur Pengolahan (Industri Olahan Makanan)'
    };

    document.getElementById('gradeSelect').addEventListener('change', function() {
        document.getElementById('recommendationTitle').textContent = recommendations[this.value] || recommendations['B'];
    });

    function previewImg(event) {
        const input = event.target;
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('imgPreview').src = e.target.result;
                document.getElementById('imgPreviewWrapper').classList.remove('d-none');
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush

