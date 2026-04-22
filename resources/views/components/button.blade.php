{{--
    Button Component
    Usage:
        <x-button>Simpan</x-button>
        <x-button variant="danger" size="sm">Hapus</x-button>
        <x-button variant="secondary" href="/url">Batal</x-button>
        <x-button type="submit" :loading="true">Memproses...</x-button>

    Props:
        variant: primary (default) | secondary | danger | ghost
        size:    sm | md (default) | lg
        href:    render as <a> tag jika diisi
        type:    button (default) | submit | reset
        loading: tampilkan spinner
--}}
@props([
    'variant' => 'primary',
    'size'    => 'md',
    'href'    => null,
    'type'    => 'button',
    'loading' => false,
])

@php
    $variantClass = match($variant) {
        'secondary' => 'btn-spmb-secondary',
        'danger'    => 'btn-spmb-danger',
        'ghost'     => 'btn-spmb-ghost',
        default     => 'btn-spmb-primary',
    };

    $sizeClass = match($size) {
        'sm' => 'text-xs px-3 py-1.5',
        'lg' => 'text-base px-6 py-3',
        default => '',
    };

    $classes = trim("{$variantClass} {$sizeClass}");
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($loading) <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> @endif
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $classes]) }} @if($loading) disabled @endif>
        @if($loading) <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> @endif
        {{ $slot }}
    </button>
@endif