<?php
session_start();

// Alleen admins mogen deze pagina bekijken
if (!isset($_SESSION["gebruiker_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

// Toevoegen
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["toevoegen"])) {
    $bestemming = $_POST["bestemming"];
    $verblijf = $_POST["verblijf"];
    $prijs = $_POST["prijs"];
    $foto = $_POST["foto"];
    $beschrijving = $_POST["beschrijving"];

    $stmt = $conn->prepare("INSERT INTO reizen (bestemming, verblijf, prijs, foto, beschrijving) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$bestemming, $verblijf, $prijs, $foto, $beschrijving]);
    header("Location: admin.php");
    exit;
}

// Wijzigen
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update"])) {
    $id = $_POST["id"];
    $bestemming = $_POST["bestemming"];
    $verblijf = $_POST["verblijf"];
    $prijs = $_POST["prijs"];
    $foto = $_POST["foto"];
    $beschrijving = $_POST["beschrijving"];

    $stmt = $conn->prepare("UPDATE reizen SET bestemming=?, verblijf=?, prijs=?, foto=?, beschrijving=? WHERE id=?");
    $stmt->execute([$bestemming, $verblijf, $prijs, $foto, $beschrijving, $id]);
    header("Location: admin.php");
    exit;
}

// Verwijderen
if (isset($_GET["verwijder"])) {
    $id = $_GET["verwijder"];
    $stmt = $conn->prepare("DELETE FROM reizen WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

$reizen = $conn->query("SELECT * FROM reizen")->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Adminpaneel</title>
    <link rel="stylesheet" href="/css/index-styling.css">
    <link rel="stylesheet" href="css/booking-styling.css">
    <style>
        form, table { margin: 20px auto; width: 90%; }
        table { border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 8px; vertical-align: top; }
        th { background-color: #f4f4f4; }
        textarea { width: 100%; }
        input[type="text"], input[type="number"] { width: 100%; }
    </style>
</head>
<body>

<h1>Adminpaneel: Reizen beheren</h1>

<h2>Nieuwe reis toevoegen</h2>
<form method="post">
    <label>Bestemming: <input type="text" name="bestemming" required></label><br>
    <label>Verblijf: <input type="text" name="verblijf" required></label><br>
    <label>Prijs (€): <input type="number" name="prijs" step="0.01" required></label><br>
    <label>Foto bestandsnaam: <input type="text" name="foto" required></label><br>
    <label>Beschrijving:<br><textarea name="beschrijving" rows="4"></textarea></label><br>
    <button type="submit" name="toevoegen">Toevoegen</button>
</form>

<h2>Bestaande reizen bewerken</h2>
<?php foreach ($reizen as $reis): ?>
    <form method="post">
        <input type="hidden" name="id" value="<?= $reis["id"] ?>">
        <table>
            <tr>
                <th>ID</th>
                <th>Bestemming</th>
                <th>Verblijf</th>
                <th>Prijs</th>
                <th>Foto</th>
                <th>Beschrijving</th>
                <th>Acties</th>
            </tr>
            <tr>
                <td><?= $reis["id"] ?></td>
                <td><input type="text" name="bestemming" value="<?= htmlspecialchars($reis["bestemming"]) ?>"></td>
                <td><input type="text" name="verblijf" value="<?= htmlspecialchars($reis["verblijf"]) ?>"></td>
                <td><input type="number" name="prijs" step="0.01" value="<?= htmlspecialchars($reis["prijs"]) ?>"></td>
                <td><input type="text" name="foto" value="<?= htmlspecialchars($reis["foto"]) ?>"></td>
                <td><textarea name="beschrijving" rows="3"><?= htmlspecialchars($reis["beschrijving"]) ?></textarea></td>
                <td>
                    <button type="submit" name="update">Opslaan</button><br><br>
                    <a href="admin.php?verwijder=<?= $reis["id"] ?>" onclick="return confirm('Weet je zeker dat je deze reis wilt verwijderen?')">Verwijder</a>
                </td>
            </tr>
        </table>
    </form>
<?php endforeach; ?>

</body>
</html>
