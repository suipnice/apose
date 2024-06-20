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

if (php_sapi_name() !== 'cli') {
    // Si l'aplication est lancée depuis le web,
    // on s'assure que l'utilisateur est connecté.
    session_start();
    if (isset($_SESSION["authen"]) === false or $_SESSION["authen"] !== 'ok') {
        die('Accès refusé.');
    }
    echo '<html><body><pre>';
}

echo "\n";
echo date("Y-m-d H:i:s") . "\nConnexion mysql.\n";
$cnx_mysql = connexionMysql();
echo "Connexion Oracle.\n";
$cnx = connexionOracle();

echo "Fetch annee_uni…\n";
requete($cnx_mysql, "DELETE FROM annee_uni");
recupSimple($cnx_mysql, $cnx, "annee_uni", queryAnneeUniApoOuverte());

$reqa = "select cod_anu from annee_uni";
$resa = mysqli_query($cnx_mysql, $reqa);

$tab_annees = [];

while (is_array($enra = mysqli_fetch_array($resa)) === true) {
    $tab_annees[] = $enra["cod_anu"];
}

echo "Fetch etapes…\n";
requete($cnx_mysql, "DELETE FROM etape");
foreach ($tab_annees as $key => $value) {
    echo "  $value.\n";
    recupSimple($cnx_mysql, $cnx, "etape", queryEtape($value));
}

echo "Fetch composantes…\n";
requete($cnx_mysql, "DELETE FROM composante");
recupSimple($cnx_mysql, $cnx, "composante", queryComposante());

requete($cnx_mysql, "DELETE FROM table_etape_apo");
foreach ($tab_annees as $key => $value) {
    echo "  $value.\n";
    recupSimple($cnx_mysql, $cnx, "table_etape_apo", queryTableEtape($value));
}

echo "Fetch epreuves…\n";
requete($cnx_mysql, "DELETE FROM epreuve");
recupSimple($cnx_mysql, $cnx, "epreuve", queryEpreuve());

echo "Fetch epr_sanctionne_elp…\n";
requete($cnx_mysql, "DELETE FROM epr_sanctionne_elp");
recupSimple($cnx_mysql, $cnx, "epr_sanctionne_elp", queryEprSanctionneElp());

echo "Fetch nbetu…\n";
requete($cnx_mysql, "DELETE FROM table_etape_nbetu");
foreach ($tab_annees as $key => $value) {
    echo "  $value.\n";
    recupSimple($cnx_mysql, $cnx, "table_etape_nbetu", queryTableEtapeNbetu($value));
}

echo "Fetch table_elp…\n";
requete($cnx_mysql, "DELETE FROM table_elp");
recupSimple($cnx_mysql, $cnx, "table_elp", queryTableElp());

echo "Fetch elp_nbetu…\n";
requete($cnx_mysql, "DELETE FROM table_elp_nbetu");
foreach ($tab_annees as $key => $value) {
    echo "  $value.\n";
    recupSimple($cnx_mysql, $cnx, "table_elp_nbetu", queryTableElpNbetu($value));
}

// Pour la SE on ne peut que tout recuperer
// (impossible de qualifer cod_etp recursivement).
echo "Fetch vet_lse…\n";
requete($cnx_mysql, "DELETE FROM vet_regroupe_lse");
recupSimple($cnx_mysql, $cnx, "vet_regroupe_lse", queryVetRegrLse());

echo "Fetch elp_lse…\n";
requete($cnx_mysql, "DELETE FROM elp_regroupe_lse");
recupSimple($cnx_mysql, $cnx, "elp_regroupe_lse", queryElpRegroupeLse());

echo "Fetch liste_elp…\n";
requete($cnx_mysql, "DELETE FROM liste_elp");
recupSimple($cnx_mysql, $cnx, "liste_elp", queryListes());

echo "Fetch lse_elp…\n";
requete($cnx_mysql, "DELETE FROM lse_regroupe_elp");
recupSimple($cnx_mysql, $cnx, "lse_regroupe_elp", queryLseRegroupeElp());

echo "Fetch chg_typ_heu…\n";
requete($cnx_mysql, "DELETE FROM elp_chg_typ_heu");
foreach ($tab_annees as $key => $value) {
    echo "  $value.\n";
    recupSimple($cnx_mysql, $cnx, "elp_chg_typ_heu", queryTableChargeTypEns($value));
}

echo "Fetch typ_heu…\n";
requete($cnx_mysql, "DELETE FROM type_heure");
recupSimple($cnx_mysql, $cnx, "type_heure", queryTableTypHeure());

echo "\n --- Script OK ---";

oci_close($cnx);
mysqli_close($cnx_mysql);

if (php_sapi_name() !== 'cli') {
    echo '</pre></body></html>';
}
