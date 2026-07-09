<?php
include 'config.php';

$sql = "SELECT * FROM products";
$results = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>STEPHIE'S CARE</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>

<body>
  <header class="container">
    <nav>
      <ul>
        <li class="brand">
          <span>S</span>TEPHIE'S STORE
        </li>
      </ul>
      <ul>
        <li><a href="userlogin.php">Sign up</a></li>
        <li><a href="index.php">Home</a></li>
        <li><a href="#contact">Contact</a></li>
        <li><a href="#services">Services</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <header class="hero">
      <h1>Welcome to Our Website</h1>
      <p>
        WELCOME TO STEPHIE'S STORE – WHERE QUALITY MEETS STYLE AND LUXURY
      </p>
      <a href="#contact" role="button">Get Started</a>
    </header>

    <div class="grid">
      <?php while ($row = $results->fetch_assoc()): ?>
        <article>
          <center>
            <img src="https://picsum.photos/200/200" alt="<?php echo $row["name"]; ?>" />
          </center>
          <span><?php echo $row["name"]; ?></span>
          <span><?php echo $row["description"] ?></span>
          <span>$<?php echo $row["price"] ?></span>
          <?php
          $id = $row["id"];
          echo "
            <a href='products.php?id=$id'>
              <button>order now</button>
            </a>
          "
          ?>
        </article>
      <?php endwhile; ?>
    </div>

    <section id="services">
      <center>
        <h2>Our Services</h2>
        <p>Providing high-quality solutions tailored to your needs.</p>
      </center>

      <div class="grid">
        <article>
          <h3>Web Development</h3>
          <p>Building fast, secure, and responsive websites using modern technologies.</p>
        </article>

        <article>
          <h3>Digital Strategy</h3>
          <p>We help you map out your digital presence to ensure you reach your goals.</p>
        </article>

        <article>
          <h3>Content Creation</h3>
          <p>Engaging, SEO-friendly copy that helps your brand stand out.</p>
        </article>
      </div>
    </section>
  </div>

  <footer class="site-footer" id="contact">
    <div class="footer-content">
      <p>&copy; 2026 STEPHIE STORE. All rights reserved.</p>
      <nav>
        <a>About</a>
        <a>Contact</a>
        <a href="/privacy">Github</a>
      </nav>
    </div>
  </footer>

  <script src="script.js"></script>
</body>

</html>
