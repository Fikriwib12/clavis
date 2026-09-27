@props([
    'name',
    'label',
    'type' => 'text',
    'value' => null,
    // The field's name in validation messages, when it differs from the visible label.
    'attribute' => null,
    // The smaller style used by the team registration form.
    'compact' => false,
])

@php
    $id = str_replace(['[', ']'], ['_', ''], $name);
    $message = $errors->first(str_replace(['[', ']'], ['.', ''], $name));
@endphp

<div @class(['flex flex-col', 'gap-1.5' => $compact, 'gap-2' => ! $compact])>
    <label
        for="{{ $id }}"
        @class([
            'font-medium',
            'text-xs text-gray-600' => $compact,
            'text-base text-gray-700' => ! $compact,
        ])
    >{{ $label }}</label>

    <input
        id="{{ $id }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @if ($type !== 'password') value="{{ $value }}" @endif
        data-label="{{ $attribute ?? $label }}"
        aria-describedby="{{ $id }}-error"
        @if ($message) aria-invalid="true" @endif
        {{ $attributes->class([
            'block w-full border bg-white text-gray-900 outline-none transition placeholder:text-gray-400',
            'read-only:cursor-default read-only:border-gray-200 read-only:bg-gray-200/70 read-only:text-gray-600',
            'aria-[invalid=true]:border-red-500 aria-[invalid=true]:focus:ring-red-500/15',
            'h-9 rounded-lg border-gray-300 px-3 text-base focus:border-purple-500 focus:ring-3 focus:ring-purple-500/15 sm:text-sm' => $compact,
            'h-12 rounded-xl border-gray-200 px-4 text-base shadow-xs focus:border-blue-500 focus:ring-4 focus:ring-blue-500/15' => ! $compact,
        ]) }}
    >

    <x-form.error :for="$id" :message="$message" />
</div>
