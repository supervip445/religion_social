<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BannerText;

class PublicBannerController extends Controller
{
    /**
     * Get active banners for slider
     */
    public function getBanners()
    {
        $banners = Banner::where('is_active', true)
            ->orderBy('order')
            ->latest()
            ->get()
            ->map(function ($banner) {
                if ($banner->image) {
                    $banner->image = url('storage/' . $banner->image);
                }
                return $banner;
            });

        return response()->json(['data' => $banners]);
    }

    /**
     * Get active banner texts for marquee
     */
    public function getBannerTexts()
    {
        $bannerTexts = BannerText::where('is_active', true)
            ->latest()
            ->get();

        return response()->json(['data' => $bannerTexts]);
    }
}

