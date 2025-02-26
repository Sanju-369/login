<?php
session_start();
$tokenFile = "valid_token.txt"; // File to store the current token

// ✅ Store the latest token
if (isset($_GET['store_token'])) {
    $token = $_GET['store_token'];
    file_put_contents($tokenFile, $token);
    $_SESSION['token'] = $token; // Store in session
    echo "TOKEN STORED";
    exit();
}

// ✅ Validate token
if (isset($_GET['get_token'])) {
    if (file_exists($tokenFile)) {
        $stored_token = trim(file_get_contents($tokenFile));

        if (!empty($stored_token) && isset($_SESSION['token']) && $_SESSION['token'] === $stored_token) {
            echo "VALID"; // ✅ Token is valid
        } else {
            echo "INVALID"; // ❌ Token mismatch
        }
    } else {
        echo "INVALID"; // ❌ No token found
    }
    exit();
}

// ✅ Logout: Expire the token
if (isset($_GET['logout'])) {
    if (file_exists($tokenFile)) {
        unlink($tokenFile); // Delete token file
    }
    unset($_SESSION['token']); // Remove session token
    session_destroy(); // Completely destroy session
    echo "TOKEN EXPIRED";
    exit();
}
?>
