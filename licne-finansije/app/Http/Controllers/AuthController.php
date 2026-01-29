<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends Controller
{
    //REGISTRACIJA

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
            }
            
            $data = $validator->validated();
            $user = User::create([
                'ime' => $data['ime'],
                'prezime' => $data['prezime'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'message' => 'Registracija uspesna',
                'user' => $user,
                'token' => $token,
            ], 201);
        
    }   

    //LOGIN

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors()
            ], 422);
        }

         // Da li u bazi postoji korisnik sa unetim email-om i lozinkom
        if(!Auth::attempt($validator->validated())) {
            return response()->json([
                'message' => 'Pogresan email ili lozinka'
            ], 401);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Uspesno prijavljivanje',
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    //LOGOUT

    public function logout(Request $request)
    {
        $user = $request->user();
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Uspesno odjavljivanje'
        ], 200);
    }

    public function me(Request $request)
    {
        return response()->json($request->user(), 200);
    }
}
