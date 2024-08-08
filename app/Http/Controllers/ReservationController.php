<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use Illuminate\Support\Facades\Validator;
use App\Helpers\StorageHelper;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ReservationController extends Controller
{
    public function index(Request $request)
    {
        $reservations = Reservation::query();

        $reserveDate = $request->query('reserve_date');
        $position = $request->query('position');
        $reserveId = $request->query('reserve_id');
        $status = $request->query('status');
        $pending = $request->query('pending');

        if ($reserveDate !== null) {
            $reservations->where('reserve_date', $reserveDate);
        }

        if ($position !== null) {
            $reservations->where('position', $position);
        }

        if ($reserveId !== null) {
            $reservations->where('reserve_id', $reserveId);
        }

        if ($status !== null && $pending !== null) {
            $reservations->where(function ($query) use ($status, $pending) {
                $query->where('status_reserve', $status)
                    ->orWhere('status_reserve', $pending);
            });
        }

        $filteredReservations = $reservations->get();

        return response()->json($filteredReservations);
    }

    public function exportExcel(Request $request)
    {
        $reservations = Reservation::query();

        $dateStart = $request->query('date_start');
        $dateEnd = $request->query('date_end');

        if ($dateStart !== null && $dateEnd !== null) {
            $reservations->whereBetween('reserve_date', [$dateStart, $dateEnd]);
        }

        $filteredReservations = $reservations->get();

        return response()->json($filteredReservations);
    }


    public function statistics(Request $request)
    {
        // Ambil semua reservasi
        $reservations = Reservation::all();

        // Hitung total harga dari kolom price dengan kondisi status_reserve adalah settlement
        $prices = Reservation::where('status_reserve', 'settlement')->sum('price');

        // Hitung total data dengan kondisi status_reserve adalah settlement
        $success_payment = Reservation::where('status_reserve', 'settlement')->count();

        // Hitung total data dengan kondisi status_reserve adalah expire atau pending
        $pending_payment = Reservation::where('status_reserve', 'expire')
            ->orWhere('status_reserve', 'pending')
            ->count();

        // Buat response JSON
        $statistics = [
            'reservations' => $reservations->count(),
            'prices' => $prices,
            'success_payment' => $success_payment,
            'pending_payment' => $pending_payment,
        ];

        return response()->json($statistics);
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

        $invoice = '';
        $reservation = Reservation::where('reserve_id', '=', $id)->first();

        if ($request->file('invoice')) {
            $invoice = StorageHelper::storePng($request->file('invoice'), to: 'invoices');
            $reservation->invoice = $invoice;
        }

        if ($request->status_reserve) {
            $reservation->status_reserve = $request->status_reserve;
        }

        if ($request->status_payment) {
            $reservation->status_payment = $request->status_payment;
        }



        if (!$reservation) {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }




        $reservation->save();

        return response()->json($reservation, 200);
    }

    public function destroy($id)
    {
        $reservation = Reservation::where('reserve_id', '=', $id)->first();

        if (!$reservation) {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }


        try {
            if ($reservation->invoice) {
                Storage::delete($reservation->invoice);
            }

            $reservation->delete();
        } catch (Exception $e) {
            return $this->sendError('Internal Server Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(
            ['message' => 'Reservation deleted successfully'],
            200
        );
    }

    public function deleteByOrderId($id)
    {
        $reservation = Reservation::where('reserve_id', '=', $id)->first();

        if (!$reservation) {
            return response()->json(
                ['message' => 'Reservation not found'],
                404
            );
        }


        try {
            if ($reservation->invoice) {
                Storage::delete($reservation->invoice);
            }

            $reservation->delete();
        } catch (Exception $e) {
            return $this->sendError('Internal Server Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json(
            ['message' => 'Reservation deleted successfully'],
            200
        );
    }
}
