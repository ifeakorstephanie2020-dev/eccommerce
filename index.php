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
</head>
<link
  rel="stylesheet"
  href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.pink.min.css">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
<body>
  <header class="container">
    <nav>
      <ul>
        <li style="color:#f7708e;">
          <h2 style="color:#f7708e;">
            <h1 style="display: inline; color:#f7708e;">S</h1>TEPHIE'S STORE
          </h2>
        </li>
      </ul>
      <ul>
        <li><a href="userlogin.php" class="contrast" style="color:#f7708e;">Sign up</a></li>
        <li><a href="index.php" class="contrast" style="color:#f7708e;">Home</a></li>
        <li><a href="#contact" class="contrast" style="color:#f7708e;">Contact</a></li>
        <li><a href="#services" class="contrast" style="color:#f7708e;">Services</a></li>
      </ul>
    </nav>
  </header>

  <center>
    <header class="hero">
      <h1>Welcome to Our Website</h1>
      <p>
        WELCOME TO STEPHIE'S STORE – WHERE  QUALITY MEETS STYLE AND LUXURY
      </p>
      <li><a href="#contact" role="button">Get Started</a></li>
    </header>
  </center>


  <div class="grid" style="grid-template-columns: auto auto auto; padding: 20px;">
    <?php while ($row = $results->fetch_assoc()): ?>
      <article>
        <center>
          <img src="https://picsum.photos/200/200" />
        </center>
        <span style="color:#f7708e; text-transform: uppercase;"><?php echo $row["name"]; ?></span> <br>
        <span><?php echo $row["description"] ?></span>
        <br>
        <span><?php echo $row["price"] ?></span> <br>
        <!-- $id = $row["id"];
           echo " -->
        <!-- <a href='products.php?id=$id'>
             <button>order now</button>
           </a>
           " -->
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

  <section id="services" style="padding: 20px;">
    <center>
      <h2 style="color:#f7708e;">Our Services</h2>
      <p>Providing high-quality solutions tailored to your needs.</p>
    </center>

    <div class="grid">
      <article>
        <h3 style="color:#f7708e;">Web Development</h3>
        <p>Building fast, secure, and responsive websites using modern technologies.</p>
      </article>

      <article>
        <h3 style="color:#f7708e;">Digital Strategy</h3>
        <p>We help you map out your digital presence to ensure you reach your goals.</p>
      </article>

      <article>
        <h3 style="color:#f7708e;">Content Creation</h3>
        <p>Engaging, SEO-friendly copy that helps your brand stand out.</p>
      </article>
    </div>
  </section>

  <footer class="site-footer" id="contact">
    <div class="footer-content">
      <center>
        <p>&copy; 2026 STEPHIE STORE. All rights reserved.</p>
      </center>
      <nav style="justify-content: center; gap:7px;">
        <a> About </a>
        <a> Contact </a>
        <a href="/privacy"> Github</a>
      </nav>
    </div>
  </footer>
</body>

</html>