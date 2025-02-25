<?php
session_start();
$tokenFile = "valid_token.txt"; // Token storage file

// Force session persistence
ini_set("session.use_cookies", 1);
ini_set("session.use_only_cookies", 1);
ini_set("session.cookie_lifetime", 3600); // Keep session alive for 1 hour
ini_set("session.gc_maxlifetime", 3600); 

// ✅ Store a new token in both session and file
if (isset($_GET['store_token'])) {
    $token = $_GET['store_token'];
    
    // Store token in a file
    file_put_contents($tokenFile, $token); 
    
    // Store token in session
    $_SESSION['token'] = $token; 
    
    echo "TOKEN STORED";
    exit();
}

// ✅ Retrieve and validate the stored token
if (isset($_GET['get_token'])) {
    if (file_exists($tokenFile)) {
        $stored_token = trim(file_get_contents($tokenFile));

        if (!empty($stored_token) && isset($_SESSION['token']) && $_SESSION['token'] === $stored_token) {
            echo "VALID"; // Token is valid
        } else {
            echo "INVALID"; // Token mismatch
        }
    } else {
        echo "INVALID"; // No token found
    }
    exit();
}

// ✅ Logout: Remove the token and destroy the session
if (isset($_GET['logout'])) {
    if (file_exists($tokenFile)) {
        unlink($tokenFile); // Delete token file
    }
    
    // Remove token from session
    unset($_SESSION['token']);
    
    // Completely destroy the session
    session_destroy(); 
    
    echo "TOKEN EXPIRED";
    exit();
}
?>
