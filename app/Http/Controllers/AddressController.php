<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Halaman Alamat Saya.
     */
    public function index(Request $request): View
    {
        $addresses = $request->user()->addresses()
            ->orderByDesc('is_default')
            ->orderBy('id')
            ->get();

        return view('addresses.index', compact('addresses'));
    }

    /**
     * Simpan alamat baru. Alamat pertama otomatis menjadi utama.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateAddress($request);

        $isFirst = $request->user()->addresses()->count() === 0;

        $address = $request->user()->addresses()->create([
            ...$validated,
            'is_default' => $isFirst,
        ]);

        if ($request->boolean('set_default')) {
            $address->markAsDefault();
        }

        return back()->with('status', 'Alamat berhasil ditambahkan.');
    }

    /**
     * Perbarui alamat milik user.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        $address->update($this->validateAddress($request));

        return back()->with('status', 'Alamat berhasil diperbarui.');
    }

    /**
     * Hapus alamat milik user.
     */
    public function destroy(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        $wasDefault = $address->is_default;
        $address->delete();

        // Alihkan status default ke alamat tersisa yang paling awal.
        if ($wasDefault) {
            $request->user()->addresses()->orderBy('id')->first()?->markAsDefault();
        }

        return back()->with('status', 'Alamat berhasil dihapus.');
    }

    /**
     * Jadikan alamat ini alamat utama.
     */
    public function setDefault(Request $request, Address $address): RedirectResponse
    {
        abort_unless($address->user_id === $request->user()->id, 404);

        $address->markAsDefault();

        return back()->with('status', 'Alamat utama diperbarui.');
    }

    /**
     * Aturan validasi data alamat.
     */
    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'recipient_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address_detail' => ['required', 'string', 'max:1000'],
            'city' => ['required', 'string', 'max:255'],
            'postal_code' => ['required', 'string', 'max:10'],
        ]);
    }
}
