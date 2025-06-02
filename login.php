<?php
session_start(); // Niet vergeten als je sessies gebruikt!

$servername = "mysql_db2";
$username = "root";
$password = "rootpassword";

try {
    $conn = new PDO("mysql:host=$servername;dbname=login_systeem", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Gebruiker ophalen op basis van gebruikersnaam
    $sql = "SELECT * FROM `gebruikers` WHERE `gebruikersnaam` = :username";
    $statement = $conn->prepare($sql);
    $statement->bindParam(':username', $_POST['username']);
    $statement->execute();
    $gebruiker = $statement->fetch();

    // Als gebruiker bestaat, controleer wachtwoord
    if ($gebruiker && password_verify($_POST['password'], $gebruiker['wachtwoord'])) {
        $_SESSION['admin'] = true;
        header("Location: admin.php");
        exit;
    } else {
        $incorrectlogin = true;
        echo "Ongeldige gebruikersnaam of wachtwoord.";
    }
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>login</title>
    <link rel="stylesheet" href="css/index-styling.css">
    <link rel="stylesheet" href="css/login-styling.css">
</head>
<body>
<header class="main-header">
    <a class="logo" href="index.html"><img src="images/logo.png" alt="JMLogo"></a>
    <nav>
        <ul class="nav-links">
            <li><a href="booking.html">booking</a></li>
            <li><a href="contact.html">Vragen & contact</a></li>
            <li><a href="overons.html">Over ons</a></li>
        </ul>
    </nav>
    <a href="login.html">  <div><img class="login-image" src="images/account-black.png" alt="login"></div> </a>
</header>

<section class="login-container">
    <div class="login-box">
        <h2>Inloggen</h2>
        <form method="post">
            <label>E-mailadres</label>
            <input id="username" name="username" required>

            <label for="password">Wachtwoord</label>
            <input id="password" name="password" required>

            <button type="submit" class="login-btn">Inloggen</button>
        </form>
        <p class="register-link">Nog geen account? <a href="registratie.php">Registreer hier</a></p>
    </div>
</section>

<footer class="main-footer">
    <ul>
        <li>Vragen & Contact</li>
        <li>Terms & Condition</li>
        <li>How we Work</li>
        <li>Privacy & cookies</li>
        <li>Volg ons op:
            <span class="socials">⨯  </span>
        </li>
    </ul>
</footer>
</body>
</html>