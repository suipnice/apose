<?php
require_once ("CAS.php");
require ("include/fonctions.php");

// on vérifie si la variable de session du profil est définie, sinon on demande une authentification CAS
if (!isset($_SESSION['authen'])) {
    $statut = authentification_CAS();

    if (in_array($statut, array("staff", "teacher", "faculty", "researcher"))) {
        $_SESSION['authen'] = "ok";
        //redirection
        echo '<meta http-equiv="Refresh" content="0;url=comp.php">';
    } else {
        echo "<p>Vous n'êtes pas autorisé à accéder à l'application.</p>";
    }
}
