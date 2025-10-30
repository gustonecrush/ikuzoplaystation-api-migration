<?php

namespace App\Http\Controllers;

use App\Models\ReservationSavingTime;
use Illuminate\Http\Request;

class ReservationSavingTimeController extends Controller
{
    // Get data (optional filters: id or id_reservation)
    public function index(Request $request)
    {
        $query = ReservationSavingTime::query();

        if ($request->has('id')) {
            $query->where('id', $request->id);
        }

        if ($request->has('id_reservation')) {
            $query->where('id_reservation', $request->id_reservation);
        }

        return response()->json($query->get());
    }

    // Create
    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_reservation' => 'required|exists:reservations,id',
            'date_saving' => 'required|date',
            'start_time_saving' => 'required|string',
            'end_time_saving' => 'required|string',
        ]);

        $savingTime = ReservationSavingTime::create($validated);

        return response()->json($savingTime, 201);
    }

    // Update
    public function update(Request $request, $id)
    {
        $savingTime = ReservationSavingTime::where('id', '=', $id)->first();

        $validated = $request->validate([
            'is_active' => 'sometimes|string'
        ]);

        $savingTime->update($validated);

        return response()->json($savingTime);
    }

    // Delete
    public function destroy($id)
    {
        $savingTime = ReservationSavingTime::findOrFail($id);
        $savingTime->delete();

        return response()->json(['message' => 'Deleted successfully']);
    }
}
