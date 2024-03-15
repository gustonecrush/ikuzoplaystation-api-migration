<?php

namespace App\Http\Controllers;

use App\Helpers\StorageHelper;
use App\Http\Resources\ContentGameResource;
use App\Models\ContentGame;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use League\Flysystem\Visibility;
use Illuminate\Support\Str;

class ContentGameController extends Controller
{
    private $games;
    private $game;

    public function __construct() 
    {
        $this->middleware('auth:api', ['except' => ['index']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->games = ContentGame::all();
        $contentGamesResource = ContentGameResource::collection($this->games);
        return $this->sendResponse(
            $contentGamesResource,
            'Succesfully get all content games!'
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:jpeg,jpg,png,webp,avif|max:2048',
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
            $file = StorageHelper::store($request->file('file'), to: 'games');
            $contentGame = new ContentGame();
            $contentGame->file_name = $file;
            $contentGame->save();

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
        $this->game = ContentGame::where('id', '=', $id)->first();

        try {
            Storage::delete($this->game->file_name);
            $this->game->delete();
        } catch (Exception $e) {
            return $this->sendError('Internal Server Error', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->sendResponse([], "Successfully delete game!");
    }
}