{{--
    Card Component
    Usage:
        <x-card>
            <x-slot:header>Judul Card</x-slot:header>
            Isi konten card di sini
            <x-slot:footer>Footer optional</x-slot:footer>
        </x-card>

    Props:
        shadow: sm (default) | md | lg | none
        noPadding: hilangkan padding body
--}}
@props([
    'shadow'    => 'sm',
    'noPadding' => false,
])

@php
    $shadowClass = match($shadow) {
        'none' => '',
        'md'   => 'shadow-md',
        'lg'   => 'shadow-lg',
        default => 'shadow-sm',
    };
@endphp

<div {{ $attributes->merge(['class' => "card-spmb {$shadowClass}"]) }}>

    @isset($header)
    <div class="card-spmb-header">
        <div class="font-semibold text-slate-800">{{ $header }}</div>
        @isset($headerAction)
            <div>{{ $headerAction }}</div>
        @endisset
    </div>
    @endisset

    <div @class(['card-spmb-body' => !$noPadding])>
        {{ $slot }}
    </div>

    @isset($footer)
    <div class="px-6 py-3 border-t border-slate-100 bg-slate-50 text-sm text-slate-500">
        {{ $footer }}
    </div>
    @endisset

</div>