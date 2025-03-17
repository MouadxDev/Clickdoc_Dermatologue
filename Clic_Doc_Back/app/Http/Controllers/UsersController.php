<?php

namespace App\Http\Controllers;

use App\Models\Entite;
use App\Models\Privilege;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $model =  User::where("entity_id","=",auth()->user()->entity_id)->where("role","=",'assistant');
        if(request()->has("toGet")) 
            return $model->paginate(request()->toGet);
        else 
            return $model->get();
        
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
        $entity = Entite::find(request()->has("entity_id") ?request()->entity_id : auth()->user()->entity_id);

        $user = new User() ;
        $user -> name = request()->name;
        $user -> email = request()->email;
        $user -> entity_id = request()->has("entity_id") ?request()->entity_id : auth()->user()->entity_id;
        $user -> password = Hash::make(request()->password);
        $user -> avatar = request()->has("avatar") ?request()->avatar :"/avatar.png";
        $user -> role = request()->has("role")? request()->role : "assistant" ;
        $user -> address = $entity->adress;
        $user -> city = $entity->city;
        $user -> experience = request()->has("experience") ?request()->experience : 1;
        $user -> license_number = request()->license_number;
        $user -> specialty = request()->specialty;
        $user -> degree = request()->degree;
        $user -> gender = request()->gender;
        $user -> about = request()->about;
        $user -> fee = request()->fee;
        $user -> save();

        if(request()->has("privileges"))
        {
            for($i=0;$i<count(request()->privileges);$i++)
            {
                $table = new Privilege();
                $table -> name = request()->privileges[$i]["name"];
                $table -> enable = request()->privileges[$i]["enable"];
                $table -> view = request()->privileges[$i]["view"];
                $table -> edit = request()->privileges[$i]["edit"];
                $table -> user_id = $user -> id;
                $table -> save();
            }
        }
        
        return $user ;

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $model =  User::where("entity_id","=",$id)->where("role",'=',"Admin");
        return $model->get();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        // Find the user by ID
        $user = User::find($id);
    
        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        // Check if the old password is provided and matches the current password
        if ($request->filled('oldPassword') && !Hash::check($request->oldPassword, $user->password)) {
            return response()->json(['message' => 'L\'ancien mot de passe est incorrect'], 400); // Return error if old password does not match
        }
    
        // Update only the necessary fields (name, email, avatar)
        $user->name = $request->name ?? $user->name;
        $user->email = $request->email ?? $user->email;
        $user->avatar = $request->avatar ?? $user->avatar;
    
        // If a new password is provided, hash and update it
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    
        // Save the updated user data
        $user->save();
    
        // Return the updated user data with a success message
        return response()->json(['message' => 'Vos informations ont été modifiées avec succès', 'user' => $user]);
    }
    
    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        $user -> delete();

        return ["message"=>"Supprimé avec succès"];
    }
}
