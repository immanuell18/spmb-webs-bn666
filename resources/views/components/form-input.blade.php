{{--
    Form Input Component
    Usage:
        <x-form-input
            name="email"
            label="Email"
            type="email"
            placeholder="contoh@email.com"
            :required="true"
        />

    Props:
        name:        nama field (required)
        label:       label teks (opsional)
        type:        text (default) | email | password | number | date | tel | textarea | select
        placeholder: placeholder teks
        required:    tampilkan * merah
        help:        teks bantuan di bawah field
        class:       kelas tambahan untuk input
--}}
@props([
    'name',
    'label'       => null,
    'type'        => 'text',
    'placeholder' => '',
    'required'    => false,
    'help'        => null,
    'rows'        => 3,
])

@php
    $hasError = $errors->has($name);
    $inputClass = 'input-spmb ' . ($hasError ? 'border-red-400 focus:ring-red-500 focus:border-red-400' : '');
    $id = 'field_' . str_replace(['.', '[', ']'], '_', $name);
@endphp

<div class="space-y-1">

    @if($label)
    <label for="{{ $id }}" class="label-spmb">
        {{ $label }}
        @if($required) <span class="text-red-500 ml-0.5">*</span> @endif
    </label>
    @endif

    @if($type === 'textarea')
        <textarea
            id="{{ $id }}"
            name="{{ $name }}"
            rows="{{ $rows }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => $inputClass]) }}
        >{{ old($name) }}</textarea>

    @elseif($type === 'select')
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => $inputClass]) }}
        >
            {{ $slot }}
        </select>

    @else
        <input
            id="{{ $id }}"
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ old($name) }}"
            placeholder="{{ $placeholder }}"
            @if($required) required @endif
            {{ $attributes->merge(['class' => $inputClass]) }}
        />
    @endif

    @if($hasError)
        <p class="text-xs text-red-500 mt-1 flex items-center gap-1">
            <span>⚠</span> {{ $errors->first($name) }}
        </p>
    @endif

    @if($help && !$hasError)
        <p class="text-xs text-slate-500 mt-1">{{ $help }}</p>
    @endif

</div>