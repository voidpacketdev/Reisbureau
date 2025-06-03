<?php
session_start();
if (!isset($_SESSION["gebruiker_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new PDO("mysql:host=mysql_db2;dbname=login_systeem", "root", "rootpassword");

$gebruiker_id = $_SESSION["gebruiker_id"];
$sql = "SELECT boekingen.id AS boeking_id, reizen.bestemming, reizen.verblijf, reizen.prijs, reizen.foto
        FROM boekingen
        JOIN reizen ON boekingen.reis_id = reizen.id
        WHERE boekingen.gebruiker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$gebruiker_id]);
$boekingen = $stmt->fetchAll();
?>

<h2>Jouw geboekte reizen</h2>
<?php foreach ($boekingen as $boeking): ?>
    <div class="boeking">
        <img src="images/vakanties/<?= htmlspecialchars($boeking["foto"]) ?>" alt="foto">
        <p><strong>Bestemming:</strong> <?= htmlspecialchars($boeking["bestemming"]) ?></p>
        <p><strong>Verblijf:</strong> <?= htmlspecialchars($boeking["verblijf"]) ?></p>
        <p><strong>Prijs:</strong> €<?= htmlspecialchars($boeking["prijs"]) ?></p>
        <a href="annuleer.php?id=<?= $boeking["boeking_id"] ?>">Annuleer</a>
    </div>
<?php endforeach; ?>
