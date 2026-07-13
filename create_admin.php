<?php
include 'config.php';
include '../security_functions.php';

startSecureSession();
sendSecurityHeaders();

$message = "";
$error = "";
$admin_exists = false;

// Check if any admin exists
$check_admin = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
if ($check_admin) {
    $admin_exists = $check_admin->fetch_assoc()['count'] > 0;
}

// Only allow admin creation if:
// 1. No admin exists (first-time setup), OR
// 2. User is already logged in as admin
$is_authorized = !$admin_exists || (isset($_SESSION["islogged"]) && $_SESSION["islogged"] === TRUE && isset($_SESSION["is_admin"]) && $_SESSION["is_admin"] === TRUE);

if (!$is_authorized) {
    // If admin exists and user is not logged in, redirect to login
    header("Location: admin.php");
    exit();
}

// Admin creation logic
if (isset($_POST['create_admin'])) {
    // Rate limiting
    if (!checkRateLimit('create_admin', 3, 3600)) {
        $error = "Too many attempts. Please try again later.";
        logSuspiciousActivity("Admin creation rate limit exceeded");
    }
    // Bot detection
    elseif (isBot()) {
        $error = "Access denied.";
        logSuspiciousActivity("Bot detected attempting admin creation");
    }
    else {
        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);
        $cpassword = sanitizeInput($_POST['cpassword']);
        $email = sanitizeInput($_POST['email']);
        $admin_code = sanitizeInput($_POST['admin_code'] ?? '');

        // Validate inputs
        if (strlen($username) < 3) {
            $error = "Username must be at least 3 characters.";
        }
        elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        }
        elseif ($password !== $cpassword) {
            $error = "Passwords do not match!";
        }
        elseif (!validateEmail($email)) {
            $error = "Please enter a valid email address.";
        }
        // Admin code verification (optional security)
        elseif ($admin_code !== 'ADMIN2026') { // Change this to your own code
            $error = "Invalid admin creation code.";
            logSuspiciousActivity("Invalid admin code attempt: $admin_code");
        }
        else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Check if username already exists
            $check_stmt = $conn->prepare("SELECT id FROM users WHERE name = ?");
            $check_stmt->bind_param("s", $username);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                $error = "Username already exists. Please choose another.";
            } else {
                // Insert new admin user
                $stmt = $conn->prepare("INSERT INTO users (name, pass, email, is_admin, created_at) VALUES (?, ?, ?, 1, NOW())");
                $stmt->bind_param("sss", $username, $hashed_password, $email);

                if ($stmt->execute()) {
                    $message = "✅ Admin account created successfully! You can now login.";
                    logSuspiciousActivity("New admin account created: $username from IP: " . getClientIP());

                    // Auto-login after creation (optional)
                    if (!$admin_exists) {
                        $_SESSION["name"] = $username;
                        $_SESSION["user_id"] = $conn->insert_id;
                        $_SESSION["islogged"] = TRUE;
                        $_SESSION["is_admin"] = TRUE;
                        session_regenerate_id(true);
                        header("Location: admin_store.php");
                        exit();
                    }
                } else {
                    $error = "❌ Error: " . $stmt->error;
                }
                $stmt->close();
            }
            $check_stmt->close();
        }
    }
}

// Get current admin count if logged in
$admin_count = 0;
if (isset($_SESSION["islogged"]) && $_SESSION["islogged"] === TRUE) {
    $count_result = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
    if ($count_result) {
        $admin_count = $count_result->fetch_assoc()['count'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin - STEPHIE'S</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <?php if (isset($_SESSION["islogged"]) && $_SESSION["islogged"] === TRUE): ?>
        <!-- Show admin header if logged in -->
        <header class="container">
            <nav>
                <ul>
                    <li class="brand">
                        <span>S</span>TEPHIE'S ADMIN
                        <span class="admin-badge">👑 Create Admin</span>
                    </li>
                </ul>
                <ul>
                    <li><a href="admin_store.php">View Store</a></li>
                    <li><a href="create.php">Add Products</a></li>
                    <li><a href="order.php">📦 Orders</a></li>
                    <li><a href="logout.php">🚪 Logout</a></li>
                </ul>
            </nav>
        </header>
    <?php endif; ?>

    <div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <article style="width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <center>
                <?php if ($admin_exists): ?>
                    <h2 style="color: var(--primary-dark);">👑 Create New Admin</h2>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                        Current admins: <strong><?php echo $admin_count; ?></strong>
                    </p>
                <?php else: ?>
                    <h2 style="color: var(--primary-dark);">🚀 First-Time Setup</h2>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">
                        Create your first admin account
                    </p>
                <?php endif; ?>
                <div style="width: 60px; height: 2px; background: var(--primary-dark); margin: var(--spacing-sm) auto var(--spacing-lg);"></div>
            </center>

            <?php if($message): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: green; background: var(--bg-card); border-color: green;">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: var(--text-danger); background: var(--bg-card); border-color: var(--text-danger);">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($is_authorized): ?>
                <form method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

                    <label>Username</label>
                    <input type="text" name="username" placeholder="Enter admin username (min 3 chars)" required minlength="3" style="margin-bottom: var(--spacing-md);">

                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="admin@example.com" required style="margin-bottom: var(--spacing-md);">

                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter password (min 8 chars)" required minlength="8" style="margin-bottom: var(--spacing-md);">

                    <label>Confirm Password</label>
                    <input type="password" name="cpassword" placeholder="Confirm password" required style="margin-bottom: var(--spacing-md);">

                    <?php if ($admin_exists): ?>
                        <label>Admin Creation Code</label>
                        <input type="text" name="admin_code" placeholder="Enter admin creation code" required style="margin-bottom: var(--spacing-md);">
                        <small style="color: var(--text-light);">Contact your administrator for the code</small>
                    <?php endif; ?>

                    <br><br>
                    <button type="submit" name="create_admin" style="width: 100%;">
                        <?php echo $admin_exists ? '👑 Create Admin Account' : '🚀 Create First Admin'; ?>
                    </button>
                </form>

                <?php if ($admin_exists): ?>
                    <center style="margin-top: var(--spacing-md);">
                        <p style="font-size: 0.8rem; color: var(--text-light);">
                            <a href="admin.php" style="color: var(--primary-dark);">← Back to Login</a>
                        </p>
                    </center>
                <?php endif; ?>
            <?php endif; ?>
        </article>
    </div>

    <script src="admin-script.js"></script>
</body>
</html>
