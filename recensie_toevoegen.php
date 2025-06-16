<?php
$conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reis_id"])) {
    $naam = $_POST["naam"];
    $email = $_POST["email"];
    $recensie = $_POST["recensie"];
    $reis_id = $_POST["reis_id"];

    $stmt = $conn->prepare("INSERT INTO recensies (naam, email, recensie, reis_id, datum) VALUES (?, ?, ?, ?, NOW())");
    $stmt->execute([$naam, $email, $recensie, $reis_id]);

    header("Location: booking.php?id=" . $reis_id);
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
    <select name="beoordeling" required>
        <option value="">-- Beoordeling --</option>
        <option value="1">1 ster</option>
        <option value="2">2 sterren</option>
        <option value="3">3 sterren</option>
        <option value="4">4 sterren</option>
        <option value="5">5 sterren</option>
    </select>



    <input type="text" name="naam" placeholder="Jouw naam" required>
    <input type="email" name="email" placeholder="Jouw e-mailadres" required>
    <textarea name="recensie" placeholder="Jouw recensie..." required></textarea>
    <input type="hidden" name="reis_id" value="<?= htmlspecialchars($reis["id"]) ?>">
    <button type="submit">Plaats recensie</button>

</form>

</body>
</html>
