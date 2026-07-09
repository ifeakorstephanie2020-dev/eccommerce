<?php

include "config.php";

$id = $_GET["id"];

$sql = "SELECT * FROM products WHERE id=$id";
$results = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($results);

if(isset($_POST['order'])){
    $email = $_POST["email"];
    $sql = "INSERT INTO orders (email,id2) VALUES ('$email', '$id')";

    if ($conn->query($sql)===TRUE) {
        $message = "Order added successfully";
    } else{
        $message = "error:" . $conn->error;
    }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S STORE - Order</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <nav style="padding: 20px; display: flex; justify-content: space-between; align-items: center; background: var(--bg-nav); border-bottom: 3px double var(--border-color);">
        <div style="display: flex; align-items: center; gap: 10px;">
            <button style="padding: 3px; height: 50px; width: 50px; font-size: 2rem; font-family: var(--font-heading); background: var(--bg-main); border: 2px solid var(--primary-dark); color: var(--primary-dark);">S</button>
            <span style="text-transform: uppercase; letter-spacing: 2px; font-size: 1.2rem;">Place Order</span>
        </div>
        <a href="index.php"><button style="padding: var(--spacing-sm) var(--spacing-lg); background: var(--primary-dark); color: var(--text-white); border: none;">Order More</button></a>
    </nav>

    <div class="container" style="display: flex; flex-wrap: wrap; gap: var(--spacing-xl); padding: var(--spacing-xl) 0; justify-content: center;">
        <article style="flex: 1; min-width: 280px; max-width: 500px; height: fit-content;">
            <h2>Order Summary</h2>
            <div style="display: flex; align-items: center; gap: var(--spacing-lg);">
                <img height="80px" width="120px" src="https://picsum.photos/200/200" alt="<?php echo $row["name"]; ?>" style="border: 2px solid var(--border-color); filter: sepia(0.3);" />
                <div>
                    <strong style="color: var(--primary-dark); font-family: var(--font-heading); font-size: 1.2rem;"><?php echo $row["name"]; ?></strong><br>
                    <?php echo $row["description"]; ?><br>
                    <span style="font-size: 1.3rem; color: var(--primary-dark);">$<?php echo $row["price"]; ?></span>
                </div>
            </div>
            <br>
            <article style="background: var(--bg-dark); border: 1px solid var(--border-color); padding: var(--spacing-lg); margin-top: var(--spacing-md);">
                <h3>Order Process:</h3>
                <ol style="padding-left: var(--spacing-lg); color: var(--text-secondary);">
                    <li>Enter your email address</li>
                    <li>Click "Place Order"</li>
                    <li>Check your email for confirmation</li>
                    <li>No payment required (demo system)</li>
                </ol>
            </article>
        </article>

        <article style="flex: 1; min-width: 280px; max-width: 500px; height: fit-content;">
            <h2>Complete Order</h2>
            <form method="post">
                <label>Your Email Address</label>
                <input type="email" name="email" placeholder="you@example.com" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">
                <small style="color: var(--text-light);">We'll send the product name to this email</small>
                <br><br>
                <center>
                    <button type="submit" name="order" style="width: 100%;">Place Order</button><br><br>
                    <a href="index.php" style="color: var(--text-secondary);">Back to store</a>
                </center>
                <?php if(isset($message)) echo "<p style='color: var(--primary-dark); text-align: center; margin-top: var(--spacing-md);'>$message</p>"; ?>
            </form>
        </article>
    </div>

    <script src="script.js"></script>
</body>

</html>
