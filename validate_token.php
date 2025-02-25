<?php
session_start();

// Check if token is provided
if (!isset($_GET['token'])) {
    http_response_code(400);
    echo "MISSING_TOKEN";
    exit();
}

$token = $_GET['token'];

// Validate the token
if (isset($_SESSION['token']) && $_SESSION['token'] === $token) {
    echo "VALID";
} else {
    http_response_code(403);
    echo "INVALID_TOKEN";
}
exit();
?>
