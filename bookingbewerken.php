<?php
session_start();
ob_start();

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

$reizen = [];

if (!empty($_GET["zoek"])) {
    $zoek = '%' . $_GET["zoek"] . '%';
    $stmt = $conn->prepare("SELECT * FROM reizen WHERE bestemming LIKE ? OR verblijf LIKE ? OR beschrijving LIKE ? ORDER BY id DESC");
    $stmt->execute([$zoek, $zoek, $zoek]);
    $reizen = $stmt->fetchAll();
} else {
    $reizen = $conn->query("SELECT * FROM reizen ORDER BY id DESC")->fetchAll();
}

if (isset($_GET["verwijder"])) {
    $stmt = $conn->prepare("DELETE FROM reizen WHERE id = ?");
    $stmt->execute([$_GET["verwijder"]]);
    $_SESSION["flash"] = "Reis verwijderd.";
    header("Location: bookingbewerken.php");
    exit;
}
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Reizen beheren</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bookingbewerken-styling.css">
</head>
<body class="bg-light">
<div class="container mt-4">
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

    <form method="get" class="mb-3 d-flex">
        <input type="text" name="zoek" value="<?= isset($_GET["zoek"]) ? htmlspecialchars($_GET["zoek"]) : '' ?>" class="form-control me-2" placeholder="Zoek op bestemming, verblijf of beschrijving">
        <button type="submit" class="btn btn-primary">Zoek</button>
    </form>

    <table class="table table-striped table-bordered align-middle">
        <thead>
        <tr>
            <th>ID</th>
            <th>Bestemming</th>
            <th>Verblijf</th>
            <th>Prijs</th>
            <th>Foto</th>
            <th>Beschrijving</th>
            <th>Acties</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reizen as $r): ?>
            <tr>
                <td><?= $r["id"] ?></td>
                <td><?= htmlspecialchars($r["bestemming"]) ?></td>
                <td><?= htmlspecialchars($r["verblijf"]) ?></td>
                <td>€<?= number_format($r["prijs"], 2, ',', '.') ?></td>
                <td><img src="<?= htmlspecialchars($r["foto"]) ?>" alt="Foto" style="max-width: 100px;"></td>
                <td><?= nl2br(htmlspecialchars($r["beschrijving"])) ?></td>
                <td class="text-center">
                    <a href="reisbewerken.php?id=<?= $r["id"] ?>" class="btn btn-sm btn-warning mb-1">Bewerk</a><br>
                    <a href="?verwijder=<?= $r["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Reis verwijderen?')">Verwijder</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>
</html>
