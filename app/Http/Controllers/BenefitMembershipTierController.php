<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BenefitMembershipTier;
use Illuminate\Http\Response;

class BenefitMembershipTierController extends Controller
{
    // GET /api/benefit-membership-tiers
    public function index()
    {
        $benefits = BenefitMembershipTier::with('membershipTiers')->get();
        return response()->json($benefits, Response::HTTP_OK);
    }

    // GET /api/benefit-membership-tiers/{id}
    public function show($id)
    {
        $benefit = BenefitMembershipTier::with('membershipTiers')->find($id);

        if (!$benefit) {
            return response()->json(['message' => 'Benefit not found'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($benefit, Response::HTTP_OK);
    }

    // POST /api/benefit-membership-tiers
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name_benefit' => 'required|string|max:255',
            'duration_benefit' => 'required|integer',
            'kuota_benefit' => 'required|integer',
            'syarat_benefit' => 'nullable|string',
            'id_membership_tier' => 'required|exists:membership_tiers,id',
        ]);

        $benefit = BenefitMembershipTier::create($validated);
        return response()->json($benefit, Response::HTTP_CREATED);
    }

    // PUT /api/benefit-membership-tiers/{id}
    public function update(Request $request, $id)
    {
        $benefit = BenefitMembershipTier::find($id);

        if (!$benefit) {
            return response()->json(['message' => 'Benefit not found'], Response::HTTP_NOT_FOUND);
        }

        $validated = $request->validate([
            'name_benefit' => 'sometimes|required|string|max:255',
            'duration_benefit' => 'sometimes|required|integer',
            'kuota_benefit' => 'sometimes|required|integer',
            'syarat_benefit' => 'nullable|string',
            'id_membership_tier' => 'sometimes|required|exists:membership_tiers,id',
        ]);

        $benefit->update($validated);

        return response()->json($benefit, Response::HTTP_OK);
    }

    // DELETE /api/benefit-membership-tiers/{id}
    public function destroy($id)
    {
        $benefit = BenefitMembershipTier::find($id);

        if (!$benefit) {
            return response()->json(['message' => 'Benefit not found'], Response::HTTP_NOT_FOUND);
        }

        $benefit->delete();
        return response()->json(['message' => 'Benefit deleted successfully'], Response::HTTP_OK);
    }
}
