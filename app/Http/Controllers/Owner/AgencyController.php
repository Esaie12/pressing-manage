<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;

class AgencyController extends Controller
{
    public function index(Request $request)
    {
        return response()->json(
            Agency::where('pressing_id', $request->user()->pressing_id)->get()
        );
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string'],
        ]);

        $agency = Agency::create($data + ['pressing_id' => $request->user()->pressing_id]);

        return response()->json($agency, 201);
    }
}
