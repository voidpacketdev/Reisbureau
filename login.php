<?php
session_start();
include("connection.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $gebruikersnaam = $_POST['username'];
    $wachtwoord = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE gebruikersnaam = :username");
    $stmt->bindParam(':username', $gebruikersnaam);
    $stmt->execute();
    $gebruiker = $stmt->fetch();

    if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
        $_SESSION['gebruiker_id'] = $gebruiker['id'];
        $_SESSION['gebruikersnaam'] = $gebruiker['gebruikersnaam'];
        $_SESSION['rol'] = $gebruiker['rol'];

        if ($gebruiker['rol'] === 'admin') {
            header("Location: admin.php");
        } else {
            header("Location: account.php");
        }
        exit;
    } else {
        $incorrectlogin = true;
    }
}
?>






<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>J&M Travel | Login</title>
    <link rel="stylesheet" href="css/index-styling.css">
    <link rel="stylesheet" href="css/login-styling.css">
</head>
<body>
<header class="main-header">
    <a class="logo" href="index.php"><img src="images/logo.png" alt="JMLogo"></a>
    <nav>
        <ul class="nav-links">
            <li><a href="booking.php">Booking</a></li>
            <li><a href="contact.html">Vragen & contact</a></li>
            <li><a href="overons.html">Over Ons</a></li>
            <a href="logout.php">Uitloggen</a>
        </ul>
    </nav>
</header>

<section class="login-container">
    <div class="login-box">
        <h2>Inloggen</h2>
        <form method="post">
            <label>E-mailadres</label>
            <input id="username" name="username" required>

            <label for="password">Wachtwoord</label>
            <input id="password" name="password" type="password" required>

            <button type="submit" class="login-btn">Inloggen</button>
        </form>
        <p class="register-link">Nog geen account? <a href="registratie.php">Registreer hier</a></p>
        <p class="register-link"> Wachtwoord vergeten? <a href="wachtwoord_vergeten.php">Klik hier</a></p>
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