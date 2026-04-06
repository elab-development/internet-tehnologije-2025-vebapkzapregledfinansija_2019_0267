<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

class UserController extends Controller
{
    public function getUser(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Korisnik nije ulogovan'], 401);
        }
        return response()->json($request->user(), 200 );
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['message' => 'Korisnik nije ulogovan'], 401);
        }

        $validator = Validator::make($request->all(), [
            'ime' => 'required|string|max:255',
            'prezime' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'profilnaSlika' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();

        $user->ime = $data['ime'];
        $user->prezime = $data['prezime'];
        $user->email = $data['email'];

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        if ($request->hasFile('profilnaSlika')) {
            // Obrada i čuvanje profilne slike
            // ...
        }

        $user->save();

        return response()->json([
            'message' => 'Profil uspešno ažuriran',
            'user' => $user,
        ]);
    }
}
