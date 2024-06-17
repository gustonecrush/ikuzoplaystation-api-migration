<?php

namespace App\Http\Controllers;

use App\Http\Resources\OpenCloseTimeResource;
use App\Models\OpenCloseTime;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class OpenCloseTimeController extends Controller
{
    private $times;
    private $time;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->times = OpenCloseTime::query();

        $date = $request->query('selected_date');

        if ($date !== null) {
            $this->times->where('date', $date);
        }

        $timeResource = OpenCloseTimeResource::collection($this->times->get());

        return $this->sendResponse(
            $timeResource,
            'Successfully delete time open!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'date' => 'required',
            'open_time' => 'required',
            'close_time' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->time = new OpenCloseTime();
        $this->time->date = $request->date;
        $this->time->open_time = $request->open_time;
        $this->time->close_time = $request->close_time;
        $this->time->save();

        return $this->sendResponse([], 'Time uploaded successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateById(Request $request, string $id)
    {
        $this->time = OpenCloseTime::find($id);

        $validator = Validator::make($request->all(), [
            'open_time' => 'required',
            'close_time' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->time->open_time = $request->open_time;
        $this->time->close_time = $request->close_time;
        $this->time->save();

        return $this->sendResponse([], 'Time updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->time = OpenCloseTime::where('id', '=', $id)->first();

        if (!$this->time) {
            return $this->sendError(
                'Data not found!',
                [],
                Response::HTTP_NOT_FOUND
            );
        }

        $this->time->delete();

        return $this->sendResponse([], 'Successfully delete time!');
    }
}
