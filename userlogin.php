<?php
include 'config.php';
include 'security_functions.php';

startSecureSession();
sendSecurityHeaders();

$message = "";
$error = "";

// CSRF Token
$csrf_token = generateCSRFToken();

// Sign up logic
if (isset($_POST['sign'])) {
    // Check rate limiting
    if (!checkRateLimit('signup', 5, 3600)) { // 5 signups per hour
        $error = "Too many signup attempts. Please try again later.";
        logSuspiciousActivity("Rate limit exceeded for signup");
    }
    elseif (isBot()) {
        $error = "Error processing your request.";
        logSuspiciousActivity("Bot detected during signup");
    }
    else {
        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);
        $cpassword = sanitizeInput($_POST['PASSWORD']);
        $email = sanitizeInput($_POST['email'] ?? '');

        // Validate username
        if (strlen($username) < 3) {
            $error = "Username must be at least 3 characters.";
        }
        // Validate password
        elseif (strlen($password) < 8) {
            $error = "Password must be at least 8 characters.";
        }
        elseif ($password !== $cpassword) {
            $error = "Passwords do not match!";
        }
        elseif (!validateEmail($email)) {
            $error = "Please enter a valid email address.";
        }
        else {
            // Hash password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Use prepared statement
            $stmt = $conn->prepare("INSERT INTO users (name, pass, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $email);

            if ($stmt->execute()) {
                $message = "✅ User added successfully! Please login.";
                // Regenerate CSRF token
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                if ($conn->errno === 1062) { // Duplicate entry
                    $error = "Username already exists. Please choose another.";
                } else {
                    $error = "❌ Error: " . $stmt->error;
                }
            }
            $stmt->close();
        }
    }
}

// Login logic
if (isset($_POST["LOGIN"])) {
    // Check rate limiting
    if (!checkRateLimit('login', 5, 300)) { // 5 login attempts per 5 minutes
        $error = "Too many login attempts. Please wait.";
        logSuspiciousActivity("Rate limit exceeded for login");
    }
    else {
        $username = sanitizeInput($_POST['username']);
        $password = sanitizeInput($_POST['password']);

        // Use prepared statement
        $stmt = $conn->prepare("SELECT id, name, pass FROM users WHERE name = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            // Verify password
            if (password_verify($password, $row['pass'])) {
                $_SESSION["name"] = $row['name'];
                $_SESSION["user_id"] = $row['id'];
                $_SESSION["islogged"] = TRUE;
                session_regenerate_id(true);
                header("Location: index.php");
                exit();
            } else {
                $error = "❌ Invalid username or password!";
                logSuspiciousActivity("Failed login attempt for user: $username");
            }
        } else {
            $error = "❌ Invalid username or password!";
            logSuspiciousActivity("Failed login attempt for user: $username");
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
    <title>STEPHIE'S STORE - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <!-- SIGN UP FORM -->
        <article id="signup" style="width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-sm);">
                <button onclick="login()" style="width: 100%;">LOGIN</button>
                <button onclick="signup()" style="width: 100%; background: var(--primary-dark); color: var(--text-white);">Sign Up</button>
            </div>
            <hr style="border: 1px solid var(--border-color); margin: var(--spacing-md) 0;">

            <?php if(isset($message) && isset($_POST['sign'])): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: green;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if(isset($error) && isset($_POST['sign'])): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: red;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <label>USERNAME</label>
                <input type="text" name="username" placeholder="Enter username (min 3 chars)" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required minlength="3">

                <label>EMAIL</label>
                <input type="email" name="email" placeholder="your@email.com" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required>

                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Enter password (min 8 chars)" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required minlength="8">

                <label>CONFIRM PASSWORD</label>
                <input type="password" name="PASSWORD" placeholder="Confirm password" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required>

                <button name="sign" style="width: 100%;">Sign Up</button>
            </form>
        </article>

        <!-- LOGIN FORM -->
        <article id="login" style="display: none; width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-sm);">
                <button onclick="login()" style="width: 100%; background: var(--primary-dark); color: var(--text-white);">LOGIN</button>
                <button onclick="signup()" style="width: 100%;">Sign Up</button>
            </div>
            <hr style="border: 1px solid var(--border-color); margin: var(--spacing-md) 0;">

            <?php if(isset($error) && isset($_POST['LOGIN'])): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: red;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">

                <label>USERNAME</label>
                <input type="text" name="username" placeholder="Enter username" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required>

                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Enter password" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required>

                <button name="LOGIN" type="submit" style="width: 100%;">LOGIN</button>
            </form>
        </article>
    </div>

    <script>
        function signup() {
            document.getElementById("signup").style.display = "block";
            document.getElementById("login").style.display = "none";
        }

        function login() {
            document.getElementById("login").style.display = "block";
            document.getElementById("signup").style.display = "none";
        }
    </script>
    <script src="script.js"></script>
</body>
</html>
