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

// Handle delete
if (isset($_GET['delete_id']) && is_numeric($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];

    // Rate limiting
    if (!checkRateLimit('delete_product', 5, 60)) {
        $error = "You're deleting too quickly. Please wait.";
        logSuspiciousActivity("Rate limit exceeded for deleting product");
    }
    // Bot detection
    elseif (isBot()) {
        $error = "Access denied.";
        logSuspiciousActivity("Bot detected attempting to delete product");
    }
    else {
        // First get product name for logging
        $name_stmt = $conn->prepare("SELECT name FROM products WHERE id = ?");
        $name_stmt->bind_param("i", $id);
        $name_stmt->execute();
        $name_result = $name_stmt->get_result();
        $product_name = $name_result->fetch_assoc()['name'] ?? 'Unknown';
        $name_stmt->close();

        // Delete using prepared statement
        $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
        $stmt->bind_param("i", $id);

        if ($stmt->execute()) {
            $message = "🗑️ Product deleted successfully!";
            logSuspiciousActivity("Product deleted: $product_name (ID: $id) by admin");
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
}

// Get all products
$sql = "SELECT * FROM products ORDER BY id DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S ADMIN - Delete</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <header class="container">
        <nav>
            <ul>
                <li class="brand">
                    <span>S</span>TEPHIE'S ADMIN
                    <span class="admin-badge">🗑️ Delete</span>
                </li>
            </ul>
            <ul>
                <li><a href="admin_store.php">View Store</a></li>
                <li><a href="create.php">Add Products</a></li>
                <li><a href="edit.php">Edit Products</a></li>
                <li><a href="order.php">📦 Orders</a></li>
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

        <center>
            <div class="warning" style="margin-bottom: var(--spacing-lg);">
                ⚠️ Warning: Deletion is permanent! There's no undo.
            </div>
        </center>

        <article>
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['id']); ?></td>
                                <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td>$<?php echo htmlspecialchars($row['price']); ?></td>
                                <td>
                                    <a href="?delete_id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('⚠️ Are you sure you want to delete <?php echo htmlspecialchars($row['name']); ?>?')"
                                        class="btn-delete"
                                        style="display: inline-block; padding: var(--spacing-xs) var(--spacing-md); border: 2px solid var(--text-danger); color: var(--text-danger);">
                                        🗑️ Delete
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-secondary); text-align: center; padding: var(--spacing-xl);">
                    No products to delete. <a href="create.php" style="color: var(--primary-dark);">Add one first!</a>
                </p>
            <?php endif; ?>
        </article>
    </div>

    <?php $conn->close(); ?>
    <script src="admin-script.js"></script>
</body>
</html>
