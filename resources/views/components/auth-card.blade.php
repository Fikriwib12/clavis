<main class="flex min-h-screen items-center justify-center bg-slate-200 px-4 py-10">
    <div {{ $attributes->class('w-full max-w-md rounded-[2rem] bg-white px-6 py-12 shadow-2xl shadow-slate-500/25 sm:px-12') }}>
        {{ $slot }}
    </div>
</main>
