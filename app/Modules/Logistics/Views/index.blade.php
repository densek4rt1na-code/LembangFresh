@extends('layouts.app')

@section('title', 'Logistik & Pengiriman Armada Mitra - LembangFresh')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark m-0"><i class="fas fa-truck-moving text-primary me-2"></i> Logistik & Pengiriman Armada Mitra</h3>
        <p class="text-muted small m-0">Koordinasi distribusi hasil hortikultura, pelacakan rute, dan arsip berkas surat jalan pengiriman.</p>
    </div>
    <div>
        <button type="button" class="btn btn-forest shadow-sm fw-semibold" onclick="openCreateModal()">
            <i class="fas fa-plus-circle me-1"></i> Tambah Pengiriman Baru
        </button>
    </div>
</div>

<!-- Filter Pencarian AJAX & Status Real-time -->
<div class="card border-0 shadow-sm rounded-3 p-3 mb-4 bg-white">
    <div class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group">
                <span class="input-group-text bg-light"><i class="fas fa-search text-muted"></i></span>
                <input type="text" id="ajaxSearch" class="form-control bg-light" 
                       placeholder="Cari kode tracking, pengirim, penerima, atau komoditas..." onkeyup="loadData()">
            </div>
        </div>

        <div class="col-md-4">
            <select id="ajaxStatus" class="form-select bg-light" onchange="loadData()">
                <option value="">Semua Status Pengiriman</option>
                <option value="menunggu">Menunggu Armada</option>
                <option value="diproses">Diproses Petani</option>
                <option value="diambil">Muatan Diambil</option>
                <option value="dikirim">Dalam Pengiriman</option>
                <option value="sampai_tujuan">Sampai di Tujuan</option>
                <option value="selesai">Selesai Diterima</option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="button" class="btn btn-outline-primary w-100 fw-semibold" onclick="loadData()">
                <i class="fas fa-sync-alt me-1" id="refreshIcon"></i> Segarkan Data
            </button>
        </div>
    </div>
</div>

<!-- Alert Notifikasi Interaktif AJAX -->
<div id="ajaxAlertContainer"></div>

<!-- Tabel Data Distribusi Pengiriman -->
<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" id="shipmentsTable">
            <thead class="table-light">
                <tr>
                    <th scope="col" class="ps-3">Kode Tracking</th>
                    <th scope="col">Rute Distribusi (Pengirim &rarr; Penerima)</th>
                    <th scope="col">Komoditas & Muatan</th>
                    <th scope="col">Jarak GIS & Metode</th>
                    <th scope="col">Ongkos Kirim</th>
                    <th scope="col">Status</th>
                    <th scope="col">Berkas Pengiriman</th>
                    <th scope="col" class="text-end pe-3" style="min-width: 170px;">Aksi</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Diisi secara dinamis -->
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <div class="spinner-border text-forest" role="status">
                            <span class="visually-hidden">Memuat...</span>
                        </div>
                        <div class="text-muted mt-2 small">Memuat data pengiriman...</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 1: Tambah Pengiriman Baru            -->
<!-- ========================================== -->
<div class="modal fade" id="modalCreate" tabindex="-1" aria-labelledby="modalCreateLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-forest text-white">
                <h5 class="modal-title fw-bold" id="modalCreateLabel">
                    <i class="fas fa-plus-circle me-1"></i> Tambah Penugasan Pengiriman
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formCreate" enctype="multipart/form-data" onsubmit="submitCreate(event)">
                <div class="modal-body p-4">
                    <div id="createErrors" class="alert alert-danger d-none"></div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengirim / Kebun Asal: <span class="text-danger">*</span></label>
                            <input type="text" name="sender_name" class="form-control" required placeholder="Contoh: Kebun Pak Agus (Cikidang)">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penerima / Lokasi Tujuan: <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" class="form-control" required placeholder="Contoh: UMKM Salad Fresh (Lembang)">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Komoditas & Detail Muatan: <span class="text-danger">*</span></label>
                            <input type="text" name="commodity_info" class="form-control" required placeholder="Contoh: Wortel Grade B (300 kg)">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat Total (kg): <span class="text-danger">*</span></label>
                            <input type="number" name="weight_kg" class="form-control" required min="1" value="100">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jarak Tempuh GIS (km): <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="distance_km" id="createDist" class="form-control" required min="0.1" value="3.5" oninput="updateCreateCost()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Pilihan Metode Distribusi: <span class="text-danger">*</span></label>
                            <select name="shipping_method" id="createMethod" class="form-select" required onchange="updateCreateCost()">
                                <option value="mitra_pengangkut">Diantar Mitra Pengangkut</option>
                                <option value="antar_petani">Diantar Langsung Petani</option>
                                <option value="ambil_sendiri">Ambil Sendiri ke Kebun</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Pengiriman: <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="menunggu">Menunggu Armada</option>
                                <option value="diproses">Diproses Petani</option>
                                <option value="diambil">Muatan Diambil</option>
                                <option value="dikirim">Dalam Pengiriman</option>
                                <option value="sampai_tujuan">Sampai di Tujuan</option>
                                <option value="selesai">Selesai Diterima</option>
                            </select>
                        </div>
                    </div>

                    <div class="p-3 mb-3 bg-light rounded-3 border">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small fw-semibold text-muted">Estimasi Biaya Ongkir (Formula LembangFresh):</span>
                            <span class="fs-5 fw-bold text-forest" id="createCostPreview">Rp 70.000</span>
                        </div>
                        <div class="small text-muted" id="createCostNote">Tarif Dasar Mitra Rp 50.000 + (4 km x Rp 5.000)</div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mitra Pengangkut:</label>
                            <select name="mitra_id" class="form-select">
                                <option value="">Pilih Mitra (Opsional)</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}">{{ $mitra->name }} ({{ $mitra->vehicle_desc ?? 'Pickup Lembang' }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <!-- Input Upload File Berkas -->
                            <label class="form-label fw-semibold">Unggah Berkas Surat Jalan / Bukti: <span class="text-danger">*</span></label>
                            <input type="file" name="document_file" class="form-control" required accept="image/*,application/pdf">
                            <div class="form-text small">Dapat berupa foto bukti pickup atau PDF surat jalan (Maks 3 MB).</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Catatan Pengantaran:</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Instruksi jalan, nomor kontak penerima di lokasi..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-forest fw-semibold" id="btnSubmitCreate">
                        <i class="fas fa-save me-1"></i> Simpan Pengiriman
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 2: Ubah Data Pengiriman (AJAX UPDATE)-->
<!-- ========================================== -->
<div class="modal fade" id="modalEdit" tabindex="-1" aria-labelledby="modalEditLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title fw-bold" id="modalEditLabel">
                    <i class="fas fa-edit me-1"></i> Ubah Data Pengiriman (<span id="editTrackingCodeHeader"></span>)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEdit" enctype="multipart/form-data" onsubmit="submitEdit(event)">
                <input type="hidden" id="editId">
                <div class="modal-body p-4">
                    <div id="editErrors" class="alert alert-danger d-none"></div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Pengirim / Kebun Asal: <span class="text-danger">*</span></label>
                            <input type="text" name="sender_name" id="editSender" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penerima / Lokasi Tujuan: <span class="text-danger">*</span></label>
                            <input type="text" name="receiver_name" id="editReceiver" class="form-control" required>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Komoditas & Muatan: <span class="text-danger">*</span></label>
                            <input type="text" name="commodity_info" id="editCommodity" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Berat (kg): <span class="text-danger">*</span></label>
                            <input type="number" name="weight_kg" id="editWeight" class="form-control" required min="1">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Jarak GIS (km): <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="distance_km" id="editDist" class="form-control" required min="0.1" oninput="updateEditCost()">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Metode Distribusi: <span class="text-danger">*</span></label>
                            <select name="shipping_method" id="editMethod" class="form-select" required onchange="updateEditCost()">
                                <option value="mitra_pengangkut">Diantar Mitra Pengangkut</option>
                                <option value="antar_petani">Diantar Langsung Petani</option>
                                <option value="ambil_sendiri">Ambil Sendiri ke Kebun</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Status Pengiriman: <span class="text-danger">*</span></label>
                            <select name="status" id="editStatus" class="form-select" required>
                                <option value="menunggu">Menunggu Armada</option>
                                <option value="diproses">Diproses Petani</option>
                                <option value="diambil">Muatan Diambil</option>
                                <option value="dikirim">Dalam Pengiriman</option>
                                <option value="sampai_tujuan">Sampai di Tujuan</option>
                                <option value="selesai">Selesai Diterima</option>
                            </select>
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Mitra Pengangkut:</label>
                            <select name="mitra_id" id="editMitra" class="form-select">
                                <option value="">Pilih Mitra (Opsional)</option>
                                @foreach($mitras as $mitra)
                                    <option value="{{ $mitra->id }}">{{ $mitra->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <!-- Input Ganti File AJAX -->
                            <label class="form-label fw-semibold">Ganti Berkas Dokumen (Opsional):</label>
                            <input type="file" name="document_file" class="form-control" accept="image/*,application/pdf">
                            <div class="form-text small" id="editFileIndicator">Kosongkan jika tidak ingin mengganti berkas saat ini.</div>
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Catatan Pengantaran:</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-semibold" id="btnSubmitEdit">
                        <i class="fas fa-check-circle me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ========================================== -->
<!-- MODAL 3: Buka Berkas & Pratinjau Dokumen   -->
<!-- ========================================== -->
<div class="modal fade" id="modalViewDoc" tabindex="-1" aria-labelledby="modalViewDocLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title fw-bold" id="modalViewDocLabel">
                    <i class="fas fa-file-invoice me-2 text-warning"></i> Berkas Pengiriman (<span id="viewDocTracking"></span>)
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4 text-center">
                <div class="p-3 bg-light rounded-3 mb-3 text-start small">
                    <div class="row">
                        <div class="col-md-6">
                            <div><strong>Pengirim:</strong> <span id="viewDocSender"></span></div>
                            <div><strong>Penerima:</strong> <span id="viewDocReceiver"></span></div>
                        </div>
                        <div class="col-md-6">
                            <div><strong>Komoditas:</strong> <span id="viewDocCommodity"></span></div>
                            <div><strong>Status:</strong> <span id="viewDocStatus"></span></div>
                        </div>
                    </div>
                </div>

                <div id="docPreviewContainer" class="border rounded-3 p-2 bg-light mb-3" style="min-height: 250px;">
                    <!-- Gambar / Iframe PDF dimuat secara dinamis -->
                </div>

                <div class="d-flex justify-content-center gap-2">
                    <a href="#" id="btnOpenInNewTab" target="_blank" class="btn btn-primary fw-semibold">
                        <i class="fas fa-external-link-alt me-1"></i> Buka Berkas di Tab Baru
                    </a>
                    <a href="#" id="btnDownloadDoc" class="btn btn-outline-success fw-semibold">
                        <i class="fas fa-download me-1"></i> Unduh Berkas
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const apiDataUrl = "{{ route('logistics.api.data') }}";
    const apiStoreUrl = "{{ route('logistics.api.store') }}";

    function showAjaxAlert(message, type = 'success') {
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        const html = `
            <div class="alert alert-${type} alert-dismissible fade show d-flex align-items-center shadow-sm" role="alert">
                <i class="fas ${icon} fs-5 me-2"></i>
                <div>${message}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        document.getElementById('ajaxAlertContainer').innerHTML = html;
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function calculateMitraCost(km) {
        return 50000 + (Math.ceil(km) * 5000);
    }

    function formatRp(num) {
        return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }

    function updateCreateCost() {
        const dist = parseFloat(document.getElementById('createDist').value) || 0;
        const method = document.getElementById('createMethod').value;
        let cost = 0;
        let note = '';

        if (method === 'ambil_sendiri') {
            cost = 0;
            note = 'Biaya pengiriman Rp 0 (Pembeli mengambil sendiri).';
        } else if (method === 'antar_petani') {
            cost = 25000;
            note = 'Tarif standar pengantaran petani Lembang.';
        } else {
            cost = calculateMitraCost(dist);
            note = `Tarif Dasar Mitra Rp 50.000 + (${Math.ceil(dist)} km x Rp 5.000).`;
        }

        document.getElementById('createCostPreview').textContent = formatRp(cost);
        document.getElementById('createCostNote').textContent = note;
    }

    function getStatusBadge(status) {
        const badges = {
            'menunggu': '<span class="badge bg-warning text-dark px-2 py-1 rounded-pill">Menunggu</span>',
            'diproses': '<span class="badge bg-info text-white px-2 py-1 rounded-pill">Diproses</span>',
            'diambil': '<span class="badge bg-primary text-white px-2 py-1 rounded-pill">Diambil</span>',
            'dikirim': '<span class="badge bg-primary text-white px-2 py-1 rounded-pill">Dikirim</span>',
            'sampai_tujuan': '<span class="badge bg-success-subtle text-success border px-2 py-1 rounded-pill">Sampai Tujuan</span>',
            'selesai': '<span class="badge bg-success text-white px-2 py-1 rounded-pill">Selesai</span>'
        };
        return badges[status] || status;
    }

    // ==========================================
    // 1. AJAX READ: Memuat Data dari API
    // ==========================================
    function loadData() {
        const search = document.getElementById('ajaxSearch').value;
        const status = document.getElementById('ajaxStatus').value;
        const icon = document.getElementById('refreshIcon');
        if (icon) icon.classList.add('fa-spin');

        $.ajax({
            url: apiDataUrl,
            type: 'GET',
            data: { search: search, status: status },
            dataType: 'json',
            success: function(response) {
                if (icon) icon.classList.remove('fa-spin');
                if (response.success) {
                    renderTable(response.data);
                }
            },
            error: function(err) {
                if (icon) icon.classList.remove('fa-spin');
                console.error("Gagal memuat data AJAX:", err);
            }
        });
    }

    function renderTable(items) {
        const tbody = document.getElementById('tableBody');
        tbody.innerHTML = '';

        if (!items || items.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center py-5 text-muted">
                        <i class="fas fa-box-open fa-3x mb-3 text-secondary opacity-50"></i>
                        <div class="fw-semibold">Tidak ada data pengiriman logistik</div>
                        <small>Silakan sesuaikan filter pencarian atau buat penugasan pengiriman baru.</small>
                    </td>
                </tr>
            `;
            return;
        }

        items.forEach(item => {
            const row = document.createElement('tr');
            row.id = `row-shipment-${item.id}`;

            const docButton = item.document_url ? `
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openDocModal(${item.id})">
                    <i class="fas fa-file-invoice me-1"></i> Buka Berkas
                </button>
            ` : '<span class="text-muted small">Tidak ada</span>';

            const methodLabel = item.shipping_method === 'mitra_pengangkut' 
                ? '<span class="badge bg-primary text-white">Mitra Pickup</span>' 
                : (item.shipping_method === 'antar_petani' 
                    ? '<span class="badge bg-success text-white">Antar Petani</span>' 
                    : '<span class="badge bg-secondary text-white">Ambil Sendiri</span>');

            row.innerHTML = `
                <td class="ps-3 fw-bold text-dark">${item.tracking_code}</td>
                <td>
                    <div class="small fw-bold text-dark"><i class="fas fa-map-marker-alt text-success me-1"></i> ${item.sender_name}</div>
                    <div class="small text-muted"><i class="fas fa-arrow-down text-danger ms-2 me-1"></i> ${item.receiver_name}</div>
                </td>
                <td>
                    <div class="fw-semibold text-dark">${item.commodity_info}</div>
                    <div class="small text-muted">Muatan: ${item.weight_kg} kg</div>
                </td>
                <td>
                    <div class="small fw-bold">${item.distance_km} km</div>
                    <div>${methodLabel}</div>
                </td>
                <td>
                    <span class="fw-bold text-success">${formatRp(item.shipping_cost)}</span>
                </td>
                <td>
                    ${getStatusBadge(item.status)}
                </td>
                <td>
                    ${docButton}
                </td>
                <td class="text-end pe-3">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="openEditModal(${item.id})" title="Ubah Data">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteShipment(${item.id}, '${item.tracking_code}')" title="Hapus Data">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // ==========================================
    // 2. AJAX CREATE: Upload File & Simpan Baru
    // ==========================================
    function openCreateModal() {
        document.getElementById('formCreate').reset();
        document.getElementById('createErrors').classList.add('d-none');
        updateCreateCost();
        const modal = new bootstrap.Modal(document.getElementById('modalCreate'));
        modal.show();
    }

    function submitCreate(e) {
        e.preventDefault();
        const form = document.getElementById('formCreate');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('btnSubmitCreate');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengunggah...';

        $.ajax({
            url: apiStoreUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Pengiriman';

                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalCreate')).hide();
                    showAjaxAlert(response.message, 'success');
                    loadData(); // Segarkan tabel tanpa reload halaman
                }
            },
            error: function(xhr) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save me-1"></i> Simpan Pengiriman';

                const errBox = document.getElementById('createErrors');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errHtml = '<ul class="mb-0 ps-3">';
                    for (const key in xhr.responseJSON.errors) {
                        errHtml += `<li>${xhr.responseJSON.errors[key][0]}</li>`;
                    }
                    errHtml += '</ul>';
                    errBox.innerHTML = errHtml;
                    errBox.classList.remove('d-none');
                } else {
                    errBox.textContent = 'Terjadi kesalahan saat menyimpan data.';
                    errBox.classList.remove('d-none');
                }
            }
        });
    }

    // ==========================================
    // 3. AJAX EDIT: Ambil Data & Perbarui
    // ==========================================
    function openEditModal(id) {
        const errBox = document.getElementById('editErrors');
        errBox.classList.add('d-none');

        $.ajax({
            url: `/logistics/api/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const item = response.data;
                    document.getElementById('editId').value = item.id;
                    document.getElementById('editTrackingCodeHeader').textContent = item.tracking_code;
                    document.getElementById('editSender').value = item.sender_name;
                    document.getElementById('editReceiver').value = item.receiver_name;
                    document.getElementById('editCommodity').value = item.commodity_info;
                    document.getElementById('editWeight').value = item.weight_kg;
                    document.getElementById('editDist').value = item.distance_km;
                    document.getElementById('editMethod').value = item.shipping_method;
                    document.getElementById('editStatus').value = item.status;
                    document.getElementById('editMitra').value = item.mitra_id || '';
                    document.getElementById('editNotes').value = item.notes || '';

                    const fileInd = document.getElementById('editFileIndicator');
                    if (item.document_path) {
                        fileInd.innerHTML = `<span class="text-success"><i class="fas fa-check-circle"></i> Berkas tersimpan:</span> <code>${item.document_path}</code>`;
                    } else {
                        fileInd.textContent = 'Belum ada berkas terunggah.';
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalEdit'));
                    modal.show();
                }
            }
        });
    }

    function submitEdit(e) {
        e.preventDefault();
        const id = document.getElementById('editId').value;
        const form = document.getElementById('formEdit');
        const formData = new FormData(form);
        const submitBtn = document.getElementById('btnSubmitEdit');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        $.ajax({
            url: `/logistics/api/${id}/update`,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Simpan Perubahan';

                if (response.success) {
                    bootstrap.Modal.getInstance(document.getElementById('modalEdit')).hide();
                    showAjaxAlert(response.message, 'success');
                    loadData();
                }
            },
            error: function(xhr) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-check-circle me-1"></i> Simpan Perubahan';

                const errBox = document.getElementById('editErrors');
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    let errHtml = '<ul class="mb-0 ps-3">';
                    for (const key in xhr.responseJSON.errors) {
                        errHtml += `<li>${xhr.responseJSON.errors[key][0]}</li>`;
                    }
                    errHtml += '</ul>';
                    errBox.innerHTML = errHtml;
                    errBox.classList.remove('d-none');
                } else {
                    errBox.textContent = 'Gagal memperbarui data pengiriman.';
                    errBox.classList.remove('d-none');
                }
            }
        });
    }

    // ==========================================
    // 4. AJAX DELETE: Hapus Data & File Storage
    // ==========================================
    function deleteShipment(id, code) {
        if (!confirm(`Apakah Anda yakin ingin menghapus pengiriman [${code}]? Berkas surat jalan pada disk storage juga akan otomatis dihapus.`)) {
            return;
        }

        $.ajax({
            url: `/logistics/api/${id}`,
            type: 'DELETE',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAjaxAlert(response.message, 'success');
                    const row = document.getElementById(`row-shipment-${id}`);
                    if (row) row.remove();
                }
            },
            error: function(err) {
                alert('Gagal menghapus data pengiriman.');
            }
        });
    }

    // ==========================================
    // 5. FITUR WAJIB: BUKA KEMBALI BERKAS UPLOAD
    // ==========================================
    function openDocModal(id) {
        $.ajax({
            url: `/logistics/api/${id}`,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    const item = response.data;
                    document.getElementById('viewDocTracking').textContent = item.tracking_code;
                    document.getElementById('viewDocSender').textContent = item.sender_name;
                    document.getElementById('viewDocReceiver').textContent = item.receiver_name;
                    document.getElementById('viewDocCommodity').textContent = item.commodity_info;
                    document.getElementById('viewDocStatus').innerHTML = getStatusBadge(item.status);

                    const container = document.getElementById('docPreviewContainer');
                    const btnOpen = document.getElementById('btnOpenInNewTab');
                    const btnDownload = document.getElementById('btnDownloadDoc');

                    btnOpen.href = item.document_url;
                    btnDownload.href = `/logistics/${item.id}/download`;

                    if (item.document_path && item.document_path.toLowerCase().endsWith('.pdf')) {
                        container.innerHTML = `
                            <embed src="${item.document_url}" type="application/pdf" width="100%" height="400px" class="rounded-3 border">
                        `;
                    } else if (item.document_url) {
                        container.innerHTML = `
                            <img src="${item.document_url}" alt="Surat Jalan" class="img-fluid rounded-3 shadow-sm" style="max-height: 420px;">
                        `;
                    } else {
                        container.innerHTML = `<div class="p-4 text-muted">Berkas belum tersedia.</div>`;
                    }

                    const modal = new bootstrap.Modal(document.getElementById('modalViewDoc'));
                    modal.show();
                }
            }
        });
    }

    // Panggil loadData saat halaman dimuat
    document.addEventListener("DOMContentLoaded", function() {
        loadData();
    });
</script>
@endpush

