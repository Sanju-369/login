<?php
session_start();
$tokenFile = "valid_token.txt"; // File to store the current token

// ✅ Store the latest token and update session
if (isset($_GET['store_token'])) {
    $token = $_GET['store_token'];
    file_put_contents($tokenFile, $token); // Save token in file
    $_SESSION['token'] = $token; // Store in session
    echo "TOKEN STORED";
    exit();
}

// ✅ Validate the stored token (Streamlit fetches this before running)
if (isset($_GET['get_token'])) {
    if (file_exists($tokenFile)) {
        $stored_token = trim(file_get_contents($tokenFile));
        
        if ($_SESSION['token'] === $stored_token && !empty($stored_token)) {
            echo "VALID"; // Token is valid
        } else {
            echo "INVALID"; // Token mismatch
        }
    } else {
        echo "INVALID"; // No token found
    }
    exit();
}

// ✅ Logout: Expire the token and destroy session
if (isset($_GET['logout'])) {
    if (file_exists($tokenFile)) {
        unlink($tokenFile); // Delete token file
    }
    unset($_SESSION['token']); // Remove session token
    session_destroy(); // Completely destroy the session
    echo "TOKEN EXPIRED";
    exit();
}
?>
