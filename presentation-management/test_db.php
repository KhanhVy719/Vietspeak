<?php
$host = '172.20.0.5';
$port = '5432';
$db = 'presentation_management';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    echo "✅ Connected successfully to PostgreSQL at $host\n";
} catch (PDOException $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
}
