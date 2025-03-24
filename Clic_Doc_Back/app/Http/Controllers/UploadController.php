<?php

namespace App\Http\Controllers;

use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

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
        $extension = $request->file->getClientOriginalExtension();
        
        // Check if the file is one of the allowed types
        if ($extension == "JPEG" || $extension == "jpeg" || $extension == "JPG" || $extension == "jpg" || 
            $extension == "PNG" || $extension == "png" || $extension == "HEIC" || $extension == "heic" || 
            $extension == "SVG" || $extension == "svg" || $extension == "WEBP" || $extension == "webp") 
        {
            // Define the file name and path
            $fileName = $pseudo . '.' . $extension;
            $filePath = 'C:\\xampp\\storage\\app\\public\\files\\' . $fileName; // Path inside XAMPP storage
    
            // Move the file to the defined path
            $request->file->move('C:\\xampp\\storage\\app\\public\\files', $fileName);
    
            // Generate the URL to access the file
            $fileUrl = url("/upload/show/{$fileName}"); // Use the file name to create a URL

            // Return the full path URL
            return response()->json(["full_path" => $fileUrl]);
        } else {
            return response()->json(["message" => "Ceci n'est pas une image"], 500);
        }
    }

    /**
     * Show the specified file.
     */
    public function show($fileName)
    {
        // Define the file path
        $filePath = 'C:\\xampp\\storage\\app\\public\\files\\' . $fileName;

        // Check if the file exists
        if (file_exists($filePath)) {
            // Return the file as a response with the correct MIME type
            return Response::download($filePath);
        } else {
            return response()->json(["message" => "File not found"], 404);
        }
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
