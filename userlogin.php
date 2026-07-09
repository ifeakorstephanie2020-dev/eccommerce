<?php
include 'config.php';
$message = "";
session_start();

if (isset($_POST['sign'])) {
    $username = $_POST['username'];
    $cpassword = $_POST['PASSWORD'];
    $password = $_POST['password'];

    if ($password === $cpassword) {
        $sql = "INSERT INTO users (name, pass) VALUE ('$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            $message = "User added successfully";
        } else {
            $message = "error:" . $conn->error;
        }
    } else {
        $message = "incorrect password";
    }
}

if (isset($_POST["LOGIN"])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE pass = '$password' AND name = '$username'";
    if ($conn->query($sql)===TRUE) {
        session_start();
        $_SESSION["name"] = $username;
        $_SESSION["islogged"] = TRUE;
        header("Location: index.php");
    } else{
        echo "error:" . $conn->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>STEPHIE'S STORE - Login</title>
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <article id="signup" style="width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-sm);">
                <button onclick="login()" style="width: 100%;">LOGIN</button>
                <button onclick="signup()" style="width: 100%;">Sign Up</button>
            </div>
            <hr style="border: 1px solid var(--border-color); margin: var(--spacing-md) 0;">
            <form action="">
                <label>USERNAME</label>
                <input type="text" name="username" placeholder="ENTER EMAIL" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="ENTER PASSWORD" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">
                <label>CONFIRM PASSWORD</label>
                <input type="text" name="PASSWORD" placeholder="ENTER PASSWORD" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">
                <button name="sign" style="width: 100%;">Sign up</button>
                <?php echo $message; ?>
            </form>
        </article>

        <article id="login" style="display: none; width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-sm);">
                <button onclick="login()" style="width: 100%;">LOGIN</button>
                <button onclick="signup()" style="width: 100%;">Sign Up</button>
            </div>
            <hr style="border: 1px solid var(--border-color); margin: var(--spacing-md) 0;">
            <form method="post">
                <?php echo $message; ?>
                <label>USERNAME</label>
                <input type="text" name="username" placeholder="enter username" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">
                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="enter password" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">
                <button name="LOGIN" type="submit" style="width: 100%;">LOGIN</button>
            </form>
        </article>
    </div>

    <script>
        function signup() {
            document.getElementById("signup").style.display = "block";
            document.getElementById("login").style.display = "none";
        }

        function login() {
            document.getElementById("login").style.display = "block";
            document.getElementById("signup").style.display = "none";
        }
    </script>
    <script src="script.js"></script>
</body>

</html>
