<?php
session_start();
header("Content-Type: text/plain");

// ---------- Database Connection Setup ----------
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
if (isset($_GET['store_token'])) {
    $token = trim($_GET['store_token']);
    if (!empty($token)) {
        $expires_at = date("Y-m-d H:i:s", time() + 3600); // 1-hour expiration

        try {
            $stmt = $pdo->prepare("INSERT INTO $table (token, expires_at) VALUES (:token, :expires_at)
                                   ON CONFLICT (token) DO UPDATE SET expires_at = :expires_at");
            $stmt->execute(['token' => $token, 'expires_at' => $expires_at]);

            // Set a secure, HTTP-only cookie for 1 hour
            setcookie("auth_token", $token, time() + 3600, "/", "", true, true);
            
            echo "TOKEN STORED";
        } catch (PDOException $e) {
            echo "SQL ERROR: " . $e->getMessage();
        }
    } else {
        echo "INVALID TOKEN";
    }
    exit();
}

// ---------- VALIDATE TOKEN ----------
if (isset($_GET['validate_token'])) {
    // Read token from secure cookie
    $token = isset($_COOKIE['auth_token']) ? trim($_COOKIE['auth_token']) : "";
    
    if (empty($token)) {
        echo "INVALID";
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT expires_at FROM $table WHERE token = :token");
        $stmt->execute(['token' => $token]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            if (strtotime($result['expires_at']) < time()) {
                echo "EXPIRED";
            } else {
                echo "VALID";
            }
        } else {
            echo "INVALID";
        }
    } catch (PDOException $e) {
        echo "SQL ERROR: " . $e->getMessage();
    }
    exit();
}

// ---------- LOGOUT ----------
if (isset($_GET['logout'])) {
    $token = isset($_COOKIE['auth_token']) ? trim($_COOKIE['auth_token']) : "";

    if (!empty($token)) {
        try {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE token = :token");
            $stmt->execute(['token' => $token]);
        } catch (PDOException $e) {
            echo "SQL ERROR: " . $e->getMessage();
        }
    }

    // Clear the secure cookie
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
