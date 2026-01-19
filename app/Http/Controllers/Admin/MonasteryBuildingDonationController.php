<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonasteryBuildingDonation;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class MonasteryBuildingDonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $donations = MonasteryBuildingDonation::latest()->get();
    //     return response()->json(['data' => $donations]);
    // }

    public function index()
{
    $donations = MonasteryBuildingDonation::latest()
        ->paginate(10);

    return response()->json([
        'success' => true,
        'data' => $donations->items(),
        'pagination' => [
            'current_page' => $donations->currentPage(),
            'per_page' => $donations->perPage(),
            'total' => $donations->total(),
            'last_page' => $donations->lastPage(),
            'has_more_pages' => $donations->hasMorePages(),
        ]
    ]);
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'donation_purpose' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $donation = MonasteryBuildingDonation::create($validated);

        // Send notification to public users
        try {
            $notificationService = new NotificationService();
            $notificationService->notifyNewMonasteryBuildingDonation($donation);
        } catch (\Exception $e) {
            \Log::error('Failed to send monastery building donation notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Monastery building donation created successfully',
            'data' => $donation,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $donation = MonasteryBuildingDonation::findOrFail($id);
        return response()->json(['data' => $donation]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $donation = MonasteryBuildingDonation::findOrFail($id);

        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'donation_purpose' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        $donation->update($validated);

        return response()->json([
            'message' => 'Monastery building donation updated successfully',
            'data' => $donation,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $donation = MonasteryBuildingDonation::findOrFail($id);
        $donation->delete();

        return response()->json([
            'message' => 'Monastery building donation deleted successfully',
        ]);
    }
}

