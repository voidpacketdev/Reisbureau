<?php
session_start();
ob_start(); // Zorgt dat header() geen fouten geeft als er output is

// Alleen admins mogen deze pagina bekijken
if (!isset($_SESSION["gebruiker_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}

$conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

// Contactbericht markeren als gelezen
if (isset($_GET["gelezen"])) {
    $id = $_GET["gelezen"];
    $stmt = $conn->prepare("UPDATE contactberichten SET gelezen = 1 WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Contactbericht verwijderen
if (isset($_GET["verwijder_bericht"])) {
    $id = $_GET["verwijder_bericht"];
    $stmt = $conn->prepare("DELETE FROM contactberichten WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

// Reizenbeheer acties
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

if (isset($_GET["verwijder"])) {
    $id = $_GET["verwijder"];
    $stmt = $conn->prepare("DELETE FROM reizen WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit;
}

$reizen = $conn->query("SELECT * FROM reizen")->fetchAll();
$berichten = $conn->query("SELECT * FROM contactberichten ORDER BY datum DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Adminpaneel</title>
    <link rel="stylesheet" href="/css/index-styling.css">
    <link rel="stylesheet" href="css/booking-styling.css">
    <style>
        form, table {
            margin: 20px auto;
            width: 90%;
        }

        table {
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            vertical-align: top;
        }

        th {
            background-color: #f4f4f4;
        }

        textarea {
            width: 100%;
        }

        input[type="text"], input[type="number"] {
            width: 100%;
        }

        .bericht-container {
            margin: 20px auto;
            width: 90%;
            background-color: #f2f2f2;
            padding: 15px;
            border-radius: 6px;
        }

        .bericht-container.gelezen {
            background-color: #d4f7d4;
        }

        .bericht-buttons {
            margin-top: 10px;
        }

        .bericht-buttons a {
            margin-right: 10px;
            text-decoration: none;
            padding: 5px 10px;
            background-color: #387aff;
            color: white;
            border-radius: 4px;
        }

        .bericht-buttons a.verwijder {
            background-color: #ff4c4c;
        }

        .bericht-buttons a:hover {
            opacity: 0.8;
        }
    </style>
</head>
<body>

<h1>Adminpaneel</h1>

<h2>Contactberichten</h2>
<?php foreach ($berichten as $bericht): ?>
    <div class="bericht-container <?= $bericht["gelezen"] ? "gelezen" : "" ?>">
        <p><strong>Naam:</strong> <?= htmlspecialchars($bericht["naam"]) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($bericht["email"]) ?></p>
        <p><strong>Telefoon:</strong> <?= htmlspecialchars($bericht["telefoon"]) ?></p>
        <p><strong>Bericht:</strong><br><?= nl2br(htmlspecialchars($bericht["bericht"])) ?></p>
        <p><strong>Datum:</strong> <?= $bericht["datum"] ?></p>
        <div class="bericht-buttons">
            <?php if (!$bericht["gelezen"]): ?>
                <a href="admin.php?gelezen=<?= $bericht["id"] ?>">Markeer als gelezen</a>
            <?php endif; ?>
            <a class="verwijder" href="admin.php?verwijder_bericht=<?= $bericht["id"] ?>" onclick="return confirm('Weet je zeker dat je dit bericht wilt verwijderen?')">Verwijder</a>
        </div>
    </div>
<?php endforeach; ?>

<hr>

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
