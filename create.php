<?php
include 'config.php';
include '../security_functions.php';

startSecureSession();
sendSecurityHeaders();

// Check admin access
if (!isset($_SESSION["islogged"]) || $_SESSION["islogged"] !== TRUE || !isset($_SESSION["is_admin"])) {
    header("Location: admin.php");
    exit();
}

$message = "";
$error = "";

if (isset($_POST['order'])) {
    // Rate limiting
    if (!checkRateLimit('add_product', 10, 300)) {
        $error = "You're adding too quickly. Please wait.";
        logSuspiciousActivity("Rate limit exceeded for adding product");
    }
    // Bot detection
    elseif (isBot()) {
        $error = "Access denied.";
        logSuspiciousActivity("Bot detected attempting to add product");
    }
    // CSRF check
    elseif (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = "Security token mismatch.";
        logSuspiciousActivity("CSRF validation failed on product creation");
    }
    else {
        $name = sanitizeInput($_POST['name']);
        $description = sanitizeInput($_POST['description']);
        $price = (float)$_POST['price'];

        // Validate
        if (strlen($name) < 2) {
            $error = "Product name must be at least 2 characters.";
        } elseif ($price <= 0) {
            $error = "Price must be greater than 0.";
        } else {
            // Use prepared statement
            $stmt = $conn->prepare("INSERT INTO products (name, description, price) VALUES (?, ?, ?)");
            $stmt->bind_param("ssd", $name, $description, $price);

            if ($stmt->execute()) {
                $message = "✨ Product added successfully!";
                logSuspiciousActivity("New product added: $name by admin");
                // Regenerate CSRF token
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            } else {
                $error = "❌ Error: " . $stmt->error;
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
    <title>STEPHIE'S ADMIN - Create</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <header class="container">
        <nav>
            <ul>
                <li class="brand">
                    <span>S</span>TEPHIE'S ADMIN
                    <span class="admin-badge">✨ Admin</span>
                </li>
            </ul>
            <ul>
                <li><a href="admin_store.php">View Store</a></li>
                <li><a href="order.php">📦 Orders</a></li>
                <li><a href="edit.php">Edit Products</a></li>
                <li><a href="delete.php">Remove Products</a></li>
                <li><a href="create_admin.php">👑 Admins</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <center>
            <h1 style="font-size: 1.8rem; letter-spacing: 4px;">
                Welcome <span style="color: var(--primary-dark);"><?php echo htmlspecialchars($_SESSION["name"]); ?></span>
            </h1>
            <div style="width: 80px; height: 2px; background: var(--border-color); margin: var(--spacing-sm) auto var(--spacing-lg);"></div>
        </center>

        <?php if($message): ?>
            <div class="message" style="color: green;"><?php echo htmlspecialchars($message); ?></div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="message" style="color: var(--text-danger);"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <article style="max-width: 600px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-dark);">➕ Add New Product</h2>
            <form method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

                <fieldset>
                    <legend>Product Details</legend>

                    <label>Product Name:</label>
                    <input type="text" name="name" required placeholder="e.g., Lipstick">

                    <label>Description:</label>
                    <input type="text" name="description" required placeholder="e.g., Long-lasting matte">

                    <label>Price ($):</label>
                    <input type="number" name="price" required placeholder="e.g., 15.99" step="0.01" min="0.01">
                </fieldset>

                <button type="submit" name="order" style="width: 100%; margin-top: var(--spacing-md);">
                    ➕ Add Product
                </button>
            </form>
        </article>
    </div>

    <script src="admin-script.js"></script>
</body>
</html>
