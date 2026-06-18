<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class AdminUserController extends Controller
{
    //GET /api/admin/users?search=&uloga=&include_deleted=1
    #[OA\Get(
        path: "/api/admin/users",
        summary: "Pregled i pretraga svih korisnika",
        description: "Vraća paginisanu listu korisnika uz mogućnost pretrage po imenu/mejlu, filtriranja po ulozi i uključivanja obrisanih korisnika.",
        operationId: "getAdminUsersIndex",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "search",
        in: "query",
        description: "Pretraga korisnika po imenu, prezimenu ili email adresi",
        required: false,
        schema: new OA\Schema(type: "string", example: "Marko")
    )]
    #[OA\Parameter(
        name: "uloga",
        in: "query",
        description: "Filtriranje korisnika prema ulozi (npr. user, admin)",
        required: false,
        schema: new OA\Schema(type: "string", example: "user")
    )]
    #[OA\Parameter(
        name: "include_deleted",
        in: "query",
        description: "Uključivanje i meko obrisanih (soft-deleted) korisnika u rezultat (1 za da, 0 za ne)",
        required: false,
        schema: new OA\Schema(type: "integer", enum: [0, 1], example: 1)
    )]
    #[OA\Parameter(
        name: "page",
        in: "query",
        description: "Broj stranice za paginaciju",
        required: false,
        schema: new OA\Schema(type: "integer", example: 1)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno učitana lista korisnika sa paginacijom",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "current_page", type: "integer", example: 1),
                new OA\Property(
                    property: "data",
                    type: "array",
                    items: new OA\Items(
                        type: "object",
                        properties: [
                            new OA\Property(property: "id", type: "integer", example: 5),
                            new OA\Property(property: "ime", type: "string", example: "Marko"),
                            new OA\Property(property: "prezime", type: "string", example: "Marković"),
                            new OA\Property(property: "email", type: "string", example: "marko@example.com"),
                            new OA\Property(property: "uloga", type: "string", example: "user"),
                            new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T15:30:00.000000Z")
                        ]
                    )
                ),
                new OA\Property(property: "first_page_url", type: "string", example: "http://localhost/api/admin/users?page=1"),
                new OA\Property(property: "from", type: "integer", example: 1),
                new OA\Property(property: "last_page", type: "integer", example: 3),
                new OA\Property(property: "last_page_url", type: "string", example: "http://localhost/api/admin/users?page=3"),
                new OA\Property(property: "next_page_url", type: "string", example: "http://localhost/api/admin/users?page=2"),
                new OA\Property(property: "path", type: "string", example: "http://localhost/api/admin/users"),
                new OA\Property(property: "per_page", type: "integer", example: 10),
                new OA\Property(property: "prev_page_url", type: "string", nullable: true, example: null),
                new OA\Property(property: "to", type: "integer", example: 10),
                new OA\Property(property: "total", type: "integer", example: 25)
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    public function index(Request $request)
    {
        $q=User::query();
        if($request->filled('search')){
            $s=$request->get('search');
            $q->where(function($w) use ($s){
                $w->where('ime','like',"%$s%")
                ->orWhere('prezime','like',"%$s%")
                ->orWhere('email','like',"%$s%");
            });
        }

        if($request->filled('uloga')){
            $q->where('uloga', $request->get('uloga'));
        }

        if($request->boolean('include_deleted')){
            $q->withTrashed();
        }

        $users = $q->orderByDesc('created_at')->paginate(10);
        return response()->json($users);
    }

    #[OA\Get(
        path: "/api/admin/users/{id}",
        summary: "Prikaz detalja pojedinačnog korisnika",
        description: "Vraća sve podatke o korisniku na osnovu njegovog ID-ja, uključujući i meko obrisane korisnike.",
        operationId: "getAdminUserById",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID korisnika",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: "Uspešno pronađen korisnik",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "id", type: "integer", example: 5),
                new OA\Property(property: "ime", type: "string", example: "Marko"),
                new OA\Property(property: "prezime", type: "string", example: "Marković"),
                new OA\Property(property: "email", type: "string", example: "marko@example.com"),
                new OA\Property(property: "uloga", type: "string", example: "user"),
                new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T15:30:00.000000Z"),
                new OA\Property(property: "deleted_at", type: "string", format: "date-time", nullable: true, example: null)
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function show($id)
    {
        $user=User::withTrashed()->findOrFail($id);
        return response()->json($user);
    }


    #[OA\Post(
        path: "/api/admin/users",
        summary: "Kreiranje novog korisnika",
        description: "Omogućava administratoru da ručno kreira novog korisnika sa definisanom ulogom.",
        operationId: "adminCreateUser",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za kreiranje novog korisnika",
        content: new OA\JsonContent(
            required: ["ime", "prezime", "email", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "ime", type: "string", example: "Petar"),
                new OA\Property(property: "prezime", type: "string", example: "Petrović"),
                new OA\Property(property: "email", type: "string", format: "email", example: "petar@example.com"),
                new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "Sifra123!"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", minLength: 8, example: "Sifra123!"),
                new OA\Property(property: "uloga", type: "string", enum: ["korisnik", "premium", "admin"], default: "korisnik", example: "korisnik")
            ]
        )
    )]
    #[OA\Response(
        response: 201,
        description: "Korisnik uspešno kreiran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik uspešno kreiran"),
                new OA\Property(
                    property: "user",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 6),
                        new OA\Property(property: "ime", type: "string", example: "Petar"),
                        new OA\Property(property: "prezime", type: "string", example: "Petrović"),
                        new OA\Property(property: "email", type: "string", example: "petar@example.com"),
                        new OA\Property(property: "uloga", type: "string", example: "korisnik"),
                        new OA\Property(property: "created_at", type: "string", format: "date-time", example: "2026-06-18T16:00:00.000000Z")
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije uspela (npr. loša šifra ili email već postoji)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Validacija nije uspela"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "The email has already been taken."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    public function store(Request $request)
    {
        // Validacija i kreiranje novog korisnika (slično kao u AuthController)
        $validator = Validator::make($request->all(), [
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'uloga' => 'nullable|in:korisnik,premium,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije uspela', 
                'errors' => $validator->errors(),
            ], 422);
        }

        $data=$validator->validated();

        // Kreiranje novog korisnika
        $user = User::create([
            'ime' => $data['ime'],
            'prezime' => $data['prezime'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'uloga' => $data['uloga'] ?? 'korisnik',
        ]);

        return response()->json([
            'message' => 'Korisnik uspešno kreiran',
            'user' => $user,
        ], 201);
    }

    #[OA\Put(
        path: "/api/admin/users/{id}",
        summary: "Ažuriranje podataka o korisniku",
        description: "Omogućava izmenu osnovnih podataka, uloge, nivoa i poena korisnika na osnovu njegovog ID-ja.",
        operationId: "adminUpdateUser",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID korisnika koji se ažurira",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za ažuriranje korisnika (šalju se samo polja koja se menjaju)",
        content: new OA\JsonContent(
            properties: [
                new OA\Property(property: "ime", type: "string", example: "Marko"),
                new OA\Property(property: "prezime", type: "string", example: "Novi Marković"),
                new OA\Property(property: "email", type: "string", format: "email", example: "markonovimail@example.com"),
                new OA\Property(property: "uloga", type: "string", enum: ["korisnik", "premium", "admin"], example: "premium"),
                new OA\Property(property: "nivo", type: "integer", minimum: 0, example: 2),
                new OA\Property(property: "poeni", type: "integer", minimum: 0, example: 350)
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Korisnik uspešno ažuriran",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik uspešno ažuriran"),
                new OA\Property(
                    property: "user",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 5),
                        new OA\Property(property: "ime", type: "string", example: "Marko"),
                        new OA\Property(property: "prezime", type: "string", example: "Novi Marković"),
                        new OA\Property(property: "email", type: "string", example: "markonovimail@example.com"),
                        new OA\Property(property: "uloga", type: "string", example: "premium"),
                        new OA\Property(property: "nivo", type: "integer", example: 2),
                        new OA\Property(property: "poeni", type: "integer", example: 350),
                        new OA\Property(property: "updated_at", type: "string", format: "date-time", example: "2026-06-18T16:15:00.000000Z")
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije uspela",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Validacija nije uspela"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "The email has already been taken."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function update(Request $request, $id)
    {
        $user=User::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ime' => 'sometimes|required|string|max:255',
            'prezime' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
            'uloga' => 'sometimes|required|in:korisnik,premium,admin',
            'nivo' => 'sometimes|required|integer|min:0',
            'poeni' => 'sometimes|required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije uspela', 
                'errors' => $validator->errors(),
            ], 422);
        }

        $data=$validator->validated();

        $user->update($data);

        return response()->json([
            'message' => 'Korisnik uspešno ažuriran',
            'user' => $user,
        ], 200);
    }

    // PATCH /api/admin/users/{id}/role
    #[OA\Patch(
        path: "/api/admin/users/{id}/role",
        summary: "Promena uloge korisnika",
        description: "Ekspresno ažuriranje samo uloge određenog korisnika (korisnik, premium ili admin).",
        operationId: "adminUpdateUserRole",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "Jedinstveni ID korisnika kome se menja uloga",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\RequestBody(
        required: true,
        description: "Nova uloga koja se dodeljuje korisniku",
        content: new OA\JsonContent(
            required: ["uloga"],
            properties: [
                new OA\Property(property: "uloga", type: "string", enum: ["korisnik", "premium", "admin"], example: "admin")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Uloga uspešno promenjena",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Uloga korisnika uspešno ažurirana"),
                new OA\Property(
                    property: "user",
                    type: "object",
                    properties: [
                        new OA\Property(property: "id", type: "integer", example: 5),
                        new OA\Property(property: "ime", type: "string", example: "Marko"),
                        new OA\Property(property: "email", type: "string", example: "marko@example.com"),
                        new OA\Property(property: "uloga", type: "string", example: "admin")
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije uspela (poslata uloga koja ne postoji na listi)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Validacija nije uspela"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "uloga", type: "array", items: new OA\Items(type: "string", example: "The selected uloga is invalid."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function updateRole(Request $request, $id)
    {
        $user=User::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'uloga' => 'required|in:korisnik,premium,admin',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validacija nije uspela', 
                'errors' => $validator->errors(),
            ], 422);
        }

        $data=$validator->validated();

        $user->update(['uloga'=>$data['uloga']]);

        return response()->json([
            'message' => 'Uloga korisnika uspešno ažurirana',
            'user' => $user,
        ], 200);
    }


    #[OA\Delete(
        path: "/api/admin/users/{id}",
        summary: "Privremeno brisanje korisnika (Soft delete)",
        description: "Postavlja deleted_at polje i privremeno skriva korisnika iz sistema bez potpunog brisanja iz baze.",
        operationId: "adminDeleteUser",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID korisnika kojeg brišemo",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: "Korisnik uspešno meko obrisan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik soft-deleteovan")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function destroy($id)
    {
        $user=User::findOrFail($id);
        $user->delete(); //soft delete
        return response()->json(['message'=>'Korisnik soft-deleteovan']);
    }

    #[OA\Post(
        path: "/api/admin/users/{id}/restore",
        summary: "Vraćanje meko obrisanog korisnika",
        description: "Poništava soft delete i ponovo aktivira korisnički nalog u sistemu.",
        operationId: "adminRestoreUser",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID korisnika kojeg vraćamo",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: "Korisnik uspešno vraćen u funkciju",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik vraćen")
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Korisnik već aktivan (nije ni bio obrisan)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik nije obrisan")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function restore($id)
    {
        $user=User::withTrashed()->findOrFail($id);
        if($user->trashed()){
            $user->restore();
            return response()->json(['message'=>'Korisnik vraćen']);
        }else{
            return response()->json(['message'=>'Korisnik nije obrisan'],400);
        }
    }

    #[OA\Delete(
        path: "/api/admin/users/{id}/force",
        summary: "Trajno brisanje korisnika iz baze (Force delete)",
        description: "Potpuno i nepovratno briše korisnika iz baze podataka. Uslov je da korisnik prethodno bude privremeno obrisan (soft-deleteovan).",
        operationId: "adminForceDeleteUser",
        tags: ["Admin Korisnici"],
        security: [["sanctum" => []]]
    )]
    #[OA\Parameter(
        name: "id",
        in: "path",
        description: "ID korisnika kojeg trajno brišemo",
        required: true,
        schema: new OA\Schema(type: "integer", example: 5)
    )]
    #[OA\Response(
        response: 200,
        description: "Korisnik uspešno trajno obrisan",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik trajno obrisan")
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Loš zahtev (Korisnik mora prvo proći kroz soft-delete)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik nije obrisan, prvo soft-delete")
            ]
        )
    )]
    #[OA\Response(response: 401, description: "Neautorizovan pristup")]
    #[OA\Response(response: 403, description: "Zabranjen pristup")]
    #[OA\Response(response: 404, description: "Korisnik nije pronađen")]
    public function forceDelete($id)
    {
        $user=User::withTrashed()->findOrFail($id);
        if($user->trashed()){
            $user->forceDelete();
            return response()->json(['message'=>'Korisnik trajno obrisan']);
        }else{
            return response()->json(['message'=>'Korisnik nije obrisan, prvo soft-delete'],400);
        }
    }
}
