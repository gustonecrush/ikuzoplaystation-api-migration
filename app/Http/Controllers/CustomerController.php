<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string',
            'username' => 'required|string|unique:customers',
            'phone_number' => 'nullable|string|unique:customers,phone_number',
            'whatsapp_number' => 'nullable|string',
            'password' => 'required|string|min:6',
            'birth_date' => 'nullable|string',
            'awareness_source' => 'nullable|string',
        ], [
            'phone_number.unique' => 'Nomor telepon sudah digunakan.',
            'username.unique' => 'Username sudah digunakan.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $validated = $validator->validated();
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

    public function checkMembership(Request $request)
    {
        $request->validate([
            'phone_number' => 'required|string'
        ]);

        // Find customer by phone number
        $customer = Customer::where('phone_number', $request->phone_number)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        // Check if the customer has an active membership
        $membership = DB::table('memberships')
            ->where('id_customer', $customer->id)
            ->where('status_tier', 'active')
            ->first();

        if ($membership) {
            return response()->json([
                'message' => 'Customer is an active member',
                'customer' => [
                    'id' => $customer->id,
                    'full_name' => $customer->full_name,
                    'phone_number' => $customer->phone_number,
                    'status_tier' => $membership->status_tier,
                    'start_periode' => $membership->start_periode,
                    'end_periode' => $membership->end_periode,
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'Customer does not have an active membership'
            ], 200);
        }
    }
}