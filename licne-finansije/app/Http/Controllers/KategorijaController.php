<?php

namespace App\Http\Controllers;

use App\Http\Resources\KategorijaResource;
use App\Models\Kategorija;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KategorijaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return KategorijaResource::collection(Kategorija::all());
    }


    //GET /kategorije/korisnik/{id}
    public function userCategories($idKorisnik)
    {
        $kategorije = Kategorija::where('idKorisnik', $idKorisnik)->get();
        return KategorijaResource::collection($kategorije);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'required|integer|exists:users,id',
            'naziv' => 'required|string|max:100',
            'opis' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $kategorija = Kategorija::create($data);

        return response()->json(new KategorijaResource($kategorija), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new KategorijaResource(Kategorija::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Kategorija $kategorija)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $kategorija = Kategorija::find($id);

        if (! $kategorija) {
            return response()->json(['message' => 'Kategorija nije pronadjena'], 404);
        }

        $validator = Validator::make($request->all(), [
            'naziv' => 'sometimes|string|max:100',
            'opis' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();
        $kategorija->update($data);

        return response()->json(new KategorijaResource($kategorija), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $kategorija = Kategorija::find($id);
        if ($kategorija) {
            $kategorija->delete();

            return response()->json(['message' => 'Kategorija je obrisana'], 200);
        } else {
            return response()->json(['message' => 'Kategorija nije pronadjena'], 404);
        }
    }
}
