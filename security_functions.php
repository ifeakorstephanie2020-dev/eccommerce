<?php
// security_functions.php - Core security functions

// Start secure session
function startSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        // Session security settings
        ini_set('session.use_strict_mode', 1);
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_only_cookies', 1);
        session_start();

        // Regenerate session ID periodically
        if (!isset($_SESSION['created'])) {
            $_SESSION['created'] = time();
        } elseif (time() - $_SESSION['created'] > 1800) { // 30 minutes
            session_regenerate_id(true);
            $_SESSION['created'] = time();
        }
    }
}

// Generate CSRF token
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// Verify CSRF token
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Prevent SQL injection with prepared statements
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Rate limiting
function checkRateLimit($key, $limit = 10, $timeWindow = 60) {
    if (!isset($_SESSION['rate_limit'][$key])) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'first_request' => time()];
        return true;
    }

    $current = time();
    $firstRequest = $_SESSION['rate_limit'][$key]['first_request'];

    if ($current - $firstRequest > $timeWindow) {
        $_SESSION['rate_limit'][$key] = ['count' => 1, 'first_request' => $current];
        return true;
    }

    $_SESSION['rate_limit'][$key]['count']++;
    return $_SESSION['rate_limit'][$key]['count'] <= $limit;
}

// Bot detection with honeypot
function isBot($userAgent = null) {
    if ($userAgent === null) {
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    }

    $botPatterns = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget',
        'python', 'java', 'http-client', 'headless', 'phantom',
        'selenium', 'puppeteer', 'playwright', 'undetected'
    ];

    foreach ($botPatterns as $pattern) {
        if (stripos($userAgent, $pattern) !== false) {
            return true;
        }
    }

    return false;
}

// Generate honeypot field
function generateHoneypot() {
    $fieldName = 'hp_' . bin2hex(random_bytes(8));
    return $fieldName;
}

// XSS Protection headers
function sendSecurityHeaders() {
    header("X-Frame-Options: DENY");
    header("X-Content-Type-Options: nosniff");
    header("X-XSS-Protection: 1; mode=block");
    header("Referrer-Policy: strict-origin-when-cross-origin");

    // Content Security Policy
    header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com https://fonts.googleapis.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: https:; font-src 'self' https://fonts.gstatic.com; connect-src 'self';");

    // HSTS - uncomment when HTTPS is enabled
    // header("Strict-Transport-Security: max-age=31536000; includeSubDomains");
}

// Validate and sanitize email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Get client IP
function getClientIP() {
    $ip = $_SERVER['REMOTE_ADDR'];

    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip = trim($ips[0]);
    }

    return $ip;
}

// Log suspicious activities
function logSuspiciousActivity($message) {
    $logFile = __DIR__ . '/security_log.txt';
    $logEntry = date('Y-m-d H:i:s') . " - IP: " . getClientIP() . " - $message\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Check if user is admin
function isAdmin() {
    return isset($_SESSION["islogged"]) && $_SESSION["islogged"] === TRUE &&
           isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === TRUE;
}
?>
