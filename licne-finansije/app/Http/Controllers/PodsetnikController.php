<?php

namespace App\Http\Controllers;

use App\Http\Resources\PodsetnikResource;
use App\Models\Podsetnik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class PodsetnikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
    path: "/api/podsetnici",
    summary: "Pregled svih podsetnika",
    tags: ["Podsetnici"],
    responses: [
        new OA\Response(response: 200, description: "Lista svih podsetnika")
    ]
)]
    public function index()
    {
        return PodsetnikResource::collection(Podsetnik::all());
    }

    //GET /podsetnici/korisnik/{id}
    #[OA\Get(
    path: "/api/podsetnici/korisnik/{id}",
    summary: "Pregled podsetnika određenog korisnika",
    tags: ["Podsetnici"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID korisnika",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Lista podsetnika korisnika")
    ]
)]
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
    #[OA\Post(
    path: "/api/podsetnici",
    summary: "Kreiranje novog podsetnika",
    tags: ["Podsetnici"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["idKorisnik", "datum_vreme", "status"],
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 1),
                new OA\Property(property: "opis", type: "string", example: "Platiti struju"),
                new OA\Property(property: "datum_vreme", type: "string", format: "date-time", example: "2026-06-20 10:00:00"),
                new OA\Property(property: "status", type: "integer", example: 0)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Podsetnik uspešno kreiran"),
        new OA\Response(response: 422, description: "Validacija nije prošla")
    ]
)]
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
    #[OA\Get(
    path: "/api/podsetnici/{id}",
    summary: "Prikaz detalja određenog podsetnika",
    tags: ["Podsetnici"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID podsetnika",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Detalji podsetnika"),
        new OA\Response(response: 404, description: "Podsetnik nije pronađen")
    ]
)]
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
    #[OA\Put(
    path: "/api/podsetnici/{id}",
    summary: "Ažuriranje postojećeg podsetnika",
    tags: ["Podsetnici"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID podsetnika",
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 1),
                new OA\Property(property: "opis", type: "string", example: "Promenjen opis podsetnika"),
                new OA\Property(property: "datum_vreme", type: "string", format: "date-time", example: "2026-06-25 12:00:00"),
                new OA\Property(property: "status", type: "integer", example: 1)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Podsetnik uspešno ažuriran"),
        new OA\Response(response: 422, description: "Validacija nije prošla")
    ]
)]
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
    #[OA\Delete(
    path: "/api/podsetnici/{id}",
    summary: "Brisanje podsetnika",
    tags: ["Podsetnici"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID podsetnika",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Podsetnik je obrisan"),
        new OA\Response(response: 404, description: "Podsetnik nije pronađen")
    ]
)]
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
