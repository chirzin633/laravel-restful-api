<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index() {}

    public function create(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['email', 'required'],
            'password' => ['required', 'min:8'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string']
        ]);

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password'])
            ]);
            $user->profile()->create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name']
            ]);

            DB::commit();

            return response()->json([
                'message' => 'User & profile has been created!',
                'data' => $user->load('profile')
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create user & profile',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function update() {}

    public function delete() {}
}
