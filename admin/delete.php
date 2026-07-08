<?php
include 'config.php';
session_start();
$message = '';

if (isset($_GET['delete_id'])) {
    $id = $_GET['delete_id'];

    $sql = "DELETE FROM products WHERE id =$id";

    if ($conn->query($sql) === TRUE) {
        $message = "user deleted successfully";
    } else {
        $message = "Error:" . $conn->error;
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
    <?php if ($message): ?>
        <div class="message"><?php echo $message; ?></div>
    <?php endif; ?>

    <center>
        <h1>Welcome <?php echo $_SESSION["name"]; ?></h1>
    </center>
    
    <center>
        <div class="warning"> ⚠ Warning: Deletion is permanent! There's no undo. </div>
    </center> <br>

    <div style="padding: 30px;">
        <?php if ($result->num_rows > 0): ?>
            <table>
                <tr >
                    <th style="color:#f7708e;">ID</th>
                    <th style="color:#f7708e;">NAME</th>
                    <th style="color:#f7708e;">DESCRIPTION</th>
                    <th style="color:#f7708e;">PRICE</th>
                    <th style="color:#f7708e;">ACTION</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo $row['name']; ?></td>
                        <td><?php echo $row['description']; ?></td>
                        <td><?php echo $row['price']; ?></td>
                        <td>
                            <!-- Delete link with JavaScript confirmation -->
                            <a style="color:#f7708e;" href="?delete_id=<?php echo $row['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete <?php echo $row['name']; ?>?')"
                                class="btn-delete">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>No users to delete. <a href="create.php">Add one first!</a></p>
        <?php endif; ?>
    </div>
    </div>


    <footer>
        
    </footer>
    <?php $conn->close(); ?>
</body>

</html>