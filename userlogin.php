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
    <title>Document</title>
</head>
<style>
    hr {
        border-top: 2px solid white;
    }

    button {
        background-color: rgb(19, 22.5, 30.5) !important;
    }
</style>
<link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.pink.min.css">

<body>
    <div style="padding: 10%;">
        <article id="signup" style="margin: auto; padding: 20px; width: 70%; margin-top: 50px; box-shadow: -5px 0px 0px 5px #f7708e ;">
            <div class="grid">
                <button onclick="login()">
                    <h1>LOGIN</h1>
                </button>
                <button onclick="signup()">
                    <h1>Sign Up</h1>
                </button>
            </div>
            <hr>
            <form action="">
                <label for="">USERNAME</label>
                <input type="text" name="username" placeholder="ENTER EMAIL">
                <label for="">PASSWORD</label>
                <input type="password" name="password" id="" placeholder="ENTER PASSWORD">
                <label for="">CONFIRM PASSWORD</label>
                <input type="text" name="PASSWORD" placeholder="ENTER PASSWORD">
                <button name="sign">Sign up</button>
            </form>

        </article>

        <article id="login" style="display: none;margin: auto; padding: 20px; width: 70%; margin-top: 50px; box-shadow: -5px 0px 0px 5px #f7708e ;">
            <div class="grid">
                <button onclick="login()">
                    <h1>LOGIN</h1>
                </button>
                <button onclick="signup()">
                    <h1>Sign Up</h1>
                </button>
            </div>

            <hr>

            <form method="post">
                <?php echo $message; ?>
                <label>USERNAME</label>
                <input type="text" name="username" placeholder="enter username">
                <Label>PASSWORD</Label>
                <input type="password" name="password" id="" placeholder="enter password">
                <button name="LOGIN" type="submit">LOGIN</button>
            </form>

        </article>
    </div>

    <div>

    </div>

    <script>
        function signup() {
            document.getElementById("signup").style = "display: block; margin: auto; padding: 20px; width: 70%; margin-top: 50px; box-shadow: -5px 0px 0px 5px #f7708e ;;"
            document.getElementById("login").style = "display: none;"
        }

        function login() {
            document.getElementById("login").style = "display: block; margin: auto; padding: 20px; width: 70%; margin-top: 50px; box-shadow: -5px 0px 0px 5px #f7708e ;;"
            document.getElementById("signup").style = "display: none;"
        }
    </script>
</body>

</html>