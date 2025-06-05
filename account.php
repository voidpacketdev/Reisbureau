<?php
session_start();
if (!isset($_SESSION["gebruiker_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

// Gebruikersgegevens ophalen
$gebruiker_id = $_SESSION["gebruiker_id"];
$stmt = $conn->prepare("SELECT gebruikersnaam, wachtwoord FROM gebruikers WHERE id = ?");
$stmt->execute([$gebruiker_id]);
$gebruiker = $stmt->fetch();

// Boekingen ophalen
$sql = "SELECT boekingen.id AS boeking_id, reizen.bestemming, reizen.verblijf, reizen.prijs, reizen.foto
        FROM boekingen
        JOIN reizen ON boekingen.reis_id = reizen.id
        WHERE boekingen.gebruiker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$gebruiker_id]);
$boekingen = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="/css/index-styling.css">
    <link rel="stylesheet" href="css/booking-styling.css">
    <link rel="stylesheet" href="css/account-styling.css">
</head>

<body>

<div class="background">
    <nav>
        <div>
            <a href="index.html"><img src="images/logo.png" alt="JMLogo"></a>
        </div>
        <div class="navigatie">
            <a href="booking.php">Booking</a>
            <a href="contact.html">Vragen & Contact</a>
            <a href="overons.html">Over ons</a>
            <a href="logout.php">Uitloggen</a>
        </div>

        <div class="login-parent">
            <form action="zoeken.php" method="get">
                <input type="text" name="zoekwoord" placeholder="Zoek vakanties...">
                <button type="submit">Zoeken</button>
            </form>


            <a href="login.php">
                <div><img class="login-image" src="images/account.png" alt="login"></div>
            </a>
        </div>
    </nav>



<h2 class="accountgegevens" >Jouw accountgegevens</h2>
<p><strong>Gebruikersnaam:</strong> <?= htmlspecialchars($gebruiker["gebruikersnaam"]) ?></p>
<p><strong>Wachtwoord (versleuteld):</strong> <?= htmlspecialchars($gebruiker["wachtwoord"]) ?></p>

<hr>

<h2>Jouw geboekte reizen</h2>
<?php foreach ($boekingen as $boeking): ?>
    <div class="boeking">
        <img src="images/vakanties/<?= htmlspecialchars($boeking["foto"]) ?>" alt="foto" width="200">
        <p><strong>Bestemming:</strong> <?= htmlspecialchars($boeking["bestemming"]) ?></p>
        <p><strong>Verblijf:</strong> <?= htmlspecialchars($boeking["verblijf"]) ?></p>
        <p><strong>Prijs:</strong> €<?= htmlspecialchars($boeking["prijs"]) ?></p>
        <a href="annuleer.php?id=<?= $boeking["boeking_id"] ?>">Annuleer</a>
    </div>
<?php endforeach; ?>

    <p class="register-link"> Ben je admin? <a href="admin.php">Klik hier</a></p>
</body>