<?php
session_start();
if (!isset($_SESSION["gebruiker_id"])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET["id"])) {
    $conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");
    $stmt = $conn->prepare("DELETE FROM boekingen WHERE id = ? AND gebruiker_id = ?");
    $stmt->execute([$_GET["id"], $_SESSION["gebruiker_id"]]);
}

header("Location: account.php");
exit;
?>
