<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pressing;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class OwnerController extends Controller
{
    public function index()
    {
        return response()->json(
            User::where('role', User::ROLE_OWNER)->with('pressing')->latest()->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'pressing_name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
        ]);

        $owner = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_OWNER,
        ]);

        $pressing = Pressing::create([
            'name' => $data['pressing_name'],
            'owner_id' => $owner->id,
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
        ]);

        $owner->update(['pressing_id' => $pressing->id]);

        return response()->json($owner->load('pressing'), 201);
    }
}
