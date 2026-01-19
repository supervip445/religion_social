<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     $donations = Donation::latest()->get()->map(function ($donation) {
    //         // Add view count
    //         try {
    //             $donation->views_count = $donation->views()->count();
    //         } catch (\Exception $e) {
    //             $donation->views_count = 0;
    //         }
    //         return $donation;
    //     });
    //     return response()->json(['data' => $donations]);
    // }

    public function index()
{
    $donations = Donation::withCount('views')
        ->latest()
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
            'donation_type' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $donation = Donation::create($validated);

        // Send notification to public users
        try {
            $notificationService = new NotificationService();
            $notificationService->notifyNewDonation($donation);
        } catch (\Exception $e) {
            \Log::error('Failed to send donation notification: ' . $e->getMessage());
        }

        return response()->json([
            'message' => 'Donation created successfully',
            'data' => $donation,
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $donation = Donation::findOrFail($id);
        // Add view count
        try {
            $donation->views_count = $donation->views()->count();
        } catch (\Exception $e) {
            $donation->views_count = 0;
        }
        return response()->json(['data' => $donation]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $donation = Donation::findOrFail($id);

        $validated = $request->validate([
            'donor_name' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
            'donation_type' => 'required|string|max:255',
            'date' => 'required|date',
            'status' => 'required|in:pending,approved,rejected',
            'notes' => 'nullable|string',
        ]);

        $donation->update($validated);

        return response()->json([
            'message' => 'Donation updated successfully',
            'data' => $donation,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $donation = Donation::findOrFail($id);
        $donation->delete();

        return response()->json([
            'message' => 'Donation deleted successfully',
        ]);
    }

    /**
     * Approve donation
     */
    public function approve($id)
    {
        $donation = Donation::findOrFail($id);
        $donation->update(['status' => 'approved']);

        return response()->json([
            'message' => 'Donation approved successfully',
            'data' => $donation,
        ]);
    }

    /**
     * Reject donation
     */
    public function reject($id)
    {
        $donation = Donation::findOrFail($id);
        $donation->update(['status' => 'rejected']);

        return response()->json([
            'message' => 'Donation rejected successfully',
            'data' => $donation,
        ]);
    }
}

