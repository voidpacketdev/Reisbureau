<?php
$servername = "mysql_db2";
$username = "root";
$password = "rootpassword";

try {
    $conn = new PDO("mysql:host=$servername;dbname=reisbureau", $username, $password);
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>J&M Travel | Registratie</title>
    <link rel="stylesheet" href="css/index-styling.css">
    <link rel="stylesheet" href="css/login-styling.css">
    <link rel="stylesheet" href="css/registratie-styling.css">
</head>
<body>
<header class="main-header">
    <a class="logo" href="index.php"><img src="images/logo.png" alt="JMLogo"></a>
    <nav>
        <div class="nav-links">
            <a href="booking.php">Booking</a>
            <a href="contact.html">Vragen & contact</a>
            <a href="overons.html">Over ons</a>
            <a href="logout.php">Uitloggen</a>
            <a class="login" href="login.php">login</a>
        </div>
    </nav>
</header>

<section class="login-container">
    <div class="login-box">
        <h2>Registratie</h2>
        <form method="post">
            <label>E-mailadres</label>
            <input id="username" name="username" required>

            <label for="password">Wachtwoord</label>
            <input id="password" name="password" type="password" required>

            <button type="submit" class="login-btn">Registreren</button>
        </form>
        <p class="register-link">Al een account? <a href="login.php">login hier</a></p>
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