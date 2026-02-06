<?php

namespace App\Http\Controllers;

use App\Models\Dokument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\DokumentResource;

class DokumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return DokumentResource::collection(Dokument::all());
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
            'idTransakcija' => 'required|integer|exists:transakcije,id',
            'nazivFajla' => 'required|string|max:255',
            'datumDodavanja' => 'required|date',
            'putanja' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:5120',
        ]);

       

        //NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validacija nije prosla', 
            'errors' => $validator->errors()], 422);
        }
        // PROSLA JE VALIDACIJA
        $data = $validator->validated();

         if ($request->hasFile('putanja')) {
             $file = $request->file('putanja');
             $path = $file->store('documents', 'public');
             $data['putanja'] = $path;
             $data['tip'] = $file->getClientOriginalExtension();
         }
         
     //   $data['transakcija_id'] = $data['idTransakcija'];
     //   unset($data['idTransakcija']);
        $dokument = Dokument::create($data);
        return response()->json(new DokumentResource($dokument), 200); 
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        return new DokumentResource(Dokument::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Dokument $dokument)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $dokument = Dokument::find($id);
        if (!$dokument) {
            return response()->json(['message' => 'Dokument nije pronadjen'], 404);
        }   
        $validator = Validator::make($request->all(), [
            'idTransakcija' => 'sometimes|integer|exists:transakcije,id',
            'nazivFajla' => 'sometimes|string|max:255',
            'datumDodavanja' => 'sometimes|date',
            'putanja' => 'nullable|string|max:255',
            'tip' => 'nullable|string|max:50',
        ]);

        //NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validacija nije prosla', 
            'errors' => $validator->errors()], 422);
    }
        //PROSLA JE VALIDACIJA - AZURIRAMO DOKUMENT
        $data = $validator->validated();
        $dokument->update($data);
        return response()->json(new DokumentResource($dokument), 200); 
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $dokument=Dokument::find($id);
        if($dokument){
            $dokument->delete();
            return response()->json(['message'=>'Dokument je obrisan'],200);
        }else{
            return response()->json(['message'=>'Dokument nije pronadjen'],404);
         }
    }
}
