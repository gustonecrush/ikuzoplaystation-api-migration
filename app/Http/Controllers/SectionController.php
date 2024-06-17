<?php

namespace App\Http\Controllers;

use App\Http\Resources\SectionResource;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SectionController extends Controller
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
        $this->sections = Section::all();
        $sectionsResource = SectionResource::collection($this->sections);
        return $this->sendResponse(
            $sectionsResource,
            'Succesfully get all sections detail!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $section = new Section();
        $section->name = $request->title;
        $section->title = $request->description;

        return $this->sendResponse([], 'Section uploaded successfully!');
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateById(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description' => 'required',
        ]);

        if ($validator->fails()) {
            $errorMessage = $validator->errors();
            return $this->sendError(
                'Error',
                $errorMessage,
                Response::HTTP_BAD_REQUEST
            );
        }

        $section = Section::find($id);
        $section->title = $request->title;
        $section->description = $request->description;
        $section->save();

        return $this->sendResponse($section, 'Section updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $this->section = Section::where('id', '=', $id)->first();

        return $this->sendResponse([], 'Successfully delete section!');
    }
}
