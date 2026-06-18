<?php

namespace App\Http\Controllers;

use App\Http\Resources\FinansijskiCiljResource;
use App\Models\FinansijskiCilj;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class FinansijskiCiljController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/finansijski-ciljevi",
        summary: "Pregled svih finansijskih ciljeva",
        description: "Vraća listu svih finansijskih ciljeva kreiranih u sistemu.",
        operationId: "getFinansijskiCiljeviIndex",
        tags: ["Finansijski Ciljevi"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitana lista finansijskih ciljeva",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "naziv", type: "string", example: "Kupovina automobila"),
                    new OA\Property(property: "ciljaniIznos", type: "number", format: "float", example: 5000.00),
                    new OA\Property(property: "trenutniIznos", type: "number", format: "float", example: 1200.50),
                    new OA\Property(property: "rok", type: "string", format: "date", example: "2026-12-31"),
                    new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T20:00:00.000000Z")
                ]
            )
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    public function index()
    {
        return FinansijskiCiljResource::collection(FinansijskiCilj::all());
    }

    //GET /finansijski-ciljevi/korisnik/{id}
    #[OA\Get(
        path: "/api/finansijski-ciljevi/korisnik/{id}",
        summary: "Pregled finansijskih ciljeva za određenog korisnika",
        description: "Vraća listu svih finansijskih ciljeva koji pripadaju prosleđenom ID-ju korisnika.",
        operationId: "getFinansijskiCiljeviByUser",
        tags: ["Finansijski Ciljevi"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID korisnika čije finansijske ciljeve tražimo",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitani ciljevi korisnika",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "naziv", type: "string", example: "Letovanje"),
                    new OA\Property(property: "ciljaniIznos", type: "number", format: "float", example: 1500.00),
                    new OA\Property(property: "trenutniIznos", type: "number", format: "float", example: 600.00),
                    new OA\Property(property: "rok", type: "string", format: "date", example: "2026-07-20"),
                    new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T20:00:00.000000Z")
                ]
            )
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function userFinancialGoals($idKorisnik)
    {
        $ciljevi = FinansijskiCilj::where('idKorisnik', $idKorisnik)->get();

        return FinansijskiCiljResource::collection($ciljevi);
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    #[OA\Post(
        path: "/api/finansijski-ciljevi",
        summary: "Kreiranje novog finansijskog cilja",
        description: "Omogućava postavljanje novog finansijskog cilja (npr. štednja za auto, letovanje) sa ciljanim iznosom, trenutnim stanjem i rokom.",
        operationId: "createFinansijskiCilj",
        tags: ["Finansijski Ciljevi"],
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za kreiranje novog finansijskog cilja",
        content: new OA\JsonContent(
            required: ["idKorisnik", "naziv", "ciljni_iznos", "trenutni_iznos", "rok"],
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "naziv", type: "string", example: "Putovanje na Siciliju"),
                new OA\Property(property: "ciljni_iznos", type: "number", format: "float", minimum: 0, example: 1500.00),
                new OA\Property(property: "trenutni_iznos", type: "number", format: "float", minimum: 0, example: 200.00),
                new OA\Property(property: "rok", type: "string", format: "date", example: "2026-07-25")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Finansijski cilj uspešno kreiran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "naziv", type: "string", example: "Putovanje na Siciliju"),
                new OA\Property(property: "ciljni_iznos", type: "number", format: "float", example: 1500.00),
                new OA\Property(property: "trenutni_iznos", type: "number", format: "float", example: 200.00),
                new OA\Property(property: "rok", type: "string", format: "date", example: "2026-07-25"),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T20:05:00.000000Z")
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
                        new OA\Property(property: "ciljni_iznos", type: "array", items: new OA\Items(type: "string", example: "The ciljni iznos field is required."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
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
    #[OA\Get(
        path: "/api/finansijski-ciljevi/{id}",
        summary: "Prikaz detalja pojedinačnog finansijskog cilja",
        description: "Vraća sve podatke o jednom finansijskom cilju na osnovu njegovog jedinstvenog ID-ja.",
        operationId: "getFinansijskiCiljById",
        tags: ["Finansijski Ciljevi"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID finansijskog cilja",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno pronađen finansijski cilj",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "naziv", type: "string", example: "Putovanje na Siciliju"),
                new OA\Property(property: "ciljni_iznos", type: "number", format: "float", example: 1500.00),
                new OA\Property(property: "trenutni_iznos", type: "number", format: "float", example: 200.00),
                new OA\Property(property: "rok", type: "string", format: "date", example: "2026-07-25"),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T20:05:00.000000Z")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Finansijski cilj nije pronađen")]
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
    #[OA\Put(
        path: "/api/finansijski-ciljevi/{id}",
        summary: "Ažuriranje postojećeg finansijskog cilja",
        description: "Omogućava delimičnu ili potpunu izmenu podataka o finansijskom cilju na osnovu njegovog ID-ja.",
        operationId: "updateFinansijskiCilj",
        tags: ["Finansijski Ciljevi"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID finansijskog cilja koji se ažurira",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za izmenu finansijskog cilja (šalju se samo polja koja želiš da promeniš)",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "naziv", type: "string", example: "Putovanje na Siciliju (Ažurirano)"),
                new OA\Property(property: "ciljni_iznos", type: "number", format: "float", minimum: 0, example: 1800.00),
                new OA\Property(property: "trenutni_iznos", type: "number", format: "float", minimum: 0, example: 450.00),
                new OA\Property(property: "rok", type: "string", format: "date", example: "2026-07-24")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Finansijski cilj uspešno ažuriran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                new OA\Property(property: "naziv", type: "string", example: "Putovanje na Siciliju (Ažurirano)"),
                new OA\Property(property: "ciljni_iznos", type: "number", format: "float", example: 1800.00),
                new OA\Property(property: "trenutni_iznos", type: "number", format: "float", example: 450.00),
                new OA\Property(property: "rok", type: "string", format: "date", example: "2026-07-24"),
                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-06-18T20:15:00.000000Z")
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
                        new OA\Property(property: "rok", type: "array", items: new OA\Items(type: "string", example: "The rok must be a valid date."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Finansijski cilj nije pronađen")]
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
    #[OA\Delete(
        path: "/api/finansijski-ciljevi/{id}",
        summary: "Brisanje finansijskog cilja",
        description: "Trajno uklanja finansijski cilj iz sistema na osnovu njegovog ID-ja.",
        operationId: "deleteFinansijskiCilj",
        tags: ["Finansijski Ciljevi"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID finansijskog cilja koji se briše",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Finansijski cilj uspešno obrisan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Finansijski cilj je obrisan")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Finansijski cilj nije pronađen")]
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
