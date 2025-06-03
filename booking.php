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
            <a href="index.html"><img src="images/logo.png" alt="JMLogo"></a>
        </div>
        <div class="navigatie">
            <a href="booking.html">Booking</a>
            <a href="contact.html">Vragen & Contact</a>
            <a href="overons.html">Over ons</a>
            <a href="logout.php">Uitloggen</a>
        </div>

        <div class="login-parent">
            <div><img class="search-image" src="images/search.png" alt="search"></div>
            <a href="login.php">
                <div><img class="login-image" src="images/account.png" alt="login"></div>
            </a>
        </div>
    </nav>

    <form class="zoekbalk">
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

    <div class="kaart">
        <img class="kaart-afbeelding" src="images/vakanties/griekeland.png" alt="Griekenland">
        <div class="kaart-inhoud">
            <div class="kaart-naam">Griekenland</div>
            <div class="kaart-locatie">Kreta, Piskopiano</div>
            <div class="kaart-prijs">Vanaf €518 p.p.</div>
            <div action="boeken.php" method="POST">
                <?php

                $conn = new PDO("mysql:host=mysql_db2;dbname=login_systeem", "root", "rootpassword");
                $reizen = $conn->query("SELECT * FROM reizen")->fetchAll();
                foreach ($reizen as $reis) {
                    echo '
    <div class="kaart">
        <img class="kaart-afbeelding" src="images/vakanties/' . htmlspecialchars($reis["foto"]) . '" alt="' . htmlspecialchars($reis["bestemming"]) . '">
        <div class="kaart-inhoud">
            <div class="kaart-naam">' . htmlspecialchars($reis["bestemming"]) . '</div>
            <div class="kaart-locatie">' . htmlspecialchars($reis["verblijf"]) . '</div>
            <div class="kaart-prijs">€' . htmlspecialchars($reis["prijs"]) . '</div>
        <a href="boeken.php?id=' . $reis["id"] . '"><button class="kaart-button">Boek</button></a>
        </div>
    </div>';
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