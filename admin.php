<?php
include 'config.php';
include '../security_functions.php';

startSecureSession();
sendSecurityHeaders();

$error = "";

// Redirect if already logged in
if (isset($_SESSION["islogged"]) && $_SESSION["islogged"] === TRUE) {
    header("Location: admin_store.php");
    exit();
}

// Login logic with rate limiting and security
if (isset($_POST["submit"])) {
    $username = sanitizeInput($_POST["username"]);
    $password = sanitizeInput($_POST["password"]);

    // Rate limiting
    if (!checkRateLimit('admin_login', 5, 300)) {
        $error = "Too many login attempts. Please wait 5 minutes.";
        logSuspiciousActivity("Admin login rate limit exceeded for IP: " . getClientIP());
    }
    // Bot detection
    elseif (isBot()) {
        $error = "Access denied.";
        logSuspiciousActivity("Bot detected attempting admin login");
    }
    else {
        // Use prepared statement with hashed passwords
        $stmt = $conn->prepare("SELECT id, name, pass FROM users WHERE name = ? AND is_admin = 1");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify hashed password
            if (password_verify($password, $row['pass'])) {
                $_SESSION["name"] = $row['name'];
                $_SESSION["user_id"] = $row['id'];
                $_SESSION["islogged"] = TRUE;
                $_SESSION["is_admin"] = TRUE;
                session_regenerate_id(true);

                // Log successful login
                logSuspiciousActivity("Admin login successful: $username from IP: " . getClientIP());
                header("Location: admin_store.php");
                exit();
            } else {
                $error = "Incorrect password or username.";
                logSuspiciousActivity("Failed admin login attempt for: $username");
            }
        } else {
            $error = "Incorrect password or username.";
            logSuspiciousActivity("Failed admin login attempt for: $username");
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S ADMIN - Login</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <article style="width: 100%; max-width: 450px; padding: var(--spacing-xl);">
            <center>
                <h1 style="color: var(--primary-dark);">S<span style="font-size: 1.5rem;">TEPHIE'S</span></h1>
                <h2 style="font-size: 1.2rem; letter-spacing: 4px; color: var(--text-secondary);">ADMIN LOGIN</h2>
                <div style="width: 60px; height: 2px; background: var(--primary-dark); margin: var(--spacing-md) auto;"></div>
            </center>

            <?php if($error): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: var(--text-danger); background: var(--bg-card); border-color: var(--text-danger);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

                <label>Username</label>
                <input type="text" placeholder="Enter username" name="username" required style="margin-bottom: var(--spacing-md);">

                <label>Password</label>
                <input type="password" placeholder="Enter password" name="password" required style="margin-bottom: var(--spacing-lg);">

                <input type="submit" name="submit" value="Login" style="width: 100%;">
            </form>

            <center style="margin-top: var(--spacing-md);">
                <p style="font-size: 0.8rem; color: var(--text-light);">
                    Need to create an admin? <a href="create_admin.php" style="color: var(--primary-dark);">Create Admin</a>
                </p>
            </center>
        </article>
    </div>
    <script src="admin-script.js"></script>
</body>
</html>
