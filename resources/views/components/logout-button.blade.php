<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button
        type="submit"
        {{ $attributes->class('inline-flex items-center justify-center rounded-lg bg-red-500 font-bold text-white shadow-lg shadow-red-500/30 transition hover:bg-red-600 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-red-500') }}
    >
        Logout
    </button>
</form>
