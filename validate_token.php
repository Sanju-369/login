<?php
session_start();
header("Content-Type: text/plain");

// File to store tokens
$token_file = "tokens.json";

// Function to load tokens from file
function loadTokens() {
    global $token_file;
    if (!file_exists($token_file)) {
        return [];
    }
    $data = file_get_contents($token_file);
    return json_decode($data, true) ?: [];
}

// Function to save tokens to file
function saveTokens($tokens) {
    global $token_file;
    file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT));
}

// ✅ Generate a new token
if (isset($_GET['action']) && $_GET['action'] === "generate") {
    $new_token = bin2hex(random_bytes(16)); // Generate a secure token
    $tokens = loadTokens();
    $tokens[] = $new_token; // Add new token to list
    saveTokens($tokens);
    echo "NEW_TOKEN: $new_token";
    exit();
}

// ✅ Validate the token
if (!isset($_GET['token']) || empty($_GET['token'])) {
    http_response_code(400);
    echo "MISSING_TOKEN";
    exit();
}

$token = $_GET['token'];
$tokens = loadTokens();

// ✅ Check if token exists
if (in_array($token, $tokens)) {
    echo "VALID";
} else {
    http_response_code(403);
    echo "INVALID_TOKEN";
}
exit();
?>
