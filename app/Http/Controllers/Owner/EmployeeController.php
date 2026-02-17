<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            User::where('role', User::ROLE_EMPLOYEE)
                ->where('pressing_id', $request->user()->pressing_id)
                ->with('agency')
                ->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'agency_id' => ['required', 'exists:agencies,id'],
        ]);

        $employee = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => User::ROLE_EMPLOYEE,
            'pressing_id' => $request->user()->pressing_id,
            'agency_id' => $data['agency_id'],
        ]);

        return response()->json($employee, 201);
    }
}
