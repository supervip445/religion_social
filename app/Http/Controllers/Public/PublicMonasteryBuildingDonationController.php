<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\MonasteryBuildingDonation;
use Illuminate\Http\Request;

class PublicMonasteryBuildingDonationController extends Controller
{
    /**
     * Get all monastery building donations
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
        ],
    ]);
}

}

