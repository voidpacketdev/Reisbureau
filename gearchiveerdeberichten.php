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

$berichten = $conn->query("SELECT * FROM contactberichten WHERE gearchiveerd = 1 ORDER BY datum DESC")->fetchAll();

if (isset($_GET["verwijder_bericht"])) {
    $stmt = $conn->prepare("DELETE FROM contactberichten WHERE id = ?");
    $stmt->execute([$_GET["verwijder_bericht"]]);
    $_SESSION["flash"] = "Bericht verwijderd.";
    header("Location: gearchiveerdeberichten.php");
    exit;
}

if (isset($_GET["dearchiveer"])) {
    $stmt = $conn->prepare("UPDATE contactberichten SET gearchiveerd = 0 WHERE id = ?");
    $stmt->execute([$_GET["dearchiveer"]]);
    $_SESSION["flash"] = "Bericht teruggezet naar inbox.";
    header("Location: gearchiveerdeberichten.php");
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
    <link rel="stylesheet" href="css/gearchiveerdeberichten-styling.css">
    <title>Gearchiveerde berichten</title>
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
    <a href="contactberichten.php">Inbox</a>
</nav>
<?php if (isset($_SESSION["flash"])): ?>
    <div class="alert alert-success text-center mx-4">
        <?= $_SESSION["flash"] ?>
        <?php unset($_SESSION["flash"]); ?>
    </div>
<?php endif; ?>
<h1 class="adminpanel-text">Adminpaneel</h1>
<div class="card mb-4">
    <div class="card-header">
        <h2 class="contactberichten-title">Gearchiveerde berichten</h2>
    </div>
        <?php if (!$berichten): ?>
            <p class="text-muted">Geen gearchiveerde berichten gevonden.</p>
        <?php endif; ?>

        <?php foreach ($berichten as $b): ?>
            <div class="card mb-3 <?= $b["gelezen"] ? 'border-success' : '' ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($b["naam"]) ?></h5>
                    <p><small>Email: <?= htmlspecialchars($b["email"]) ?> | Tel: <?= htmlspecialchars($b["telefoon"]) ?> | Datum: <?= $b["datum"] ?></small></p>
                    <p><?= nl2br(htmlspecialchars($b["bericht"])) ?></p>
                    <div>
                        <a href="?dearchiveer=<?= $b["id"] ?>" class="btn btn-sm btn-secondary">Terug naar inbox</a>
                        <a href="?verwijder_bericht=<?= $b["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Bericht definitief verwijderen?')">Verwijder</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>