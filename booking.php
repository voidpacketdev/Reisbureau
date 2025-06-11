

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="css/index-styling.css">
    <link rel="stylesheet" href="css/booking-styling.css">
    <title>Document</title>
</head>
<body>

<div class="background">
    <nav>
        <div>
            <a href="index.php"><img src="images/logo.png" alt="JMLogo"></a>
        </div>
        <div class="navigatie">
            <a href="booking.php">Booking</a>
            <a href="contact.html">Vragen & Contact</a>
            <a href="overons.html">Over ons</a>
            <a href="logout.php">Uitloggen</a>
        </div>

        <div class="login-parent">
            <form action="zoeken.php" method="get" class="nav-zoekbalk">
                <input type="text" name="zoekwoord" placeholder="Zoek vakanties..." class="nav-zoekveld">
                <button type="submit" class="nav-zoekknop">Zoeken</button>
            </form>

            <a href="login.php">
                <div><img class="login-image" src="images/account.png" alt="login"></div>
            </a>
        </div>
    </nav>

    <form class="zoekbalk" method="get" action="">

    <input class="zoekveld" type="text" name="bestemming" placeholder="Bestemming">
        <input class="zoekveld" type="date" name="datum">
        <select class="zoekveld" name="hoelang">
            <option value="">Hoelang</option>
            <option value="3">3 dagen</option>
            <option value="5">5 dagen</option>
            <option value="7">1 week</option>
            <option value="14">2 weken</option>
        </select>
        <select class="zoekveld" name="personen">
            <option value="">Personen</option>
            <option value="1">1 persoon</option>
            <option value="2">2 personen</option>
            <option value="3">3 personen</option>
            <option value="4+">4 of meer</option>
        </select>
        <button type="submit" class="zoekknop">Toon vakanties</button>
    </form>
</div>
<div class="landen-kaarten">


        <?php
        $conn = new PDO("mysql:host=mysql_db2;dbname=reisbureau", "root", "rootpassword");
        $where = [];
        $params = [];

        if (!empty($_GET['bestemming'])) {
            $where[] = "bestemming LIKE ?";
            $params[] = '%' . $_GET['bestemming'] . '%';
        }

        if (!empty($_GET['hoelang'])) {
            $where[] = "beschrijving LIKE ?"; // of maak hier een aparte kolom voor in de toekomst
            $params[] = '%' . $_GET['hoelang'] . ' dagen%';
        }

        if (!empty($_GET['personen'])) {
            $where[] = "beschrijving LIKE ?"; // eventueel kolom `capaciteit` toevoegen
            $params[] = '%' . $_GET['personen'] . ' persoon%';
        }

        $query = "SELECT * FROM reizen";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $stmt = $conn->prepare($query);
        $stmt->execute($params);
        $reizen = $stmt->fetchAll();


        foreach ($reizen as $reis) {
            echo '
        <div class="kaart">
            <img class="kaart-afbeelding" src="images/vakanties/' . htmlspecialchars($reis["foto"]) . '" alt="' . htmlspecialchars($reis["bestemming"]) . '">
            <div class="kaart-inhoud">
                <div class="kaart-naam">' . htmlspecialchars($reis["bestemming"]) . '</div>
                <div class="kaart-locatie">' . htmlspecialchars($reis["verblijf"]) . '</div>
                <div class="kaart-prijs">€' . htmlspecialchars($reis["prijs"]) . ' p.p.</div>
                <p class="kaart-beschrijving">' . nl2br(htmlspecialchars($reis["beschrijving"])) . '</p>
                <a href="boeken.php?id=' . $reis["id"] . '"><button class="kaart-button">Boek</button></a>
<a href="recensie_toevoegen.php?id=' . $reis["id"] . '"><button class="kaart-button">Plaats recensie</button></a>

            </div>
        </div>';
            $recensieStmt = $conn->prepare("SELECT * FROM recensies WHERE reis_id = ?");
            $recensieStmt->execute([$reis["id"]]);
            $recensies = $recensieStmt->fetchAll();

            echo '<div class="recensies">';
            echo '<h4>Recensies:</h4>';
            if ($recensies) {
                foreach ($recensies as $r) {
                    echo '<div class="recensie">';
                    echo '<strong>' . htmlspecialchars($r["naam"]) . '</strong> op ' . date("d-m-Y H:i", strtotime($r["datum"])) . '<br>';
                    echo nl2br(htmlspecialchars($r["beoordeling"]));
                    echo '</div><hr>';
                }
            } else {
                echo 'Nog geen recensies.';
            }
            echo '</div>';

        }
        ?>
            </div>


        </div>
    </div>
</div>
</div>

<footer>
    <a href="contact.html">Vragen & Contact</a>
    <a href="termscondition.html">Terms & Condition</a>
    <a href="howwework.html">How We Work</a>
    <a href="privacycookies.html">Privacy & Cookies</a>
    <div class="footer-socials">
        <div>Volg ons op</div>
        <div class="icons">
            <img src="/images/twitter%201.png" alt="X">
            <img src="/images/facebook%201.png" alt="Facebook">
            <img src="/images/instagram%20(3)%201.png" alt="Instagram">
        </div>
    </div>
</footer>


</body>
</html>