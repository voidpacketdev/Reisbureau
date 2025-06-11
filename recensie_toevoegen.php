<?php
$conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reis_id"])) {
    $naam = $_POST["naam"];
    $beoordeling = $_POST["beoordeling"];
    $reis_id = $_POST["reis_id"];

    $stmt = $conn->prepare("INSERT INTO recensies (naam, beoordeling, reis_id, datum) VALUES (?, ?, ?, NOW())");
    $stmt->execute([$naam, $beoordeling, $reis_id]);

    header("Location: booking.php"); // Of terug naar boeken.php?id=...
    exit;
}

// Ophalen van reis voor weergave
if (isset($_GET["id"])) {
    $stmt = $conn->prepare("SELECT * FROM reizen WHERE id = ?");
    $stmt->execute([$_GET["id"]]);
    $reis = $stmt->fetch();
    if (!$reis) {
        die("Reis niet gevonden.");
    }
} else {
    die("Geen reis-id opgegeven.");
}



$stmt = $conn->prepare("INSERT INTO recensies (reis_id, naam, email, beoordeling, datum) VALUES (?, ?, ?, ?, NOW())");
$stmt->execute([
    $_POST["reis_id"],
    $_POST["naam"],
    $_POST["email"],
    $_POST["beoordeling"]
]);

header("Location: booking.php?id=" . $_POST["reis_id"]);
exit;

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Recensie toevoegen</title>
    <link rel="stylesheet" href="css/booking-styling.css">
</head>
<body>
<h2>Recensie plaatsen voor: <?= htmlspecialchars($reis["bestemming"]) ?></h2>
<form action="recensie_toevoegen.php" method="post">

    <input type="hidden" name="reis_id" value="..."> <!-- al aanwezig -->
    <input type="text" name="naam" placeholder="Jouw naam" required>
    <input type="email" name="email" placeholder="Jouw e-mailadres" required>
    <textarea name="beoordeling" placeholder="Jouw recensie..." required></textarea>
    <button type="submit">Plaats recensie</button>
</form>

</body>
</html>
