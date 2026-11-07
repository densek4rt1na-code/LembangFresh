@props(['status', 'type' => 'status'])

@php
    $class = 'badge ';
    $label = $status;

    if ($type === 'grade') {
        $class .= match(strtoupper($status)) {
            'A' => 'bg-success text-white',
            'B' => 'bg-warning text-dark',
            'C' => 'bg-secondary text-white',
            default => 'bg-light text-dark border',
        };
        $label = 'Grade ' . strtoupper($status);
    } elseif ($type === 'role') {
        $class .= match($status) {
            'admin' => 'bg-dark text-white',
            'petani' => 'bg-success text-white',
            'pembeli' => 'bg-info text-white',
            'mitra' => 'bg-primary text-white',
            default => 'bg-secondary text-white',
        };
        $label = ucfirst($status);
    } else {
        $class .= match($status) {
            'tersedia', 'selesai', 'terpenuhi', '1', 1, true => 'bg-success text-white',
            'sebagian_terjual', 'diproses', 'menunggu', 'dibuat' => 'bg-warning text-dark',
            'diambil', 'dikirim' => 'bg-primary text-white',
            'sampai_tujuan' => 'bg-info text-white',
            'habis', 'ditolak', 'dibatalkan', '0', 0, false => 'bg-danger text-white',
            default => 'bg-secondary text-white',
        };
        $label = match($status) {
            '1', 1, true => 'Terverifikasi',
            '0', 0, false => 'Belum Terverifikasi',
            default => ucwords(str_replace('_', ' ', (string)$status)),
        };
    }
@endphp

<span {{ $attributes->merge(['class' => $class . ' px-2 py-1 rounded-pill fw-semibold']) }}>
    {{ $label }}
</span>

