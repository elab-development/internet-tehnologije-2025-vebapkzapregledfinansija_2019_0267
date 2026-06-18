<?php

namespace App\Http\Controllers;

use App\Http\Resources\KategorijaResource;
use App\Models\Kategorija;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class KategorijaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
    path: "/api/kategorije",
    summary: "Pregled svih kategorija",
    tags: ["Kategorije"],
    responses: [
        new OA\Response(response: 200, description: "Lista svih kategorija")
    ]
)]
    public function index()
    {
        return KategorijaResource::collection(Kategorija::all());
    }


    //GET/kategorije/korisnik/{id}
    #[OA\Get(
        path: "/api/kategorije/korisnik/{id}",
        summary: "Pregled kategorija određenog korisnika",
        tags: ["Kategorije"],
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
            new OA\Response(response: 200, description: "Lista kategorija korisnika")
        ]
    )]
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
    #[OA\Post(
        path: "/api/kategorije",
        summary: "Kreiranje nove kategorije",
        tags: ["Kategorije"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["idKorisnik", "naziv"],
                properties: [
                    new OA\Property(property: "idKorisnik", type: "integer", example: 1),
                    new OA\Property(property: "naziv", type: "string", example: "Hrana"),
                    new OA\Property(property: "opis", type: "string", example: "Troškovi za namirnice")
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Kategorija uspešno kreirana"),
            new OA\Response(response: 422, description: "Validacija nije prošla")
        ]
    )]
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
    #[OA\Get(
    path: "/api/kategorije/{id}",
    summary: "Prikaz detalja određene kategorije",
    tags: ["Kategorije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID kategorije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Detalji kategorije"),
        new OA\Response(response: 404, description: "Kategorija nije pronađena")
    ]
)]
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
    #[OA\Put(
    path: "/api/kategorije/{id}",
    summary: "Ažuriranje postojeće kategorije",
    tags: ["Kategorije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID kategorije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "naziv", type: "string", example: "Novi naziv kategorije"),
                new OA\Property(property: "opis", type: "string", example: "Novi opis kategorije")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Kategorija uspešno ažurirana"),
        new OA\Response(response: 404, description: "Kategorija nije pronađena"),
        new OA\Response(response: 422, description: "Validacija nije prošla")
    ]
)]
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
    #[OA\Delete(
    path: "/api/kategorije/{id}",
    summary: "Brisanje kategorije",
    tags: ["Kategorije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID kategorije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Kategorija je obrisana"),
        new OA\Response(response: 404, description: "Kategorija nije pronađena")
    ]
)]
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
