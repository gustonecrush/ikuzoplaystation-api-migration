<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Catalog;
use Illuminate\Support\Facades\Validator;
use App\Helpers\StorageHelper;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
use Exception;

class CatalogController extends Controller
{
    public function index(Request $request)
    {
        $no_seat = $request->input('no_seat');

        if ($no_seat) {
            $catalogs = Catalog::where('no_seat', $no_seat)->get();
        } else {
            $catalogs = Catalog::all();
        }

        return response()->json($catalogs);
    }

    public function search(Request $request)
    {
        $catalog_txt = $request->input('catalog_txt');

        if ($catalog_txt) {
            // Use LIKE to filter catalog_txt with partial matching and return all columns
            $catalogs = Catalog::where('catalog_txt', 'like', '%' . $catalog_txt . '%')
                ->orderBy('no_seat')
                ->get()
                ->groupBy('catalog_txt');
        } else {
            // Return all catalogs grouped by catalog_txt with all columns
            $catalogs = Catalog::orderBy('no_seat')
                ->get()
                ->groupBy('catalog_txt');
        }

        // Check if any catalogs were found
        if ($catalogs->isEmpty()) {
            return response()->json(['message' => 'Catalog not found'], 404);
        }

        // Format the response
        $formattedCatalogs = $catalogs->map(function ($items, $catalog_txt) {
            return [
                'catalog_txt' => $catalog_txt,
                'catalog_img' => $items->first()->catalog_img, // Assuming all items have the same catalog_img
                'no_seat' => $items->pluck('no_seat')->toArray(),
            ];
        });

        // Return the formatted catalog data as JSON
        return response()->json($formattedCatalogs);
    }





    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'no_seat' => 'required|integer',
            'catalog_txt' => 'required',
            'catalog_img' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $catalogImgPath = '';
        if ($request->hasFile('catalog_img')) {
            $catalogImgPath = StorageHelper::storePng($request->file('catalog_img'), to: 'catalogs');
        }

        $catalog = Catalog::create([
            'no_seat' => $request->input('no_seat'),
            'catalog_txt' => $request->input('catalog_txt'),
            'catalog_img' => $catalogImgPath,
        ]);

        return response()->json($catalog, 201);
    }

    public function show($id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json(
                ['message' => 'Catalog not found'],
                404
            );
        }
        return response()->json($catalog);
    }

    public function updateById(Request $request, $id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json(
                ['message' => 'Catalog not found'],
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'no_seat' => 'sometimes|required|integer',
            'catalog_txt' => 'sometimes|required',
            'catalog_img' => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        if ($request->hasFile('catalog_img')) {
            if ($catalog->catalog_img) {
                Storage::delete($catalog->catalog_img);
            }
            $catalogImgPath = StorageHelper::storePng($request->file('catalog_img'), to: 'catalogs');
            $catalog->catalog_img = $catalogImgPath;
        }

        if ($request->has('no_seat')) {
            $catalog->no_seat = $request->input('no_seat');
        }

        $catalog->save();

        return response()->json($catalog, 200);
    }

    public function destroy($id)
    {
        $catalog = Catalog::find($id);
        if (!$catalog) {
            return response()->json(
                ['message' => 'Catalog not found'],
                404
            );
        }

        try {
            if ($catalog->catalog_img) {
                Storage::delete($catalog->catalog_img);
            }
            $catalog->delete();
        } catch (Exception $e) {
            return response()->json(
                ['message' => 'Internal Server Error', 'error' => $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        return response()->json(
            ['message' => 'Catalog deleted successfully'],
            200
        );
    }
}
