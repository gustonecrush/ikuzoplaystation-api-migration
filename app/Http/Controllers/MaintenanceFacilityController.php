<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalog;
use Illuminate\Support\Facades\Validator;
use App\Helpers\StorageHelper;
use App\Models\MaintenanceFacility;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Exception;

class MaintenanceFacilityController extends Controller
{
    public function index(Request $request)
    {
        $no_seat = $request->input('no_seat');

        if ($no_seat) {
            $maintenances = MaintenanceFacility::where('no_seat', $no_seat)->get();
        } else {
            $maintenances = MaintenanceFacility::all();
        }

        return response()->json($maintenances);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_seat' => 'required|integer',
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $maintenance = MaintenanceFacility::create([
            'no_seat' => $request->input('no_seat'),
            'status' => $request->input('status'),
        ]);

        return response()->json($maintenance, 201);
    }

    public function show($id)
    {
        $maintenance = MaintenanceFacility::find($id);
        if (!$maintenance) {
            return response()->json(
                ['message' => 'Maintenance not found'],
                404
            );
        }
        return response()->json($maintenance);
    }

    public function updateById(Request $request, $id)
    {
        $maintenance = MaintenanceFacility::find($id);
        if (!$maintenance) {
            return response()->json(
                ['message' => 'Maintenance not found'],
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'no_seat' => 'sometimes|required|integer',
            'status' => 'sometimes|required',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }


        if ($request->has('no_seat')) {
            $maintenance->no_seat = $request->input('no_seat');
        }

        if ($request->has('status')) {
            $maintenance->status = $request->input('status');
        }

        $maintenance->save();

        return response()->json($maintenance, 200);
    }

    public function destroy($id)
    {
        $maintenance = MaintenanceFacility::find($id);
        if (!$maintenance) {
            return response()->json(
                ['message' => 'Maintenance not found'],
                404
            );
        }

        try {
            $maintenance->delete();
        } catch (Exception $e) {
            return response()->json(
                ['message' => 'Internal Server Error', 'error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            ['message' => 'Maintenance deleted successfully'],
            200
        );
    }
}
