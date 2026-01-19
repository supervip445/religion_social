// ====== UPDATED NOTIFICATION SERVER CODE ======
// Replace your existing server.js/index.js with this code

require("dotenv").config();

const express = require("express");
const http = require("http");
const socketIo = require("socket.io");
const cors = require("cors");

const app = express();
app.use(express.json());
app.use(cors());

// ====== In-Memory Online User Storage ======
const onlineUsers = new Map();

const server = http.createServer(app);
const io = socketIo(server, {
  cors: { origin: "*" },
});

// ====== HTTP ENDPOINT FOR LARAVEL ======
// This allows Laravel to send notifications via HTTP POST
app.post('/api/notify', (req, res) => {
  const { to_user_id, title, body, notification_data } = req.body;

  if (!to_user_id || !title || !body) {
    return res.status(400).json({ error: 'Missing required fields' });
  }

  // If to_user_id is 'public' or 'all', broadcast to all online users
  if (to_user_id === 'public' || to_user_id === 'all') {
    console.log(`📢 Broadcasting notification to all users: ${title}`);
    io.emit('receive_noti', {
      to_user_id: 'public',
      title,
      body,
      notification_data,
    });
    return res.json({ success: true, message: 'Notification broadcasted to all users' });
  }

  // Send to specific user
  const targetSocketId = onlineUsers.get(to_user_id);
  if (targetSocketId) {
    console.log(`📨 Sending noti to user_id=${to_user_id}`);
    io.to(targetSocketId).emit('receive_noti', {
      to_user_id,
      title,
      body,
      notification_data,
    });
    return res.json({ success: true, message: 'Notification sent' });
  } else {
    console.log(`⚠️ User ${to_user_id} is not online.`);
    return res.json({ success: false, message: 'User not online' });
  }
});

// ====== SOCKET CONNECTION ======
io.on("connection", (socket) => {
  console.log("🔌 Client connected:", socket.id);

  // ====== REGISTER USER ======
  socket.on("register", (user_id) => {
    if (!user_id) return;

    onlineUsers.set(user_id, socket.id);
    console.log(`✅ User registered: user_id=${user_id}, socket=${socket.id}`);
    io.emit("online_users", Array.from(onlineUsers.keys()));
  });

  // ====== SEND NOTIFICATION ======
  socket.on("send_noti", (data) => {
    const { to_user_id } = data;
    if (!to_user_id) {
      console.log("❌ Missing to_user_id in send_noti");
      return;
    }

    // Handle public broadcasts
    if (to_user_id === 'public' || to_user_id === 'all') {
      console.log(`📢 Broadcasting notification to all users: ${data.title}`);
      io.emit('receive_noti', data);
      return;
    }

    const targetSocketId = onlineUsers.get(to_user_id);
    if (targetSocketId) {
      console.log(`📨 Sending noti to user_id=${to_user_id}`);
      io.to(targetSocketId).emit("receive_noti", data);
    } else {
      console.log(`⚠️ User ${to_user_id} is not online.`);
    }
  });

  // ====== ON DISCONNECT ======
  socket.on("disconnect", () => {
    console.log("❌ Disconnected:", socket.id);
    // remove from online list
    for (let [uid, sid] of onlineUsers.entries()) {
      if (sid === socket.id) {
        onlineUsers.delete(uid);
        break;
      }
    }
    io.emit("online_users", Array.from(onlineUsers.keys()));
  });
});

// ====== START SERVER ======
const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
  console.log(`🚀 Notification Server running on port ${PORT}`);
});

