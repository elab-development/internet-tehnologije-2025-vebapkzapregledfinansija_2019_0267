<?php

namespace App\Http\Controllers;

use App\Models\Transakcija;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\TransakcijaResource;

class TransakcijaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() 
    {
        return TransakcijaResource::collection(Transakcija::all());
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
            'idKategorija' => 'required|integer|exists:kategorije,id',
            'iznos' => 'required|numeric',
            'datumVreme' => 'required|date',
            'tipTransakcije' => 'required|string|in:prihod,rashod',
            'valuta' => 'required|string|max:3',
        ]);
        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validacija nije prosla', 
            'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $transakcija = Transakcija::create($data);
        return response()->json(new TransakcijaResource($transakcija), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new TransakcijaResource(Transakcija::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Transakcija $transakcija)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $transakcija = Transakcija::find($id);
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'sometimes|required|integer|exists:users,id',
            'idKategorija' => 'sometimes|required|integer|exists:kategorije,id',
            'iznos' => 'sometimes|required|numeric',
            'datumVreme' => 'sometimes|required|date',
            'tipTransakcije' => 'sometimes|required|string|in:prihod,rashod',
            'valuta' => 'sometimes|required|string|max:3',
        ]);
        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validacija nije prosla', 
            'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $transakcija->update($data);
        return response()->json(new TransakcijaResource($transakcija), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $transakcija = Transakcija::find($id);
       if ($transakcija) {
           $transakcija->delete();
           return response()->json(['message' => 'Transakcija je obrisana'], 200);
       } else {
           return response()->json(['message' => 'Transakcija nije pronadjena'], 404);
       }

    }
}
