<?php
include 'config.php';
session_start();
$message = '';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM products WHERE id =$id";

    if ($conn->query($sql) === TRUE) {
        $message = "🗑️ Product deleted successfully!";
    } else {
        $message = "❌ Error:" . $conn->error;
    }
}

$sql = "SELECT * FROM products";
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
            </ul>
        </nav>
    </header>

    <div class="container">
        <center>
            <h1 style="font-size: 1.8rem; letter-spacing: 4px;">
                Welcome <span style="color: var(--primary-dark);"><?php echo $_SESSION["name"]; ?></span>
            </h1>
            <div style="width: 80px; height: 2px; background: var(--border-color); margin: var(--spacing-sm) auto var(--spacing-lg);"></div>
        </center>

        <?php if($message): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <center>
            <div class="warning" style="margin-bottom: var(--spacing-lg);">
                ⚠️ Warning: Deletion is permanent! There's no undo.
            </div>
        </center>

        <article>
            <?php if ($result->num_rows > 0): ?>
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
                                <td><?php echo $row['id']; ?></td>
                                <td><strong><?php echo $row['name']; ?></strong></td>
                                <td><?php echo $row['description']; ?></td>
                                <td>$<?php echo $row['price']; ?></td>
                                <td>
                                    <a href="?delete_id=<?php echo $row['id']; ?>"
                                        onclick="return confirm('⚠️ Are you sure you want to delete <?php echo $row['name']; ?>?')"
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
