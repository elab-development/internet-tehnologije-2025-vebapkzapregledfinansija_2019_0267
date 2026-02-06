<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinansijskiCiljResource;
use App\Models\FinansijskiCilj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FinansijskiCiljController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return FinansijskiCiljResource::collection(FinansijskiCilj::all());
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
            'ciljni_iznos' => 'required|numeric|min:0',
            'trenutni_iznos' => 'required|numeric|min:0',
            'rok' => 'required|date',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }
        $data = $validator->validated();
        $finansijskiCilj = FinansijskiCilj::create($data);

        return response()->json(new FinansijskiCiljResource($finansijskiCilj), 200);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new FinansijskiCiljResource(FinansijskiCilj::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FinansijskiCilj $finansijskiCilj)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $finansijskiCilj = FinansijskiCilj::find($id);
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'sometimes|integer|exists:users,id',
            'naziv' => 'sometimes|string|max:100',
            'ciljni_iznos' => 'sometimes|numeric|min:0',
            'trenutni_iznos' => 'sometimes|numeric|min:0',
            'rok' => 'sometimes|date',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        } else {
            $data = $validator->validated();
            $finansijskiCilj->update($data);

            return response()->json(new FinansijskiCiljResource($finansijskiCilj), 200);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $finansijskiCilj = FinansijskiCilj::find($id);
        if (! $finansijskiCilj) {
            return response()->json(['message' => 'Finansijski cilj nije pronadjen'], 404);
        }
        $finansijskiCilj->delete();

        return response()->json(['message' => 'Finansijski cilj je obrisan'], 200);
    }
}
