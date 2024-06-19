<?php
/**
 * ApoSE index.php
 * php version 7
 *
 * @category Education
 * @package  Apose
 * @author   2014 - CRI Université Lille 2 <cri@univ-lille.fr>
 * @author   2021-2024 - UniCA DSI SEN <dsi.sen@univ-cotedazur.fr>
 * @author   2022 - Université Toulouse 1 Capitole <dsi@univ-tlse1.fr>
 * @license  GNU GPL
 * @link     https://git.unice.fr/dsi-sen/apose
 */
require_once "../CAS.php";
require "../include/fonctions.php";

// On vérifie si la variable de session du profil est définie,
// sinon on demande une authentification CAS.
if (isset($_SESSION['authen']) === false) {
    $statut = authentificationCAS();
    $authorized = [
        "staff",
        "teacher",
        "faculty",
        "researcher",
        "employee"
    ];
    if (in_array($statut, $authorized)) {
        $_SESSION['authen'] = "ok";
        // Redirection.
        echo '<meta http-equiv="Refresh" content="0;url=comp.php">';
    } else {
        $user = phpCAS::getUser();
        include "../include/header.php";
        if ($statut === "") {
            $statut = "aucune";
        }

        echo '<div class="container">
        <div class="box mt-6">
            <div class="content">
                <h1 class="has-text-centered">
                    Consultation de la structure des enseignements APOGEE
                </h1>';
        echo "<p>Vous êtes authentifié avec le login <strong>$user</strong>
            (affiliation : <strong>$statut</strong>).</p>";
        echo '<div class="notification is-warning">';
        echo '<strong>' . getIconText('key', "Accès refusé") . '</strong>';
        echo "<p>Vous n’êtes pas autorisé à accéder à cette application
            (Vous n’êtes pas affilié en tant que <strong>personnel</strong>
            de l’établissement).</p>";
        echo "<p>Votre dossier RH est peut-être incomplet.
            Contactez votre référent RH de proximité pour en savoir plus.</p>";
        echo "</div></div></div></div>";
        include "../include/footer.php";
    } // end if authorized status
}
