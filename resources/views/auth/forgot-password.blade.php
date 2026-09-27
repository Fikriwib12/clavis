<x-layout title="Lupa Kata Sandi">
    <x-auth-card>
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Lupa Kata Sandi</h1>
            <p class="mt-3 text-base text-gray-500">Masukkan email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>
        </div>

        <x-status class="mt-8" />

        <form method="POST" action="{{ route('password.email') }}" class="mt-8 flex flex-col gap-6" data-validate>
            @csrf

            <x-form.input
                name="email"
                type="email"
                label="Email"
                :value="old('email')"
                autocomplete="email"
                maxlength="100"
                required
                autofocus
                data-rules="required|max:100|email"
            />

            <button type="submit" class="h-13 w-full rounded-xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-wait disabled:opacity-70">
                Kirim Tautan Reset
            </button>
        </form>

        <p class="mt-8 text-center text-base text-gray-600">
            Sudah ingat kata sandi?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Masuk di sini</a>
        </p>
    </x-auth-card>
</x-layout>
