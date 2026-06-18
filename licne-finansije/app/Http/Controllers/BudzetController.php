<?php

namespace App\Http\Controllers;

use App\Http\Resources\BudzetResource;
use App\Models\Budzet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class BudzetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // GET /budzeti - svi budzeti
    #[OA\Get(
        path: "/api/budzeti",
        summary: "Pregled svih budžeta",
        description: "Vraća listu svih kreiranih budžeta u sistemu.",
        operationId: "getBudzetiIndex",
        tags: ["Budžeti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitana lista budžeta",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "naziv", type: "string", example: "Budžet za hranu"),
                    new OA\Property(property: "iznos", type: "number", format: "float", example: 500.00),
                    new OA\Property(property: "potroseno", type: "number", format: "float", example: 120.50),
                    new OA\Property(property: "user_id", type: "integer", example: 5),
                    new OA\Property(property: "kategorija_id", type: "integer", example: 2),
                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T12:00:00.000000Z")
                ]
            )
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    public function index()
    {
        return BudzetResource::collection(Budzet::all());
    }

    // GET /budzeti/korisnik/{id}
    #[OA\Get(
        path: "/api/budzeti/korisnik/{id}",
        summary: "Pregled budžeta za određenog korisnika",
        description: "Vraća listu svih budžeta koji pripadaju prosleđenom ID-ju korisnika.",
        operationId: "getBudzetiByUser",
        tags: ["Budžeti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID korisnika čije budžete tražimo",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitani budžeti korisnika",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "naziv", type: "string", example: "Budžet za hranu"),
                    new OA\Property(property: "iznos", type: "number", format: "float", example: 500.00),
                    new OA\Property(property: "potroseno", type: "number", format: "float", example: 120.50),
                    new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                    new OA\Property(property: "kategorija_id", type: "integer", example: 2),
                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T12:00:00.000000Z")
                ]
            )
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
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
    // POST /budzeti - kreiranje budzeta
    #[OA\Post(
        path: "/api/budzeti",
        summary: "Kreiranje novog budžeta",
        description: "Omogućava kreiranje novog budžeta za određenog korisnika, mesec i godinu sa definisanim limitom.",
        operationId: "createBudzet",
        tags: ["Budžeti"],
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za kreiranje novog budžeta",
        content: new OA\JsonContent(
            required: ["idKorisnik", "mesec", "godina", "limit", "potroseno"],
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "mesec", type: "integer", minimum: 1, maximum: 12, example: 6),
                new OA\Property(property: "godina", type: "integer", example: 2026),
                new OA\Property(property: "limit", type: "number", format: "float", minimum: 0, example: 60000.00),
                new OA\Property(property: "potroseno", type: "number", format: "float", minimum: 0, example: 0.00)
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Budžet uspešno kreiran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "mesec", type: "integer", example: 6),
                new OA\Property(property: "godina", type: "integer", example: 2026),
                new OA\Property(property: "limit", type: "number", format: "float", example: 60000.00),
                new OA\Property(property: "potroseno", type: "number", format: "float", example: 0.00),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T18:45:00.000000Z")
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije prošla",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Validacija nije prosla"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "idKorisnik", type: "array", items: new OA\Items(type: "string", example: "The selected id korisnik is invalid."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'required|integer|exists:users,id', // spoljni je kljuc pa moramo da osiguramo da postoji u tabeli users kolona id
            'mesec' => 'required|integer|min:1|max:12',
            'godina' => 'required|integer',
            'limit' => 'required|numeric|min:0',
            'potroseno' => 'required|numeric|min:0',
        ]);

        // NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }

        // PROSLA JE VALIDACIJA
        $data = $validator->validated();
        $budzet = Budzet::create($data);

        return response()->json(new BudzetResource($budzet), 201);
    }

    /**
     * Display the specified resource.
     */
    // GET /budzeti/{budzet} - samo jedan budzet
    #[OA\Get(
        path: "/api/budzeti/{id}",
        summary: "Prikaz detalja pojedinačnog budžeta",
        description: "Vraća sve podatke o jednom budžetu na osnovu njegovog ID-ja.",
        operationId: "getBudzetById",
        tags: ["Budžeti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID budžeta",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno pronađen budžet",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "mesec", type: "integer", example: 6),
                new OA\Property(property: "godina", type: "integer", example: 2026),
                new OA\Property(property: "limit", type: "number", format: "float", example: 60000.00),
                new OA\Property(property: "potroseno", type: "number", format: "float", example: 12050.00),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T12:00:00.000000Z")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Budžet nije pronađen")]
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
    // PUT /budzeti/{budzet} - azuriranje budzeta
    #[OA\Put(
        path: "/api/budzeti/{id}",
        summary: "Ažuriranje postojećeg budžeta",
        description: "Omogućava delimičnu ili potpunu izmenu podataka o budžetu (mesec, godina, limit, potrošeno) na osnovu njegovog ID-ja.",
        operationId: "updateBudzet",
        tags: ["Budžeti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID budžeta koji se ažurira",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za izmenu budžeta (šalju se samo polja koja želiš da promeniš)",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "mesec", type: "integer", minimum: 1, maximum: 12, example: 7),
                new OA\Property(property: "godina", type: "integer", example: 2026),
                new OA\Property(property: "limit", type: "number", format: "float", minimum: 0, example: 65000.00),
                new OA\Property(property: "potroseno", type: "number", format: "float", minimum: 0, example: 1500.00)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Budžet uspešno ažuriran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "mesec", type: "integer", example: 7),
                new OA\Property(property: "godina", type: "integer", example: 2026),
                new OA\Property(property: "limit", type: "number", format: "float", example: 65000.00),
                new OA\Property(property: "potroseno", type: "number", format: "float", example: 1500.00),
                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-06-18T19:00:00.000000Z")
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije prošla",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Validacija nije prosla"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "mesec", type: "array", items: new OA\Items(type: "string", example: "The mesec must be between 1 and 12."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Budžet nije pronađen")]
    public function update(Request $request, $id)
    {
        $budzet = Budzet::find($id);

        // AKO GA NE PRONAĐE
        if (! $budzet) {
            return response()->json(['message' => 'Budzet nije pronadjen'], 404);
        }
        // PRONADJEN JE - PROVERA VALIDNOSTI
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'sometimes|integer|exists:users,id',
            'mesec' => 'sometimes|integer|min:1|max:12',
            'godina' => 'sometimes|integer',
            'limit' => 'sometimes|numeric|min:0',
            'potroseno' => 'sometimes|numeric|min:0',
        ]);

        // NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }
        // PROSLA JE VALIDACIJA - AZURIRAMO BUDZET
        $data = $validator->validated();
        $budzet->update($data);

        return response()->json(new BudzetResource($budzet), 200);

    }

    /**
     * Remove the specified resource from storage.
     */
    // DELETE /budzeti/{budzet} - brisanje budzeta
    #[OA\Delete(
        path: "/api/budzeti/{id}",
        summary: "Brisanje budžeta",
        description: "Trajno uklanja budžet iz sistema na osnovu njegovog ID-ja.",
        operationId: "deleteBudzet",
        tags: ["Budžeti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID budžeta koji se briše",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Budžet uspešno obrisan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Budzet je obrisan")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Budžet nije pronađen")]
    public function destroy($id)
    {
        $budzet = Budzet::find($id);

        // AKO GA NE PRONAĐE
        if (! $budzet) {
            return response()->json(['message' => 'Budzet nije pronadjen'], 404);
        }

        // PRONADJEN JE - BRISEMO GA
        $budzet->delete();

        return response()->json(['message' => 'Budzet je obrisan'], 200);

    }
}
