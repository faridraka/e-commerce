@extends('layouts.auth')

@section('title', 'Daftar Akun Baru - Lokalku')

@section('header')
    <header class="w-full bg-surface-container-lowest border-b border-border-default">
        <div class="max-w-7xl mx-auto px-margin-desktop h-16 flex items-center justify-between">
            <a class="flex items-center gap-space-sm group" href="{{ route('home') }}">
                <div class="w-8 h-8 rounded-lg bg-primary-container flex items-center justify-center">
                    <span class="material-symbols-outlined text-on-primary text-[20px]">storefront</span>
                </div>
                <span class="font-headline-md text-headline-md tracking-tight text-primary font-bold">Lokalku</span>
            </a>
            <a class="flex items-center gap-space-xs font-body-md text-body-md text-on-surface-variant hover:text-primary transition-colors" href="{{ route('home') }}">
                <span class="material-symbols-outlined text-[18px]">arrow_back</span>
                <span>Kembali ke Beranda</span>
            </a>
        </div>
    </header>
@endsection

@section('content')
<main class="flex-1 w-full max-w-7xl mx-auto px-margin-desktop py-space-xl flex flex-col justify-center items-center">
    <div class="flex flex-col w-full">
        <div class="w-full max-w-7xl mx-auto flex flex-col lg:flex-row items-stretch justify-center gap-space-xl my-auto">
            <!-- Left 5 Columns: Brand Panel -->
            <div class="w-full lg:w-5/12 bg-primary-container text-on-primary rounded-xl shadow-md p-space-xl lg:p-10 flex flex-col justify-between relative overflow-hidden">
                <div class="absolute -right-20 -top-20 w-80 h-80 rounded-full bg-primary-fixed opacity-10 pointer-events-none blur-2xl"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 rounded-full bg-tertiary-container opacity-20 pointer-events-none blur-xl"></div>

                <div class="relative z-10 flex flex-col">
                    <div class="flex items-center justify-between mb-space-lg">
                        <div class="flex items-center gap-space-sm">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-lowest flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-primary text-[24px]" style="font-variation-settings: 'FILL' 1;">storefront</span>
                            </div>
                            <span class="font-headline-md text-headline-md tracking-tight font-bold text-on-primary">Lokalku</span>
                        </div>
                        <span class="px-space-md py-1 bg-surface-container-lowest/15 backdrop-blur-sm text-on-primary rounded-full font-caption text-caption flex items-center gap-1 font-medium">
                            <span class="w-2 h-2 rounded-full bg-tertiary-fixed"></span>
                            Sentra Pasar Digital UMKM
                        </span>
                    </div>
                    <h1 class="font-display text-display text-on-primary mb-space-sm leading-tight">
                        Gabung &amp; Belanja Produk Lokal
                    </h1>
                    <p class="font-body-md text-body-md text-primary-fixed leading-relaxed mb-space-xl">
                        Dukung kemandirian perajin dan pelaku usaha lokal dengan berbelanja langsung dari sumbernya di seluruh pelosok nusantara.
                    </p>

                    <div class="flex flex-col gap-space-lg">
                        <div class="flex items-start gap-space-md bg-surface-container-lowest/10 backdrop-blur-sm p-space-md rounded-xl">
                            <div class="w-11 h-11 min-w-[44px] rounded-full bg-surface-container-lowest text-primary flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[24px]">qr_code_scanner</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-primary">Bayar QRIS &amp; VA Otomatis</h2>
                                <p class="font-body-md text-body-md text-primary-fixed/90 mt-0.5">
                                    Transaksi instan bebas ribet langsung diverifikasi otomatis dalam hitungan detik.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-md bg-surface-container-lowest/10 backdrop-blur-sm p-space-md rounded-xl">
                            <div class="w-11 h-11 min-w-[44px] rounded-full bg-surface-container-lowest text-primary flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[24px]">local_shipping</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-primary">Lacak Pesanan Real-Time</h2>
                                <p class="font-body-md text-body-md text-primary-fixed/90 mt-0.5">
                                    Pantau status pengiriman langsung dari toko UMKM hingga paket tiba di pintu rumah Anda.
                                </p>
                            </div>
                        </div>
                        <div class="flex items-start gap-space-md bg-surface-container-lowest/10 backdrop-blur-sm p-space-md rounded-xl">
                            <div class="w-11 h-11 min-w-[44px] rounded-full bg-surface-container-lowest text-primary flex items-center justify-center shadow-sm">
                                <span class="material-symbols-outlined text-[24px]">favorite</span>
                            </div>
                            <div class="flex flex-col min-w-0">
                                <h2 class="font-headline-sm text-headline-sm font-semibold text-on-primary">Dukung Langsung UMKM</h2>
                                <p class="font-body-md text-body-md text-primary-fixed/90 mt-0.5">
                                    100% margin adil mengalir langsung memberdayakan produsen dan sentra kerajinan daerah.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative z-10 mt-space-xl flex flex-col gap-space-md">
                    <div class="flex items-center gap-space-sm overflow-x-auto pb-1">
                        <div class="flex items-center gap-space-xs bg-surface-container-lowest/15 backdrop-blur-sm px-3 py-1.5 rounded-full text-on-primary font-caption text-caption">
                            <span class="material-symbols-outlined text-[16px] text-accent-yellow">coffee</span>
                            <span>Kopi Gayo Aceh</span>
                        </div>
                        <div class="flex items-center gap-space-xs bg-surface-container-lowest/15 backdrop-blur-sm px-3 py-1.5 rounded-full text-on-primary font-caption text-caption">
                            <span class="material-symbols-outlined text-[16px] text-accent-yellow">hive</span>
                            <span>Madu Hutan Sumbawa</span>
                        </div>
                        <div class="flex items-center gap-space-xs bg-surface-container-lowest/15 backdrop-blur-sm px-3 py-1.5 rounded-full text-on-primary font-caption text-caption">
                            <span class="material-symbols-outlined text-[16px] text-tertiary-fixed">texture</span>
                            <span>Tenun Sasak</span>
                        </div>
                    </div>
                    <div class="bg-surface-container-lowest text-on-surface rounded-xl p-space-md shadow-sm flex items-center gap-space-md">
                        <div class="w-10 h-10 rounded-full bg-blue-subtle text-primary flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-[20px]" style="font-variation-settings: 'FILL' 1;">verified</span>
                        </div>
                        <div class="flex flex-col min-w-0">
                            <p class="font-body-md text-body-md font-semibold text-on-surface leading-snug">
                                1.200+ Produk Terkurasi &amp; 250+ UMKM
                            </p>
                            <p class="font-caption text-caption text-ink-muted">
                                Terverifikasi resmi dari Sabang hingga Merauke
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right 7 Columns: Form Container -->
            <div class="w-full lg:w-7/12 flex flex-col items-center justify-center">
                <div class="w-full max-w-[440px] bg-surface-container-lowest rounded-xl shadow-sm p-7 lg:p-8 flex flex-col">
                    <div class="flex flex-col mb-space-lg">
                        <h2 class="font-headline-md text-headline-md font-bold text-on-surface">
                            Buat Akun Baru
                        </h2>
                        <p class="font-body-md text-body-md text-ink-muted mt-1">
                            Sudah punya akun?
                            <a class="text-primary-container font-semibold hover:underline transition-colors ml-1" href="{{ route('login') }}">Masuk</a>
                        </p>
                    </div>

                    <form class="flex flex-col gap-space-md" id="reg-form" method="POST" action="{{ route('register') }}">
                        @csrf

                        <!-- Field 1: Nama Lengkap -->
                        <div class="flex flex-col gap-1">
                            <label class="font-body-md text-body-md font-medium text-on-surface" for="full_name">
                                Nama Lengkap
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-ink-muted text-[20px] pointer-events-none">person</span>
                                <input class="w-full h-11 pl-10 pr-3 rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-ink-muted shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all @error('name') bg-error-container text-error @enderror" id="full_name" name="name" placeholder="Contoh: Budi Santoso" type="text" value="{{ old('name') }}" required autofocus>
                            </div>
                            @error('name')
                                <p class="font-caption text-caption text-secondary flex items-center gap-1 mt-0.5">
                                    <span class="material-symbols-outlined text-[16px]">error</span>
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Field 2: Email -->
                        <div class="flex flex-col gap-1">
                            <label class="font-body-md text-body-md font-medium text-on-surface" for="email_input">
                                Email
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-ink-muted text-[20px] pointer-events-none">mail</span>
                                <input class="w-full h-11 pl-10 pr-10 rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-ink-muted shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all @error('email') bg-error-container text-error @enderror" id="email_input" name="email" placeholder="nama@email.com" type="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="material-symbols-outlined absolute right-3 text-secondary text-[20px]" style="font-variation-settings: 'FILL' 1;">error</span>
                                @enderror
                            </div>
                            @error('email')
                                <p class="font-caption text-caption text-secondary flex items-center gap-1 mt-0.5">
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Field 3: Kata Sandi -->
                        <div class="flex flex-col gap-1">
                            <label class="font-body-md text-body-md font-medium text-on-surface" for="password_input">
                                Kata Sandi
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-ink-muted text-[20px] pointer-events-none">lock</span>
                                <input class="w-full h-11 pl-10 pr-11 rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-ink-muted shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all font-code-mono @error('password') bg-error-container text-error @enderror" id="password_input" name="password" oninput="updatePasswordStrength(this.value)" placeholder="Masukkan kata sandi" type="password" required>
                                <button aria-label="Tampilkan atau sembunyikan kata sandi" class="absolute right-0 w-11 h-11 flex items-center justify-center text-ink-muted hover:text-on-surface focus:outline-none" onclick="togglePasswordVisibility('password_input', 'eye-icon-pass')" type="button">
                                    <span class="material-symbols-outlined text-[20px]" id="eye-icon-pass">visibility</span>
                                </button>
                            </div>
                            <div class="flex items-center justify-between mt-1">
                                <span class="font-caption text-caption text-ink-muted">Minimal 8 karakter (huruf &amp; angka)</span>
                                <span class="font-caption text-caption font-bold text-ink-muted" id="strength-label">—</span>
                            </div>
                            <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden flex gap-1 mt-0.5">
                                <div class="h-full w-1/3 bg-surface-container transition-all" id="bar-1"></div>
                                <div class="h-full w-1/3 bg-surface-container transition-all" id="bar-2"></div>
                                <div class="h-full w-1/3 bg-surface-container transition-all" id="bar-3"></div>
                            </div>
                            @error('password')
                                <p class="font-caption text-caption text-secondary flex items-center gap-1 mt-0.5">
                                    <span>{{ $message }}</span>
                                </p>
                            @enderror
                        </div>

                        <!-- Field 4: Konfirmasi Kata Sandi -->
                        <div class="flex flex-col gap-1">
                            <label class="font-body-md text-body-md font-medium text-on-surface" for="confirm_password_input">
                                Konfirmasi Kata Sandi
                            </label>
                            <div class="relative flex items-center">
                                <span class="material-symbols-outlined absolute left-3 text-ink-muted text-[20px] pointer-events-none">lock_reset</span>
                                <input class="w-full h-11 pl-10 pr-11 rounded-lg bg-surface-container-lowest text-on-surface font-body-md text-body-md placeholder:text-ink-muted shadow-sm focus:outline-none focus:ring-2 focus:ring-primary-container focus:ring-offset-2 transition-all font-code-mono" id="confirm_password_input" name="password_confirmation" oninput="updateMatchState()" placeholder="Ulangi kata sandi" type="password" required>
                                <button aria-label="Tampilkan atau sembunyikan konfirmasi kata sandi" class="absolute right-0 w-11 h-11 flex items-center justify-center text-ink-muted hover:text-on-surface focus:outline-none" onclick="togglePasswordVisibility('confirm_password_input', 'eye-icon-confirm')" type="button">
                                    <span class="material-symbols-outlined text-[20px]" id="eye-icon-confirm">visibility</span>
                                </button>
                            </div>
                            <!-- Match Indicator (driven by JS) -->
                            <div class="hidden items-center gap-1 font-caption text-caption text-success-text mt-0.5" id="match-helper-success">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">check_circle</span>
                                <span class="font-medium">Kata sandi cocok</span>
                            </div>
                            <div class="hidden items-center gap-1 font-caption text-caption text-secondary mt-0.5" id="match-helper-error">
                                <span class="material-symbols-outlined text-[16px]" style="font-variation-settings: 'FILL' 1;">cancel</span>
                                <span class="font-medium">Kata sandi tidak sesuai</span>
                            </div>
                        </div>

                        <!-- Primary Submit Button -->
                        <button class="w-full h-11 mt-space-xs rounded-full bg-primary-container hover:bg-blue-hover text-on-primary font-body-md text-body-md font-bold shadow-sm transition-all flex items-center justify-center gap-space-xs active:scale-[0.99] cursor-pointer" id="btn-submit" type="submit">
                            <span id="btn-text">Daftar Akun</span>
                        </button>
                    </form>

                    <p class="font-caption text-caption text-ink-muted text-center mt-space-md leading-relaxed">
                        Dengan mendaftar, Anda menyetujui
                        <a class="text-primary-container hover:underline font-medium" href="#">Syarat &amp; Ketentuan</a>
                        serta
                        <a class="text-primary-container hover:underline font-medium" href="#">Kebijakan Privasi</a>
                        Lokalku.
                    </p>
                </div>
            </div>
        </div>
    </div>
</main>

@push('scripts')
<script>
    function togglePasswordVisibility(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.textContent = 'visibility_off';
        } else {
            input.type = 'password';
            icon.textContent = 'visibility';
        }
    }

    function updatePasswordStrength(val) {
        const label = document.getElementById('strength-label');
        const b1 = document.getElementById('bar-1');
        const b2 = document.getElementById('bar-2');
        const b3 = document.getElementById('bar-3');

        if (!val || val.length < 5) {
            label.textContent = 'Lemah';
            label.className = 'font-caption text-caption font-bold text-secondary';
            b1.className = 'h-full w-1/3 bg-secondary-container transition-all';
            b2.className = 'h-full w-1/3 bg-surface-container transition-all';
            b3.className = 'h-full w-1/3 bg-surface-container transition-all';
        } else if (val.length < 8) {
            label.textContent = 'Sedang';
            label.className = 'font-caption text-caption font-bold text-accent-yellow';
            b1.className = 'h-full w-1/3 bg-accent-yellow transition-all';
            b2.className = 'h-full w-1/3 bg-accent-yellow transition-all';
            b3.className = 'h-full w-1/3 bg-surface-container transition-all';
        } else {
            label.textContent = 'Kuat';
            label.className = 'font-caption text-caption font-bold text-success-text';
            b1.className = 'h-full w-1/3 bg-tertiary-container transition-all';
            b2.className = 'h-full w-1/3 bg-tertiary-container transition-all';
            b3.className = 'h-full w-1/3 bg-tertiary-container transition-all';
        }
        updateMatchState();
    }

    function updateMatchState() {
        const pass = document.getElementById('password_input').value;
        const confirm = document.getElementById('confirm_password_input').value;
        const matchSuccess = document.getElementById('match-helper-success');
        const matchError = document.getElementById('match-helper-error');

        if (confirm.length === 0) {
            matchSuccess.classList.add('hidden');
            matchError.classList.add('hidden');
        } else if (pass === confirm) {
            matchSuccess.classList.remove('hidden');
            matchError.classList.add('hidden');
        } else {
            matchSuccess.classList.add('hidden');
            matchError.classList.remove('hidden');
        }
    }

    updatePasswordStrength('');
</script>
@endpush
@endsection
