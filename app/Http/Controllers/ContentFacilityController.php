<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Resources\ContentGameResource;
use App\Models\ContentFacility;
use App\Models\ContentGame;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\Visibility;
use Illuminate\Support\Str;

class ContentFacilityController extends Controller
{
    private $facilities;
    private $facility;

    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->facilities = ContentFacility::all();
        $contentFacilitiesResource = ContentGameResource::collection($this->facilities);
        return $this->sendResponse(
            $contentFacilitiesResource,
            'Succesfully get all content games!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('file'); 
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($request->file('file')->isValid()) {
            $file = StorageHelper::store($request->file('file'), to: 'facilities');
            $contentFacilitiy = new ContentFacility();
            $contentFacilitiy->file_name = $file;
            $contentFacilitiy->save();

            return $this->sendResponse(
                $file,
                'File uploaded successfully!'
            );
        } else {
            return $this->sendError(
                'Error',
                'File uploaded failed, please check your size!',
                Response::HTTP_BAD_REQUEST
            );
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->facility = ContentFacility::where('id', '=', $id)->first();

        try {
            Storage::delete($this->facility->file_name);
            $this->facility->delete();
        } catch (Exception $e) {
            return $this->sendError('Internal Server Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse([], "Successfully delete facility!");
    }
}