<x-layout title="Masuk">
    <x-auth-card>
        <div class="text-center">
            <h1 class="text-4xl leading-tight font-extrabold tracking-tight text-gray-900">Selamat Datang Kembali</h1>
            <p class="mt-3 text-base text-gray-500">Masuk ke akun Anda</p>
        </div>

        <x-status class="mt-8" />

        <form method="POST" action="{{ route('login.store') }}" class="mt-8 flex flex-col gap-6" data-validate>
            @csrf

            <x-form.input
                name="username"
                label="Username"
                :value="old('username')"
                autocomplete="username"
                maxlength="15"
                required
                autofocus
                data-rules="required|between:6,15|alpha_num"
            />

            <x-form.input
                name="password"
                type="password"
                label="Kata Sandi"
                autocomplete="current-password"
                maxlength="16"
                required
                data-rules="required|between:8,16"
            />

            <div class="flex items-center justify-between gap-4">
                <label class="inline-flex items-center gap-2.5 text-base text-gray-700">
                    <input type="checkbox" name="remember" value="1" class="size-5 rounded-md accent-blue-600" @checked(old('remember'))>
                    Ingat saya
                </label>

                <a href="{{ route('password.request') }}" class="text-base font-medium text-blue-600 hover:text-blue-700">Lupa kata sandi?</a>
            </div>

            <button type="submit" class="h-13 w-full rounded-xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-wait disabled:opacity-70">
                Masuk
            </button>
        </form>

        <p class="mt-8 text-center text-base text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-blue-600 hover:text-blue-700">Daftar di sini</a>
        </p>
    </x-auth-card>
</x-layout>
