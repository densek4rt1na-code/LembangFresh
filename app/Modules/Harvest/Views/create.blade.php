@extends('layouts.app')

@section('title', 'Tambah Data Hasil Panen Hortikultura - LembangFresh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold text-dark m-0"><i class="fas fa-plus-circle text-success me-2"></i> Tambah Hasil Panen Baru</h3>
                    <p class="text-muted small m-0">Pendaftaran komoditas hasil panen petani di Kecamatan Lembang</p>
                </div>
                <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Kembali
                </a>
            </div>

            <!-- Form Menggunakan Enctype Multipart Untuk Upload Berkas -->
            <form action="{{ route('harvests.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="commodity" class="form-label fw-semibold">Nama Komoditas Hortikultura: <span class="text-danger">*</span></label>
                        <input type="text" name="commodity" id="commodity" 
                               class="form-control @error('commodity') is-invalid @enderror" 
                               value="{{ old('commodity') }}" required placeholder="Contoh: Wortel, Tomat, Brokoli">
                        @error('commodity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="variety" class="form-label fw-semibold">Varietas / Jenis Khusus:</label>
                        <input type="text" name="variety" id="variety" 
                               class="form-control @error('variety') is-invalid @enderror" 
                               value="{{ old('variety') }}" placeholder="Contoh: Brastagi Jumbo / Beef Organik">
                        @error('variety')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-4">
                        <label for="quantity" class="form-label fw-semibold">Jumlah Hasil Panen (kg): <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="quantity" id="quantity" 
                                   class="form-control @error('quantity') is-invalid @enderror" 
                                   value="{{ old('quantity') }}" min="1" required placeholder="500">
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
                                   value="{{ old('price_per_kg') }}" min="500" required placeholder="5000">
                        </div>
                        @error('price_per_kg')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="grade" class="form-label fw-semibold">Grade Mutu Kualitas: <span class="text-danger">*</span></label>
                        <select name="grade" id="gradeSelect" class="form-select @error('grade') is-invalid @enderror" required>
                            <option value="A" {{ old('grade') === 'A' ? 'selected' : '' }}>Grade A (Kualitas Baik/Super)</option>
                            <option value="B" {{ old('grade', 'B') === 'B' ? 'selected' : '' }}>Grade B (Layak Konsumsi/Campur)</option>
                            <option value="C" {{ old('grade') === 'C' ? 'selected' : '' }}>Grade C (Visual Rendah/Olahan)</option>
                        </select>
                        @error('grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Kotak Rekomendasi Jalur Pemasaran Dinamis Sesuai SDD Bab 4 -->
                <div class="p-3 mb-3 rounded-3 border bg-light" id="recommendationCard">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-route text-success fs-4 me-2"></i>
                        <div>
                            <span class="small text-muted fw-semibold">Rekomendasi Jalur Pemasaran Otomatis:</span>
                            <div class="fw-bold text-forest fs-6" id="recommendationTitle">Pasar Alternatif (UMKM, Rumah Makan, Katering)</div>
                            <div class="small text-muted" id="recommendationDesc">Masih sangat layak konsumsi dengan bentuk tidak seragam, diserap oleh pelaku usaha kuliner.</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="harvest_date" class="form-label fw-semibold">Tanggal Panen: <span class="text-danger">*</span></label>
                        <input type="date" name="harvest_date" id="harvest_date" 
                               class="form-control @error('harvest_date') is-invalid @enderror" 
                               value="{{ old('harvest_date', date('Y-m-d')) }}" required>
                        @error('harvest_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="ready_date" class="form-label fw-semibold">Tanggal Siap Didistribusikan: <span class="text-danger">*</span></label>
                        <input type="date" name="ready_date" id="ready_date" 
                               class="form-control @error('ready_date') is-invalid @enderror" 
                               value="{{ old('ready_date', date('Y-m-d', strtotime('+1 day'))) }}" required>
                        @error('ready_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Input Upload File Foto -->
                <div class="mb-3">
                    <label for="photo" class="form-label fw-semibold">Unggah Foto Hasil Panen (Opsional):</label>
                    <input type="file" name="photo" id="photo" 
                           class="form-control @error('photo') is-invalid @enderror" 
                           accept="image/jpeg,image/png,image/jpg,image/webp" onchange="previewImg(event)">
                    <div class="form-text small">Format file: JPG, JPEG, PNG, WEBP. Ukuran maksimal: 2048 KB (2 MB).</div>
                    @error('photo')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <!-- Pratinjau Gambar -->
                    <div class="mt-2 d-none" id="imgPreviewWrapper">
                        <div class="small fw-semibold text-muted mb-1">Pratinjau Foto:</div>
                        <img id="imgPreview" src="#" alt="Pratinjau" class="img-thumbnail rounded-3" style="max-height: 180px;">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label fw-semibold">Deskripsi / Catatan Tambahan:</label>
                    <textarea name="description" id="description" rows="3" 
                              class="form-control @error('description') is-invalid @enderror" 
                              placeholder="Kondisi panen, metode tanam organik, atau ketersediaan peti kemas...">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('harvests.index') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-forest px-4 shadow-sm fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Data Panen
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
        'A': {
            title: 'Pasar Normal (Supermarket, Hotel, Restoran)',
            desc: 'Bentuk, ukuran, dan kesegaran terbaik yang memenuhi standar pasar modern retail dan hotel berbintang.'
        },
        'B': {
            title: 'Pasar Alternatif (UMKM, Rumah Makan, Katering)',
            desc: 'Masih sangat layak konsumsi dengan bentuk tidak seragam, cepat diserap oleh pelaku usaha kuliner Lembang & Bandung.'
        },
        'C': {
            title: 'Jalur Pengolahan (Industri Olahan Makanan)',
            desc: 'Kualitas visual kurang seragam, diarahkan untuk diolah menjadi pasta saus, keripik sayur, atau sambal.'
        }
    };

    function updateRecommendation() {
        const grade = document.getElementById('gradeSelect').value;
        const data = recommendations[grade] || recommendations['B'];
        document.getElementById('recommendationTitle').textContent = data.title;
        document.getElementById('recommendationDesc').textContent = data.desc;
    }

    document.getElementById('gradeSelect').addEventListener('change', updateRecommendation);
    updateRecommendation();

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

