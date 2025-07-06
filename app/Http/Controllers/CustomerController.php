<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function register(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'username' => 'required|string|unique:customers',
            'phone_number' => 'nullable|string',
            'whatsapp_number' => 'nullable|string',
            'password' => 'required|string|min:6',
            'birth_date' => 'nullable|string',
            'awareness_source' => 'nullable|string',
        ]);

        $validated['password'] = bcrypt($validated['password']);

        $customer = Customer::create($validated);

        return response()->json(['message' => 'Customer registered successfully'], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (!$token = Auth::guard('customer')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => 72000
        ]);
    }

    public function profile()
    {
        return response()->json(Auth::guard('customer')->user());
    }

    public function updateProfile(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth('customer')->user();

        $validated = $request->validate([
            'full_name' => 'sometimes|required|string',
            'username' => 'sometimes|required|string|unique:customers,username,' . $customer->id,
            'phone_number' => 'nullable|string',
            'whatsapp_number' => 'nullable|string',
            'birth_date' => 'nullable|string',
            'awareness_source' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'data' => $customer
        ]);
    }

    public function resetPassword(Request $request)
    {
        /** @var \App\Models\Customer $customer */
        $customer = auth('customer')->user();

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        if (!Hash::check($validated['current_password'], $customer->password)) {
            return response()->json(['message' => 'Current password is incorrect.'], 400);
        }

        $customer->password = bcrypt($validated['new_password']);
        $customer->save();

        return response()->json(['message' => 'Password updated successfully.']);
    }


    public function logout()
    {
        Auth::guard('customer')->logout();
        return response()->json(['message' => 'Logged out']);
    }
}
