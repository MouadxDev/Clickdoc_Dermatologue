<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;

class TasksController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if(auth()->user()->role == "assistant")
        {
            return Task::join('users as u','tasks.user_id','=','u.id')
            ->where('tasks.user_id','=',auth()->user()->id)
            ->select('tasks.*','u.name as assistant')
            ->get();
        }
        else
        {
            return Task::join('users as u','tasks.user_id','=','u.id')
            ->where('tasks.entity_id','=',auth()->user()->entity_id)
            ->select('tasks.*','u.name as assistant')
            ->get();
        }
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
        $task = new Task();
        $task ->user_id = request()->user_id;
        $task ->entity_id = auth()->user()->entity_id;
        $task ->libelle = request()->libelle;
        $task -> expiration_date = request() -> expiration_date ;
        $task -> status = "en attente";
        $task -> save();

        return $task;
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $task = Task::find($id);
        $task -> status = request() -> status ;
        $task -> save();

        return $task;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $task = Task::find($id);
        $task -> delete();

        return ["message"=>"OK"];
    }
}
