<?php

namespace App\Http\Controllers;

use App\Models\ConstFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConstFileController extends Controller
{
    public function index(Request $request)
    {
        $query = ConstFile::query();

        // Filter by search keyword
        if ($request->has('q')) {
            $query->where('label', 'like', '%' . $request->q . '%');
        }

        // Filter by category
        if ($request->has('category')) {
            $query->where('category_id', $request->category);
        }

        $results = $query->latest()->paginate(10);

        return response()->json($results);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'category_id' => 'required|string',
            'label' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $constFile = ConstFile::create([
            'category_id' => $request->category_id,
            'label' => $request->label,
        ]);

        return response()->json($constFile, 201);
    }
}
