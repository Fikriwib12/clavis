@props([
    'name',
    'label',
    // A list of backed enum cases; each case's value is both the option value and its text.
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    // The field's name in validation messages, when it differs from the visible label.
    'attribute' => null,
])

@php
    $id = str_replace(['[', ']'], ['_', ''], $name);
    $message = $errors->first(str_replace(['[', ']'], ['.', ''], $name));
@endphp

<div class="flex flex-col gap-1.5">
    <label for="{{ $id }}" class="text-xs font-medium text-gray-600">{{ $label }}</label>

    <div class="relative">
        <select
            id="{{ $id }}"
            name="{{ $name }}"
            data-label="{{ $attribute ?? $label }}"
            aria-describedby="{{ $id }}-error"
            @if ($message) aria-invalid="true" @endif
            {{ $attributes->class('block h-9 w-full appearance-none rounded-lg border border-gray-300 bg-white py-0 pr-10 pl-3 text-base text-gray-900 outline-none transition focus:border-purple-500 focus:ring-3 focus:ring-purple-500/15 aria-[invalid=true]:border-red-500 aria-[invalid=true]:focus:ring-red-500/15 sm:text-sm') }}
        >
            @if ($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif

            @foreach ($options as $option)
                <option value="{{ $option->value }}" @selected($selected === $option->value)>{{ $option->value }}</option>
            @endforeach
        </select>

        <svg class="pointer-events-none absolute top-1/2 right-3 size-4 -translate-y-1/2 text-gray-900" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path d="m5 7.5 5 5 5-5" stroke-linecap="round" stroke-linejoin="round" />
        </svg>
    </div>

    <x-form.error :for="$id" :message="$message" />
</div>
