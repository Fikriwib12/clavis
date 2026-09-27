<x-layout title="Daftar">
    <x-auth-card>
        <div class="text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-gray-900">Buat Akun Baru</h1>
            <p class="mt-3 text-base text-gray-500">Daftar sekarang untuk bergabung dengan turnamen kami</p>
        </div>

        <form method="POST" action="{{ route('register.store') }}" class="mt-10 flex flex-col gap-6" data-validate>
            @csrf

            <x-form.input
                name="name"
                label="Nama Lengkap"
                :value="old('name')"
                autocomplete="name"
                maxlength="100"
                required
                autofocus
                data-rules="required|max:100|alpha_space"
            />

            <x-form.input
                name="username"
                label="Nama Pengguna"
                :value="old('username')"
                autocomplete="username"
                maxlength="15"
                required
                data-rules="required|between:6,15|alpha_num"
            />

            <x-form.input
                name="email"
                type="email"
                label="Email"
                :value="old('email')"
                autocomplete="email"
                maxlength="100"
                required
                data-rules="required|max:100|email"
            />

            <x-form.input
                name="password"
                type="password"
                label="Kata Sandi"
                autocomplete="new-password"
                maxlength="16"
                required
                data-rules="required|between:8,16"
            />

            <x-form.input
                name="phone"
                type="tel"
                label="Nomor Telepon"
                :value="old('phone')"
                autocomplete="tel"
                inputmode="numeric"
                maxlength="14"
                required
                data-rules="required|digits_between:7,14"
            />

            <button type="submit" class="mt-2 h-13 w-full rounded-xl bg-blue-600 text-lg font-bold text-white shadow-lg shadow-blue-600/30 transition hover:bg-blue-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 disabled:cursor-wait disabled:opacity-70">
                Daftar
            </button>
        </form>

        <p class="mt-8 text-center text-base text-gray-600">
            Sudah punya akun?
            <a href="{{ route('login') }}" class="font-semibold text-blue-600 hover:text-blue-700">Masuk di sini</a>
        </p>
    </x-auth-card>
</x-layout>
