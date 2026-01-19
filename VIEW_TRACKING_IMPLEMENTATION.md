# View Tracking Implementation Guide

## Overview
This document describes the view tracking system that records public user IP addresses when they view details of Posts, Dhamma Talks, Biographies, Lessons, and Donations.

## Database Structure

### Migration
- **File**: `database/migrations/2025_01_15_000011_create_views_table.php`
- **Table**: `views`
- **Fields**:
  - `id` (primary key)
  - `viewable_type` (string) - Model class name (e.g., "App\Models\Post")
  - `viewable_id` (bigInteger) - ID of the viewed item
  - `ip_address` (string, 45 chars) - IPv4 or IPv6 address
  - `user_agent` (string, nullable) - Browser user agent
  - `created_at`, `updated_at` (timestamps)

### Model
- **File**: `app/Models/View.php`
- **Relationships**: Polymorphic relationship to viewable models

## Backend Implementation

### Models Updated
All models now have:
- `views()` relationship method
- `views_count` accessor attribute

**Updated Models:**
- `app/Models/Post.php`
- `app/Models/Dhamma.php`
- `app/Models/Biography.php`
- `app/Models/Lesson.php`
- `app/Models/Donation.php`

### ViewService
- **File**: `app/Services/ViewService.php`
- **Methods**:
  - `trackView()` - Records a view (prevents duplicate views from same IP within 24 hours)
  - `getViewStats()` - Returns total views, unique IPs, and recent views
  - `getViewsWithIPs()` - Returns view records with IP addresses

### Public Controllers Updated
All public detail controllers now track views when accessed:

1. **PublicPostController** - `show()` method
2. **PublicDhammaController** - `show()` method
3. **PublicBiographyController** - `show()` method
4. **PublicLessonController** - `show()` method
5. **PublicDonationController** - Added `show()` method

### Admin Controllers
- **ViewController**: `app/Http/Controllers/Admin/ViewController.php`
  - `getViews()` - Get views with IP addresses for an item
  - `getStats()` - Get view statistics for an item

- **Admin Controllers Updated** (to include view counts):
  - PostController - `index()` and `show()` methods
  - (Other controllers need similar updates)

## API Routes

### Public Routes
- `GET /api/public/posts/{id}` - Tracks view
- `GET /api/public/dhammas/{id}` - Tracks view
- `GET /api/public/biographies/{id}` - Tracks view
- `GET /api/public/lessons/{id}` - Tracks view
- `GET /api/public/donations/{id}` - Tracks view (new route)

### Admin Routes
- `GET /api/admin/views?viewable_type=App\Models\Post&viewable_id=1` - Get views with IPs
- `GET /api/admin/views/stats?viewable_type=App\Models\Post&viewable_id=1` - Get statistics

## Frontend Implementation (TODO)

### React Admin Panel
Need to update:
1. Posts admin page - Display view count column
2. Dhamma Talks admin page - Display view count column
3. Biographies admin page - Display view count column
4. Lessons admin page - Display view count column
5. Donations admin page - Display view count column
6. Add modal/dialog to show IP addresses when clicking view count

### Flutter Admin Panel
Need to update:
1. All admin screens to display view counts
2. Add detail view to show IP addresses

## Usage Example

### Track a view (automatic in public controllers)
```php
$viewService = new ViewService();
$viewService->trackView(Post::class, $postId, $request);
```

### Get view statistics
```php
$stats = $viewService->getViewStats(Post::class, $postId);
// Returns: ['total_views' => 150, 'unique_ips' => 45, 'recent_views_24h' => 12]
```

### Get views with IP addresses
```php
$views = $viewService->getViewsWithIPs(Post::class, $postId, 100);
// Returns collection of View models with ip_address, user_agent, created_at
```

## Next Steps

1. Run migration: `php artisan migrate`
2. Update remaining admin controllers (DhammaController, BiographyController, LessonController, DonationController)
3. Update React admin panels to display view counts
4. Update Flutter admin panels to display view counts
5. Add UI components to show IP addresses in admin panels

