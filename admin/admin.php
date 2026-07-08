<?php
$message;
if (isset($_POST["submit"])) {
    $name = $_POST["username"];
    $password = $_POST["password"];

    if ($name === "admin" && $password === "admin123"){
        session_start();
        $_SESSION["name"] = $name;
        $_SESSION["islogged"] = TRUE;

        if ($_SESSION["islogged"] === TRUE) {
            header("Location: create.php");
        }
    } else {
        $message = "incorrect passsword or username";
    }
}


?>

<html>

<head>
    <title>
        admin login
    </title>
</head>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.pink.min.css">

<body>
    <article style="margin: auto; padding: 20px; width: 70%; margin-top: 50px; box-shadow: -5px 0px 0px 5px #f7708e ;">
        <form method="post">
            <center>
                <h1>Admin Login</h1>
            </center>
            <input type="text" placeholder="Username" name="username" />
            <input type="password" placeholder="Password" name="password" />
            <input type="submit" name="submit">
        </form>

        <?php echo $message ?>
    </article>
</body>

</html>