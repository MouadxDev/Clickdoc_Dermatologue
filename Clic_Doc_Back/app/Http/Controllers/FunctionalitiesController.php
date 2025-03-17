<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class FunctionalitiesController extends Controller
{

    public function isInitiated()
    {
        if(User::count()==0)
        {
            return ["message"=>true];
        }
        else
        {
            return response()->json(["message"=>"The app is already initiated"],500);
        }
    }

    public function initiate(Request $request)
    {
        if(User::count()==0)
        {
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

            User::create([
                "name"=>"Administrateur",
                "email"=>request()->email,
                "password"=>Hash::make(request()->password),
                "role"=>"superAdmin",
                "address"=>"Lorem Ipsum Dolores",
                "city"=>"Gotham",
                "gender"=>"M"
            ]);

            return ["message"=>"L'application vient d'être initiée avec les données fournies"];
        }

        else {
            return response()->json(["message"=>"L'application a été déjà initiée , essayez de vous connecter"],500);
        }

    }
}
