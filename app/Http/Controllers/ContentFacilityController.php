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
            $file = StorageHelper::store($request->file('pict'), 'facilities');

            // Debug file path
            try {
                $file = StorageHelper::store($request->file('pict'), 'facilities');

                // Check if file path is empty or invalid
                if (!$file) {
                    return $this->sendError(
                        'Error',
                        'File path is invalid or empty after storage!',
                        Response::HTTP_BAD_REQUEST
                    );
                }
            } catch (\Exception $e) {
                // Catch the specific exception and return the error message
                return $this->sendError(
                    'Error',
                    'File storage failed: ' . $e->getMessage(),
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }


            $contentFacility = new ContentFacility();
            $contentFacility->name = $request->name;
            $contentFacility->price = $request->price;
            $contentFacility->capacity = $request->capacity;
            $contentFacility->benefits = $request->benefits;
            $contentFacility->pict = $file;

            // Check if save operation succeeds
            if (!$contentFacility->save()) {
                return $this->sendError(
                    'Error',
                    'Failed to save content facility!',
                    Response::HTTP_INTERNAL_SERVER_ERROR
                );
            }

            return $this->sendResponse(
                $file,
                'File uploaded and content facility saved successfully!'
            );
        } else {
            return $this->sendError(
                'Error',
                'File upload failed, please check your size!',
                Response::HTTP_BAD_REQUEST
            );
        }
    }


    /**
     * Update the specified resource in storage.
     */

    public function updateById(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'pict' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:204800',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('pict');
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $contentFacility = ContentFacility::find($id);


        if ($request->hasFile('pict')) {
            if ($request->file('pict')->isValid()) {
                // Delete the previous file if it exists
                Storage::delete($contentFacility->pict);

                // Store the new file
                $file = StorageHelper::store($request->file('pict'), to: 'facilities');
                $contentFacility->pict = $file;
            } else {
                return $this->sendError(
                    'Error',
                    'File upload failed, please check your file!',
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        // Update other fields
        $contentFacility->name = $request->name;
        $contentFacility->price = $request->price;
        $contentFacility->capacity = $request->capacity;
        $contentFacility->benefits = $request->benefits;
        $contentFacility->save();

        return $this->sendResponse(
            $contentFacility,
            'Content updated successfully!'
        );
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