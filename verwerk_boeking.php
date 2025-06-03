<?php
session_start();
if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}
$conn = new PDO("mysql:host=mysql_db2;dbname=login_systeem", "root", "rootpassword");

$reis_id = $_POST['reis_id'];
$gebruiker_id = $_SESSION['gebruiker_id'];

$sql = "INSERT INTO boekingen (gebruiker_id, reis_id) VALUES (:gebruiker_id, :reis_id)";
$stmt = $conn->prepare($sql);
$stmt->execute([':gebruiker_id' => $gebruiker_id, ':reis_id' => $reis_id]);

header('Location: account.php');
exit;
?>
