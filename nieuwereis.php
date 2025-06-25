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

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["toevoegen"])) {
        if (!is_numeric($_POST["prijs"])) {
            $_SESSION["flash"] = "Prijs moet een geldig getal zijn.";
        } else {
            $stmt = $conn->prepare("INSERT INTO reizen (bestemming, verblijf, prijs, foto, beschrijving) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST["bestemming"], $_POST["verblijf"],
                $_POST["prijs"], $_POST["foto"], $_POST["beschrijving"]
            ]);
            $_SESSION["flash"] = "Reis succesvol toegevoegd!";
        }
        header("Location: nieuwereis.php");
        exit;
    }
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
    <link rel="stylesheet" href="css/admin-styling.css">
    <title>Nieuwe Reis Toevoegen | Adminpaneel</title>
</head>
<body>
<div class="container-fluid py-4">
    <div class="header-nav">
        <a href="booking.php">Booking</a>
        <a href="contact.html">Vragen & Contact</a>
        <a href="logout.php">Uitloggen</a>
        <a href="account.php">Klantenpaneel</a>
    </div>
    <nav>
        <a href="bookingbewerken.php">Bookingen bewerken</a>
        <a href="admin.php">Terug naar adminpaneel</a>
        <a href="contactberichten.php">Contactenberichten</a>
    </nav>
    <h1 class="text-center mb-4">Adminpaneel</h1>
    <?php if (isset($_SESSION["flash"])): ?>
        <div class="alert alert-success text-center">
            <?= $_SESSION["flash"] ?>
            <?php unset($_SESSION["flash"]); ?>
        </div>
    <?php endif; ?>
<div class="container">
    <div class="card mb-4 mx-auto">
        <div class="card-header text-center"><h2 class="h5 mb-0">Nieuwe reis toevoegen</h2></div>
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
</body>
</html>
