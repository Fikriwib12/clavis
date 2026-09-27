@php
    $genderRule = 'required|in:'.implode(',', array_column($genders, 'value'));
@endphp

<x-layout title="Pendaftaran Turnamen" class="bg-gray-100">
    <header class="flex justify-end px-4 pt-4 sm:px-6">
        <x-logout-button class="px-3.5 py-2 text-xs" />
    </header>

    <main class="px-4 pt-6 pb-16 sm:pt-10">
        <div class="mx-auto max-w-2xl rounded-2xl bg-white px-5 py-10 shadow-xl shadow-gray-300/60 sm:px-10">
            <div class="text-center">
                <h1 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">Pendaftaran Turnamen Tim</h1>
                <p class="mt-2 text-sm text-gray-500">Isi data tim Anda untuk mendaftar.</p>
            </div>

            <form
                method="POST"
                action="{{ route('tournament.store') }}"
                class="mt-8 rounded-xl border border-dashed border-gray-300 bg-gray-50 px-4 py-8 sm:px-6"
                data-validate
            >
                @csrf

                <h2 class="text-center text-2xl font-bold text-gray-900">Data Tim</h2>

                <div class="mt-6 flex flex-col gap-6">
                    <x-form.input
                        compact
                        name="team_name"
                        label="Nama Tim"
                        :value="old('team_name')"
                        autocomplete="off"
                        maxlength="15"
                        required
                        autofocus
                        data-rules="required|between:4,15|team_name"
                    />

                    <fieldset class="flex flex-col gap-4">
                        <legend class="mb-4 w-full border-b border-gray-200 pb-2 text-sm font-bold text-gray-800">Kapten Tim</legend>

                        <x-form.input compact readonly name="captain[name]" label="Nama Lengkap" attribute="Nama kapten" :value="$user->name" />

                        <x-form.input compact readonly name="captain[phone]" label="Nomor Telepon" attribute="Nomor telepon kapten" :value="$user->phone" />

                        <x-form.select
                            name="captain[gender]"
                            label="Jenis Kelamin"
                            attribute="Jenis kelamin kapten"
                            placeholder="Pilih Jenis Kelamin"
                            :options="$genders"
                            :selected="old('captain.gender')"
                            required
                            :data-rules="$genderRule"
                        />
                    </fieldset>

                    <fieldset class="flex flex-col gap-4">
                        <legend class="mb-4 w-full border-b border-gray-200 pb-2 text-sm font-bold text-gray-800">Anggota Tim</legend>

                        <x-form.input
                            compact
                            name="member[name]"
                            label="Nama Lengkap"
                            attribute="Nama anggota"
                            :value="old('member.name')"
                            autocomplete="off"
                            maxlength="100"
                            required
                            data-rules="required|max:100|alpha_space|different:captain_name"
                        />

                        <x-form.input
                            compact
                            name="member[phone]"
                            type="tel"
                            label="Nomor Telepon"
                            attribute="Nomor telepon anggota"
                            :value="old('member.phone')"
                            autocomplete="off"
                            inputmode="numeric"
                            maxlength="14"
                            required
                            data-rules="required|digits_between:7,14"
                        />

                        <x-form.select
                            name="member[gender]"
                            label="Jenis Kelamin"
                            attribute="Jenis kelamin anggota"
                            placeholder="Pilih Jenis Kelamin"
                            :options="$genders"
                            :selected="old('member.gender')"
                            required
                            :data-rules="$genderRule"
                        />
                    </fieldset>
                </div>

                <div class="mt-6 text-center">
                    <button
                        type="button"
                        class="text-sm font-medium text-blue-600 underline underline-offset-2 hover:text-blue-700"
                        aria-haspopup="dialog"
                        data-dialog-open="rules-dialog"
                    >
                        Baca Aturan &amp; Regulasi Turnamen
                    </button>
                </div>

                <button type="submit" class="mt-5 h-11 w-full rounded-lg bg-purple-600 text-sm font-bold text-white shadow-lg shadow-purple-600/30 transition hover:bg-purple-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600 disabled:cursor-wait disabled:opacity-70">
                    Daftarkan Tim
                </button>
            </form>
        </div>
    </main>

    <dialog
        id="rules-dialog"
        aria-labelledby="rules-dialog-title"
        class="m-auto max-h-[85vh] w-[calc(100%-2rem)] max-w-lg overflow-y-auto rounded-2xl bg-white p-0 text-gray-900 shadow-2xl backdrop:bg-gray-950/50"
    >
        <div class="p-6 sm:p-8">
            <h2 id="rules-dialog-title" class="text-xl font-bold">Aturan &amp; Regulasi Turnamen</h2>

            <ol class="mt-4 list-decimal space-y-2 pl-5 text-sm leading-relaxed text-gray-600">
                <li>Setiap tim terdiri dari tepat 2 (dua) orang: 1 kapten tim dan 1 anggota tim.</li>
                <li>Kapten tim adalah pemilik akun yang mendaftarkan tim. Setiap akun hanya dapat mendaftarkan satu tim.</li>
                <li>Setiap pemain hanya boleh terdaftar di satu tim.</li>
                <li>Nama tim terdiri dari 4-15 karakter (huruf, angka, dan garis bawah) serta tidak boleh mengandung unsur SARA, pornografi, atau kata-kata kasar.</li>
                <li>Nama dan nomor telepon pemain harus sesuai dengan data asli dan dapat dihubungi selama turnamen berlangsung.</li>
                <li>Dilarang menggunakan cheat, exploit, atau program pihak ketiga yang memberikan keuntungan tidak adil. Pelanggaran akan berakibat diskualifikasi.</li>
                <li>Tim wajib siap bertanding sesuai jadwal. Keterlambatan lebih dari 10 menit dianggap kalah WO (walkover).</li>
                <li>Jadwal dan informasi pertandingan dikirimkan ke email kapten tim.</li>
                <li>Keputusan panitia bersifat final dan tidak dapat diganggu gugat.</li>
            </ol>

            <form method="dialog" class="mt-6">
                <button type="submit" class="h-11 w-full rounded-lg bg-purple-600 text-sm font-bold text-white transition hover:bg-purple-700 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">
                    Saya Mengerti
                </button>
            </form>
        </div>
    </dialog>
</x-layout>
