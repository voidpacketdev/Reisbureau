<?php
$servername = "mysql_db2";
$username = "root";
$password = "rootpassword";

try {
    $conn = new PDO("mysql:host=$servername;dbname=reisbureau", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $gebruikersnaam = $_POST['username'];
        $nieuwWachtwoord = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

        // Controleer of gebruiker bestaat
        $check = $conn->prepare("SELECT * FROM gebruikers WHERE gebruikersnaam = :username");
        $check->bindParam(':username', $gebruikersnaam);
        $check->execute();
        $gebruiker = $check->fetch();

        if ($gebruiker) {
            // Wachtwoord bijwerken
            $update = $conn->prepare("UPDATE gebruikers SET wachtwoord = :new_password WHERE gebruikersnaam = :username");
            $update->bindParam(':new_password', $nieuwWachtwoord);
            $update->bindParam(':username', $gebruikersnaam);
            $update->execute();
            echo "Wachtwoord is succesvol bijgewerkt!";
        } else {
            echo "Gebruiker niet gevonden.";
        }
    }
} catch(PDOException $e) {
    echo "Fout bij verbinding: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Wachtwoord Vergeten</title>
</head>
<body>
<h2>Wachtwoord Vergeten</h2>
<form method="post">
    <label>Gebruikersnaam:</label>
    <input type="text" name="username" required><br>

    <label>Nieuw wachtwoord:</label>
    <input type="password" name="new_password" required><br>

    <button type="submit">Wachtwoord resetten</button>
</form>

<a href="index.html">klik hier om terug te gaan naar hoofdpagina</a>
</body>
</html>
