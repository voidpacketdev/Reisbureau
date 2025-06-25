<?php
session_start();
ob_start();

if (!isset($_SESSION["gebruiker_id"]) || $_SESSION["rol"] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adminpaneel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin-styling.css">
</head>
<body class="bg-light">
<div class="container-fluid py-4">
    <div class="header-nav">
        <a href="booking.php">Booking</a>
        <a href="contact.html">Vragen & Contact</a>
        <a href="logout.php">Uitloggen</a>
        <a href="account.php">Klantenpaneel</a>
    </div>
    <nav>
        <a href="bookingbewerken.php">Bookingen bewerken</a>
        <a href="nieuwereis.php">Nieuwe reis toevoegen</a>
        <a href="contactberichten.php">Contactenberichten</a>
    </nav>
    <h1 class="text-center mb-4">Adminpaneel</h1>

</div>
</body>
</html>
