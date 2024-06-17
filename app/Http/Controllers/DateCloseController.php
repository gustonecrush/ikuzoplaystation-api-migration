<?php

namespace App\Http\Controllers;

use App\Http\Resources\DateCloseResource;
use App\Models\DateClose;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class DateCloseController extends Controller
{
    private $dates;
    private $date;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->dates = DateClose::query();

        $date = $request->query('selected_date');

        if ($date !== null) {
            $this->dates->where('date', $date);
        }

        $timeResource = DateCloseResource::collection($this->dates->get());

        return $this->sendResponse(
            $timeResource,
            'Successfully delete date close!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'start_date' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->date = new DateClose();
        $this->date->start_date = $request->start_date;
        $this->date->end_date = $request->end_date;
        $this->date->save();

        return $this->sendResponse([], 'Date Close uploaded successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateById(Request $request, string $id)
    {
        $this->date = DateClose::find($id);

        $validator = Validator::make($request->all(), [
            'start_date' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->date->start_date = $request->start_date;
        $this->date->end_date = $request->end_date;
        $this->date->save();

        return $this->sendResponse([], 'Date Close updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->date = DateClose::where('id', '=', $id)->first();

        if (!$this->date) {
            return $this->sendError(
                'Data not found!',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->date->delete();

        return $this->sendResponse([], 'Successfully delete date close!');
    }
}
