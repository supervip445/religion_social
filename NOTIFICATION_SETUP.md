# Notification System Setup Guide

## Overview
This notification system allows public users (without authentication) to receive real-time notifications with sound when admins create new posts, dhamma talks, or biographies.

## Components

### 1. Laravel Backend
- **NotificationService** (`app/Services/NotificationService.php`): Sends notifications via HTTP to the notification server
- **Controllers Updated**: 
  - `PostController.php` - Sends notification when post is published
  - `DhammaController.php` - Sends notification when dhamma talk is created
  - `BiographyController.php` - Sends notification when biography is created

### 2. Notification Server (Node.js/Socket.IO)
- **File**: `notification_server_http_endpoint.js`
- **Location**: Update your existing notification server with this code
- **Features**:
  - HTTP endpoint `/api/notify` for Laravel to send notifications
  - Socket.IO for real-time communication with clients
  - Broadcasts to all users when `to_user_id` is 'public' or 'all'

### 3. React Frontend
- **NotificationService** (`dhamma_center/src/services/notificationService.js`): Socket.IO client
- **NotificationContext** (`dhamma_center/src/contexts/NotificationContext.jsx`): React context for notifications
- **NotificationBell** (`dhamma_center/src/components/public/NotificationBell.jsx`): UI component with bell icon
- **Sound File**: `public/sounds/noti.wav` (already exists)

### 4. Flutter App
- **NotificationService** (`dhamma_apk/lib/services/notification_service.dart`): Socket.IO client
- **NotificationProvider** (`dhamma_apk/lib/providers/notification_provider.dart`): State management
- **NotificationBell** (`dhamma_apk/lib/widgets/notification_bell.dart`): UI widget

## Setup Instructions

### Step 1: Update Notification Server
1. Copy the code from `notification_server_http_endpoint.js`
2. Replace your existing notification server code with the updated version
3. Restart your notification server
4. The server should now accept HTTP POST requests at `/api/notify`

### Step 2: Configure Laravel
1. The notification server URL is already configured in `config/notification.php`
2. Default URL: `https://maxwin688.site`
3. Update `.env` if needed:
   ```
   NOTIFICATION_SERVER_URL=https://maxwin688.site
   NOTIFICATION_SERVER_ENDPOINT=/
   ```

### Step 3: Install Flutter Dependencies
```bash
cd dhamma_apk
flutter pub get
```

### Step 4: Test the System
1. Start your Laravel server
2. Start your notification server
3. Open the React app in a browser
4. Open the Flutter app
5. Create a new post/dhamma talk/biography in the admin panel
6. Both React and Flutter apps should receive notifications with sound

## How It Works

1. **Admin creates content** → Laravel controller calls `NotificationService`
2. **NotificationService** → Sends HTTP POST to notification server `/api/notify`
3. **Notification server** → Receives HTTP request and broadcasts via Socket.IO
4. **Public users** → Connected via Socket.IO, receive notification and play sound
5. **UI updates** → Notification bell shows unread count, dropdown shows notifications

## Public User ID System
- Public users don't have accounts
- Each session gets a unique ID stored in `sessionStorage` (React) or `SharedPreferences` (Flutter)
- Format: `public_[timestamp]_[random]`
- This ID is registered with the notification server for tracking

## Notification Types
- `post`: New post created (only if status is 'published')
- `dhamma`: New dhamma talk created
- `biography`: New biography created

## Routes
Notifications include routes that navigate users to the relevant page:
- Posts: `/posts/{id}`
- Dhamma Talks: `/dhammas/{id}`
- Biographies: `/biographies/{id}`

## Troubleshooting

### Notifications not working?
1. Check notification server is running
2. Check Laravel logs for notification sending errors
3. Check browser console (React) or debug console (Flutter) for connection errors
4. Verify sound file exists at `public/sounds/noti.wav`

### Sound not playing?
1. Check browser permissions for audio (React)
2. Check Flutter audio player permissions
3. Verify sound file path is correct
4. Try loading sound from network URL directly

### Socket.IO connection issues?
1. Check CORS settings on notification server
2. Verify notification server URL is correct
3. Check firewall/network settings
4. Look for connection errors in console

