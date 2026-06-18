<?php

namespace App\Http\Controllers;

use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class AuthController extends Controller
{
    // REGISTRACIJA

    #[OA\Post(
        path: "/api/register",
        summary: "Registracija korisnika (uz opcioni upload profilne slike) + slanje linka za verifikaciju email-a",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    required: ["ime", "prezime", "email", "password", "password_confirmation"],
                    properties: [
                        new OA\Property(property: "ime", type: "string", example: "Marko"),
                        new OA\Property(property: "prezime", type: "string", example: "Marković"),
                        new OA\Property(property: "email", type: "string", format: "email", example: "marko@email.com"),
                        new OA\Property(property: "password", type: "string", format: "password", example: "12345678"),
                        new OA\Property(property: "password_confirmation", type: "string", example: "12345678"),
                        new OA\Property(
                            property: "profilnaSlika",
                            type: "string",
                            format: "binary",
                            description: "Profilna slika (jpg/jpeg/png, max 2MB)"
                        )
                    ],
                    type: "object"
                )
            )
        ),
        responses: [
            new OA\Response(response: 201, description: "Registracija uspesna"),
            new OA\Response(response: 422, description: "Validation errors")
        ]
    )]
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'profilnaSlika' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $user = User::create([
            'ime' => $data['ime'],
            'prezime' => $data['prezime'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'uloga' => 'korisnik',
        ]);

        // Verifikacioni mejl
        $url = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60), // Token vazi 60 minuta
            ['id' => $user->id]
        );
        // $url = url('/api/email/verify/' . $user->id);
        Mail::to($user->email)->send(new VerifyEmail($user, $url));

        return response()->json([
            'message' => 'Registracija uspesna',
            'user' => $user,
        ], 201);

    }

    // LOGIN

    #[OA\Post(
        path: "/api/login",
        summary: "Prijava korisnika (Sanctum token). Ako email nije verifikovan vraca poruku.",
        tags: ["Auth"],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ["email", "password"],
                properties: [
                    new OA\Property(property: "email", type: "string", format: "email", example: "marko@email.com"),
                    new OA\Property(property: "password", type: "string", example: "12345678")
                ],
                type: "object"
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Uspesno prijavljivanje"),
            new OA\Response(response: 400, description: "Email adresa nije verifikovana."),
            new OA\Response(response: 401, description: "Pogresan email ili lozinka"),
            new OA\Response(response: 422, description: "Validation errors")
        ]
    )]
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Da li u bazi postoji korisnik sa unetim email-om i lozinkom
        if (! Auth::attempt($validator->validated())) {
            return response()->json([
                'message' => 'Pogresan email ili lozinka',
            ], 401);
        }

        $user = Auth::user();
        if ($user->email_verified_at == null) {
            return response()->json([
                'message' => 'Email adresa nije verifikovana.',
            ], 400);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Uspesno prijavljivanje',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    // LOGOUT

    #[OA\Post(
        path: "/api/logout",
        summary: "Odjava korisnika (briše trenutni token)",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Uspesna odjava"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Uspesno odjavljivanje',
        ], 200);
    }

    #[OA\Get(
        path: "/api/me",
        summary: "Podaci o trenutno ulogovanom korisniku",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        responses: [
            new OA\Response(response: 200, description: "Podaci o korisniku"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }

    #[OA\Get(
        path: "/api/email/verify/{id}",
        summary: "Verifikacija email-a preko potpisanog linka (temporary signed route)",
        tags: ["Auth"],
        parameters: [
            new OA\Parameter(
                name: "id",
                in: "path",
                required: true,
                description: "ID korisnika",
                schema: new OA\Schema(type: "integer"),
                example: 1
            ),
            new OA\Parameter(
                name: "expires",
                in: "query",
                required: true,
                description: "Timestamp isteka linka (automatski u signed URL-u)",
                schema: new OA\Schema(type: "integer")
            ),
            new OA\Parameter(
                name: "signature",
                in: "query",
                required: true,
                description: "Potpis linka (automatski u signed URL-u)",
                schema: new OA\Schema(type: "string")
            )
        ],
        responses: [
            new OA\Response(response: 200, description: "Email verifikovan (ili vec verifikovan)"),
            new OA\Response(response: 401, description: "Link nevazeći ili je istekao")
        ]
    )]
    public function verifyEmail(Request $request, $id)
    {

        if (! request()->hasValidSignature()) {
            return response()->json([
                'message' => 'Verifikacioni link nije validan ili je istekao.',
            ], 400);
        }

        $user = User::findOrFail($id);

        if ($user->email_verified_at) {
            return response()->json([
                'message' => 'Email adresa je vec verifikovana.',
            ], 400);
        }

        $user->email_verified_at = now();
        $user->save();

        return response()->json([
            'message' => 'Email adresa je uspesno verifikovana.',
        ], 200);
    }

    #[OA\Post(
        path: "/api/update-profile",
        summary: "Azuriranje profila korisnika (ime, prezime, email, password, profilna slika)",
        tags: ["Auth"],
        security: [["bearerAuth" => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\MediaType(
                mediaType: "multipart/form-data",
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(property: "ime", type: "string", example: "Marko"),
                        new OA\Property(property: "prezime", type: "string", example: "Marković"),
                        new OA\Property(property: "email", type: "string", format: "email", example: "marko_novo@email.com"),
                        new OA\Property(property: "password", type: "string", format: "password", example: "novasifra123"),
                        new OA\Property(property: "password_confirmation", type: "string", example: "novasifra123"),
                        new OA\Property(
                            property: "profilnaSlika",
                            type: "string",
                            format: "binary",
                            description: "Nova profilna slika (jpg/jpeg/png, max 2MB)"
                        )
                    ],
                    type: "object"
                )
            )
        ),
        responses: [
            new OA\Response(response: 200, description: "Profil uspesno azuriran"),
            new OA\Response(response: 422, description: "Validation errors"),
            new OA\Response(response: 401, description: "Unauthorized")
        ]
    )]
    public function updateProfile(Request $request)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'ime' => 'sometimes|required|string|max:255',
            'prezime' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'sometimes|required|string|min:8|confirmed',
            'profilnaSlika' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);

        return response()->json([
            'message' => 'Profil uspesno azuriran',
            'user' => $user,
        ], 200);
    }
}
