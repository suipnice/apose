<?php
/**
 * ApoSE apogee_recup.php // SCRIPT DE RECUPERATION DES DONNEES APOGEE
 * php version 7
 *
 * @category Education
 * @package  Apose
 * @author   2014 - CRI Université Lille 2 <cri@univ-lille.fr>
 * @author   2021-2024 - UniCA DSI <dsi.sen@univ-cotedazur.fr>
 * @author   2022 - Université Toulouse 1 Capitole <dsi@univ-tlse1.fr>
 * @license  GNU GPL
 * @link     https://github.com/suipnice/apose
 */

// die("recup apogee stoppé.");
error_reporting(E_ALL);
require "../include/fonctions.php";
require "../include/func_apogee.php";

echo "\n******************************************************\n";

$start_time = new DateTime();

if (php_sapi_name() !== 'cli') {
    // Si l'aplication est lancée depuis le web,
    // on s'assure que l'utilisateur est connecté.
    session_start();
    if (isset($_SESSION["authen"]) === false or $_SESSION["authen"] !== 'ok') {
        die('Accès refusé.');
    }
    $annee_uni = filter_input(INPUT_GET, "annee_uni", FILTER_VALIDATE_INT);
    echo '<html><body><pre>';
} else {
    $short_options = "a:";
    $long_options = ["annee:"];
    $options = getopt($short_options, $long_options);

    if (isset($options["a"]) === true || isset($options["annee"]) === true) {
        $annee_uni = $options["a"] ?? $options["annee"];
    }
}

echo "\n";
printlog("Connexion mysql.");
$cnx_mysql = connexionMysql();
printlog("Connexion Oracle.");
$cnx = connexionOracle();

if (isset($annee_uni) === false) {
    // Set clean = true to delete all existing entries before inserts.
    $clean = true;

    // Si l'année n'est pas définie, on demande l'année courante à APOGEE.
    printlog("Fetch annee_uni from APOGEE…");
    requete($cnx_mysql, "DELETE FROM annee_uni");
    recupSimple($cnx_mysql, $cnx, "annee_uni", queryAnneeUniApoOuverte());

    $reqa = "SELECT cod_anu FROM annee_uni";
    $resa = mysqli_query($cnx_mysql, $reqa);

    $tab_annees = [];

    while (is_array($enra = mysqli_fetch_array($resa)) === true) {
        $tab_annees[] = $enra["cod_anu"];
    }
} else {
    // Do not delete existing entries before inserts.
    $clean = false;
    $tab_annees = [$annee_uni];

    $req_insert_sql = "INSERT INTO annee_uni
                       VALUES($annee_uni)
                       ON DUPLICATE KEY UPDATE cod_anu=$annee_uni";
    requete($cnx_mysql, $req_insert_sql);
}

if ($clean === true) {
    $tables = [
        "composante",
        "epreuve",
        "epr_sanctionne_elp",
        "table_elp",
        "vet_regroupe_lse",
        "elp_regroupe_lse",
        "liste_elp",
        "lse_regroupe_elp",
        "type_heure"
    ];

    foreach ($tables as $table) {
        printlog("DELETING ".$table."…");
        requete($cnx_mysql, "DELETE FROM $table");
    }
}

if (isset($annee_uni) === false) {
    // On ne met pas à jour les tables transversales
    // lors d'une synchro d'année antérieure.
    printlog("Update composantes…");
    recupSimple($cnx_mysql, $cnx, "composante", queryComposante());
    printlog("Fetch epreuves…");
    recupSimple($cnx_mysql, $cnx, "epreuve", queryEpreuve());
    printlog("Fetch epr_sanctionne_elp…");
    recupSimple($cnx_mysql, $cnx, "epr_sanctionne_elp", queryEprSanctionneElp());
    printlog("Fetch table_elp…");
    recupSimple($cnx_mysql, $cnx, "table_elp", queryTableElp());

    // Pour la SE on ne peut que tout recuperer
    // (impossible de qualifer cod_etp recursivement).
    printlog("Fetch vet_lse…");
    recupSimple($cnx_mysql, $cnx, "vet_regroupe_lse", queryVetRegrLse());
    printlog("Fetch elp_lse…");
    recupSimple($cnx_mysql, $cnx, "elp_regroupe_lse", queryElpRegroupeLse());

    printlog("Fetch liste_elp…");
    recupSimple($cnx_mysql, $cnx, "liste_elp", queryListes());
    printlog("Fetch lse_elp…");
    recupSimple($cnx_mysql, $cnx, "lse_regroupe_elp", queryLseRegroupeElp());
    printlog("Fetch typ_heu…");
    recupSimple($cnx_mysql, $cnx, "type_heure", queryTableTypHeure());
}


foreach ($tab_annees as $key => $value) {
    printlog("## ANNEE $value.");

    printlog("Fetch etapes…");
    requete($cnx_mysql, "DELETE FROM etape WHERE cod_anu = $value");
    recupSimple($cnx_mysql, $cnx, "etape", queryEtape($value));

    printlog("Fetch table_etape_apo…");
    requete($cnx_mysql, "DELETE FROM table_etape_apo WHERE cod_anu = $value");
    recupSimple($cnx_mysql, $cnx, "table_etape_apo", queryTableEtape($value));

    printlog("Fetch nbetu…");
    requete($cnx_mysql, "DELETE FROM table_etape_nbetu WHERE cod_anu = $value");
    recupSimple($cnx_mysql, $cnx, "table_etape_nbetu", queryTableEtapeNbetu($value));

    printlog("Fetch elp_nbetu…");
    requete($cnx_mysql, "DELETE FROM table_elp_nbetu WHERE cod_anu = $value");
    recupSimple($cnx_mysql, $cnx, "table_elp_nbetu", queryTableElpNbetu($value));

    printlog("Fetch chg_typ_heu…");
    requete($cnx_mysql, "DELETE FROM elp_chg_typ_heu WHERE cod_anu = $value");
    recupSimple($cnx_mysql, $cnx, "elp_chg_typ_heu", queryTableChargeTypEns($value));
}

printlog("--- Script OK ---");

oci_close($cnx);
$cnx_mysql->close();

if (php_sapi_name() !== 'cli') {
    echo '</pre></body></html>';
}

$end_time = new DateTime();
$interval = $end_time->diff($start_time);
echo $interval->format("Total script duration: %H:%I:%S\n\n");
