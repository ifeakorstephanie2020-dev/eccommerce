<?php
include '../config.php';
include '../security_functions.php';

startSecureSession();
sendSecurityHeaders();

// Check if admin is logged in
if (!isset($_SESSION["islogged"]) || $_SESSION["islogged"] !== TRUE || !isset($_SESSION["is_admin"])) {
    header("Location: admin.php");
    exit();
}

// Get all products with order count
$sql = "SELECT p.*,
        (SELECT COUNT(*) FROM orders WHERE product_id = p.id) as order_count
        FROM products p
        ORDER BY p.id DESC";
$results = $conn->query($sql);

// Get total stats
$stats_sql = "SELECT
    (SELECT COUNT(*) FROM products) as total_products,
    (SELECT COUNT(*) FROM orders) as total_orders,
    (SELECT COUNT(DISTINCT email) FROM orders) as total_customers,
    (SELECT IFNULL(SUM(price), 0) FROM orders LEFT JOIN products ON orders.product_id = products.id) as total_revenue";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Get admin count
$admin_count_result = $conn->query("SELECT COUNT(*) as count FROM users WHERE is_admin = 1");
$admin_count = $admin_count_result->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S ADMIN - Store View</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <header class="container">
        <nav>
            <ul>
                <li class="brand">
                    <span>S</span>TEPHIE'S ADMIN
                    <span class="admin-badge">👁️ Store View</span>
                </li>
            </ul>
            <ul>
                <li><a href="order.php">📦 Orders</a></li>
                <li><a href="create.php">➕ Add Products</a></li>
                <li><a href="edit.php">✏️ Edit Products</a></li>
                <li><a href="delete.php">🗑️ Remove Products</a></li>
                <li><a href="create_admin.php">👑 Admins</a></li>
                <li><a href="logout.php" style="color: var(--text-danger);">🚪 Logout</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <center>
            <h1 style="font-size: 1.8rem; letter-spacing: 4px;">
                👁️ Store Overview
            </h1>
            <div style="width: 80px; height: 2px; background: var(--border-color); margin: var(--spacing-sm) auto var(--spacing-lg);"></div>
            <p style="color: var(--text-secondary); font-style: italic;">
                Welcome <?php echo htmlspecialchars($_SESSION["name"]); ?> — Total Admins: <?php echo $admin_count; ?>
            </p>
        </center>

        <!-- Stats Dashboard -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); margin-bottom: var(--spacing-xl);">
            <article style="text-align: center; background: var(--bg-card); border-left: 4px solid var(--primary-dark);">
                <h3 style="color: var(--primary-dark); font-size: 2rem;"><?php echo $stats['total_products']; ?></h3>
                <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Total Products</p>
            </article>

            <article style="text-align: center; background: var(--bg-card); border-left: 4px solid var(--primary-dark);">
                <h3 style="color: var(--primary-dark); font-size: 2rem;"><?php echo $stats['total_orders']; ?></h3>
                <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Total Orders</p>
            </article>

            <article style="text-align: center; background: var(--bg-card); border-left: 4px solid var(--primary-dark);">
                <h3 style="color: var(--primary-dark); font-size: 2rem;"><?php echo $stats['total_customers']; ?></h3>
                <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Unique Customers</p>
            </article>

            <article style="text-align: center; background: var(--bg-card); border-left: 4px solid var(--primary-dark);">
                <h3 style="color: var(--primary-dark); font-size: 2rem;">$<?php echo number_format($stats['total_revenue'], 2); ?></h3>
                <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Total Revenue</p>
            </article>
        </div>

        <!-- Admin Controls -->
        <div style="display: flex; gap: var(--spacing-md); flex-wrap: wrap; justify-content: center; margin-bottom: var(--spacing-xl);">
            <a href="create.php" role="button" style="background: var(--primary-dark); color: var(--text-white); border: none;">
                ➕ Add Product
            </a>
            <a href="order.php" role="button" style="background: var(--bg-dark); border-color: var(--border-color);">
                📦 View Orders
            </a>
            <a href="edit.php" role="button" style="background: var(--bg-dark); border-color: var(--border-color);">
                ✏️ Edit Products
            </a>
            <a href="delete.php" role="button" style="background: var(--bg-dark); border-color: var(--border-color);">
                🗑️ Remove Products
            </a>
            <a href="create_admin.php" role="button" style="background: var(--bg-dark); border-color: var(--border-color);">
                👑 Create Admin
            </a>
            <a href="../index.php" role="button" style="background: var(--bg-dark); border-color: var(--border-color);">
                👤 View as Customer
            </a>
        </div>

        <!-- Product Grid - Admin View -->
        <h2 style="color: var(--primary-dark); margin-top: var(--spacing-xl); text-align: center; letter-spacing: 2px;">
            📋 All Products
        </h2>
        <div style="width: 60px; height: 2px; background: var(--border-color); margin: var(--spacing-sm) auto var(--spacing-lg);"></div>

        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: var(--spacing-lg);">
            <?php if ($results && $results->num_rows > 0): ?>
                <?php while ($row = $results->fetch_assoc()): ?>
                    <article style="position: relative; padding: var(--spacing-lg); background: var(--bg-card); border: 2px solid var(--border-color);">
                        <div style="position: absolute; top: -10px; right: -10px; background: var(--primary-dark); color: var(--text-white); padding: 2px var(--spacing-sm); font-size: 0.7rem; letter-spacing: 1px; border-radius: var(--radius-sm);">
                            ID: #<?php echo htmlspecialchars($row['id']); ?>
                        </div>

                        <center>
                            <img src="https://picsum.photos/200/200" alt="<?php echo htmlspecialchars($row["name"]); ?>" style="width: 100%; max-width: 200px; height: auto; border: 2px solid var(--border-color); filter: sepia(0.3);" />
                        </center>

                        <h3 style="color: var(--primary-dark); font-family: var(--font-heading); margin-top: var(--spacing-md);">
                            <?php echo htmlspecialchars($row["name"]); ?>
                        </h3>

                        <p style="color: var(--text-secondary); font-size: 0.9rem;">
                            <?php echo htmlspecialchars($row["description"]); ?>
                        </p>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin: var(--spacing-sm) 0;">
                            <span style="font-size: 1.3rem; color: var(--primary-dark); font-weight: bold;">
                                $<?php echo htmlspecialchars($row["price"]); ?>
                            </span>
                            <span style="font-size: 0.8rem; color: var(--text-light);">
                                📦 <?php echo $row['order_count']; ?> orders
                            </span>
                        </div>

                        <!-- Admin Actions -->
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-sm); margin-top: var(--spacing-md);">
                            <a href="edit.php?edit_id=<?php echo $row['id']; ?>" style="text-align: center; padding: var(--spacing-xs); border: 2px solid var(--primary-dark); color: var(--primary-dark); font-size: 0.8rem; transition: all var(--transition-fast);">
                                ✏️ Edit
                            </a>
                            <a href="delete.php?delete_id=<?php echo $row['id']; ?>"
                               onclick="return confirm('⚠️ Delete <?php echo htmlspecialchars($row['name']); ?>?')"
                               style="text-align: center; padding: var(--spacing-xs); border: 2px solid var(--text-danger); color: var(--text-danger); font-size: 0.8rem; transition: all var(--transition-fast);">
                                🗑️ Delete
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            <?php else: ?>
                <article style="grid-column: 1 / -1; text-align: center; padding: var(--spacing-xl);">
                    <h3 style="color: var(--text-secondary);">📭 No products in store</h3>
                    <p style="color: var(--text-light);">Start adding products to your store!</p>
                    <a href="create.php" role="button" style="margin-top: var(--spacing-md); display: inline-block;">
                        ➕ Add First Product
                    </a>
                </article>
            <?php endif; ?>
        </div>

        <article style="margin-top: var(--spacing-xl); background: var(--bg-dark); border: 1px solid var(--border-color); text-align: center;">
            <p style="color: var(--text-secondary); font-size: 0.85rem;">
                💡 <strong>Admin Tip:</strong> Use the buttons above to manage your store.
                <a href="../index.php" style="color: var(--primary-dark);">View as customer</a> to see the public storefront.
            </p>
        </article>
    </div>

    <?php $conn->close(); ?>
    <script src="admin-script.js"></script>
</body>
</html>
