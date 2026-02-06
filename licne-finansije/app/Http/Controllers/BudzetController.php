<?php

namespace App\Http\Controllers;

use App\Models\Budzet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\BudzetResource;

class BudzetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    //GET /budzeti - svi budzeti
    public function index()
    {
        return BudzetResource::collection(Budzet::all());
    }


    // GET /budzeti/korisnik/{id}
    public function userBudgets($idKorisnik)
    {
        $budzeti = Budzet::where('idKorisnik', $idKorisnik)->get();
        return BudzetResource::collection($budzeti);
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
    //POST /budzeti - kreiranje budzeta
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'required|integer|exists:users,id', //spoljni je kljuc pa moramo da osiguramo da postoji u tabeli users kolona id
            'mesec' => 'required|integer|min:1|max:12',
            'godina' => 'required|integer',
            'limit' => 'required|numeric|min:0',
            'potroseno' => 'required|numeric|min:0',
        ]);

        //NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validacija nije prosla', 
            'errors' => $validator->errors()], 422);
        }   

        //PROSLA JE VALIDACIJA
        $data = $validator->validated();
        $budzet = Budzet::create($data);
        return response()->json(new BudzetResource($budzet), 201);
    }
    /**
     * Display the specified resource.
     */
    //GET /budzeti/{budzet} - samo jedan budzet
    public function show($id)
    {
       return new BudzetResource(Budzet::findOrFail($id));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Budzet $budzet)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    //PUT /budzeti/{budzet} - azuriranje budzeta
    public function update(Request $request, $id)
    {
        $budzet = Budzet::find($id);

        //AKO GA NE PRONAĐE
        if (!$budzet) {
            return response()->json(['message' => 'Budzet nije pronadjen'], 404);
        }   
        //PRONADJEN JE - PROVERA VALIDNOSTI
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'sometimes|integer|exists:users,id', 
            'mesec' => 'sometimes|integer|min:1|max:12',
            'godina' => 'sometimes|integer',
            'limit' => 'sometimes|numeric|min:0',
            'potroseno' => 'sometimes|numeric|min:0',
        ]);

        //NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
            'message' => 'Validacija nije prosla', 
            'errors' => $validator->errors()], 422);
        }
        //PROSLA JE VALIDACIJA - AZURIRAMO BUDZET
        $data = $validator->validated();
        $budzet->update($data);
        return response()->json(new BudzetResource($budzet), 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    //DELETE /budzeti/{budzet} - brisanje budzeta
    public function destroy($id)
    {
        $budzet = Budzet::find($id);

        //AKO GA NE PRONAĐE
        if (!$budzet) {
            return response()->json(['message' => 'Budzet nije pronadjen'], 404);
        }

        //PRONADJEN JE - BRISEMO GA
        $budzet->delete();
        return response()->json(['message' => 'Budzet je obrisan'], 200);

    }
}
