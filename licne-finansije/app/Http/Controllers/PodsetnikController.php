<?php

namespace App\Http\Controllers;

use App\Http\Resources\PodsetnikResource;
use App\Models\Podsetnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PodsetnikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return PodsetnikResource::collection(Podsetnik::all());
    }

    //GET /podsetnici/korisnik/{id}
    public function userReminders($idKorisnik)
    {
        $podsetnici = Podsetnik::where('idKorisnik', $idKorisnik)->get();
        return PodsetnikResource::collection($podsetnici);
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
            'opis' => 'nullable|string',
            'datum_vreme' => 'required|date',
            'status' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $podsetnik = Podsetnik::create($data);

        return response()->json(new PodsetnikResource($podsetnik), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new PodsetnikResource(Podsetnik::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Podsetnik $podsetnik)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $podsetnik = Podsetnik::find($id);
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'sometimes|required|integer|exists:users,id',
            'opis' => 'sometimes|nullable|string',
            'datum_vreme' => 'sometimes|required|date',
            'status' => 'sometimes|required|integer',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $podsetnik->update($data);

        return response()->json(new PodsetnikResource($podsetnik), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $podsetnik = Podsetnik::find($id);
        if ($podsetnik) {
            $podsetnik->delete();

            return response()->json(['message' => 'Podsetnik je obrisan'], 200);
        } else {
            return response()->json(['message' => 'Podsetnik nije pronadjen'], 404);
        }
    }
}
