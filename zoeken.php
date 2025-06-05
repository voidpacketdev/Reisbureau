<?php
$servername = "mysql_db2";
$username = "root";
$password = "rootpassword";
$conn = new PDO("mysql:host=$servername;dbname=reisbureau", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$resultaten = [];

if (isset($_GET['zoekwoord']) && !empty($_GET['zoekwoord'])) {
    $zoekwoord = '%' . $_GET['zoekwoord'] . '%';
    $sql = "SELECT * FROM vakanties WHERE locatie LIKE :zoekwoord OR land LIKE :zoekwoord";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':zoekwoord', $zoekwoord);
    $stmt->execute();
    $resultaten = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Zoeken</title>
    <link rel="stylesheet" href="css/index-styling.css">
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

    <div class="zoekresultaten">
        <?php foreach ($resultaten as $vakantie): ?>
            <div class="zoekresultaat-kaart">
                <img src="images/vakanties/<?php echo htmlspecialchars($vakantie['afbeelding']); ?>"
                     alt="<?php echo htmlspecialchars($vakantie['locatie']); ?>">
                <div class="zoekresultaat-inhoud">
                    <h3><?php echo htmlspecialchars($vakantie['land']); ?></h3>
                    <p><?php echo htmlspecialchars($vakantie['locatie']); ?></p>
                    <p class="prijs">€<?php echo htmlspecialchars($vakantie['prijs']); ?> p.p.</p>
                    <a class="boek-button" href="booking.php">Boek nu</a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


</body>
</html>
