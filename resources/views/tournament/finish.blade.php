<x-layout title="Pendaftaran Berhasil" class="bg-gray-100">
    <main class="flex min-h-screen items-center justify-center px-4 py-10">
        <div class="w-full max-w-2xl rounded-3xl bg-white px-6 py-14 text-center shadow-2xl shadow-gray-300/70 sm:px-12">
            <svg class="mx-auto size-20 text-green-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
            </svg>

            <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Pendaftaran Berhasil!</h1>

            <p class="mt-4 text-lg text-gray-600">
                Selamat, tim <span class="font-semibold text-gray-900">{{ $team->team_name }}</span> telah berhasil terdaftar untuk turnamen ini.
            </p>

            <p class="mt-4 text-sm text-gray-500">Cek email Anda untuk detail lebih lanjut dan informasi jadwal.</p>

            <div class="mt-8 flex justify-center">
                <x-logout-button class="px-5 py-2.5 text-base" />
            </div>
        </div>
    </main>
</x-layout>
