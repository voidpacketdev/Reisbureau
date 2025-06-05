<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");

    $stmt = $conn->prepare("INSERT INTO contactberichten (naam, email, telefoon, bericht, gelezen) VALUES (?, ?, ?, ?, 0)");
    $stmt->execute([
        $_POST["name"],
        $_POST["email"],
        $_POST["telefoon"],
        $_POST["message"]
    ]);

    header("Location: contact.html?success=1");
    exit;
}
?>
