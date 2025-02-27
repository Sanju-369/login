<?php
session_start();
header("Content-Type: text/plain");

// ---------- Database Connection Setup ----------
// Update these values with your actual PostgreSQL settings
$dsn = "pgsql:host=dpg-cuv9sadsvqrc73btnrcg-a;dbname=sam_ttbj;port=5432";
$db_user = "sam_ttbj_user";
$db_pass = "ELmECV1xOPM5DmcIp5mR5y2zkBCBu5Oc";

try {
    $pdo = new PDO($dsn, $db_user, $db_pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

$table = "auth_tokens"; // Table to store tokens

// ---------- STORE TOKEN ----------
// When index.php sends ?store_token=TOKEN, store it in the database and set a secure cookie.
if (isset($_GET['store_token'])) {
    $token = trim($_GET['store_token']);
    if (!empty($token)) {
        // Set expiry time for 1 hour from now.
        $expires_at = date("Y-m-d H:i:s", time() + 3600);
        // Insert or update token in the database (using token as unique key)
        $stmt = $pdo->prepare("INSERT INTO $table (token, expires_at) VALUES (:token, :expires_at)
                                ON CONFLICT (token) DO UPDATE SET expires_at = :expires_at");
        $stmt->execute(['token' => $token, 'expires_at' => $expires_at]);
        
        // Set a secure, HTTP-only cookie for the token (expires in 1 hour)
        setcookie("auth_token", $token, time() + 3600, "/", "", true, true);
        
        echo "TOKEN STORED";
    } else {
        echo "INVALID TOKEN";
    }
    exit();
}

// ---------- VALIDATE TOKEN ----------
// When Streamlit calls ?validate_token=1, validate using the secure cookie and database.
if (isset($_GET['validate_token'])) {
    // Read token from the secure cookie.
    $token = isset($_COOKIE['auth_token']) ? trim($_COOKIE['auth_token']) : "";
    if (empty($token)) {
        echo "INVALID";
        exit();
    }
    // Fetch token record from the database.
    $stmt = $pdo->prepare("SELECT expires_at FROM $table WHERE token = :token");
    $stmt->execute(['token' => $token]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        // Check if the token is expired.
        if (strtotime($result['expires_at']) < time()) {
            echo "EXPIRED";
        } else {
            echo "VALID";
        }
    } else {
        echo "INVALID";
    }
    exit();
}

// ---------- LOGOUT ----------
// When logout is requested, delete the token from the database and clear the cookie.
if (isset($_GET['logout'])) {
    // Read token from cookie.
    $token = isset($_COOKIE['auth_token']) ? trim($_COOKIE['auth_token']) : "";
    if (!empty($token)) {
        $stmt = $pdo->prepare("DELETE FROM $table WHERE token = :token");
        $stmt->execute(['token' => $token]);
    }
    // Clear the cookie.
    setcookie("auth_token", "", time() - 3600, "/", "", true, true);
    session_unset();
    session_destroy();
    echo "TOKEN EXPIRED";
    exit();
}

// ---------- Default Response ----------
echo "ACCESS DENIED";
exit();
?>
