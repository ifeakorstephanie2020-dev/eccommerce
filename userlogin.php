<?php
include 'config.php';
$message = "";
session_start();

// Sign up logic
if (isset($_POST['sign'])) {
    $username = $_POST['username'];
    $cpassword = $_POST['PASSWORD'];
    $password = $_POST['password'];

    if ($password === $cpassword) {
        $sql = "INSERT INTO users (name, pass) VALUES ('$username', '$password')";

        if ($conn->query($sql) === TRUE) {
            $message = "✅ User added successfully! Please login.";
        } else {
            $message = "❌ Error: " . $conn->error;
        }
    } else {
        $message = "❌ Passwords do not match!";
    }
}

// Login logic - FIXED
if (isset($_POST["LOGIN"])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if user exists with matching credentials
    $sql = "SELECT * FROM users WHERE name = '$username' AND pass = '$password'";
    $result = $conn->query($sql);

    // Check if any row was returned
    if ($result->num_rows > 0) {
        $_SESSION["name"] = $username;
        $_SESSION["islogged"] = TRUE;
        header("Location: index.php");
        exit(); // Always call exit after header redirect
    } else {
        $message = "❌ Invalid username or password!";
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
        <!-- SIGN UP FORM -->
        <article id="signup" style="width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-sm);">
                <button onclick="login()" style="width: 100%;">LOGIN</button>
                <button onclick="signup()" style="width: 100%; background: var(--primary-dark); color: var(--text-white);">Sign Up</button>
            </div>
            <hr style="border: 1px solid var(--border-color); margin: var(--spacing-md) 0;">

            <?php if(isset($message) && isset($_POST['sign'])): ?>
                <div class="message" style="margin-bottom: var(--spacing-md);"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post">
                <label>USERNAME</label>
                <input type="text" name="username" placeholder="Enter username" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">

                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Enter password" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">

                <label>CONFIRM PASSWORD</label>
                <input type="password" name="PASSWORD" placeholder="Confirm password" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">

                <button name="sign" style="width: 100%;">Sign Up</button>
            </form>
        </article>

        <!-- LOGIN FORM -->
        <article id="login" style="display: none; width: 100%; max-width: 500px; padding: var(--spacing-xl);">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--spacing-sm);">
                <button onclick="login()" style="width: 100%; background: var(--primary-dark); color: var(--text-white);">LOGIN</button>
                <button onclick="signup()" style="width: 100%;">Sign Up</button>
            </div>
            <hr style="border: 1px solid var(--border-color); margin: var(--spacing-md) 0;">

            <?php if(isset($message) && isset($_POST['LOGIN'])): ?>
                <div class="message" style="margin-bottom: var(--spacing-md);"><?php echo $message; ?></div>
            <?php endif; ?>

            <form method="post">
                <label>USERNAME</label>
                <input type="text" name="username" placeholder="Enter username" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">

                <label>PASSWORD</label>
                <input type="password" name="password" placeholder="Enter password" style="width: 100%; padding: var(--spacing-sm); border: 2px solid var(--border-color); background: var(--bg-card); font-family: var(--font-main);">

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
