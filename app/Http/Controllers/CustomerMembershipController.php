<?php

namespace App\Http\Controllers;

use App\Models\CustomerMembership;
use Illuminate\Http\Request;

class CustomerMembershipController extends Controller
{
    public function index()
    {
        return CustomerMembership::with(['customer', 'membershipTier'])->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'id_membership' => 'required|exists:membership_tiers,id',
            'start_periode' => 'required|date',
            'end_periode' => 'required|date|after_or_equal:start_periode',
            'status_tier' => 'required|string',
            'status_benefit' => 'required|string',
            'status_payment' => 'required|string',
            'status_birthday_treat' => 'required|string',
            'kuota_weekly' => 'nullable|integer|min:0',
            'membership_count' => 'nullable|integer|min:0',
        ]);

        $membership = CustomerMembership::create($validated);

        return response()->json($membership, 201);
    }

    public function show($id)
    {
        $membership = CustomerMembership::with(['customer', 'membershipTier'])->findOrFail($id);
        return response()->json($membership);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'id_customer' => 'required|exists:customers,id',
            'id_membership' => 'required|exists:membership_tiers,id',
            'start_periode' => 'required|date',
            'end_periode' => 'required|date|after_or_equal:start_periode',
            'status_tier' => 'required|string',
            'status_benefit' => 'required|string',
            'status_payment' => 'required|string',
            'status_birthday_treat' => 'required|string',
            'kuota_weekly' => 'nullable|integer|min:0',
            'membership_count' => 'nullable|integer|min:0',
        ]);

        $membership = CustomerMembership::findOrFail($id);
        $membership->update($validated);

        return response()->json($membership);
    }

    public function destroy($id)
    {
        $membership = CustomerMembership::findOrFail($id);
        $membership->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}