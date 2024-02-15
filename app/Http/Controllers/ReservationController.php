<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    // Display a listing of the reservations.
    public function index(Request $request)
    {
        $reservations = Reservation::all();

        $reserveDate = $request->query('reserve_date');
        $position = $request->query('position');

        if ($reserveDate != null) {
            $query = Reservation::where('reserve_date', $reserveDate);

            if ($position !== null) {
                $query->where('position', $position);
            }

            $reservations = $query->get();
        }

        return response()->json($reservations);
    }

    // Store a newly created reservation in storage.
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'reserve_name' => 'required|string',
            'reserve_date' => 'required|date',
            'reserve_start_time' => 'required',
            'reserve_end_time' => 'required',
            'price' => 'required',
            'location' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $reservation = Reservation::create($request->all());
        return response()->json($reservation, 201);
    }

    // Display the specified reservation.
    public function show($id)
    {
        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }
        return response()->json($reservation);
    }

    // Update the specified reservation in storage.
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'reserve_name' => 'string',
            'reserve_date' => 'date',
            'reserve_start_time' => 'date_format:H:i:s',
            'reserve_end_time' => 'date_format:H:i:s',
            'price' => 'numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $reservation = Reservation::find($id);
        if (!$reservation) {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }

        $reservation->update($request->all());
        return response()->json($reservation, 200);
    }

    // Remove the specified reservation from storage.
    public function destroy($id)
    {
        $reservation = Reservation::where('reserve_id', '=', $id);
        if (!$reservation) {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }

        $reservation->delete();
        return response()->json(
            ['message' => 'Reservation deleted successfully'],
            200
        );
    }
}
