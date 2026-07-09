<?php
include '../config.php';
session_start();

// Check if admin is logged in
if ($_SESSION["islogged"] != TRUE) {
    header("Location: admin.php");
    exit();
}

// Get all orders with product details
$sql = "SELECT orders.id, orders.email, orders.id2, orders.order_date, products.name, products.description, products.price
        FROM orders
        LEFT JOIN products ON orders.id2 = products.id
        ORDER BY orders.order_date DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S ADMIN - Orders</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <header class="container">
        <nav>
            <ul>
                <li class="brand">
                    <span>S</span>TEPHIE'S ADMIN
                    <span class="admin-badge">📦 Orders</span>
                </li>
            </ul>
            <ul>
                <li><a href="admin_store.php">View Store</a></li>
                <li><a href="create.php">Add Products</a></li>
                <li><a href="edit.php">Edit Products</a></li>
                <li><a href="delete.php">Remove Products</a></li>
            </ul>
        </nav>
    </header>

    <div class="container">
        <center>
            <h1 style="font-size: 1.8rem; letter-spacing: 4px;">
                📦 Order Management
            </h1>
            <div style="width: 80px; height: 2px; background: var(--border-color); margin: var(--spacing-sm) auto var(--spacing-lg);"></div>
            <p style="color: var(--text-secondary); font-style: italic;">
                Welcome <?php echo $_SESSION["name"]; ?> — View all customer orders below
            </p>
        </center>

        <article style="margin-top: var(--spacing-xl);">
            <?php if ($result && $result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Product</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Customer Email</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $row['id']; ?></strong></td>
                                <td><strong style="color: var(--primary-dark);"><?php echo $row['name'] ?: '⚠️ Product Deleted'; ?></strong></td>
                                <td><?php echo $row['description'] ?: 'N/A'; ?></td>
                                <td><?php echo $row['price'] ? '$' . $row['price'] : 'N/A'; ?></td>
                                <td>
                                    <a href="mailto:<?php echo $row['email']; ?>" style="color: var(--primary-dark);">
                                        <?php echo $row['email']; ?>
                                    </a>
                                </td>
                                <td style="font-size: 0.85rem; color: var(--text-secondary);">
                                    <?php echo date('M d, Y - h:i A', strtotime($row['order_date'])); ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <div style="margin-top: var(--spacing-md); padding: var(--spacing-md); background: var(--bg-dark); border: 1px solid var(--border-color); text-align: center; font-family: var(--font-heading); letter-spacing: 1px;">
                    Total Orders: <strong style="color: var(--primary-dark);"><?php echo $result->num_rows; ?></strong>
                </div>

            <?php else: ?>
                <div style="text-align: center; padding: var(--spacing-xl);">
                    <h3 style="color: var(--text-secondary); font-family: var(--font-heading);">
                        📭 No orders placed yet
                    </h3>
                    <p style="color: var(--text-light); margin-top: var(--spacing-sm);">
                        Customers haven't placed any orders. Check back later!
                    </p>
                    <a href="create.php" style="display: inline-block; margin-top: var(--spacing-md);" role="button">
                        ➕ Add Products to Sell
                    </a>
                </div>
            <?php endif; ?>
        </article>

        <!-- Optional: Quick Stats -->
        <?php if ($result && $result->num_rows > 0): ?>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); margin-top: var(--spacing-xl);">
                <article style="text-align: center; background: var(--bg-card);">
                    <h3 style="color: var(--primary-dark); font-size: 2rem;"><?php echo $result->num_rows; ?></h3>
                    <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Total Orders</p>
                </article>

                <?php
                // Get unique customers count
                $sql_customers = "SELECT COUNT(DISTINCT email) as total FROM orders";
                $customer_result = $conn->query($sql_customers);
                $customer_count = $customer_result->fetch_assoc()['total'];
                ?>
                <article style="text-align: center; background: var(--bg-card);">
                    <h3 style="color: var(--primary-dark); font-size: 2rem;"><?php echo $customer_count; ?></h3>
                    <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Unique Customers</p>
                </article>

                <?php
                // Get total revenue
                $sql_revenue = "SELECT SUM(products.price) as total FROM orders LEFT JOIN products ON orders.id2 = products.id";
                $revenue_result = $conn->query($sql_revenue);
                $total_revenue = $revenue_result->fetch_assoc()['total'] ?: 0;
                ?>
                <article style="text-align: center; background: var(--bg-card);">
                    <h3 style="color: var(--primary-dark); font-size: 2rem;">$<?php echo number_format($total_revenue, 2); ?></h3>
                    <p style="color: var(--text-secondary); text-transform: uppercase; letter-spacing: 1px; font-size: 0.8rem;">Total Revenue</p>
                </article>
            </div>
        <?php endif; ?>

    </div>

    <?php $conn->close(); ?>
    <script src="admin-script.js"></script>
</body>

</html>
