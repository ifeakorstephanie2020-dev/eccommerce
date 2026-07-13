<?php
include "config.php";
include "security_functions.php";

// Start secure session
startSecureSession();
sendSecurityHeaders();

// Check if id is set
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = (int)$_GET["id"];

// Use prepared statement
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$results = $stmt->get_result();
$row = $results->fetch_assoc();

// Check if product exists
if (!$row) {
    header("Location: index.php");
    exit();
}

$message = "";
$error = "";

// CSRF Token
$csrf_token = generateCSRFToken();

// Bot detection with honeypot
$honeypot = generateHoneypot();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order'])) {
    // Check honeypot
    if (!empty($_POST[$honeypot])) {
        logSuspiciousActivity("Honeypot triggered - possible bot");
        $error = "Error processing your request.";
    }
    // CSRF Check
    elseif (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = "Security token mismatch.";
        logSuspiciousActivity("CSRF validation failed");
    }
    // Rate limiting
    elseif (!checkRateLimit('order_' . $id, 3, 300)) { // 3 orders per 5 minutes
        $error = "You're ordering too quickly. Please wait.";
        logSuspiciousActivity("Rate limit exceeded for product ID: $id");
    }
    // Bot detection
    elseif (isBot()) {
        $error = "Error processing your request.";
        logSuspiciousActivity("Bot detected attempting to order product ID: $id");
    }
    else {
        $email = sanitizeInput($_POST["email"]);
        $ip = getClientIP();
        $userAgent = sanitizeInput($_SERVER['HTTP_USER_AGENT'] ?? '');

        if (!validateEmail($email)) {
            $error = "Please enter a valid email address.";
        } else {
            // Use prepared statement
            $stmt = $conn->prepare("INSERT INTO orders (email, product_id, ip_address, user_agent) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("siss", $email, $id, $ip, $userAgent);

            if ($stmt->execute()) {
                $message = "✅ Order placed successfully!";
                // Regenerate CSRF token after successful order
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                $error = "❌ Error: " . $stmt->error;
                logSuspiciousActivity("Order insertion failed: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S STORE - Order</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <nav style="padding: 20px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-nav); border-bottom: 3px double var(--border-color);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <button style="padding: 3px; height: 50px; width: 50px; font-size: 2rem; font-family: var(--font-heading); background: var(--bg-main); border: 2px solid var(--primary-dark); color: var(--primary-dark);">S</button>
            <span style="text-transform: uppercase; letter-spacing: 2px; font-size: 1.2rem;">Place Order</span>
        </div>
        <a href="index.php"><button style="padding: var(--spacing-sm) var(--spacing-lg); background: var(--primary-dark); color: var(--text-white); border: none;">Order More</button></a>
    </nav>

    <div class="container" style="display: flex; flex-wrap: wrap; gap: var(--spacing-xl); padding: var(--spacing-xl) 0; justify-content: center;">
        <article style="flex: 1; min-width: 280px; max-width: 500px; height: fit-content;">
            <h2>Order Summary</h2>
            <div style="display: flex; align-items: center; gap: var(--spacing-lg);">
                <img height="80px" width="120px" src="https://picsum.photos/200/200" alt="<?php echo htmlspecialchars($row["name"]); ?>" style="border: 2px solid var(--border-color); filter: sepia(0.3);" />
                <div>
                    <strong style="color: var(--primary-dark); font-family: var(--font-heading); font-size: 1.2rem;"><?php echo htmlspecialchars($row["name"]); ?></strong><br>
                    <?php echo htmlspecialchars($row["description"]); ?><br>
                    <span style="font-size: 1.3rem; color: var(--primary-dark);">$<?php echo htmlspecialchars($row["price"]); ?></span>
                </div>
            </div>
            <br>
            <article style="background: var(--bg-dark); border: 1px solid var(--border-color); padding: var(--spacing-lg); margin-top: var(--spacing-md);">
                <h3>Order Process:</h3>
                <ol style="padding-left: var(--spacing-lg); color: var(--text-secondary);">
                    <li>Enter your email address</li>
                    <li>Click "Place Order"</li>
                    <li>Check your email for confirmation</li>
                    <li>No payment required (demo system)</li>
                </ol>
            </article>
        </article>

        <article style="flex: 1; min-width: 280px; max-width: 500px; height: fit-content;">
            <h2>Complete Order</h2>

            <?php if($message): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: green;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <?php if($error): ?>
                <div class="message" style="margin-bottom: var(--spacing-md); color: red;"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="post">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <!-- Honeypot field - invisible to humans -->
                <div style="display: none;">
                    <input type="text" name="<?php echo htmlspecialchars($honeypot); ?>" value="">
                    <input type="text" name="website" value="">
                </div>

                <label>Your Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);" required>
                <small style="color: var(--text-light);">We'll send the product name to this email</small>
                <br><br>
                <center>
                    <button type="submit" name="order" style="width: 100%;">Place Order</button><br><br>
                    <a href="index.php" style="color: var(--text-secondary);">Back to store</a>
                </center>
            </form>
        </article>
    </div>

    <script src="script.js"></script>
</body>
</html>
