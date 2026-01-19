<?php

namespace Database\Seeders;

use App\Models\Banner;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get SuperAdmin user
        $admin = User::where('type', 'super_admin')->first();
        
        if (!$admin) {
            $this->command->error('SuperAdmin user not found. Please run UsersTableSeeder first.');
            return;
        }

        // Ensure storage directory exists
        $storagePath = storage_path('app/public/banners');
        if (!File::exists($storagePath)) {
            File::makeDirectory($storagePath, 0755, true);
        }

        // Source directory for banner images
        $sourceDir = public_path('assets/banners');
        
        if (!File::exists($sourceDir)) {
            $this->command->warn("Banner images directory not found: {$sourceDir}");
            $this->command->info('Creating sample banners without images...');
            
            // Create banners without images
            $banners = [
                [
                    'order' => 1,
                    'is_active' => true,
                ],
                [
                    'order' => 2,
                    'is_active' => true,
                ],
                [
                    'order' => 3,
                    'is_active' => true,
                ],
            ];
            
            foreach ($banners as $banner) {
                Banner::create([
                    'image' => null,
                    'order' => $banner['order'],
                    'is_active' => $banner['is_active'],
                    'admin_id' => $admin->id,
                ]);
            }
            
            return;
        }

        // Get all banner image files
        $imageFiles = File::files($sourceDir);
        
        if (empty($imageFiles)) {
            $this->command->warn('No banner images found in assets/banners directory.');
            return;
        }

        // Sort files by name for consistent ordering
        usort($imageFiles, function($a, $b) {
            return strcmp($a->getFilename(), $b->getFilename());
        });

        $order = 1;
        foreach ($imageFiles as $file) {
            $filename = $file->getFilename();
            $extension = $file->getExtension();
            
            // Skip non-image files
            if (!in_array(strtolower($extension), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                continue;
            }

            // Generate unique filename
            $newFilename = 'banner_' . time() . '_' . $order . '.' . $extension;
            $destinationPath = 'banners/' . $newFilename;

            // Copy file to storage
            try {
                File::copy($file->getPathname(), storage_path('app/public/' . $destinationPath));
                
                Banner::create([
                    'image' => $destinationPath,
                    'order' => $order,
                    'is_active' => true,
                    'admin_id' => $admin->id,
                ]);
                
                $this->command->info("Created banner: {$filename} (order: {$order})");
                $order++;
            } catch (\Exception $e) {
                $this->command->error("Failed to copy banner image {$filename}: " . $e->getMessage());
            }
        }

        $this->command->info('Banner seeding completed!');
    }
}

