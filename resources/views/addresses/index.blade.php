@extends('layouts.app')

@section('title', 'Alamat Saya - Lokalku')

@section('content')
<div class="max-w-[1280px] w-full mx-auto px-4 py-6 md:py-8 space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="font-display text-2xl font-bold leading-tight text-ink-primary">Alamat Saya</h1>
            <p class="font-body text-sm text-ink-secondary">Kelola alamat pengiriman pesanan Anda.</p>
        </div>
        <button id="add-address-button" type="button" class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary px-5 py-3 font-body text-sm font-semibold text-on-primary transition hover:bg-primary/90">
            <span class="material-symbols-outlined text-[18px]">add</span>
            Tambah Alamat Baru
        </button>
    </div>

    <div class="rounded-2xl border border-outline-variant bg-surface-card p-6">
        @if ($addresses->isEmpty())
            <div id="address-empty-state">
                <x-not-found title="Belum Ada Alamat" message="Tambahkan alamat untuk memudahkan pengiriman pesananmu." actionUrl="#" actionLabel="Tambah Alamat Baru" />
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($addresses as $address)
                <div class="flex flex-col rounded-xl border border-outline-variant bg-surface-container p-5 {{ $address->is_default ? 'border-primary/40' : '' }}">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <span class="material-symbols-outlined text-[20px] text-primary">person</span>
                            <h3 class="font-body text-sm font-semibold text-ink-primary">{{ $address->recipient_name }}</h3>
                            @if ($address->is_default)
                            <span class="inline-flex items-center rounded-full bg-tertiary-fixed/40 px-2.5 py-0.5 font-body text-xs font-medium text-tertiary">Utama</span>
                            @endif
                        </div>
                    </div>
                    <p class="mt-2 font-body text-sm text-ink-secondary">{{ $address->phone }}</p>
                    <p class="mt-1 font-body text-sm text-ink-secondary">{{ $address->address_detail }}</p>
                    <p class="mt-1 font-body text-sm text-ink-muted">{{ $address->city }} {{ $address->postal_code }}</p>
                    <div class="mt-4 flex items-center gap-2 border-t border-outline-variant pt-4">
                        @if (!$address->is_default)
                        <form action="{{ route('addresses.setDefault', $address) }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-outline-variant bg-surface-card px-3 py-2 font-body text-xs font-medium text-ink-primary transition hover:border-primary hover:text-primary">
                                <span class="material-symbols-outlined text-[14px]">star</span>
                                Jadikan Utama
                            </button>
                        </form>
                        @endif
                        <button type="button" data-edit-address="{{ $address->id }}" data-recipient="{{ $address->recipient_name }}" data-phone="{{ $address->phone }}" data-detail="{{ $address->address_detail }}" data-city="{{ $address->city }}" data-postal="{{ $address->postal_code }}" data-default="{{ $address->is_default ? 'true' : 'false' }}" class="inline-flex items-center gap-1 rounded-lg border border-outline-variant bg-surface-card px-3 py-2 font-body text-xs font-medium text-ink-primary transition hover:border-primary hover:text-primary">
                            <span class="material-symbols-outlined text-[14px]">edit</span>
                            Edit
                        </button>
                        <form action="{{ route('addresses.destroy', $address) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="inline-flex items-center gap-1 rounded-lg border border-outline-variant bg-surface-card px-3 py-2 font-body text-xs font-medium text-error-text transition hover:bg-error-container">
                                <span class="material-symbols-outlined text-[14px]">delete</span>
                                Hapus
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
</div>

{{-- Add/Edit Modal --}}
<div id="address-modal" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 p-4">
    <div class="w-full max-w-md rounded-2xl bg-surface-card p-6 shadow-xl">
        <div class="flex items-center justify-between">
            <h2 id="address-modal-title" class="font-display text-lg font-bold text-ink-primary">Tambah Alamat Baru</h2>
            <button id="address-modal-close" type="button" class="flex h-8 w-8 items-center justify-center rounded-full bg-surface-container text-ink-secondary transition hover:bg-surface-container-high">
                <span class="material-symbols-outlined text-[18px]">close</span>
            </button>
        </div>
        <form id="address-modal-form" action="{{ route('addresses.store') }}" method="POST" class="mt-5 space-y-4">
            @csrf
            <div>
                <label for="recipient_name" class="mb-2 block font-body text-sm font-medium text-ink-primary">Nama Penerima</label>
                <input type="text" id="recipient_name" name="recipient_name" value="{{ old('recipient_name') }}" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Nama penerima" />
                @error('recipient_name')
                <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                    <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                </p>
                @enderror
            </div>
            <div>
                <label for="phone" class="mb-2 block font-body text-sm font-medium text-ink-primary">Nomor Telepon</label>
                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="08xxxxxxxxxx" />
                @error('phone')
                <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                    <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                </p>
                @enderror
            </div>
            <div>
                <label for="address_detail" class="mb-2 block font-body text-sm font-medium text-ink-primary">Alamat Lengkap</label>
                <textarea id="address_detail" name="address_detail" rows="3" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Nama jalan, gedung, nomor rumah, dll.">{{ old('address_detail') }}</textarea>
                @error('address_detail')
                <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                    <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                </p>
                @enderror
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="city" class="mb-2 block font-body text-sm font-medium text-ink-primary">Kota</label>
                    <input type="text" id="city" name="city" value="{{ old('city') }}" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Kota" />
                    @error('city')
                    <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                        <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                    </p>
                    @enderror
                </div>
                <div>
                    <label for="postal_code" class="mb-2 block font-body text-sm font-medium text-ink-primary">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" value="{{ old('postal_code') }}" class="w-full rounded-xl border border-outline-variant bg-surface-container px-4 py-3 font-body text-sm text-ink-primary focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20" placeholder="Kode pos" />
                    @error('postal_code')
                    <p class="mt-1.5 flex items-center gap-1 font-body text-xs text-error-text">
                        <span class="material-symbols-outlined text-[14px]">error</span>{{ $message }}
                    </p>
                    @enderror
                </div>
            </div>
            @if ($addresses->isNotEmpty())
            <div class="flex items-center gap-2">
                <input type="checkbox" id="set_default" name="set_default" value="1" class="h-4 w-4 rounded border-outline-variant text-primary focus:ring-primary/20" />
                <label for="set_default" class="font-body text-sm text-ink-primary">Jadikan alamat utama</label>
            </div>
            @endif
            <div class="flex justify-end gap-3 border-t border-outline-variant pt-4">
                <button type="button" id="address-modal-cancel" class="rounded-xl border border-outline-variant px-5 py-3 font-body text-sm font-semibold text-ink-primary transition hover:bg-surface-container">Batal</button>
                <button type="submit" class="rounded-xl bg-primary px-5 py-3 font-body text-sm font-semibold text-on-primary transition hover:bg-primary/90">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('address-modal');
        const modalTitle = document.getElementById('address-modal-title');
        const modalForm = document.getElementById('address-modal-form');
        const storeUrl = '{{ route('addresses.store') }}';
        const fields = ['recipient_name', 'phone', 'address_detail', 'city', 'postal_code'];
        const setDefault = document.getElementById('set_default');

        function openModal() {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeModal() {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // Add mode
        document.getElementById('add-address-button').addEventListener('click', () => {
            modalTitle.textContent = 'Tambah Alamat Baru';
            modalForm.action = storeUrl;
            fields.forEach(f => document.getElementById(f).value = '');
            if (setDefault) setDefault.checked = false;
            openModal();
        });

        // Edit mode
        document.querySelectorAll('[data-edit-address]').forEach(btn => {
            btn.addEventListener('click', () => {
                const id = btn.dataset.editAddress;
                modalTitle.textContent = 'Edit Alamat';
                modalForm.action = '{{ url("addresses") }}/' + id;
                fields.forEach(f => document.getElementById(f).value = btn.dataset[f.replace('_', '')] ?? '');
                if (setDefault) setDefault.checked = btn.dataset.default === 'true';
                openModal();
            });
        });

        // Close handlers
        document.getElementById('address-modal-close').addEventListener('click', closeModal);
        document.getElementById('address-modal-cancel').addEventListener('click', closeModal);
        modal.addEventListener('click', (e) => { if (e.target === modal) closeModal(); });

        // Trigger empty state action (x-not-found button) to open the modal
        document.querySelectorAll('#address-empty-state a').forEach(link => {
            link.setAttribute('href', '#');
            link.addEventListener('click', (e) => {
                e.preventDefault();
                document.getElementById('add-address-button').click();
            });
        });
    });
</script>
@endpush
