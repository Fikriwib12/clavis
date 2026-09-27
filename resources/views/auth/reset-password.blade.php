<x-layout title="Atur Ulang Kata Sandi">
    <x-auth-card>
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Atur Ulang Kata Sandi</h1>
            <p class="mt-3 text-base text-gray-500">Buat kata sandi baru untuk akun Anda.</p>
        </div>

        <form method="POST" action="{{ route('password.store') }}" class="mt-10 flex flex-col gap-6" data-validate>
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <x-form.input
                name="email"
                type="email"
                label="Email"
                :value="old('email', $email)"
                autocomplete="email"
                maxlength="100"
                required
                data-rules="required|max:100|email"
            />

            <x-form.input
                name="password"
                type="password"
                label="Kata Sandi Baru"
                attribute="Kata sandi"
                autocomplete="new-password"
                maxlength="16"
                required
                autofocus
                data-rules="required|between:8,16"
            />

            <x-form.input
                name="password_confirmation"
                type="password"
                label="Konfirmasi Kata Sandi"
                attribute="Konfirmasi kata sandi"
                autocomplete="new-password"
                maxlength="16"
                required
                data-rules="required|same:password"
            />

            <button type="submit" class="h-13 w-full rounded-xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-wait disabled:opacity-70">
                Simpan Kata Sandi
            </button>
        </form>
    </x-auth-card>
</x-layout>
