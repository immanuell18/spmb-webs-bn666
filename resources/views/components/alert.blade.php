{{--
    Alert Component
    Usage:
        <x-alert type="success">Data berhasil disimpan!</x-alert>
        <x-alert type="error" :dismissible="false">Error tidak bisa di-dismiss</x-alert>

    Props:
        type:       success | error | info | warning
        dismissible: true (default) | false
--}}
@props([
    'type'        => 'info',
    'dismissible' => true,
])

@php
    $config = match($type) {
        'success' => ['class' => 'alert-spmb-success', 'icon' => '<i class="bi bi-check-circle-fill text-success me-1"></i>'],
        'error'   => ['class' => 'alert-spmb-error',   'icon' => '<i class="bi bi-x-circle-fill text-danger me-1"></i>'],
        'warning' => ['class' => 'alert-spmb-warning', 'icon' => '<i class="bi bi-exclamation-triangle-fill text-warning me-1"></i>'],
        default   => ['class' => 'alert-spmb-info',    'icon' => 'ℹ️'],
    };
@endphp

<div {{ $attributes->merge(['class' => $config['class'] . ' animate-spmb-fade-in']) }} role="alert">
    <span class="text-base flex-shrink-0">{{ $config['icon'] }}</span>
    <div class="flex-1 text-sm">{{ $slot }}</div>
    @if($dismissible)
        <button type="button" onclick="this.closest('[role=alert]').remove()"
                class="flex-shrink-0 opacity-60 hover:opacity-100 text-lg leading-none transition-opacity">
            &times;
        </button>
    @endif
</div>