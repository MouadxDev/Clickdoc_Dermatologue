<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiSummaryController extends Controller
{
    public function summarize(Request $request)
    {
        $data = $request->input('data'); 
        $token = env('HUGGINGFACE_API_TOKEN');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
        ])->post('https://router.huggingface.co/hf-inference/models/facebook/bart-large-cnn', [
            'inputs' => $text,
        ]);

        return response()->json([
            'summary' => $response->json()
        ]);
    }
}
