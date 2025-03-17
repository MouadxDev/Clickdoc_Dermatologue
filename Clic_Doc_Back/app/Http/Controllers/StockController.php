<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StockController extends Controller
{
    /**
     * Display a listing of the Stocks with pagination.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $stocks = Stock::paginate(25); // Adjust the per-page limit if necessary
    
        // Transform the stock data before returning
        $transformedData = $stocks->map(function ($item) {
            $daysLeft = $item->expiration_date ? Carbon::now()->diffInDays(Carbon::parse($item->expiration_date), false) : null;
            
            // Determine expiration status & corresponding emoji
            if ($daysLeft === null) {
                $status = 'unknown';
                $statusIcon = '❓';  // Unknown status
            } elseif ($daysLeft < 0) {
                $status = 'expired';
                $statusIcon = '🔴';  // Expired
            } elseif ($daysLeft <= 7) {
                $status = 'critical';
                $statusIcon = '⏳';  // Almost expired
            } elseif ($daysLeft <= 30) {
                $status = 'warning';
                $statusIcon = '⚠️';  // Warning, expiring soon
            } else {
                $status = 'ok';
                $statusIcon = '✅';  // All good
            }
    
            return [
                'id' => $item->id,
                'name' => $item->name,
                'stock' => $item->stock,
                'expiration_date' => $item->expiration_date,
                'days_left' => $daysLeft,
                'status' => $status,
                'status_icon' => $statusIcon,
            ];
        });
    
        return response()->json([
            'current_page' => $stocks->currentPage(),
            'data' => $transformedData,
            'first_page_url' => $stocks->url(1),
            'from' => $stocks->firstItem(),
            'last_page' => $stocks->lastPage(),
            'last_page_url' => $stocks->url($stocks->lastPage()),
            'links' => $stocks->linkCollection(),
            'next_page_url' => $stocks->nextPageUrl(),
            'path' => $stocks->path(),
            'per_page' => $stocks->perPage(),
            'prev_page_url' => $stocks->previousPageUrl(),
            'to' => $stocks->lastItem(),
            'total' => $stocks->total(),
        ]);
    }

    /**
     * Store a newly created Stock in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer',
            'expiration_date' => 'required|date',
        ]);

        $stock = Stock::create($validated);

        return response()->json($stock, 201);
    }

    /**
     * Display the specified Stock.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show(Stock $stock)
    {
        return response()->json($stock);
    }

    /**
     * Update the specified Stock in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */

     public function update(Request $request, string $id)
     {
         $validated = $request->validate([
             'name' => 'nullable|string|max:255',
             'stock' => 'nullable|integer|min:0',
             'expiration_date' => 'nullable|date',
         ]);
     
         $stock = Stock::findOrFail($id);
     
         $stock->update($validated);
     
         return response()->json($stock);
     }

    /**
     * Remove the specified Stock from storage.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function destroy(Stock $stock)
    {
        $stock->delete();

        return response()->json(null, 204);
    }
}
