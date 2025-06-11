<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $telefoon = $_POST['telefoon'] ?? '';
    $message = $_POST['message'] ?? '';

    if ($name && $email && $telefoon && $message) {
        try {
            $conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO contactberichten (naam, email, telefoon, bericht, gelezen) VALUES (?, ?, ?, ?, 0)");
            $stmt->execute([$name, $email, $telefoon, $message]);

            header("Location: contact.html?success=1");
            exit;
        } catch (PDOException $e) {
            header("Location: contact.html?success=0");
            exit;
        }
    } else {
        header("Location: contact.html?success=0");
        exit;
    }
}
