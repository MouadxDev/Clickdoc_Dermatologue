<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;

class UploadController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $pseudo = time();
        $extension = request()->file->getClientOriginalExtension();
        if($extension=="JPEG" || $extension=="jpeg" || $extension=="JPG" ||  $extension=="jpg" || $extension=="PNG" || $extension=="png" || $extension=="HEIC" || $extension=="heic" || $extension=="SVG" || $extension=="svg" || $extension=="WEBP" || $extension=="webp" )
        {
            $fileName = $pseudo .'.'. $extension;
            request()->file->move(public_path('files'), $fileName);
            $fichier = new Upload();
            $fichier -> name = $pseudo ;
            $fichier -> extension = $extension;
            $fichier -> full_path = env("APP_URL", '/')."/files/".$fileName;
            $fichier -> save();
            return ["full_path"=>$fichier->full_path];
        }
        else {
            return response()->json(["message"=>"Ceci n'est pas une image"],500);
        }
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
