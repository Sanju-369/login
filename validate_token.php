<?php
session_start();

$tokenFile = "valid_token.txt"; // File to store the current valid token

// ✅ Store the latest token when the user logs in
if (isset($_GET['store_token'])) {
    $token = $_GET['store_token'];
    file_put_contents($tokenFile, $token); // Save the token to a file
    $_SESSION['token'] = $token; // Store token in session
    echo "TOKEN STORED";
    exit();
}

// ✅ Validate the stored token (Streamlit fetches this)
if (isset($_GET['validate_token'])) {
    if (file_exists($tokenFile)) {
        $stored_token = trim(file_get_contents($tokenFile));

        // Ensure the stored token matches the session token
        if (!empty($stored_token) && isset($_SESSION['token']) && $_SESSION['token'] === $stored_token) {
            echo "VALID"; // ✅ Token is valid
        } else {
            echo "INVALID"; // ❌ Token is invalid
        }
    } else {
        echo "INVALID"; // ❌ No token found
    }
    exit();
}

// ✅ Logout: Expire the token and destroy session
if (isset($_GET['logout'])) {
    if (file_exists($tokenFile)) {
        unlink($tokenFile); // Delete the token file
    }
    unset($_SESSION['token']); // Remove token from session
    session_destroy(); // Completely destroy the session
    echo "TOKEN EXPIRED";
    exit();
}
?>
