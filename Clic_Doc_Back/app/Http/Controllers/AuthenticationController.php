<?php

namespace App\Http\Controllers;

use App\Models\Entite;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function login(Request $request)
    {
        try {
            $validateUser = Validator::make(
                $request->all(),
                [
                    'email' => 'required|email',
                    'password' => 'required'
                ]
            );

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'validation error',
                    'errors' => $validateUser->errors()
                ], 401);
            }

            if (!Auth::attempt($request->only(['email', 'password']))) {
                return response()->json([
                    'status' => false,
                    'message' => 'Email et/ou mot de passe inexistants.',
                ], 401);
            }

            $user = User::where('email', $request->email)->first();
            $entite = Entite::find($user->entity_id);
            $privileges = Privilege::where("user_id",'=',$user->id)->get();

            return response()->json([
                'status' => true,
                'message' => 'Utilisateur connecté avec succès',
                'user' => $user,
                'entite' => $entite,
                'privileges' => $privileges,
                'token' => $user->createToken("API TOKEN")->plainTextToken
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }

    public function logout(Request $request)
    {
        auth('sanctum')->user()->tokens()->delete();
        return response()->json(['message' => "Utilisateur déconnecté avec succès"]);
    }
}
