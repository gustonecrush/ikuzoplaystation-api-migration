<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::query();

        $reserveDate = $request->query('reserve_date');
        $position = $request->query('position');
        $reserveId = $request->query('reserve_id');

        if ($reserveDate !== null) {
            $reservations->where('reserve_date', $reserveDate);
        }

        if ($position !== null) {
            $reservations->where('position', $position);
        }

        if ($reserveId !== null) {
            $reservations->where('reserve_id', $reserveId);
        }

        $filteredReservations = $reservations->get();

        return response()->json($filteredReservations);
    }

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

    public function updateById(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status_reserve' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $reservation = Reservation::where('reserve_id', '=', $id)->first();
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }

        $reservation->update($request->all());

        return response()->json($reservation, 200);
    }

    public function destroy($id)
    {
        $reservation = Reservation::where('id', '=', $id);
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
