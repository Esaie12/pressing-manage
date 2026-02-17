<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileUiController extends Controller
{
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.auth()->id()],
            'gender' => ['nullable', 'in:homme,femme,autre'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'photo' => ['nullable', 'image', 'max:2048'],
            'remove_photo' => ['nullable', 'boolean'],
        ]);

        $user = auth()->user();
        if ((bool) ($data['remove_photo'] ?? false) && $user->photo_path) {
            Storage::disk('public')->delete($user->photo_path);
            $data['photo_path'] = null;
        }

        if ($request->hasFile('photo')) {
            if ($user->photo_path) {
                Storage::disk('public')->delete($user->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('profiles', 'public');
        }

        unset($data['photo'], $data['remove_photo']);

        $user->update($data);

        return back()->with('success', 'Profil mis à jour.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($data['current_password'], auth()->user()->password)) {
            return back()->with('error', 'Mot de passe actuel incorrect.');
        }

        auth()->user()->update(['password' => Hash::make($data['password'])]);

        return back()->with('success', 'Mot de passe modifié.');
    }
}
