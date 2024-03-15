<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Resources\ContentFacilityResource;
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
    public function index(Request $request)
    {
        $name = $request->input('name');

        if ($name) {
            $this->facilities = ContentFacility::where('name', $name)->get();
        } else {
            $this->facilities = ContentFacility::all();
        }

        $contentFacilitiesResource = ContentFacilityResource::collection($this->facilities);

        return $this->sendResponse(
            $contentFacilitiesResource,
            'Successfully get all content facilities!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pict' => 'required|file|mimes:jpeg,jpg,png|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('pict'); 
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($request->file('pict')->isValid()) {
            $file = StorageHelper::store($request->file('pict'), to: 'facilities');
            $contentFacility = new ContentFacility();
            $contentFacility->name = $request->name;
            $contentFacility->price = $request->price;
            $contentFacility->capacity = $request->capacity;
            $contentFacility->benefits = $request->benefits;
            $contentFacility->pict = $file;
            $contentFacility->save();

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
            Storage::delete($this->facility->pict);
            $this->facility->delete();
        } catch (Exception $e) {
            return $this->sendError('Internal Server Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse([], "Successfully delete facility!");
    }
}