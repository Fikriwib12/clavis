@props(['for', 'message' => null])

{{-- Server-side errors render here; resources/js/form-validation.js writes client-side errors to the same element. --}}
<p
    id="{{ $for }}-error"
    data-error-for="{{ $for }}"
    class="text-sm text-red-600"
    @unless ($message) hidden @endunless
>{{ $message }}</p>
