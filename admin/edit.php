<?php
include 'config.php';

$message = "";
$editproducts = null;
session_start();

if (isset($_GET['edit_id'])) {
    $id = $_GET['edit_id'];
    $sql = "SELECT * FROM products WHERE id = $id";
    $result = $conn->query($sql);
    $editproducts = $result->fetch_assoc();
}

if (isset(($_POST['order']))) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $sql = "UPDATE products SET name = '$name',description='$description',price ='$price' WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        $message = "✏️ Product updated successfully!";
        $editproducts = null;
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

        <div class="admin-grid">
            <!-- LEFT: Product List -->
            <article>
                <h3 style="color: var(--primary-dark);">📋 Select Product to Edit</h3>
                <?php if ($result->num_rows > 0): ?>
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
                                    <td><?php echo $row['name']; ?></td>
                                    <td>$<?php echo $row['price']; ?></td>
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
                        ✏️ Editing: <?php echo $editproducts['name']; ?>
                    </h3>
                    <form method="POST">
                        <input type="hidden" name="id" value="<?php echo $editproducts['id']; ?>">

                        <label>Product Name:</label>
                        <input type="text" name="name" value="<?php echo $editproducts['name']; ?>" required>

                        <label>Description:</label>
                        <input type="text" name="description" value="<?php echo $editproducts['description']; ?>" required>

                        <label>Price ($):</label>
                        <input type="number" name="price" value="<?php echo $editproducts['price']; ?>" required step="0.01">

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
