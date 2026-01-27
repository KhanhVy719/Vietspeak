const express = require('express');
const cors = require('cors');
const path = require('path');

const app = express();
const PORT = 3000;

// Middleware
app.use(cors());
app.use(express.json());
app.use(express.static(__dirname));

// Routes
app.get('/', (req, res) => {
    res.sendFile(path.join(__dirname, 'index.html'));
});

app.get('/login', (req, res) => {
    res.sendFile(path.join(__dirname, 'login.html'));
});

app.get('/account', (req, res) => {
    res.sendFile(path.join(__dirname, 'account.html'));
});

app.get('/khoa-hoc', (req, res) => {
    res.sendFile(path.join(__dirname, 'khoa-hoc.html'));
});

app.get('/ai', (req, res) => {
    res.sendFile(path.join(__dirname, 'ai.html'));
});

app.get('/thu-vien', (req, res) => {
    res.sendFile(path.join(__dirname, 'thu-vien.html'));
});

app.get('/camnang', (req, res) => {
    res.sendFile(path.join(__dirname, 'camnang.html'));
});

// Start server
app.listen(PORT, () => {
    console.log(`🚀 VietSpeak server running at http://localhost:${PORT}`);
    console.log(`📚 Pages available:`);
    console.log(`   - Homepage: http://localhost:${PORT}`);
    console.log(`   - Login: http://localhost:${PORT}/login`);
    console.log(`   - Account: http://localhost:${PORT}/account`);
    console.log(`   - Courses: http://localhost:${PORT}/khoa-hoc`);
    console.log(`   - AI Analysis: http://localhost:${PORT}/ai`);
    console.log(`   - Library: http://localhost:${PORT}/thu-vien`);
});

app.get('/register', (req, res) => {
    res.sendFile(path.join(__dirname, 'register.html'));
});
