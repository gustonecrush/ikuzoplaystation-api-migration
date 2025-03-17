<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Resources\ContentSectionResource;
use App\Models\ContentSection;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ContentSectionController extends Controller
{
    private $sections;
    private $section;

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->sections = ContentSection::all();
        $contentSectionsResource = ContentSectionResource::collection(
            $this->sections
        );
        return $this->sendResponse(
            $contentSectionsResource,
            'Succesfully get all content games!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'content' => 'required|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('content');
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        if ($request->file('content')->isValid()) {
            $file = StorageHelper::store($request->file('content'), to: 'sections');
            $contentSection = new ContentSection();
            $contentSection->name = $request->name;
            $contentSection->title = $request->title;
            $contentSection->description = $request->description;
            $contentSection->is_button = $request->is_button;
            $contentSection->link_button = $request->link_button;
            $contentSection->label_button = $request->label_button;
            $contentSection->content = $file;
            $contentSection->content_type =  $request->file('content')->getMimeType();
            $contentSection->save();

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
     * Update the specified resource in storage.
     */

    public function updateById(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'nullable|file|mimes:jpeg,jpg,png,mp4,mov,avi|max:2048',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors()->first('content');
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $contentSection = ContentSection::find($id);


        if ($request->hasFile('content')) {
            if ($request->file('content')->isValid()) {
                // Delete the previous file if it exists
                Storage::delete($contentSection->content);

                // Store the new file
                $file = StorageHelper::store($request->file('content'), to: 'sections');
                $contentSection->content = $file;
                $contentSection->content_type = $request->file('content')->getMimeType();
            } else {
                return $this->sendError(
                    'Error',
                    'File upload failed, please check your file!',
                    Response::HTTP_BAD_REQUEST
                );
            }
        }

        // Update other fields
        $contentSection->name = $request->name;
        $contentSection->title = $request->title;
        $contentSection->description = $request->description;
        $contentSection->is_button = $request->is_button;
        $contentSection->link_button = $request->link_button;
        $contentSection->label_button = $request->label_button;
        $contentSection->save();

        return $this->sendResponse(
            $contentSection,
            'Content updated successfully!'
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->section = ContentSection::where('id', '=', $id)->first();

        try {
            Storage::delete($this->section->content);
            $this->section->delete();
        } catch (Exception $e) {
            return $this->sendError(
                'Internal Server Error',
                $e->getMessage(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return $this->sendResponse([], 'Successfully delete section!');
    }
}
