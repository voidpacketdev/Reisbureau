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

$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    echo "Ongeldige ID.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("UPDATE reizen SET bestemming = ?, verblijf = ?, prijs = ?, foto = ?, beschrijving = ? WHERE id = ?");
    $stmt->execute([
        $_POST['bestemming'],
        $_POST['verblijf'],
        $_POST['prijs'],
        $_POST['foto'],
        $_POST['beschrijving'],
        $_POST['id']
    ]);
    $_SESSION["flash"] = "Reis succesvol bijgewerkt.";
    header("Location: bookingbewerken.php");
    exit;
}

$stmt = $conn->prepare("SELECT * FROM reizen WHERE id = ?");
$stmt->execute([$id]);
$reis = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$reis) {
    echo "Reis niet gevonden.";
    exit;
}
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Reis Bewerken</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1>Reis bewerken</h1>

    <form method="POST" class="bg-white p-4 border rounded">
        <input type="hidden" name="id" value="<?= $reis['id'] ?>">

        <div class="mb-3">
            <label for="bestemming" class="form-label">Bestemming</label>
            <input type="text" name="bestemming" id="bestemming" class="form-control" value="<?= htmlspecialchars($reis['bestemming']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="verblijf" class="form-label">Verblijf</label>
            <input type="text" name="verblijf" id="verblijf" class="form-control" value="<?= htmlspecialchars($reis['verblijf']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="prijs" class="form-label">Prijs (€)</label>
            <input type="number" name="prijs" id="prijs" class="form-control" step="0.01" value="<?= htmlspecialchars($reis['prijs']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="foto" class="form-label">Foto URL</label>
            <input type="text" name="foto" id="foto" class="form-control" value="<?= htmlspecialchars($reis['foto']) ?>" required>
        </div>

        <div class="mb-3">
            <label for="beschrijving" class="form-label">Beschrijving</label>
            <textarea name="beschrijving" id="beschrijving" class="form-control" rows="4" required><?= htmlspecialchars($reis['beschrijving']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Opslaan</button>
        <a href="bookingbewerken.php" class="btn btn-secondary">Annuleren</a>
    </form>
</div>
</body>
</html>
