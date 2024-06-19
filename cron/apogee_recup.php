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
 * @link     https://git.unice.fr/dsi-sen/apose
 */

 //die("recup apogee stoppé.");
// SCRIPT DE RECUPERATION DES DONNEES APOGEE
error_reporting(E_ALL);
require "../include/fonctions.php";
require "../include/func_apogee.php";

echo "\n******************************************************";
echo "";

if (php_sapi_name() !== 'cli') {
    // Si l'aplication est lancée depuis le web,
    // on s'assure que l'utilisateur est connecté.
    session_start();
    if ($_SESSION['authen'] !== 'ok') {
        die('Accès refusé.');
    }
    echo '<html><body><pre>';
}

echo "\n<br>";
echo date("Y-m-d H:i:s") . "<br>Connexion a mysql ...";
$cnx_mysql = connexionMysql();
echo "<br>Connexion a oracle...";
$cnx = connexionOracle();

requete($cnx_mysql, "DELETE FROM annee_uni");
recupSimple($cnx_mysql, $cnx, "annee_uni", queryAnneeUniApoOuverte());

$reqa = "select cod_anu from annee_uni";
$resa = mysqli_query($cnx_mysql, $reqa);

$tab_annees = array();

while ($enra = mysqli_fetch_array($resa)) {
    $tab_annees[] = $enra["cod_anu"];
}

requete($cnx_mysql, "DELETE FROM etape");
foreach ($tab_annees as $key => $value) {
    recupSimple($cnx_mysql, $cnx, "etape", queryEtape($value));
}

requete($cnx_mysql, "DELETE FROM composante");
recupSimple($cnx_mysql, $cnx, "composante", queryComposante());

requete($cnx_mysql, "DELETE FROM table_etape_apo");
foreach ($tab_annees as $key => $value) {
    recupSimple($cnx_mysql, $cnx, "table_etape_apo", queryTableEtape($value));
}

requete($cnx_mysql, "DELETE FROM epreuve");
recupSimple($cnx_mysql, $cnx, "epreuve", queryEpreuve());

requete($cnx_mysql, "DELETE FROM epr_sanctionne_elp");
recupSimple($cnx_mysql, $cnx, "epr_sanctionne_elp", queryEprSanctionneElp());

requete($cnx_mysql, "DELETE FROM table_etape_nbetu");
foreach ($tab_annees as $key => $value) {
    //echo "<br>table table_etape_nbetu ".$value."<br>";
    recupSimple($cnx_mysql, $cnx, "table_etape_nbetu", queryTableEtapeNbetu($value));
}

requete($cnx_mysql, "DELETE FROM table_elp");
recupSimple($cnx_mysql, $cnx, "table_elp", queryTableElp());

requete($cnx_mysql, "DELETE FROM table_elp_nbetu");
foreach ($tab_annees as $key => $value) {
    recupSimple($cnx_mysql, $cnx, "table_elp_nbetu", queryTableElpNbetu($value));
}

// Pour la SE on ne peut que tout recuperer
// (impossible de qualifer cod_etp recursivement)
requete($cnx_mysql, "DELETE FROM vet_regroupe_lse");
recupSimple($cnx_mysql, $cnx, "vet_regroupe_lse", queryVetRegrLse());

requete($cnx_mysql, "DELETE FROM elp_regroupe_lse");
recupSimple($cnx_mysql, $cnx, "elp_regroupe_lse", queryElpRegroupeLse());

requete($cnx_mysql, "DELETE FROM liste_elp");
recupSimple($cnx_mysql, $cnx, "liste_elp", queryListes());

requete($cnx_mysql, "DELETE FROM lse_regroupe_elp");
recupSimple($cnx_mysql, $cnx, "lse_regroupe_elp", queryLseRegroupeElp());

//modif oct. 2014 suite patch apogee 4.50
requete($cnx_mysql, "DELETE FROM elp_chg_typ_heu");
foreach ($tab_annees as $key => $value) {
    recupSimple($cnx_mysql, $cnx, "elp_chg_typ_heu", queryTableChargeTypEns($value));
}

//modif oct. 2014 suite patch apogee 4.50
requete($cnx_mysql, "DELETE FROM type_heure");
recupSimple($cnx_mysql, $cnx, "type_heure", queryTableTypHeure());

echo "<br> script ok";

oci_close($cnx);
mysqli_close($cnx_mysql);

if (php_sapi_name() !== 'cli') {
    echo '</pre></body></html>';
}
