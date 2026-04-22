{{--
    Badge / Status Component
    Usage:
        <x-badge status="SUBMIT" />
        <x-badge status="ADM_PASS" />
        <x-badge status="PAID" />
        <x-badge status="LULUS" />

    Props:
        status: SUBMIT | ADM_PASS | ADM_REJECT | PAID | LULUS | TIDAK_LULUS | CADANGAN
        label:  override teks badge (opsional)
--}}
@props(['status', 'label' => null])

@php
    $map = [
        'SUBMIT'      => ['class' => 'badge-submit',     'icon' => '⏳', 'text' => 'Menunggu Verifikasi'],
        'ADM_PASS'    => ['class' => 'badge-adm-pass',   'icon' => '<i class="bi bi-check-circle-fill text-success me-1"></i>', 'text' => 'Lulus Administrasi'],
        'ADM_REJECT'  => ['class' => 'badge-adm-reject', 'icon' => '<i class="bi bi-x-circle-fill text-danger me-1"></i>', 'text' => 'Berkas Ditolak'],
        'PAID'        => ['class' => 'badge-paid',       'icon' => '💰', 'text' => 'Sudah Bayar'],
        'LULUS'       => ['class' => 'badge-lulus',      'icon' => '<i class="bi bi-award-fill text-success me-1"></i>', 'text' => 'Lulus'],
        'TIDAK_LULUS' => ['class' => 'badge-tidak-lulus','icon' => '<i class="bi bi-x-circle text-danger me-1"></i>', 'text' => 'Tidak Lulus'],
        'CADANGAN'    => ['class' => 'badge-cadangan',   'icon' => '<i class="bi bi-clipboard-data text-warning me-1"></i>', 'text' => 'Cadangan'],
    ];

    $config = $map[$status] ?? ['class' => 'badge-spmb bg-gray-100 text-gray-600', 'icon' => '•', 'text' => $status];
    $displayText = $label ?? $config['text'];
@endphp

<span {{ $attributes->merge(['class' => $config['class']]) }}>
    <span>{{ $config['icon'] }}</span>
    {{ $displayText }}
</span>