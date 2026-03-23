<?php

namespace App\Http\Controllers;

use App\Http\Resources\TransakcijaResource;
use App\Models\Transakcija;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TransakcijaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return TransakcijaResource::collection(Transakcija::all());
    }

    //GET/transakcije/korisnik/{id}
    public function userTransactions($idKorisnik)
    {
        $transakcije = Transakcija::where('idKorisnik', $idKorisnik)->get();
        return TransakcijaResource::collection($transakcije);
    }

    //GET/transakcije/korisnik/{idKorisnik}/kategorija/{idKategorija}
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

    public function pregledTransakcija(Request $request)
    {
        $userId = $request->user()->id;

        $transakcije = Transakcija::where('idKorisnik', $userId)->orderByDesc('datum_vreme')->get();

        return response()->json(TransakcijaResource::collection($transakcije));
    }

    public function mojiPrihodi(Request $request)
    {
        $userId = $request->user()->id;

        $transakcije = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'prihod')
            ->orderByDesc('datum_vreme')
            ->get();

        return response()->json(TransakcijaResource::collection($transakcije));

    }

    public function mojiRashodi(Request $request)
    {
        $userId = $request->user()->id;

        $transakcije = Transakcija::where('idKorisnik', $userId)
            ->where('tipTransakcije', 'rashod')
            ->orderByDesc('datum_vreme')
            ->get();

        return response()->json(TransakcijaResource::collection($transakcije));

    }

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