# Parayana Dhamma Center - Project Flow Documentation

## 📋 Project Overview

This is a **multi-platform Dhamma Center application** built with:
- **Laravel** (Backend API)
- **React** (Web Frontend)
- **Flutter** (Mobile App)

All three platforms share the same Laravel API backend and provide similar functionality for managing and viewing Dhamma content.

---

## 🏗️ Architecture Overview

```
┌─────────────────┐         ┌─────────────────┐
│   React Web     │         │  Flutter Mobile  │
│   (dhamma_center)│         │   (dhamma_apk)   │
└────────┬────────┘         └────────┬────────┘
         │                            │
         │  HTTP/REST API             │
         │  (Axios/Dio)               │
         │                            │
         └────────────┬───────────────┘
                      │
         ┌────────────▼────────────┐
         │   Laravel Backend       │
         │   (REST API)            │
         │   routes/api.php        │
         └────────────┬────────────┘
                      │
         ┌────────────▼────────────┐
         │   MySQL Database        │
         └─────────────────────────┘

         ┌─────────────────────────┐
         │  Notification Server    │
         │  (Node.js/Socket.IO)    │
         │  + Firebase FCM         │
         └─────────────────────────┘
```

---

## 🔧 Technology Stack

### Backend (Laravel)
- **Framework**: Laravel 10.x
- **PHP**: 8.1+
- **Database**: MySQL
- **Authentication**: Laravel Sanctum
- **Key Packages**:
  - `kreait/firebase-php` - Firebase Cloud Messaging
  - `bavix/laravel-wallet` - Wallet system
  - `guzzlehttp/guzzle` - HTTP client

### Frontend (React)
- **Framework**: React 19.x
- **Build Tool**: Vite
- **Routing**: React Router DOM 7.x
- **HTTP Client**: Axios
- **Styling**: Tailwind CSS 4.x
- **Real-time**: Socket.IO Client
- **State Management**: React Context API

### Mobile (Flutter)
- **Framework**: Flutter 3.8+
- **HTTP Client**: Dio
- **Routing**: GoRouter
- **State Management**: Provider
- **Real-time**: Socket.IO Client
- **Push Notifications**: Firebase Cloud Messaging
- **Storage**: SharedPreferences

---

## 🔄 Project Flow

### 1. **Laravel Backend Structure**

#### API Routes (`routes/api.php`)
- **Public Routes** (`/api/public/*`): No authentication required
  - Posts, Dhamma Talks, Biographies, Donations, Monasteries, Lessons
  - Likes, Comments, Banners
- **Admin Routes** (`/api/admin/*`): Requires Sanctum authentication
  - All CRUD operations for content management
  - Admin authentication and profile management

#### Controllers
- **Admin Controllers** (`app/Http/Controllers/Admin/`):
  - `PostController`, `DhammaController`, `BiographyController`
  - `DonationController`, `MonasteryController`, `LessonController`
  - `CategoryController`, `BannerController`, etc.
- **Public Controllers** (`app/Http/Controllers/Public/`):
  - `PublicPostController`, `PublicDhammaController`, etc.
  - `LikeController`, `CommentController`

#### Services
- **NotificationService** (`app/Services/NotificationService.php`):
  - Sends notifications via Socket.IO server
  - Sends push notifications via Firebase FCM
  - Triggered when admins create/update content

#### Models
- `Post`, `Dhamma`, `Biography`, `Donation`, `Monastery`
- `User`, `Category`, `Comment`, `Like`, `View`
- `Lesson`, `AcademicYear`, `Subject`, `SchoolClass`

---

### 2. **React Web Application Flow**

#### Entry Point
- **`dhamma_center/src/App.jsx`**: Main router configuration
- **Routes**:
  - Public: `/`, `/posts/:id`, `/dhammas/:id`, `/biographies`, etc.
  - Admin: `/admin/login`, `/admin/dashboard`, `/admin/posts`, etc.

#### API Integration
- **Base URL**: `https://goldencitycasino123.pro/api` (configured in `src/hooks/BaseUrl.jsx`)
- **API Service** (`src/services/api.js`):
  - Axios instance with interceptors
  - Auto-adds Bearer token for admin routes
  - Handles FormData for file uploads
- **Public Service** (`src/services/publicService.js`):
  - All public API endpoints (posts, dhammas, biographies, etc.)
- **Auth Service** (`src/services/authService.js`):
  - Login, logout, profile management
  - Token stored in `localStorage`

#### State Management
- **AuthContext** (`src/contexts/AuthContext.jsx`): Admin authentication state
- **NotificationContext** (`src/contexts/NotificationContext.jsx`): Real-time notifications

#### Real-time Notifications
- **NotificationService** (`src/services/notificationService.js`):
  - Connects to Socket.IO server (`https://maxwin688.site`)
  - Generates unique public user ID (stored in `sessionStorage`)
  - Listens for `receive_noti` events
  - Plays sound notification (`public/sounds/noti.wav`)

#### Components Structure
- **Public Components** (`src/components/public/`):
  - `Navbar.jsx`, `Footer.jsx`, `BannerSlider.jsx`
  - `CommentSection.jsx`, `LikeDislike.jsx`
- **Admin Components** (`src/components/admin/`):
  - `AdminLayout.jsx`, `ProtectedRoute.jsx`
  - Form components for CRUD operations

---

### 3. **Flutter Mobile Application Flow**

#### Entry Point
- **`dhamma_apk/lib/main.dart`**:
  - Initializes Firebase and FCM
  - Sets up GoRouter with all routes
  - Provides AuthProvider and NotificationProvider

#### API Integration
- **Base URL**: `https://goldencitycasino123.pro/api` (configured in `lib/config/api_config.dart`)
- **ApiService** (`lib/services/api_service.dart`):
  - Dio instance with interceptors
  - Auto-adds Bearer token from SharedPreferences
  - Handles FormData for multipart requests
- **PublicService** (`lib/services/public_service.dart`):
  - All public API endpoints
- **AuthService** (`lib/services/auth_service.dart`):
  - Admin authentication
  - Token stored in SharedPreferences

#### State Management
- **AuthProvider** (`lib/providers/auth_provider.dart`): Admin auth state
- **NotificationProvider** (`lib/providers/notification_provider.dart`): Notifications state

#### Real-time Notifications
- **NotificationService** (`lib/services/notification_service.dart`):
  - Connects to Socket.IO server
  - Generates unique public user ID (stored in SharedPreferences)
  - Listens for notifications
- **FCMService** (`lib/services/fcm_service.dart`):
  - Firebase Cloud Messaging integration
  - Auto-subscribes to 'public' topic
  - Handles foreground and background notifications

#### Screens Structure
- **Public Screens** (`lib/screens/`):
  - `home_screen.dart`, `post_detail_screen.dart`
  - `dhamma_detail_screen.dart`, `biographies_screen.dart`
  - `donations_screen.dart`, `monasteries_screen.dart`
- **Admin Screens** (`lib/screens/admin/`):
  - `dashboard_admin_screen.dart`, `posts_admin_screen.dart`
  - All admin management screens

---

## 🔔 Notification System Flow

### Dual Notification System

1. **Socket.IO (Real-time)** - When app is open:
   ```
   Admin creates content
   → Laravel NotificationService
   → HTTP POST to notification server (/api/notify)
   → Notification server broadcasts via Socket.IO
   → React/Flutter clients receive notification
   → Play sound + update UI
   ```

2. **Firebase FCM (Push)** - When app is closed:
   ```
   Admin creates content
   → Laravel NotificationService
   → Firebase Cloud Messaging
   → Sends to 'public' topic
   → Flutter app receives push notification
   → Shows system notification
   ```

### Notification Types
- `post`: New post created (only if status is 'published')
- `dhamma`: New dhamma talk created
- `biography`: New biography created
- `lesson`: New lesson created

---

## 📊 Data Flow Examples

### Example 1: Viewing Posts (Public)

**React Flow:**
```
User visits Home page
→ Home.jsx calls publicService.getPosts()
→ Axios GET /api/public/posts
→ Laravel PublicPostController@index
→ Returns JSON with posts
→ React displays posts in UI
```

**Flutter Flow:**
```
User opens HomeScreen
→ HomeScreen calls PublicService.getPosts()
→ Dio GET /api/public/posts
→ Laravel PublicPostController@index
→ Returns JSON with posts
→ Flutter displays posts in ListView
```

### Example 2: Admin Creates Post

**React Admin Flow:**
```
Admin fills form in Posts.jsx
→ Submits via postService.create()
→ Axios POST /api/admin/posts (with Bearer token)
→ Laravel PostController@store
→ Validates and saves to database
→ NotificationService sends notifications
→ Returns success response
→ React refreshes post list
```

**Notification Flow:**
```
PostController@store calls NotificationService
→ NotificationService sends HTTP POST to notification server
→ Notification server broadcasts via Socket.IO
→ React/Flutter clients receive notification
→ UI updates with new post notification
```

### Example 3: Like/Dislike (Public)

**React Flow:**
```
User clicks like button
→ LikeDislike component calls publicService.toggleLike()
→ Axios POST /api/public/likes/toggle
→ Laravel LikeController@toggle
→ Tracks by IP address (no auth needed)
→ Returns updated like counts
→ React updates UI
```

**Flutter Flow:**
```
User taps like button
→ LikeDislike widget calls PublicService.toggleLike()
→ Dio POST /api/public/likes/toggle
→ Laravel LikeController@toggle
→ Returns updated counts
→ Flutter updates UI
```

---

## 🔐 Authentication Flow

### Admin Authentication

**React:**
1. Admin enters credentials in Login.jsx
2. `authService.login()` → POST `/api/admin/login`
3. Laravel returns token + user data
4. Token stored in `localStorage` as `admin_token`
5. Axios interceptor adds `Authorization: Bearer {token}` to admin requests
6. Protected routes check `AuthContext.isAuthenticated`

**Flutter:**
1. Admin enters credentials in LoginScreen
2. `AuthService.login()` → POST `/api/admin/login`
3. Token stored in SharedPreferences as `admin_token`
4. Dio interceptor adds token to admin requests
5. GoRouter redirects unauthenticated users from admin routes

---

## 📁 Key Directories

### Laravel
```
app/
├── Http/Controllers/
│   ├── Admin/          # Admin CRUD controllers
│   └── Public/         # Public read-only controllers
├── Models/             # Eloquent models
├── Services/           # Business logic (NotificationService, etc.)
└── Middleware/         # Auth, CORS, etc.

routes/
└── api.php             # All API routes

database/
├── migrations/         # Database schema
└── seeders/            # Initial data
```

### React
```
dhamma_center/
├── src/
│   ├── components/
│   │   ├── public/     # Public UI components
│   │   └── admin/      # Admin UI components
│   ├── pages/
│   │   ├── public/     # Public pages
│   │   └── admin/      # Admin pages
│   ├── services/       # API services
│   ├── contexts/       # React contexts (Auth, Notification)
│   └── hooks/          # Custom hooks
```

### Flutter
```
dhamma_apk/
├── lib/
│   ├── screens/        # All screens
│   ├── widgets/        # Reusable widgets
│   ├── services/       # API services
│   ├── providers/      # State management
│   ├── models/         # Data models
│   └── config/         # Configuration
```

---

## 🌐 API Endpoints Summary

### Public Endpoints (No Auth)
- `GET /api/public/posts` - List posts
- `GET /api/public/posts/{id}` - Get post detail
- `GET /api/public/dhammas` - List dhamma talks
- `GET /api/public/dhammas/{id}` - Get dhamma detail
- `GET /api/public/biographies` - List biographies
- `GET /api/public/biographies/{id}` - Get biography detail
- `GET /api/public/donations` - List approved donations
- `GET /api/public/monasteries` - List monasteries
- `GET /api/public/lessons` - List lessons
- `GET /api/public/banners` - Get active banners
- `POST /api/public/likes/toggle` - Toggle like/dislike
- `GET /api/public/likes/counts` - Get like counts
- `GET /api/public/comments` - Get comments
- `POST /api/public/comments` - Add comment

### Admin Endpoints (Auth Required)
- `POST /api/admin/login` - Admin login
- `GET /api/admin/check` - Check auth status
- `POST /api/admin/logout` - Logout
- `GET /api/admin/profile` - Get profile
- `PUT /api/admin/profile` - Update profile
- `POST /api/admin/change-password` - Change password
- `GET /api/admin/posts` - List posts (admin)
- `POST /api/admin/posts` - Create post
- `PUT /api/admin/posts/{id}` - Update post
- `DELETE /api/admin/posts/{id}` - Delete post
- (Similar CRUD for dhammas, biographies, donations, etc.)

---

## 🚀 Development Setup

### Laravel
```bash
# Install dependencies
composer install

# Setup environment
cp .env.example .env
php artisan key:generate

# Run migrations
php artisan migrate

# Seed database
php artisan db:seed

# Start server
php artisan serve
```

### React
```bash
cd dhamma_center
npm install
npm run dev
```

### Flutter
```bash
cd dhamma_apk
flutter pub get
flutter run
```

---

## 🔗 Configuration Files

### API Base URLs
- **React**: `dhamma_center/src/hooks/BaseUrl.jsx`
- **Flutter**: `dhamma_apk/lib/config/api_config.dart`

### Notification Server
- **URL**: `https://maxwin688.site`
- **Laravel Config**: `config/notification.php`
- **React**: `VITE_NOTIFICATION_SERVER_URL` env variable
- **Flutter**: Hardcoded in `notification_service.dart`

### Firebase
- **Service Account**: `storage/app/firebase/notification-88e7c-c50770d57b15.json`
- **Project ID**: `notification-88e7c`

---

## 📝 Key Features

1. **Content Management**: Posts, Dhamma Talks, Biographies, Lessons
2. **Donations**: Track and display donations
3. **Monasteries**: Manage monastery information
4. **Interactions**: Likes, Comments (no auth required)
5. **Real-time Notifications**: Socket.IO + FCM
6. **Admin Panel**: Full CRUD for all content
7. **View Tracking**: Track content views
8. **Banners**: Manage homepage banners
9. **Academic System**: Years, Subjects, Classes, Lessons

---

## 🔄 Common Workflows

### Creating Content (Admin)
1. Admin logs in (React/Flutter)
2. Navigates to content section (e.g., Posts)
3. Clicks "Create New"
4. Fills form with title, content, images, etc.
5. Submits form
6. Laravel validates and saves
7. Notification sent to all public users
8. UI refreshes with new content

### Viewing Content (Public)
1. User opens app (React/Flutter)
2. Sees homepage with recent posts/dhammas
3. Clicks on item to view detail
4. Can like, comment, view related content
5. Receives notifications for new content

---

## 🛠️ Important Notes

1. **CORS**: Laravel must allow requests from React/Flutter origins
2. **File Uploads**: Both React and Flutter use FormData for image uploads
3. **Authentication**: Only admin routes require authentication
4. **Public Users**: No registration needed; tracked by IP/session ID
5. **Notifications**: Dual system (Socket.IO for real-time, FCM for push)
6. **Database**: MySQL with Eloquent ORM
7. **Storage**: Laravel handles file storage in `storage/app/public`

---

This documentation provides a comprehensive overview of how Laravel, React, and Flutter work together in this project. All three platforms share the same API backend and provide consistent functionality across web and mobile platforms.

