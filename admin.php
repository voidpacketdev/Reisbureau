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

// CRUD-acties
if (isset($_GET["gelezen"])) {
    $stmt = $conn->prepare("UPDATE contactberichten SET gelezen = 1 WHERE id = ?");
    $stmt->execute([$_GET["gelezen"]]);
    header("Location: admin.php");
    exit;
}

if (isset($_GET["verwijder_bericht"])) {
    $stmt = $conn->prepare("DELETE FROM contactberichten WHERE id = ?");
    $stmt->execute([$_GET["verwijder_bericht"]]);
    header("Location: admin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["toevoegen"])) {
        $stmt = $conn->prepare("INSERT INTO reizen (bestemming, verblijf, prijs, foto, beschrijving) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_POST["bestemming"], $_POST["verblijf"],
            $_POST["prijs"], $_POST["foto"], $_POST["beschrijving"]
        ]);
        header("Location: admin.php");
        exit;
    }
    if (isset($_POST["update"])) {
        $stmt = $conn->prepare("UPDATE reizen SET bestemming=?, verblijf=?, prijs=?, foto=?, beschrijving=? WHERE id=?");
        $stmt->execute([
            $_POST["bestemming"], $_POST["verblijf"],
            $_POST["prijs"], $_POST["foto"], $_POST["beschrijving"],
            $_POST["id"]
        ]);
        header("Location: admin.php");
        exit;
    }
}

if (isset($_GET["verwijder"])) {
    $stmt = $conn->prepare("DELETE FROM reizen WHERE id = ?");
    $stmt->execute([$_GET["verwijder"]]);
    header("Location: admin.php");
    exit;
}

$berichten = $conn->query("SELECT * FROM contactberichten ORDER BY datum DESC")->fetchAll();
$reizen = [];

if (!empty($_GET["zoek"])) {
    $zoek = '%' . $_GET["zoek"] . '%';
    $stmt = $conn->prepare("SELECT * FROM reizen WHERE bestemming LIKE ? OR verblijf LIKE ? OR beschrijving LIKE ? ORDER BY id DESC");
    $stmt->execute([$zoek, $zoek, $zoek]);
    $reizen = $stmt->fetchAll();
} else {
    $reizen = $conn->query("SELECT * FROM reizen ORDER BY id DESC")->fetchAll();
}

?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <title>Adminpaneel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-styling.css">
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="navigatie">
        <a href="booking.php">Booking</a>
        <a href="contact.html">Vragen & Contact</a>
        <a href="overons.html">Over ons</a>
        <a href="logout.php">Uitloggen</a>
    </div>
    <h1 class="text-center mb-4">Adminpaneel</h1>

    <!-- Contactberichten -->
    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Contactberichten</h2></div>
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
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Nieuwe reis toevoegen -->
    <div class="container" >
        <div class="card mb-4 mx-auto">
            <div class="card-header">
                <h2 class="h5 mb-0 text-center">Nieuwe reis toevoegen</h2>
            </div>
            <div class="card-body">
                <form method="post" class="row g-3">
                    <div class="col-md-6">
                        <input name="bestemming" class="form-control" placeholder="Bestemming" required>
                    </div>
                    <div class="col-md-6">
                        <input name="verblijf" class="form-control" placeholder="Verblijf" required>
                    </div>
                    <div class="col-md-4">
                        <input name="prijs" type="number" step="0.01" class="form-control" placeholder="Prijs (€)" required>
                    </div>
                    <div class="col-md-4">
                        <input name="foto" class="form-control" placeholder="Foto bestandsnaam" required>
                    </div>
                    <div class="col-md-12">
                        <textarea name="beschrijving" class="form-control" rows="3" placeholder="Beschrijving"></textarea>
                    </div>
                    <div class="col-12 text-end">
                        <button type="submit" name="toevoegen" class="btn btn-success">Toevoegen</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bestaande reizen bewerken -->
    <div class="card mb-4">
        <div class="card-header"><h2 class="h5 mb-0">Bestaande reizen beheren</h2></div>
        <div class="card-body">
            <form method="get" class="mb-3 d-flex justify-content-end" >
                <input type="text" name="zoek" value="<?= isset($_GET["zoek"]) ? htmlspecialchars($_GET["zoek"]) : '' ?>"
                       class="form-control me-2"  placeholder="Zoek op bestemming, verblijf of beschrijving">
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
                        <form method="post">
                            <td><?= $r["id"] ?><input type="hidden" name="id" value="<?= $r["id"] ?>"></td>
                            <td><input name="bestemming" class="form-control" value="<?= htmlspecialchars($r["bestemming"]) ?>"></td>
                            <td><input name="verblijf" class="form-control" value="<?= htmlspecialchars($r["verblijf"]) ?>"></td>
                            <td><input name="prijs" type="number" step="0.01" class="form-control" value="<?= htmlspecialchars($r["prijs"]) ?>"></td>
                            <td><input name="foto" class="form-control" value="<?= htmlspecialchars($r["foto"]) ?>"></td>
                            <td><textarea name="beschrijving" class="form-control" rows="2"><?= htmlspecialchars($r["beschrijving"]) ?></textarea></td>
                            <td class="align-middle text-center">
                                <button type="submit" name="update" class="btn btn-sm btn-primary mb-1">Opslaan</button><br>
                                <a href="?verwijder=<?= $r["id"] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Verwijderen?')">Verwijder</a>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

</div> <!-- container-fluid -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
