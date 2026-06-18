<?php

namespace App\Http\Controllers;

use App\Http\Resources\DokumentResource;
use App\Models\Dokument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class DokumentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: "/api/dokumenti",
        summary: "Pregled svih dokumenata",
        description: "Vraća listu svih dokumenata koji su sačuvani u sistemu.",
        operationId: "getDokumentiIndex",
        tags: ["Dokumenti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitana lista dokumenata",
        content: new OA\JsonContent(
            type: "array",
            items: new OA\Items(
                type: "object",
                properties: [
                    new OA\Property(property: "id", type: "integer", example: 1),
                    new OA\Property(property: "naziv", type: "string", example: "Racun_za_struju.pdf"),
                    new OA\Property(property: "putanja", type: "string", example: "documents/2026/06/racun.pdf"),
                    new OA\Property(property: "tip", type: "string", example: "pdf"),
                    new OA\Property(property: "idKorisnik", type: "integer", example: 5),
                    new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T19:30:00.000000Z")
                ]
            )
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
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
    #[OA\Post(
        path: "/api/dokumenti",
        summary: "Otpremanje novog dokumenta",
        description: "Omogućava slanje fajla (računa, izvoda, slika) i njegovo povezivanje sa određenom transakcijom.",
        operationId: "storeDokument",
        tags: ["Dokumenti"],
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za dokument i fajl koji se kači",
        content: new OA\MediaType(
            mediaType: "multipart/form-data",
            schema: new OA\Schema(
                required: ["idTransakcija", "nazivFajla", "datumDodavanja", "putanja"],
                properties: [
                    new OA\Property(property: "idTransakcija", type: "integer", description: "ID transakcije za koju se vezuje dokument", example: 12),
                    new OA\Property(property: "nazivFajla", type: "string", description: "Naziv pod kojim će se fajl voditi", example: "mesecni_racun_za_infostan"),
                    new OA\Property(property: "datumDodavanja", type: "string", format: "date", example: "2026-06-18"),
                    new OA\Property(property: "putanja", type: "string", format: "binary", description: "Fajl koji se šalje (pdf, jpg, png, docx, xlsx, max 5MB)")
                ]
            )
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Dokument uspešno sačuvan i fajl otpremljen",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idTransakcija", type: "integer", example: 12),
                new OA\Property(property: "nazivFajla", type: "string", example: "mesecni_racun_za_infostan"),
                new OA\Property(property: "putanja", type: "string", example: "documents/AbCdEf12345.pdf"),
                new OA\Property(property: "tip", type: "string", example: "pdf"),
                new OA\Property(property: "datumDodavanja", type: "string", format: "date", example: "2026-06-18"),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T19:55:00.000000Z")
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije prošla (npr. prevelik fajl ili pogrešan format)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Validacija nije prosla"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "putanja", type: "array", items: new OA\Items(type: "string", example: "The putanja must be a file of type: jpg, jpeg, png, pdf, docx, xlsx."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idTransakcija' => 'required|integer|exists:transakcije,id',
            'nazivFajla' => 'required|string|max:255',
            'datumDodavanja' => 'required|date',
            'putanja' => 'required|file|mimes:jpg,jpeg,png,pdf,docx,xlsx|max:5120',
        ]);

       

        // NIJE PROSLA VALIDACIJA
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
    #[OA\Get(
        path: "/api/dokumenti/{id}",
        summary: "Prikaz detalja pojedinačnog dokumenta",
        description: "Vraća metapodatke o dokumentu (naziv, putanju na serveru, tip) na osnovu njegovog ID-ja.",
        operationId: "getDokumentById",
        tags: ["Dokumenti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID dokumenta",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno pronađen dokument",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idTransakcija", type: "integer", example: 12),
                new OA\Property(property: "nazivFajla", type: "string", example: "mesecni_racun_za_infostan"),
                new OA\Property(property: "putanja", type: "string", example: "documents/AbCdEf12345.pdf"),
                new OA\Property(property: "tip", type: "string", example: "pdf"),
                new OA\Property(property: "datumDodavanja", type: "string", format: "date", example: "2026-06-18"),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T19:55:00.000000Z")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Dokument nije pronađen")]
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
    #[OA\Put(
        path: "/api/dokumenti/{id}",
        summary: "Ažuriranje metapodataka dokumenta",
        description: "Omogućava izmenu detalja o dokumentu (naziv, datum dodavanja ili tekstualna putanja) na osnovu njegovog ID-ja.",
        operationId: "updateDokument",
        tags: ["Dokumenti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID dokumenta koji se ažurira",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za izmenu dokumenta (šalju se samo polja koja želiš da promeniš)",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "idTransakcija", type: "integer", example: 12),
                new OA\Property(property: "nazivFajla", type: "string", example: "novi_naziv_racuna"),
                new OA\Property(property: "datumDodavanja", type: "string", format: "date", example: "2026-06-18"),
                new OA\Property(property: "putanja", type: "string", nullable: true, example: "documents/NoviSmer.pdf"),
                new OA\Property(property: "tip", type: "string", nullable: true, example: "pdf")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Dokument uspešno ažuriran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 1),
                new OA\Property(property: "idTransakcija", type: "integer", example: 12),
                new OA\Property(property: "nazivFajla", type: "string", example: "novi_naziv_racuna"),
                new OA\Property(property: "putanja", type: "string", example: "documents/NoviSmer.pdf"),
                new OA\Property(property: "tip", type: "string", example: "pdf"),
                new OA\Property(property: "datumDodavanja", type: "string", format: "date", example: "2026-06-18"),
                new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-06-18T20:10:00.000000Z")
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
                        new OA\Property(property: "idTransakcija", type: "array", items: new OA\Items(type: "string", example: "The selected id transakcija is invalid."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Dokument nije pronađen")]
    public function update(Request $request, $id)
    {
        $dokument = Dokument::find($id);
        if (! $dokument) {
            return response()->json(['message' => 'Dokument nije pronadjen'], 404);
        }
        $validator = Validator::make($request->all(), [
            'idTransakcija' => 'sometimes|integer|exists:transakcije,id',
            'nazivFajla' => 'sometimes|string|max:255',
            'datumDodavanja' => 'sometimes|date',
            'putanja' => 'nullable|string|max:255',
            'tip' => 'nullable|string|max:50',
        ]);

        // NIJE PROSLA VALIDACIJA
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije prosla',
                'errors' => $validator->errors()], 422);
        }
        // PROSLA JE VALIDACIJA - AZURIRAMO DOKUMENT
        $data = $validator->validated();
        $dokument->update($data);

        return response()->json(new DokumentResource($dokument), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
        path: "/api/dokumenti/{id}",
        summary: "Brisanje dokumenta",
        description: "Uklanja dokument iz sistema i baze podataka na osnovu njegovog ID-ja.",
        operationId: "deleteDokument",
        tags: ["Dokumenti"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID dokumenta koji se briše",
        required: true,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Dokument uspešno obrisan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Dokument je obrisan")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 404, description: "Dokument nije pronađen")]
    public function destroy($id)
    {
        $dokument = Dokument::find($id);
        if ($dokument) {
            $dokument->delete();

            return response()->json(['message' => 'Dokument je obrisan'], 200);
        } else {
            return response()->json(['message' => 'Dokument nije pronadjen'], 404);
        }
    }
}
