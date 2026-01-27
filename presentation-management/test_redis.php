<?php
try {
    $redis = new Redis();
    $result = $redis->connect('host.docker.internal', 6379);
    if ($result) {
        echo "✅ Redis connection successful!\n";
    } else {
        echo "❌ Redis connection failed\n";
    }
} catch (Exception $e) {
    echo "❌ Redis error: " . $e->getMessage() . "\n";
}
