<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Halaman Profil Saya.
     */
    public function show(Request $request): View
    {
        return view('profile.show', [
            'user' => $request->user(),
            'ordersCount' => $request->user()->orders()->count(),
            'addressesCount' => $request->user()->addresses()->count(),
        ]);
    }

    /**
     * Perbarui profil: nama, dan (opsional) kata sandi baru.
     */
    public function update(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
        ];

        if ($request->filled('new_password')) {
            $rules += [
                'current_password' => ['required', 'current_password'],
                'new_password' => ['required', 'string', 'min:8', 'confirmed', Rules\Password::defaults()],
            ];
        }

        $validated = $request->validate($rules);

        $data = ['name' => $validated['name']];

        if (isset($validated['new_password'])) {
            $data['password'] = Hash::make($validated['new_password']);
        }

        $request->user()->forceFill($data)->save();

        return back()->with('status', 'Profil berhasil diperbarui.');
    }
}
