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
$editproducts = null;

if (isset($_GET['edit_id']) && is_numeric($_GET['edit_id'])) {
    $id = (int)$_GET['edit_id'];

    // Use prepared statement
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $editproducts = $result->fetch_assoc();
    $stmt->close();
}

if (isset($_POST['order'])) {
    // Rate limiting
    if (!checkRateLimit('edit_product', 10, 300)) {
        $error = "You're editing too quickly. Please wait.";
        logSuspiciousActivity("Rate limit exceeded for editing product");
    }
    // Bot detection
    elseif (isBot()) {
        $error = "Access denied.";
        logSuspiciousActivity("Bot detected attempting to edit product");
    }
    // CSRF check
    elseif (!isset($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = "Security token mismatch.";
        logSuspiciousActivity("CSRF validation failed on product edit");
    }
    else {
        $id = (int)$_POST['id'];
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
            $stmt = $conn->prepare("UPDATE products SET name = ?, description = ?, price = ? WHERE id = ?");
            $stmt->bind_param("ssdi", $name, $description, $price, $id);

            if ($stmt->execute()) {
                $message = "✏️ Product updated successfully!";
                logSuspiciousActivity("Product updated: ID $id by admin");
                // Regenerate CSRF token
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                $editproducts = null; // Reset form
            } else {
                $error = "❌ Error: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}

// Get all products for listing
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S ADMIN - Edit</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <header class="container">
        <nav>
            <ul>
                <li class="brand">
                    <span>S</span>TEPHIE'S ADMIN
                    <span class="admin-badge">✏️ Edit</span>
                </li>
            </ul>
            <ul>
                <li><a href="admin_store.php">View Store</a></li>
                <li><a href="create.php">Add Products</a></li>
                <li><a href="order.php">📦 Orders</a></li>
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

        <div class="admin-grid">
            <!-- LEFT: Product List -->
            <article>
                <h3 style="color: var(--primary-dark);">📋 Select Product to Edit</h3>
                <?php if ($result && $result->num_rows > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                                    <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                                    <td>
                                        <a href="?edit_id=<?php echo $row['id']; ?>" style="color: var(--primary-dark);">
                                            ✏️ Edit
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p style="color: var(--text-secondary);">No products to edit. <a href="create.php" style="color: var(--primary-dark);">Add one first!</a></p>
                <?php endif; ?>
            </article>

            <!-- RIGHT: Edit Form -->
            <?php if ($editproducts): ?>
                <article>
                    <h3 style="color: var(--primary-dark);">
                        ✏️ Editing: <?php echo htmlspecialchars($editproducts['name']); ?>
                    </h3>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editproducts['id']); ?>">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(generateCSRFToken()); ?>">

                        <label>Product Name:</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($editproducts['name']); ?>" required>

                        <label>Description:</label>
                        <input type="text" name="description" value="<?php echo htmlspecialchars($editproducts['description']); ?>" required>

                        <label>Price ($):</label>
                        <input type="number" name="price" value="<?php echo htmlspecialchars($editproducts['price']); ?>" required step="0.01" min="0.01">

                        <button type="submit" name="order" style="width: 100%; margin-top: var(--spacing-md);">
                            💾 Update Product
                        </button>
                    </form>
                </article>
            <?php else: ?>
                <article style="display: flex; justify-content: center; align-items: center; min-height: 200px;">
                    <p style="color: var(--text-light); text-align: center; font-family: var(--font-heading); letter-spacing: 2px;">
                        Select a product from the list to edit
                    </p>
                </article>
            <?php endif; ?>
        </div>
    </div>

    <?php $conn->close(); ?>
    <script src="admin-script.js"></script>
</body>
</html>
