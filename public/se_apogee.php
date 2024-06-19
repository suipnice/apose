<?php
/**
 * ApoSE se_apogee.php
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
session_start();
$authen = $_SESSION["authen"];
if (!$_SESSION["authen"] !== 'ok') {
    session_destroy();
    echo '<meta http-equiv="Refresh" content="0;url=index.php">';
} else {
    include "../include/fonctions.php";
    include "../include/header.php";
    $cnx_mysql = connexionMysql();

    $pdf = "";
    $res = "";
    $res0 = "";//Affichage du nb etape
    $res2 = "";
    $res3 = "";//Liste des Etapes non Modélisées dans Apogée
    $adresse = "";
    $cpt = 0;

    // Recupération de la composante et de l'annee
    $comp = filter_input(INPUT_POST, "Liste_Comp");
    $cod_anu = filter_input(INPUT_POST, "cod_anu");

    // Memorisation et prise en compte du choix des boutons radios lors du retour
    $def_zero=array('options'=>array('default'=>'0'));
    $def_one=array('options'=>array('default'=>'1'));

    $radio_numero = filter_input(INPUT_POST, 'numero', FILTER_DEFAULT, $def_zero);
    $radio_ladd = filter_input(INPUT_POST, 'ladd', FILTER_DEFAULT, $def_zero);
    $radio_charge = filter_input(INPUT_POST, 'charge', FILTER_DEFAULT, $def_zero);

    $radio_epr = filter_var($_SESSION['epr'], FILTER_DEFAULT, $def_zero);
    $radio_ses = filter_var($_SESSION['cod_ses'], FILTER_DEFAULT, $def_one);
    $res2 .= "<pre class='debug'>authen=$authen</pre>";
    $res2 .= "<pre class='debug'>radio_EPR=$radio_epr</pre>";
    $res2 .= "<h3>Liste des années d’études disponibles sur APOGEE :</h3>";

    $res3 .= "<hr><h3>Liste des années d’études non modélisées sur APOGEE :</h3>";

    $reqcycle = "SELECT DISTINCT(etape.cod_cyc) FROM etape
                WHERE etape.cod_cmp='" . $comp . "'
                AND etape.cod_anu='" . $cod_anu . "'
                ORDER BY cod_cyc";

    $res2 .= "<fieldset><legend>Choisissez une année à afficher</legend>";
    $cycle_index = 0;

    $table_headers = '<thead><tr class="bg-primary">';
    $table_headers .= '<th scope="col" class="no-sort">Choix</th>';
    $table_headers .= '<th scope="col">Titre</th>';
    $table_headers .= '<th scope="col">Recr.</th>';
    $table_headers .= '<th scope="col">Nombre d’étudiants inscrits</th>';
    $table_headers .= '</tr></thead>';

    $table_headers2 = '<thead><tr class="bg-secondary">';
    $table_headers2 .= '<th scope="col">Titre</th>';
    $table_headers2 .= '<th scope="col">Recr.</th>';
    $table_headers2 .= '<th scope="col">Nombre d’étudiants inscrits</th>';
    $table_headers2 .= '</tr></thead><tbody>';

    $rescycle = requete($cnx_mysql, $reqcycle);
    while ($enrcycle = mysqli_fetch_array($rescycle)) {

        $reqetape = "SELECT etape.cod_etp,etape.cod_vrs_vet,
                    etape.lic_etp,etape.lib_etp,
                    etape.cod_cyc, etape.cod_cmp, etape.cod_anu,cod_lse,
                    DAA_DEB_RCT_VET,DAA_FIN_RCT_VET
                FROM etape
                LEFT JOIN vet_regroupe_lse
                ON (etape.cod_vrs_vet = vet_regroupe_lse.cod_vrs_vet)
                AND (etape.cod_etp = vet_regroupe_lse.cod_etp)
                GROUP BY etape.cod_etp, etape.cod_vrs_vet, etape.lic_etp,
                         etape.lib_etp, etape.cod_cyc, etape.cod_cmp, etape.cod_anu,
                         etape.DAA_DEB_RCT_VET, etape.DAA_FIN_RCT_VET
                HAVING etape.cod_cmp='" . $comp . "'
                AND etape.cod_anu='" . $cod_anu . "'
                and etape.cod_cyc='" . $enrcycle[0] . "'
                ORDER BY etape.lib_etp";

        $req = requete($cnx_mysql, $reqetape);

        if ($cycle_index > 0) {
            $res2 .= "</tbody></table>";
            $res3 .= "</tbody></table>";
        }
        $cycle_index++;

        $table_css = 'class="table is-striped is-fullwidth
            caption-top sortable"';
        $res2 .= "<table $table_css>";
        $res3 .= "<table $table_css>";
        $res2 .= "<caption id='cyc" . $enrcycle[0] . "'>
            Cycle " . $enrcycle[0] . "</caption>";
        $res3 .= "<caption>Cycle " . $enrcycle[0] . "</caption>";
        $res2 .= $table_headers;
        $res3 .= $table_headers2;

        while ($r = mysqli_fetch_assoc($req)) {
            $cpt = $cpt + 1;
            $cod_etp = $r['cod_etp'];
            $lib_etp = $r['lib_etp'];
            $cod_vrs_vet = $r['cod_vrs_vet'];
            $deb_rec = $r['DAA_DEB_RCT_VET'];
            $fin_rec = $r['DAA_FIN_RCT_VET'];
            if ($r['cod_lse'] !== '') {
                $res2 .= "<tr><td>
                    <input type=\"radio\" name=\"RefEtp\"
                        id='$cod_etp$cod_vrs_vet'
                        value=\"$cod_etp|$cod_vrs_vet|$comp|$cod_anu|" . $enrcycle[0] . "\"
                        OnClick=\"submit();\">";
                $res2 .= "<input type='hidden' name='cod_anu' value='$cod_anu'>";
                $res2 .= "<input type='hidden' name='cycle'
                                 value=" . $enrcycle[0] . ">";
                $res2 .= "</td>";
                $res2 .= "<td><label for='$cod_etp$cod_vrs_vet'>
                    $lib_etp ($cod_etp / $cod_vrs_vet)</label></td>";
                $res2 .= "<td>$deb_rec/$fin_rec</td>";
            } else {
                $res3 .= "<tr>";
                $res3 .= "<td> $lib_etp ($cod_etp / $cod_vrs_vet)</td>";
                $res3 .= "<td>$deb_rec/$fin_rec</td>";
            }
            //Recup nb etu
            $sqletu = "SELECT nb_etu FROM table_etape_apo
                WHERE table_etape_apo.cod_etp='$cod_etp'
                AND table_etape_apo.cod_vrs_vet='$cod_vrs_vet'
                AND cod_anu='" . $cod_anu . "'
                AND cod_cmp='" . $comp . "'";
            $resetu = mysqli_query($cnx_mysql, $sqletu);
            if (mysqli_num_rows($resetu) === 0) {
                if ($r['cod_lse'] !== '') {
                    $res2 .= "<td> -- </td></tr>";
                } else {
                    $res3 .= "<td> -- </td></tr>";
                }
            } else {
                while ($enretu = mysqli_fetch_array($resetu)) {
                    if ($r['cod_lse'] !== '') {
                        $res2 .= "<td>$enretu[0]</td></tr>";

                    } else {
                        $res3 .= "<td>$enretu[0]</td></tr>";
                    }
                }
            }

        } //fin while liste etape
    } //Fin While recup Cycle
    $res2 .= "</table></fieldset></form>";
    $res3 .= "</table>";
    $cpt = 0;

    ?>

    <div class="container">
        <div class="content p-2">
            <?php
            //Affichage du libellé de la composante
            $reqa = requete(
                $cnx_mysql,
                "select lib_cmp from composante where cod_cmp='$comp'"
            );
            while ($row = mysqli_fetch_row($reqa)) {
                $lib_comp = $row[0];
            }//fin while lib composante
            ?>
            <a href="comp.php"><span class='bouton-submit'>Retour</span></a>

            <h1 class="has-text-centered mt-2">
                Consultation de la structure des enseignements
            </h1>
            <h2 class="has-text-centered">
                <?php echo $lib_comp; ?> | <?php echo $cod_anu; ?>
            </h2>

            <form class="option" method="post" action="se_apogee-2.php">
                <input type="hidden" name="type" value="tableau">
                <fieldset class="option-recherche">
                    <legend>Options d’affichage :</legend>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">
                                INDICATEUR NUMÉRIQUE DE L’ARBORESCENCE :
                            </span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="numero" value="1" <?php
                                    if ($radio_numero === '1') {
                                        echo "checked";
                                    } ?> > Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" name="numero" value="0" <?php
                                    if ($radio_numero === '0') {
                                        echo "checked";
                                    } ?> > Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">
                                LIBELLÉS DE L’ANNEXE DESCRIPTIVE DU DIPLÔME :
                            </span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="ladd" value="1" <?php
                                    if ($radio_ladd === '1') {
                                        echo "checked";
                                    } ?> > Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" name="ladd" value="0" <?php
                                    if ($radio_ladd === '0') {
                                        echo "checked";
                                    }
                                    ?> > Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">CHARGES D’ENSEIGNEMENTS :</span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="charge" value="1" <?php
                                    if ($radio_charge === '1') {
                                        echo "checked";
                                    } ?> > Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" name="charge" value="0" <?php
                                    if ($radio_charge === '0') {
                                        echo "checked";
                                    } ?> > Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">INFORMATIONS DES ÉPREUVES :</span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" class="session"
                                           name="epr" id="epr-1" value="1"
                                        <?php
                                        if ($radio_epr === '1') {
                                            echo "checked";
                                        } ?> > Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" class="session"
                                           name="epr" id="epr-0" value="0"
                                    <?php
                                    if ($radio_epr === '0') {
                                        echo "checked";
                                    } ?> > Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="field-session" class="field is-horizontal <?php
                    if ($_SESSION['epr'] !== 1) {
                        echo "is-invisible";
                    } ?> ">
                        <div class="field-label">
                            <span class="label p-2">SESSIONS :</span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio p-2 sess-1">
                                    <input type="radio" name="cod_ses" value="1"
                                        <?php
                                        if ($radio_ses === '1') {
                                            echo "checked";
                                        } ?> > 1
                                </label>
                                <label class="radio p-2 sess-2">
                                    <input type="radio" name="cod_ses" value="2"
                                        <?php
                                        if ($radio_ses === '2') {
                                            echo "checked";
                                        } ?> > 2
                                </label>
                                <label class="radio p-2 sess-0">
                                    <input type="radio" name="cod_ses" value="0"
                                        <?php
                                        if ($radio_ses === '0') {
                                            echo "checked";
                                        } ?> > Unique
                                </label>
                                <label class="radio p-2">
                                    <input type="radio" name="cod_ses" value="4"
                                        <?php
                                        if ($radio_ses === '4') {
                                            echo "checked";
                                        } ?> > Toutes les sessions
                                </label>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <?php
                echo $res0;
                echo $res2;
                echo $res;
                echo $res3;
                ?>
                <a href="comp.php"><span class='bouton-submit'>Retour</span></a>
        </div>
    </div>

    <?php
    include "../include/footer.php";
}
?>
