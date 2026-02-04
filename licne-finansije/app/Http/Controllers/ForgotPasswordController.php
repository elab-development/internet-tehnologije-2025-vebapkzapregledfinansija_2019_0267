<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\ResetPasswordMail;


class ForgotPasswordController extends Controller
{
    public function sendResetLink(Request $request)
    {
        // Logika za slanje linka za resetovanje lozinke

        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email|max:255|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Neuspesna validacija email adrese',
                'errors' => $validator->errors()
            ], 422);
        }

        $email = $validator->validated()['email'];
        $user = User::where('email', $email)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Korisnik sa datom email adresom ne postoji'
            ], 404);
        }

        // Generisanje tokena za resetovanje lozinke
        $token = Str::random(60);

        // Upisemo u password_reset_tokens tabelu
        DB::table('password_reset_tokens')->updateOrInsert( //jedan korisnik moze vise puta traziti reset lozinke
            ['email' => $user->email],
            [
                'token' => Hash::make($token),
                'created_at' =>Carbon::now()
            ]
        );

        // Kreiramo URL za resetovanje lozinke
        $resetUrl = config('app.frontend_url', config('app.url')) . 
            '/reset-password?token=' . urlencode($token) . 
            '&email=' . urlencode($user->email);


        // Saljemo mejl korisniku sa linkom za resetovanje lozinke (Mailtrap)
        Mail::to($user->email)->send(new ResetPasswordMail($user, $token, $resetUrl));

        return response()->json([
            'message' => 'Poslat link za resetovanje lozinke na email adresu ako postoji u sistemu'
        ], 200);

    }

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
                'errors' => $validator->errors()
            ], 422);
        }

        $data=$validator->validated();

        // Provjeravamo ispravnost mejla i tokena
        $record = DB::table('password_reset_tokens')->where('email', $data['email'])->first();

        if(!$record){
            return response()->json([
                'message' => 'Neispravan token ili email'
            ], 404);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(60)->isPast()) {
            return response()->json([
                'message' => 'Token za resetovanje lozinke je istekao. Posaljite novi zahtev za resetovanje lozinke.'
            ], 400);
        }
        // Proveravamo da li je token validan (poredimo heširanu vrednost u bazi sa prosleđenim tokenom)
        if (!Hash::check($data['token'], $record->token)) {
            return response()->json([
                'message' => 'Token za resetovanje lozinke nije validan'
            ], 400);
        }

        // Ako je sve u redu, resetujemo lozinku
        $user = User::where('email', $data['email'])->first();
        $user->password = Hash::make($data['password']);
        $user->save();

        // Brisemo token iz baze
        DB::table('password_reset_tokens')->where('email', $data['email'])->delete();

        return response()->json([
            'message' => 'Lozinka uspesno resetovana'
        ], 200);

    }




}
