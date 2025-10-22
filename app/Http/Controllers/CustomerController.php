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

    public function index()
    {
        $customers = Customer::all();

        return response()->json([
            'status' => 'success',
            'data' => $customers,
        ]);
    }

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
            'benefits' => 'nullable'
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
            'benefits' => 'nullable'
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

        // 1. Find customer by phone number
        $customer = Customer::where('phone_number', $request->phone_number)->first();

        if (!$customer) {
            return response()->json([
                'message' => 'Customer not found'
            ], 404);
        }

        // 2. Get active membership + membership tier info
        $membership = DB::table('customer_memberships')
            ->join('membership_tiers', 'customer_memberships.id_membership', '=', 'membership_tiers.id')
            ->where('customer_memberships.id_customer', $customer->id)
            ->where('customer_memberships.status_tier', 'Active')
            ->select(
                'customer_memberships.*',
                'membership_tiers.full_name as tier_name',
                'membership_tiers.price',
                'membership_tiers.period',
                'membership_tiers.benefits',
                'membership_tiers.icon'
            )
            ->first();

        // 3. Return response
        if ($membership) {
            return response()->json([
                'message' => 'Customer is an active member',
                'customer' => [
                    'id' => $customer->id,
                    'full_name' => $customer->full_name,
                    'phone_number' => $customer->phone_number,
                ],
                'membership' => [
                    'status_tier' => $membership->status_tier,
                    'start_periode' => $membership->start_periode,
                    'end_periode' => $membership->end_periode,
                    'status_payment' => $membership->status_payment,
                    'status_benefit' => $membership->status_benefit,
                    'status_birthday_treat' => $membership->status_birthday_treat,
                    'kuota_weekly' => $membership->kuota_weekly,
                    'membership_count' => $membership->membership_count,
                ],
                'membership_tier' => [
                    'tier_name' => $membership->tier_name,
                    'price' => $membership->price,
                    'period' => $membership->period,
                    'benefits' => $membership->benefits,
                    'icon' => $membership->icon,
                ]
            ], 200);
        } else {
            return response()->json([
                'message' => 'Customer does not have an active membership'
            ], 400);
        }
    }
}
