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
        $message = "product updated successfully!";
        $editproducts = null;
    } else {
        $message = "error:" . $conn->error;
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
    <title>STEPHIE'S STORE</title>
</head>
<link rel="stylesheet" href="https://unpkg.com/@picocss/pico@latest/css/pico.min.css">

<body>

    <header class="container">
        <nav>
            <ul>
                <li style="color:#f7708e;">
                    <h2 style="color:#f7708e;">
                        <h1 style="display: inline; color:#f7708e;">S</h1>TEPHIE'S ADMIN
                    </h2>
                </li>
            </ul>
            <ul>
                <li><a href="../index.php" class="contrast" style="color:#f7708e;">View Store</a></li>
                <li><a href="edit.php" class="contrast" style="color:#f7708e;">Edit Products</a></li>
                <li><a href="delete.php" class="contrast" style="color:#f7708e;">Remove Products</a></li>
            </ul>
        </nav>
    </header>

    <center>
        <h1>Welcome <?php echo $_SESSION["name"]; ?></h1>
    </center>
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <div class="container">
        <!-- LEFT: List of users -->
        <div class="product list">
            <h3 > 📋 Select product to edit:</h3>
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <tr>
                        <th style="color:#f7708e;">NAME</th>
                        <th style="color:#f7708e;">DESCRIPTION</th>
                        <th style="color:#f7708e;">PRICE</th>
                        <th style="color:#f7708e;">ACTIONS</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['name']; ?></td>
                            <td><?php echo $row['description']; ?></td>
                            <td><?php echo $row['price']; ?></td>
                            <td>
                                <a href="?edit_id=<?php echo $row['id']; ?>" style="color:#f7708e;" class="btn-edit">✏ Edit</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>
            <?php else: ?>
                <p>No users to edit. <a href="create.php">Create one first!</a></p>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Edit form (shows when user selected) -->
        <?php if ($editproducts): ?>
            <div class="edit-form">
                <h3>
                    ✏
                    Editing User #<?php echo $editproducts['id']; ?></h3>
                <form method="POST">
                    <input type="hidden" name="id" value="<?php echo $editproducts['id']; ?>">

                    <label>Name:</label>
                    <input type="text" name="name" value="<?php echo $editproducts['name']; ?>" required>

                    <label>description:</label>
                    <input type="text" name="description" value="<?php echo $editproducts['description']; ?>" required>

                    <label>price:</label>
                    <input type="number" name="price" value="<?php echo $editproducts['price']; ?>" required>

                    <button type="submit" name="order">
                        💾
                        Update product</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <?php $conn->close(); ?>

</body>

</html>