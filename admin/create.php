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
        $message = "product added successfully";
    } else {
        $message = "error:" . $conn->error;
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
    <title>STEPHIES'S STORE</title>
</head>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.pink.min.css"
>

<body>

    <header class="container">
        <nav>
            <ul>
        <li style="color:#f7708e;"><h2 style="color:#f7708e;"><h1 style="display: inline; color:#f7708e;" >S</h1>TEPHIE'S ADMIN</h2></li>
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
    <article style=" margin: 40px; outline:#f7708e 1px solid;">
        <form method="POST" >
        <fieldset style="padding: 20px;">
            <label>Name:</label>
            <input type="text" name="name" required>

            <label>description:</label>
            <input type="text" name="description" required>

            <label>price:</label>
            <input type="number" name="price" required>
        </fieldset>

        <button type="submit" name="order">➕ Add product </button>
    </form>
    </article>
</body>

</html>