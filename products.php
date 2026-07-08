<?php

include "config.php";

$id = $_GET["id"];

$sql = "SELECT * FROM products WHERE id=$id";
$results = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($results);

// include "config.php";

// $id = $_GET["id"];

// $sql = "SELECT * FROM products WHERE id=$id";
// $results = mysqli_query($conn, $sql);
// $row = mysqli_fetch_assoc($results);

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
    <title>Document</title>
</head>

<style>
    .logo-icon {
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        border-radius: 4px;
    }
</style>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.pink.min.css">

<body>
    <nav style="padding: 20px;">
        <div>
            <div class="logo-icon">
                <button style="padding: 3px; height: 50px; width: 50px;"><h1>S</h1></button>
                <pre style="background:rgb(19, 22.5, 30.5);">   </pre>
                <span style="text-transform: capitalize; font-weight:lighter; font-size:larger;"> place order</span>
            </div>

        </div >
        <a href="index.php"><button  style="padding: 5px;background: #f7708e;">order more</button></a>
    </nav>
    <div class="grid" style="display: flex; align-items: center;">
        <article style="height: 100%; color:#f7708e; width: 500px; margin: 30px; margin-top:50px;  ">
            <h2>Order Summary</h2>
            <div style="display: flex; align-items: center; gap:20px;">
                <img height="80px" width="120px" src="https://thf.bing.com/th/id/OIP.wOX2I1M53b7Ta9AEDtBXQAHaHa?w=186&h=186&c=7&r=0&o=7&cb=thfc1falcon4&dpr=1.3&pid=1.7&rm=3" /><br>
                <figure>
                    <?php echo $row["name"] ?> <br>
                    <?php echo $row["description"] ?> <br>
                    $ <?php echo $row["price"] ?> <br>
                </figure>
            </div>
            <br> <br>
            <article style="background-color:rgb(19, 22.5, 30.5);">
                <h3>Order Process:</h3>
                <ol style="color:#f7708e;">
                    <li>Enter your email address</li>
                    <li>Click "Place Order" </li>
                    <li>Check your email for confirmation</li>
                    <li>No payment required (demo system)</li>
                </ol>
            </article>
        </article>

        <article style="width: 500px; outline: 1px solid pink; margin: auto; margin-right: 10px; ">
            <h2>Complete Order</h2>
            <h6>Your Email Address</h6>
            <form method="post">
                <input type="email" name="email" id="" placeholder="you@example.com">
            <small>we'll send the product name to this email</small>

            <center>
                <button style="width: 80%;" name="order">Place Order</button><br>

                <a href="">Back to store</a>

            </center>
            </form>

        </article>

    </div>
</body>

</html>