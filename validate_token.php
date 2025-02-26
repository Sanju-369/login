<?php
// ✅ Ensure session settings are set **before** starting the session
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_only_cookies', 1);
    ini_set('session.use_trans_sid', 0);
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 1); // Ensure this matches your server setup
    ini_set('session.gc_maxlifetime', 3600); // Optional: Set session timeout

    session_start(); // ✅ Start the session only if not already started
} else {
    session_start(); // ✅ Start session safely
}

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

// ✅ Logout: Expire the token and destroy session
if (isset($_GET['logout'])) {
    if (file_exists($tokenFile)) {
        unlink($tokenFile); // Delete token file
    }
    session_unset(); // ✅ Unset all session variables
    session_destroy(); // ✅ Destroy the session
    echo "TOKEN EXPIRED";
    exit();
}
?>
