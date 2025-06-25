<?php
session_start();

if (!isset($_SESSION["gebruiker_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}

try {
    $conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB-verbinding mislukt.");
}

$berichten = $conn->query("SELECT * FROM contactberichten WHERE gelezen = 0 AND gearchiveerd = 0 ORDER BY datum DESC")->fetchAll();
$reizen = [];

if (isset($_GET["gelezen"])) {
$stmt = $conn->prepare("UPDATE contactberichten SET gelezen = 1 WHERE id = ?");
$stmt->execute([$_GET["gelezen"]]);
$_SESSION["flash"] = "Bericht gemarkeerd als gelezen.";
header("Location: contactberichten.php");
exit;
}

if (isset($_GET["archiveer"])) {
    $stmt = $conn->prepare("UPDATE contactberichten SET gearchiveerd = 1 WHERE id = ?");
    $stmt->execute([$_GET["archiveer"]]);
    $_SESSION["flash"] = "Bericht gearchiveerd.";
    header("Location: contactberichten.php");
    exit;
}

if (isset($_GET["verwijder_bericht"])) {
$stmt = $conn->prepare("DELETE FROM contactberichten WHERE id = ?");
$stmt->execute([$_GET["verwijder_bericht"]]);
$_SESSION["flash"] = "Bericht verwijderd.";
header("Location: admin.php");
exit;
}

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/contactberichten-styling.css">
    <title>Contactberichten | Adminpaneel</title>
</head>
<body>
<div class="header-nav">
    <a href="bookingbewerken.php">Bookingen bewerken</a>
    <a href="nieuwereis.php">Nieuwe reis toevoegen</a>
    <a href="logout.php">Uitloggen</a>
    <a href="account.php">Klantenpaneel</a>
</div>
<nav>
    <a href="gearchiveerdeberichten.php">Gearchiveerde berichten</a>
    <a href="gelezenberichten.php">Gelezen berichten</a>
    <a href="admin.php">Terug naar adminpaneel</a>
</nav>
<h1 class="adminpanel-text">Adminpaneel</h1>
<div class="card mb-4">
    <div class="card-header">
        <h2 class="contactberichten-title">Inbox Berichten</h2>
    </div>
    <div class="card-body">
        <?php if (!$berichten): ?>
            <p class="text-muted">Geen berichten gevonden.</p>
        <?php endif; ?>
        <?php foreach ($berichten as $b): ?>
            <div class="card mb-3 <?= $b["gelezen"] ? 'border-success' : '' ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($b["naam"]) ?></h5>
                    <p class="mb-1"><small>Email: <?= htmlspecialchars($b["email"]) ?> | Tel: <?= htmlspecialchars($b["telefoon"]) ?> | Datum: <?= $b["datum"] ?></small></p>
                    <p class="card-text"><?= nl2br(htmlspecialchars($b["bericht"])) ?></p>
                    <div>
                        <?php if (!$b["gelezen"]): ?>
                            <a href="?gelezen=<?= $b["id"] ?>" class="btn btn-sm btn-primary">Markeer als gelezen</a>
                        <?php endif; ?>
                        <a href="?verwijder_bericht=<?= $b["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Verwijderen?')">Verwijder</a>
                        <a href="?archiveer=<?= $b["id"] ?>" class="btn btn-sm btn-warning" onclick="return confirm('Weet je zeker dat je dit bericht wilt archiveren?')">Archiveer</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
</body>
</html>
