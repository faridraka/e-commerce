@extends('layouts.app')

@section('title', 'Profil Saya - Lokalku')

@section('content')
<div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-6">
    {{-- Header --}}
    <div>
        <h1 class="font-display text-2xl font-bold leading-tight text-ink-primary">Profil Saya</h1>
        <p class="font-body text-sm text-ink-secondary">Kelola informasi akun dan preferensi Anda.</p>
    </div>

    {{-- Profile Header Card --}}
    <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
        <div class="flex flex-col items-center gap-4 sm:flex-row sm:items-center sm:gap-6">
            <div class="flex h-20 w-20 items-center justify-center rounded-full bg-primary-container text-3xl font-display font-bold text-primary">
                {{ mb_substr($user->name, 0, 1) }}
            </div>
            <div class="text-center sm:text-left">
                <div class="flex flex-col items-center gap-2 sm:flex-row sm:items-center sm:gap-3">
                    <h2 class="font-display text-xl font-bold text-ink-primary">{{ $user->name }}</h2>
                    @if ($user->role === 'seller')
                    <span class="inline-flex items-center rounded-full bg-blue-subtle px-3 py-1 font-body text-xs font-medium text-primary">Penjual</span>
                    @else
                    <span class="inline-flex items-center rounded-full bg-[#EBF5D6] px-3 py-1 font-body text-xs font-medium text-success-text">Pembeli</span>
                    @endif
                </div>
                <p class="mt-1 font-body text-sm text-ink-secondary">{{ $user->email }}</p>
                <p class="mt-1 font-body text-xs text-ink-muted">Bergabung {{ $user->created_at->translatedFormat('d F Y') }}</p>
            </div>
        </div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <a href="{{ route('orders.index') }}" class="flex items-center gap-4 rounded-2xl border border-outline-variant bg-surface-card p-6 transition hover:border-primary/30">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-primary-container">
                <span class="material-symbols-outlined text-[24px] text-primary">shopping_bag</span>
            </div>
            <div>
                <p class="font-display text-2xl font-bold text-ink-primary">{{ $ordersCount }}</p>
                <p class="font-body text-sm text-ink-secondary">Total Pesanan</p>
            </div>
            <span class="material-symbols-outlined ml-auto text-[20px] text-ink-muted">chevron_right</span>
        </a>
        <a href="{{ route('addresses.index') }}" class="flex items-center gap-4 rounded-2xl border border-outline-variant bg-surface-card p-6 transition hover:border-primary/30">
            <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-secondary-container">
                <span class="material-symbols-outlined text-[24px] text-secondary">location_on</span>
            </div>
            <div>
                <p class="font-display text-2xl font-bold text-ink-primary">{{ $addressesCount }}</p>
                <p class="font-body text-sm text-ink-secondary">Alamat Tersimpan</p>
            </div>
            <span class="material-symbols-outlined ml-auto text-[20px] text-ink-muted">chevron_right</span>
        </a>
    </div>

    {{-- Edit Profile Form --}}
    <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
        <h2 class="font-display text-lg font-bold text-ink-primary">Edit Profil</h2>
        <form action="{{ route('profile.update') }}" method="POST" class="mt-6 space-y-5">
            @method('PATCH')
            @csrf
            <div>
                <label for="name" class="mb-2 block font-body text-sm font-medium text-ink-primary">Nama Lengkap</label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary placeholder-ink-muted focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Nama Anda" />
                @error('name')
                <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                    <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                </p>
                @enderror
            </div>
            <div>
                <label for="email" class="mb-2 block font-body text-sm font-medium text-ink-primary">Email</label>
                <input type="email" id="email" value="{{ $user->email }}" disabled class="w-full cursor-not-allowed rounded-xl border border-outline-variant bg-surface-container/50 px-4 py-3 font-body text-sm text-ink-muted" />
                <p class="mt-1.5 font-body text-xs text-ink-muted">Email tidak dapat diubah.</p>
            </div>

            {{-- Change Password --}}
            <div class="border-t border-outline-variant pt-5">
                <h3 class="font-body text-sm font-semibold text-ink-primary">Ganti Kata Sandi <span class="font-normal text-ink-muted">(Opsional)</span></h3>
                <div class="mt-4 space-y-4">
                    <div>
                        <label for="current_password" class="mb-2 block font-body text-sm font-medium text-ink-primary">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Kosongkan jika tidak ingin mengganti" />
                        @error('current_password')
                        <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                            <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                        </p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label for="new_password" class="mb-2 block font-body text-sm font-medium text-ink-primary">Kata Sandi Baru</label>
                            <input type="password" id="new_password" name="new_password" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Kosongkan jika tidak ingin mengganti" />
                            @error('new_password')
                            <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                                <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                            </p>
                            @enderror
                        </div>
                        <div>
                            <label for="new_password_confirmation" class="mb-2 block font-body text-sm font-medium text-ink-primary">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" id="new_password_confirmation" name="new_password_confirmation" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Ulangi kata sandi baru" />
                            @error('new_password_confirmation')
                            <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                                <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                            </p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end border-t border-outline-variant pt-5">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 font-body text-sm font-semibold text-on-primary transition hover:bg-primary/90">
                    <span class="material-symbols-outlined text-[18px]">save</span>
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
