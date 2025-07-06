<?php

namespace App\Http\Controllers;

use App\Models\MembershipTier;
use Illuminate\Http\Request;

class MembershipTierController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index', 'show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(MembershipTier::all());
    }

    /*
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string',
            'price' => 'required|numeric',
            'period' => 'required|string',
            'benefits' => 'required|string',
            'icon' => 'nullable|string',
        ]);

        $tier = MembershipTier::create($validated);

        return response()->json($tier, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $tier = MembershipTier::findOrFail($id);
        return response()->json($tier);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $tier = MembershipTier::find($id);

        $validated = $request->validate([
            'full_name' => 'sometimes|required|string',
            'price' => 'sometimes|required|numeric',
            'period' => 'sometimes|required|string',
            'benefits' => 'sometimes|required|string',
            'icon' => 'nullable|string',
        ]);

        $tier->update($validated);

        return response()->json($validated);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $tier = MembershipTier::findOrFail($id);
        $tier->delete();

        return response()->json(['message' => 'Membership tier deleted successfully.']);
    }
}
