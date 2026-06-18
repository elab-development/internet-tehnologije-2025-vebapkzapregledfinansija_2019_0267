<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransakcijaResource;
use App\Models\Transakcija;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class TransakcijaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    
    #[OA\Get(
    path: "/api/transakcije",
    summary: "Pregled svih transakcija",
    tags: ["Transakcije"],
    responses: [
        new OA\Response(response: 200, description: "Lista svih transakcija")
    ]
)]
    public function index()
    {
        return TransakcijaResource::collection(Transakcija::all());
    }

    //GET/transakcije/korisnik/{id}
    #[OA\Get(
    path: "/api/transakcije/korisnik/{id}",
    summary: "Pregled transakcija određenog korisnika",
    tags: ["Transakcije"],
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
        new OA\Response(response: 200, description: "Lista transakcija korisnika")
    ]
)]
    public function userTransactions($idKorisnik)
    {
        $transakcije = Transakcija::where('idKorisnik', $idKorisnik)->get();
        return TransakcijaResource::collection($transakcije);
    }

    //GET/transakcije/korisnik/{idKorisnik}/kategorija/{idKategorija}
    #[OA\Get(
    path: "/api/transakcije/korisnik/{idKorisnik}/kategorija/{idKategorija}",
    summary: "Pregled transakcija korisnika za određenu kategoriju",
    tags: ["Transakcije"],
    parameters: [
        new OA\Parameter(
            name: "idKorisnik",
            in: "path",
            required: true,
            description: "ID korisnika",
            schema: new OA\Schema(type: "integer")
        ),
        new OA\Parameter(
            name: "idKategorija",
            in: "path",
            required: true,
            description: "ID kategorije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Lista transakcija za zadatu kategoriju")
    ]
)]
    public function userCategoryTransactions($idKorisnik, $idKategorija)
    {
        $transakcije = Transakcija::where('idKorisnik', $idKorisnik)
            ->where('idKategorija', $idKategorija)
            ->get();
        return TransakcijaResource::collection($transakcije);
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
    path: "/api/transakcije",
    summary: "Kreiranje nove transakcije",
    tags: ["Transakcije"],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["idKorisnik", "idKategorija", "iznos", "datum_vreme", "tipTransakcije", "valuta"],
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 1),
                new OA\Property(property: "idKategorija", type: "integer", example: 1),
                new OA\Property(property: "iznos", type: "number", format: "float", example: 1500.50),
                new OA\Property(property: "datum_vreme", type: "string", format: "date-time", example: "2026-06-18 20:00:00"),
                new OA\Property(property: "tipTransakcije", type: "string", enum: ["PRIHOD", "RASHOD"], example: "RASHOD"),
                new OA\Property(property: "valuta", type: "string", example: "RSD"),
                new OA\Property(property: "opis", type: "string", example: "Kupovina namirnica")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Transakcija uspešno kreirana"),
        new OA\Response(response: 422, description: "Validacija nije prošla")
    ]
)]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'required|integer|exists:users,id',
            'idKategorija' => 'required|integer|exists:kategorije,id',
            'iznos' => 'required|numeric',
            'datum_vreme' => 'required|date',
            'tipTransakcije' => 'required|string|in:PRIHOD,RASHOD',
            'valuta' => 'required|string|max:3',
            'opis' => 'nullable|string|max:255',
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
    #[OA\Get(
    path: "/api/transakcije/{id}",
    summary: "Prikaz detalja određene transakcije",
    tags: ["Transakcije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID transakcije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Detalji transakcije"),
        new OA\Response(response: 404, description: "Transakcija nije pronađena")
    ]
)]
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
    #[OA\Put(
    path: "/api/transakcije/{id}",
    summary: "Ažuriranje postojeće transakcije",
    tags: ["Transakcije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID transakcije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "idKorisnik", type: "integer", example: 1),
                new OA\Property(property: "idKategorija", type: "integer", example: 1),
                new OA\Property(property: "iznos", type: "number", format: "float", example: 2000.00),
                new OA\Property(property: "datum_vreme", type: "string", format: "date-time", example: "2026-06-18 20:30:00"),
                new OA\Property(property: "tipTransakcije", type: "string", enum: ["PRIHOD", "RASHOD"], example: "RASHOD"),
                new OA\Property(property: "valuta", type: "string", example: "RSD"),
                new OA\Property(property: "opis", type: "string", example: "Izmenjen opis")
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Transakcija uspešno ažurirana"),
        new OA\Response(response: 422, description: "Validacija nije prošla")
    ]
)]
    public function update(Request $request, $id)
    {
        $transakcija = Transakcija::find($id);
        $validator = Validator::make($request->all(), [
            'idKorisnik' => 'sometimes|required|integer|exists:users,id',
            'idKategorija' => 'sometimes|required|integer|exists:kategorije,id',
            'iznos' => 'sometimes|required|numeric',
            'datum_vreme' => 'sometimes|required|date',
            'tipTransakcije' => 'sometimes|required|string|in:PRIHOD,RASHOD',
            'valuta' => 'sometimes|required|string|max:3',
            'opis' => 'nullable|string|max:255',
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

    #[OA\Patch(
    path: "/api/transakcije/{id}/valuta",
    summary: "Ažuriranje valute i iznosa transakcije",
    tags: ["Transakcije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID transakcije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["valuta", "iznos"],
            properties: [
                new OA\Property(property: "valuta", type: "string", example: "EUR"),
                new OA\Property(property: "iznos", type: "number", format: "float", example: 12.50)
            ]
        )
    ),
    responses: [
        new OA\Response(response: 200, description: "Valuta i iznos uspešno ažurirani")
    ]
)]
    public function updateValuta(Request $request, $id)
    {
        $transakcija= Transakcija::findOrFail($id);
        $transakcija->valuta = $request->input('valuta');
        $transakcija->iznos= $request->input('iznos');
        $transakcija->save();   
        return response()->json(new TransakcijaResource($transakcija), 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    #[OA\Delete(
    path: "/api/transakcije/{id}",
    summary: "Brisanje transakcije",
    tags: ["Transakcije"],
    parameters: [
        new OA\Parameter(
            name: "id",
            in: "path",
            required: true,
            description: "ID transakcije",
            schema: new OA\Schema(type: "integer")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Transakcija je obrisana"),
        new OA\Response(response: 404, description: "Transakcija nije pronađena")
    ]
)]
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

    #[OA\Get(
    path: "/api/transakcije/pregled",
    summary: "Pregled svih transakcija ulogovanog korisnika",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    responses: [
        new OA\Response(response: 200, description: "Lista transakcija ulogovanog korisnika")
    ]
)]
    public function pregledTransakcija(Request $request)
    {
        $userId = $request->user()->id;

        $transakcije = Transakcija::where('idKorisnik', $userId)->orderByDesc('datum_vreme')->get();

        return response()->json(TransakcijaResource::collection($transakcije));
    }

    #[OA\Get(
    path: "/api/transakcije/prihodi",
    summary: "Pregled svih prihoda ulogovanog korisnika",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    responses: [
        new OA\Response(response: 200, description: "Lista prihoda ulogovanog korisnika")
    ]
)]
    public function mojiPrihodi(Request $request)
    {
        $userId = $request->user()->id;

        $transakcije = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'prihod')
            ->orderByDesc('datum_vreme')
            ->get();

        return response()->json(TransakcijaResource::collection($transakcije));

    }

    #[OA\Get(
    path: "/api/transakcije/rashodi",
    summary: "Pregled svih rashoda ulogovanog korisnika",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    responses: [
        new OA\Response(response: 200, description: "Lista rashoda ulogovanog korisnika")
    ]
)]
    public function mojiRashodi(Request $request)
    {
        $userId = $request->user()->id;

        $transakcije = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'rashod')
            ->orderByDesc('datum_vreme')
            ->get();

        return response()->json(TransakcijaResource::collection($transakcije));

    }

    #[OA\Get(
    path: "/api/transakcije/prihodi-paginacija",
    summary: "Pregled prihoda ulogovanog korisnika sa paginacijom",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    parameters: [
        new OA\Parameter(
            name: "per_page",
            in: "query",
            required: false,
            description: "Broj stavki po stranici",
            schema: new OA\Schema(type: "integer", default: 10)
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Paginisana lista prihoda")
    ]
)]
    public function mojiPrihodiPaginacija(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 10);

        $query = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'prihod')
            ->orderByDesc('datum_vreme');

        $paginator = $query->paginate($perPage);

        return TransakcijaResource::collection($paginator);

    }

    #[OA\Get(
    path: "/api/transakcije/rashodi-paginacija",
    summary: "Pregled rashoda ulogovanog korisnika sa paginacijom",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    parameters: [
        new OA\Parameter(
            name: "per_page",
            in: "query",
            required: false,
            description: "Broj stavki po stranici",
            schema: new OA\Schema(type: "integer", default: 10)
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Paginisana lista rashoda")
    ]
)]
    public function mojiRashodiPaginacija(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 10);

        $query = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'rashod')
            ->orderByDesc('datum_vreme');

        $paginator = $query->paginate($perPage);

        return TransakcijaResource::collection($paginator);
    }

    #[OA\Get(
    path: "/api/transakcije/prihodi-paginacija-filter",
    summary: "Pregled prihoda ulogovanog korisnika sa filterima i paginacijom",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    parameters: [
        new OA\Parameter(
            name: "per_page",
            in: "query",
            description: "Broj stavki po stranici",
            schema: new OA\Schema(type: "integer", default: 10)
        ),
        new OA\Parameter(
            name: "idKategorija",
            in: "query",
            description: "Filtriranje po ID-u kategorije",
            required: false,
            schema: new OA\Schema(type: "integer")
        ),
        new OA\Parameter(
            name: "datumOd",
            in: "query",
            description: "Datum početka pretrage (YYYY-MM-DD)",
            required: false,
            schema: new OA\Schema(type: "string", format: "date")
        ),
        new OA\Parameter(
            name: "datumDo",
            in: "query",
            description: "Datum završetka pretrage (YYYY-MM-DD)",
            required: false,
            schema: new OA\Schema(type: "string", format: "date")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Filtrirana lista prihoda")
    ]
)]
    public function mojiPrihodiPaginacijaFilter(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 10);

        $query = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'prihod');

        if ($request->filled ('idKategorija')) {
            $query->where('idKategorija', $request->get('idKategorija'));
        }

        if($request->filled('datumOd')) {
            $query->whereDate('datum_vreme', '>=', $request->get('datumOd'));
        }

        if($request->filled('datumDo')) {
            $query->whereDate('datum_vreme', '<=', $request->get('datumDo'));
        }

        $query->orderByDesc('datum_vreme');

        $paginator = $query->paginate($perPage);

        return TransakcijaResource::collection($paginator);
    }

    #[OA\Get(
    path: "/api/transakcije/rashodi-paginacija-filter",
    summary: "Pregled rashoda ulogovanog korisnika sa filterima i paginacijom",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    parameters: [
        new OA\Parameter(
            name: "per_page",
            in: "query",
            description: "Broj stavki po stranici",
            schema: new OA\Schema(type: "integer", default: 10)
        ),
        new OA\Parameter(
            name: "idKategorija",
            in: "query",
            description: "Filtriranje po ID-u kategorije",
            required: false,
            schema: new OA\Schema(type: "integer")
        ),
        new OA\Parameter(
            name: "datumOd",
            in: "query",
            description: "Datum početka pretrage (YYYY-MM-DD)",
            required: false,
            schema: new OA\Schema(type: "string", format: "date")
        ),
        new OA\Parameter(
            name: "datumDo",
            in: "query",
            description: "Datum završetka pretrage (YYYY-MM-DD)",
            required: false,
            schema: new OA\Schema(type: "string", format: "date")
        )
    ],
    responses: [
        new OA\Response(response: 200, description: "Filtrirana lista rashoda")
    ]
)]
    public function mojiRashodiPaginacijaFilter(Request $request)
    {
        $userId = $request->user()->id;
        $perPage = (int) $request->get('per_page', 10);

        $query = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'rashod');

        if ($request->filled ('idKategorija')) {
            $query->where('idKategorija', $request->get('idKategorija'));
        }

        if($request->filled('datumOd')) {
            $query->whereDate('datum_vreme', '>=', $request->get('datumOd'));
        }

        if($request->filled('datumDo')) {
            $query->whereDate('datum_vreme', '<=', $request->get('datumDo'));
        }

        $query->orderByDesc('datum_vreme');

        $paginator = $query->paginate($perPage);

        return TransakcijaResource::collection($paginator);
    }

    #[OA\Get(
    path: "/api/transakcije/export",
    summary: "Export transakcija u CSV formatu",
    tags: ["Transakcije"],
    security: [["sanctum" => []]],
    responses: [
        new OA\Response(
            response: 200, 
            description: "CSV fajl sa listom transakcija",
            content: new OA\MediaType(
                mediaType: "text/csv"
            )
        )
    ]
)]
    public function exportCsv (Request $request) {
        $userId = $request->user()->id;

        $transakcije = Transakcija::with('dokumenti')
        ->where('idKorisnik', $userId)
        ->orderBy('datum_vreme', 'asc')
        ->get();

        $columns = ['ID', 'Datum i vreme', 'Tip transakcije', 'Iznos', 'Valuta', 'Opis', 'Dokument'];

        $callback = function() use ($transakcije, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns, ';');

            foreach ($transakcije as $transakcija) {
                $dokument = $transakcija->dokument ? $transakcija->dokument->nazivFajla : 'Ne postoji dokument';
                fputcsv($file, [
                    $transakcija->id,
                    $transakcija->datum_vreme,
                    $transakcija->tipTransakcije->value,
                    $transakcija->iznos,
                    $transakcija->valuta,
                    $transakcija->opis,
                    $dokument
                ]);
            }

            fclose($file);
        };

       $filename = 'transakcije_' . $userId . '_' . now()->format('Y-m-d') . '.csv';

        return response()->streamDownload($callback, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
}