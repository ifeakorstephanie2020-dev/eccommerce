<?php
include 'config.php';
$message = "";
session_start();

if (isset($_POST['order'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];

    $sql = "INSERT INTO products (name, description, price) VALUES ('$name','$description',$price)";

    if ($conn->query($sql) === TRUE) {
        $message = "✨ Product added successfully!";
    } else {
        $message = "❌ Error:" . $conn->error;
    }
}

if ($_SESSION["islogged"] != TRUE){
    header("Location: admin.php");
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

        <article style="max-width: 600px; margin: 0 auto;">
            <h2 style="text-align: center; color: var(--primary-dark);">➕ Add New Product</h2>
            <form method="POST">
                <fieldset>
                    <legend>Product Details</legend>

                    <label>Product Name:</label>
                    <input type="text" name="name" required placeholder="e.g., Lipstick">

                    <label>Description:</label>
                    <input type="text" name="description" required placeholder="e.g., Long-lasting matte">

                    <label>Price ($):</label>
                    <input type="number" name="price" required placeholder="e.g., 15.99" step="0.01">
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
