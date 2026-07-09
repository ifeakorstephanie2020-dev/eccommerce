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
        $message = "incorrect password or username";
    }
}
?>

<html>

<head>
    <title>STEPHIE'S ADMIN</title>
    <link rel="stylesheet" href="admin-styles.css">
</head>

<body>
    <div class="container" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <article style="width: 100%; max-width: 450px; padding: var(--spacing-xl);">
            <center>
                <h1 style="color: var(--primary-dark);">S<span style="font-size: 1.5rem;">TEPHIE'S</span></h1>
                <h2 style="font-size: 1.2rem; letter-spacing: 4px; color: var(--text-secondary);">ADMIN LOGIN</h2>
                <div style="width: 60px; height: 2px; background: var(--primary-dark); margin: var(--spacing-md) auto;"></div>
            </center>
            <form method="post">
                <label>Username</label>
                <input type="text" placeholder="Enter username" name="username" style="margin-bottom: var(--spacing-md);">
                <label>Password</label>
                <input type="password" placeholder="Enter password" name="password" style="margin-bottom: var(--spacing-lg);">
                <input type="submit" name="submit" value="Login" style="width: 100%;">
            </form>
            <?php if(isset($message)): ?>
                <div class="message" style="margin-top: var(--spacing-md); color: var(--text-danger);"><?php echo $message; ?></div>
            <?php endif; ?>
        </article>
    </div>
    <script src="admin-script.js"></script>
</body>

</html>
