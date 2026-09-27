@if (session('status'))
    <div role="status" {{ $attributes->class('rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700') }}>
        {{ session('status') }}
    </div>
@endif
