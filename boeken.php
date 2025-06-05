<?php
session_start();
if (!isset($_SESSION["gebruiker_id"])) {
    header("Location: login.php");
    exit;
}

$conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

if (isset($_POST["boek"])) {
    // Boeking verwerken
    $reis_id = $_POST["reis_id"];
    $gebruiker_id = $_SESSION["gebruiker_id"];

    $stmt = $conn->prepare("INSERT INTO boekingen (gebruiker_id, reis_id) VALUES (?, ?)");
    $stmt->execute([$gebruiker_id, $reis_id]);

    header("Location: account.php");
    exit;
}

if (isset($_GET["id"])) {
    $reis_id = $_GET["id"];

    // Reisgegevens ophalen
    $stmt = $conn->prepare("SELECT * FROM reizen WHERE id = ?");
    $stmt->execute([$reis_id]);
    $reis = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$reis) {
        echo "Reis niet gevonden.";
        exit;
    }
} else {
    echo "Geen reis geselecteerd.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Boek jouw reis</title>
</head>
<body>

<h2>Boek jouw reis naar <?= htmlspecialchars($reis["bestemming"]) ?></h2>
<img src="images/vakanties/<?= htmlspecialchars($reis["foto"]) ?>" alt="Reisfoto" width="300">
<p>Verblijf: <?= htmlspecialchars($reis["verblijf"]) ?></p>
<p>Prijs: €<?= htmlspecialchars($reis["prijs"]) ?></p>

<form method="post" action="">
    <input type="hidden" name="reis_id" value="<?= htmlspecialchars($reis["id"]) ?>">
    <label for="vertrekdatum">Vertrekdatum:</label>
    <input type="date" id="vertrekdatum" name="vertrekdatum" required>
    <br><br>
    <button type="submit" name="boek">Boek nu</button>
</form>

</body>
</html>
