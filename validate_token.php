<?php
session_start();

$tokenFile = "valid_token.txt"; // File to store the current token

// ✅ Store the latest token and update session
if (isset($_GET['store_token'])) {
    $token = trim($_GET['store_token']);
    
    if (!empty($token)) {
        file_put_contents($tokenFile, $token); // Save token in file
        $_SESSION['token'] = $token; // Store in session
        echo "TOKEN STORED";
    } else {
        echo "INVALID TOKEN";
    }
    exit();
}

// ✅ Validate the stored token (Streamlit fetches this before running)
if (isset($_GET['validate_token'])) {
    if (file_exists($tokenFile)) {
        $stored_token = trim(file_get_contents($tokenFile));
        
        if (isset($_SESSION['token']) && $_SESSION['token'] === $stored_token && !empty($stored_token)) {
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
        unlink($tokenFile); // Delete token file to expire token immediately
    }
    unset($_SESSION['token']); // Remove session token
    session_destroy(); // Destroy entire session
    echo "TOKEN EXPIRED";
    exit();
}

// ✅ If accessed directly without parameters
echo "ACCESS DENIED";
exit();
?>
