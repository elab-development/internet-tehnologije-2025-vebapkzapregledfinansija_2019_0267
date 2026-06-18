<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use OpenApi\Attributes as OA;


class ForgotPasswordController extends Controller
{
    #[OA\Post(
        path: "/api/password/forgot",
        summary: "Slanje linka za resetovanje lozinke",
        description: "Prima email adresu korisnika, generiše privremeni token za resetovanje i šalje e-mail sa jedinstvenim linkom (npr. preko Mailtrap-a).",
        operationId: "authSendResetLink",
        tags: ["Autentifikacija"]
    )]
    #[OA\RequestBody(
        required: true,
        description: "Email adresa korisnika koji je zaboravio lozinku",
        content: new OA\JsonContent(
            required: ["email"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "korisnik@example.com")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Zahtev je uspešno obrađen i e-mail je poslat",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Poslat link za resetovanje lozinke na email adresu ako postoji u sistemu")
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije prošla (loš format email adrese)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Neuspesna validacija email adrese"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "email", type: "array", items: new OA\Items(type: "string", example: "The email field must be a valid email address."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Korisnik sa tim email-om ne postoji u bazi podataka",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Korisnik sa datom email adresom ne postoji")
            ]
        )
    )]
    public function sendResetLink(Request $request)
    {
        // Logika za slanje linka za resetovanje lozinke

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Neuspesna validacija email adrese',
                'errors' => $validator->errors(),
            ], 422);
        }

        $email = $validator->validated()['email'];
        $user = User::where('email', $email)->first();

        if (! $user) {
            return response()->json([
                'message' => 'Korisnik sa datom email adresom ne postoji',
            ], 404);
        }

        // Generisanje tokena za resetovanje lozinke
        $token = Str::random(60);

        // Upisemo u password_reset_tokens tabelu
        DB::table('password_reset_tokens')->updateOrInsert( // jedan korisnik moze vise puta traziti reset lozinke
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' => Carbon::now(),
            ]
        );

        // Kreiramo URL za resetovanje lozinke
        $resetUrl = config('app.frontend_url', config('app.url')).
            '/reset-password?token='.urlencode($token).
            '&email='.urlencode($user->email);

        // Saljemo mejl korisniku sa linkom za resetovanje lozinke (Mailtrap)
        Mail::to($user->email)->send(new ResetPasswordMail($user, $token, $resetUrl));

        return response()->json([
            'message' => 'Poslat link za resetovanje lozinke na email adresu ako postoji u sistemu',
        ], 200);

    }

    #[OA\Post(
        path: "/api/password/reset",
        summary: "Resetovanje lozinke pomoću tokena",
        description: "Prima email, token koji je poslat na mejl, kao i novu lozinku (uz potvrdu lozinke). Proverava validnost i istek tokena (60 minuta), pa ažurira lozinku u bazi podataka.",
        operationId: "authResetPassword",
        tags: ["Autentifikacija"]
    )]
    #[OA\RequestBody(
        required: true,
        description: "Podaci za resetovanje lozinke",
        content: new OA\JsonContent(
            required: ["email", "token", "password", "password_confirmation"],
            properties: [
                new OA\Property(property: "email", type: "string", format: "email", example: "korisnik@example.com"),
                new OA\Property(property: "token", type: "string", example: "a1b2c3d4e5f6..."),
                new OA\Property(property: "password", type: "string", format: "password", minLength: 8, example: "NovaLozinka123"),
                new OA\Property(property: "password_confirmation", type: "string", format: "password", example: "NovaLozinka123")
            ]
        )
    )]
    #[OA\Response(
        response: 200,
        description: "Lozinka je uspešno promenjena",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Lozinka uspesno resetovana")
            ]
        )
    )]
    #[OA\Response(
        response: 400,
        description: "Token nije validan ili je istekao",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Token za resetovanje lozinke je istekao. Posaljite novi zahtev za resetovanje lozinke.")
            ]
        )
    )]
    #[OA\Response(
        response: 422,
        description: "Validacija nije prošla (npr. lozinke se ne poklapaju ili su prekratke)",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Neuspesna validacija"),
                new OA\Property(
                    property: "errors",
                    type: "object",
                    properties: [
                        new OA\Property(property: "password", type: "array", items: new OA\Items(type: "string", example: "The password confirmation does not match."))
                    ]
                )
            ]
        )
    )]
    #[OA\Response(
        response: 404,
        description: "Kombinacija email-a i tokena nije pronađena u evidenciji",
        content: new OA\JsonContent(
            type: "object",
            properties: [
                new OA\Property(property: "message", type: "string", example: "Neispravan token ili email")
            ]
        )
    )]
    public function resetPassword(Request $request)
    {
        // Logika za resetovanje lozinke
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Neuspesna validacija',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        // Provjeravamo ispravnost mejla i tokena
        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if (! $record) {
            return response()->json([
                'message' => 'Neispravan token ili email',
            ], 404);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Token za resetovanje lozinke je istekao. Posaljite novi zahtev za resetovanje lozinke.',
            ], 400);
        }
        // Proveravamo da li je token validan (poredimo heširanu vrednost u bazi sa prosleđenim tokenom)
        if (! Hash::check($data['token'], $record->token)) {
            return response()->json([
                'message' => 'Token za resetovanje lozinke nije validan',
            ], 400);
        }

        // Ako je sve u redu, resetujemo lozinku
        $user = User::where('email', $data['email'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        // Brisemo token iz baze
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return response()->json([
            'message' => 'Lozinka uspesno resetovana',
        ], 200);

    }
}
