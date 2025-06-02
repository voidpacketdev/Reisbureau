<?php
$servername = "mysql_db2";
$username = "root";
$password = "rootpassword";

try {
    $conn = new PDO("mysql:host=$servername;dbname=login_systeem", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check of formulier verzonden is
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $gebruikersnaam = $_POST['username'];
        $wachtwoord = $_POST['password'];

        // Controleer of gebruiker al bestaat
        $check = $conn->prepare("SELECT * FROM `gebruikers` WHERE `gebruikersnaam` = :username");
        $check->bindParam(':username', $gebruikersnaam);
        $check->execute();

        if ($check->rowCount() > 0) {
            echo "Gebruikersnaam bestaat al!";
        } else {
            // Hash wachtwoord en sla op
            $hashedWachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `gebruikers` (`gebruikersnaam`, `wachtwoord`) VALUES (:username, :password)";
            $statement = $conn->prepare($sql);
            $statement->bindParam(':username', $gebruikersnaam);
            $statement->bindParam(':password', $hashedWachtwoord);
            $statement->execute();

            echo "Account succesvol aangemaakt! <a href='login.php'>Login hier</a>";
        }
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
        <h2>Registratie</h2>
        <form method="post">
            <label>E-mailadres</label>
            <input id="username" name="username" required>

            <label for="password">Wachtwoord</label>
            <input id="password" name="password" required>

            <button type="submit" class="login-btn">Inloggen</button>
        </form>
        <p class="register-link">al een account? <a href="login.php">login hier</a></p>
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