<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AdminUserController extends Controller
{
    //GET /api/admin/users?search=&uloga=&include_deleted=1
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

    public function show($id)
    {
        $user=User::withTrashed()->findOrFail($id);
        return response()->json($user);
    }


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

    public function update(Request $request, $id)
    {
        $user=User::withTrashed()->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'ime' => 'sometimes|required|string|max:255',
            'prezime' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,'.$user->id,
            'uloga' => 'sometimes|required|in:korisnik,premium,admin',
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


    public function destroy($id)
    {
        $user=User::findOrFail($id);
        $user->delete(); //soft delete
        return response()->json(['message'=>'Korisnik soft-deleteovan']);
    }

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
