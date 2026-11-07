@extends('layouts.app')

@section('title', 'Profil Pengguna & Titik Koordinat GIS - LembangFresh')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 p-md-5">
            <div class="d-flex align-items-center justify-content-between mb-4 border-bottom pb-3">
                <div>
                    <h3 class="fw-bold text-dark m-0"><i class="fas fa-user-cog text-forest me-2"></i> Pengaturan Profil & Koordinat GIS</h3>
                    <p class="text-muted small m-0">Perbarui data kontak dan titik lokasi kebun/tempat usaha Anda di Kecamatan Lembang</p>
                </div>
                <x-status-badge :status="auth()->user()->role" type="role" />
            </div>

            <form action="{{ route('profile.update') }}" method="POST">
                @csrf

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label fw-semibold">Nama Lengkap / Nama Usaha: <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="email" class="form-label fw-semibold">Alamat Email (Tetap):</label>
                        <input type="email" id="email" class="form-control bg-light" value="{{ $user->email }}" disabled>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label for="phone" class="form-label fw-semibold">Nomor WhatsApp / Telepon: <span class="text-danger">*</span></label>
                        <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror"
                               value="{{ old('phone', $user->phone) }}" required>
                        @error('phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="address" class="form-label fw-semibold">Alamat di Kecamatan Lembang: <span class="text-danger">*</span></label>
                        <input type="text" name="address" id="address" class="form-control @error('address') is-invalid @enderror"
                               value="{{ old('address', $user->address) }}" required>
                        @error('address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                @if($user->isMitra())
                    <div class="row g-3 mb-3 p-3 bg-light rounded-3 border">
                        <div class="col-md-8">
                            <label for="vehicle_desc" class="form-label fw-semibold">Deskripsi Kendaraan Armada:</label>
                            <input type="text" name="vehicle_desc" id="vehicle_desc" class="form-control"
                                   value="{{ old('vehicle_desc', $user->vehicle_desc) }}" placeholder="Contoh: Pickup Suzuki Carry (D 1234 XY)">
                        </div>
                        <div class="col-md-4">
                            <label for="vehicle_capacity" class="form-label fw-semibold">Kapasitas Muatan (kg):</label>
                            <input type="number" name="vehicle_capacity" id="vehicle_capacity" class="form-control"
                                   value="{{ old('vehicle_capacity', $user->vehicle_capacity) }}" min="0">
                        </div>
                    </div>
                @endif

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label fw-semibold m-0">
                            <i class="fas fa-crosshairs text-danger me-1"></i> Titik Lokasi GIS di Peta (Klik pada Peta untuk Memilih):
                        </label>
                        <span class="small text-muted">Klik pada area kebun/lokasi Anda di Lembang</span>
                    </div>

                    <div id="pickerMap" style="height: 280px; width: 100%; border-radius: 10px;" class="mb-2"></div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Latitude</span>
                                <input type="text" name="lat" id="latInput" class="form-control bg-light"
                                       value="{{ old('lat', $user->lat ?? -6.8120) }}" readonly>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light">Longitude</span>
                                <input type="text" name="lng" id="lngInput" class="form-control bg-light"
                                       value="{{ old('lng', $user->lng ?? 107.6180) }}" readonly>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary px-4">Batal</a>
                    <button type="submit" class="btn btn-forest px-4 shadow-sm fw-semibold">
                        <i class="fas fa-save me-1"></i> Simpan Perubahan Profil
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const defaultLat = {{ $user->lat ?? -6.8120 }};
        const defaultLng = {{ $user->lng ?? 107.6180 }};

        const pickerMap = L.map('pickerMap').setView([defaultLat, defaultLng], 13);

        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '&copy; OpenStreetMap kontributor'
        }).addTo(pickerMap);

        let marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(pickerMap);

        function updateInputs(lat, lng) {
            document.getElementById('latInput').value = lat.toFixed(6);
            document.getElementById('lngInput').value = lng.toFixed(6);
        }

        marker.on('dragend', function(e) {
            const pos = marker.getLatLng();
            updateInputs(pos.lat, pos.lng);
        });

        pickerMap.on('click', function(e) {
            marker.setLatLng(e.latlng);
            updateInputs(e.latlng.lat, e.latlng.lng);
        });
    });
</script>
@endpush

