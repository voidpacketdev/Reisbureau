<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam']);
    $email = trim($_POST['email']);
    $recensie = trim($_POST['recensie']);

    if ($naam !== '' && $email !== '' && $recensie !== '') {
        try {
            $conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $conn->prepare("INSERT INTO recensies (naam, email, recensie) VALUES (?, ?, ?)");
            $stmt->execute([$naam, $email, $recensie]);

            header("Location: booking.html?bericht=bedankt");
            exit;
        } catch (PDOException $e) {
            echo "Fout bij opslaan recensie: " . $e->getMessage();
        }
    } else {
        echo "Vul alle velden in.";
    }
} else {
    header("Location: booking.html");
    exit;
}
