<?php
session_start();
if ($_SESSION['authen'] != 'ok') {
    session_destroy();
    echo '<meta http-equiv="Refresh" content="0;url=index.php">';
} else {
    include "include/fonctions.php";
    include "header.php";
    $cnx_mysql = connexion_mysql();

    $pdf = "";
    $res = "";
    $res0 = "";//Affichage du nb etape
    $res2 = "";
    $res3 = "";//Liste des Etapes non Modélisées dans Apogée
    $adresse = "";
    $cpt = 0;

    // Recupération de la composante et de l'annee
    $comp = $_POST["Liste_Comp"];
    $cod_anu = $_POST['cod_anu'];

    // Memorisation et prise en compte du choix des boutons radios lors du retour
    if (isset($_POST['numero'])) {
        $radio_numero = $_POST['numero'];
    } else {
        $radio_numero = '0';
    }
    if (isset($_POST['ladd'])) {
        $radio_ladd = $_POST['ladd'];
    } else {
        $radio_ladd = '0';
    }
    if (isset($_POST['charge'])) {
        $radio_charge = $_POST['charge'];
    } else {
        $radio_charge = '0';
    }
    if (isset($_SESSION['epr'])) {
        $radio_epr = $_SESSION['epr'];
    } else {
        $radio_epr = '0';
    }
    if (isset($_SESSION['cod_ses'])) {
        $radio_ses = $_SESSION['cod_ses'];
    } else {
        $radio_ses = '1';
    }


    $res2 .= "<h3>Liste des années d'études disponibles sur APOGEE :</h3><table>";

    $res3 .= "<hr><h3>Liste des années d'études non modélisées sur APOGEE :</h3>
            <table border=1 cellspacing=0 cellpadding=0>";

    $reqcycle = "SELECT distinct(etape.cod_cyc) from etape
                    where etape.cod_cmp='" . $_POST['Liste_Comp'] . "'
                        AND etape.cod_anu='" . $_POST['cod_anu'] . "'
                    order by cod_cyc";
    debug($reqcycle);

    $rescycle = requete($cnx_mysql, $reqcycle);

    while ($enrcycle = mysqli_fetch_array($rescycle)) {

        $reqetape = "SELECT etape.cod_etp,etape.cod_vrs_vet,etape.lic_etp,etape.lib_etp, etape.cod_cyc, etape.cod_cmp, etape.cod_anu,cod_lse,DAA_DEB_RCT_VET,DAA_FIN_RCT_VET
                FROM etape
                LEFT JOIN vet_regroupe_lse
                ON (etape.cod_vrs_vet = vet_regroupe_lse.cod_vrs_vet)
                AND (etape.cod_etp = vet_regroupe_lse.cod_etp)
                GROUP BY etape.cod_etp, etape.cod_vrs_vet, etape.lic_etp, etape.lib_etp, etape.cod_cyc, etape.cod_cmp, etape.cod_anu, etape.DAA_DEB_RCT_VET, etape.DAA_FIN_RCT_VET
                HAVING etape.cod_cmp='" . $_POST['Liste_Comp'] . "'
                AND etape.cod_anu='" . $_POST['cod_anu'] . "'
                and etape.cod_cyc='" . $enrcycle[0] . "'
                ORDER BY etape.lib_etp";
        debug($reqetape);
        $req = requete($cnx_mysql, $reqetape);

        $res2 .= "<tr><td colspan='2' class='bg-primary'><a name=cyc" . $enrcycle[0] . "></a><span style='font-size:1.2em;color:#FFFFFF;'>Cycle " . $enrcycle[0] . "</span></td></tr>";
        $res3 .= "<tr><td colspan='2' bgcolor='#b9dcfa'><font size=+0.5>Cycle " . $enrcycle[0] . "</font></td></tr>";

        while ($r = mysqli_fetch_assoc($req)) {
            $cpt = $cpt + 1;
            $cod_etp = $r['cod_etp'];
            $lib_etp = $r['lib_etp'];
            $cod_vrs_vet = $r['cod_vrs_vet'];
            $deb_rec = $r['DAA_DEB_RCT_VET'];
            $fin_rec = $r['DAA_FIN_RCT_VET'];
            if ($r['cod_lse'] != '') {
                $res2 .= "<tr><td><a name=$cod_etp$cod_vrs_vet></a><input type=\"radio\" name=\"RefEtp\" value=\"$cod_etp|$cod_vrs_vet|$comp|$cod_anu|" . $enrcycle[0] . "\" OnClick=\"submit();\">";
                $res2 .= "<input type=hidden name=cod_anu value=$cod_anu>";
                $res2 .= "<input type=hidden name=cycle value=" . $enrcycle[0] . ">";
                $res2 .= "</td>";
                $res2 .= "<td> $lib_etp ($cod_etp / $cod_vrs_vet) <b>-- Recr. $deb_rec/$fin_rec</b>";
                //-- Nombre d'étudiants inscrits : $nb_etu</td></tr>";
            } else {
                $res3 .= "<tr>";
                $res3 .= "<td> $lib_etp ($cod_etp / $cod_vrs_vet)<b> -- Recr. $deb_rec/$fin_rec</b>";
                // -- Nombre d'étudiants inscrits : $nb_etu</td></tr>";
            }
            //Recup nb etu
            $sqletu = "select nb_etu from table_etape_apo
                where table_etape_apo.cod_etp='$cod_etp'
                and table_etape_apo.cod_vrs_vet='$cod_vrs_vet'
                and cod_anu='" . $_POST['cod_anu'] . "'
                and cod_cmp='" . $_POST['Liste_Comp'] . "'";
            $resetu = mysqli_query($cnx_mysql, $sqletu);
            if (mysqli_num_rows($resetu) == 0) {
                echo "</td></tr>";
            } else {
                while ($enretu = mysqli_fetch_array($resetu)) {
                    if ($r['cod_lse'] != '') {
                        $res2 .= " -- Nombre d’étudiants inscrits : <b>$enretu[0]</b></td></tr>";
                    } else {
                        $res3 .= " -- Nombre d’étudiants inscrits : <b>$enretu[0]</b></td></tr>";
                    }
                }
            }

        } //fin while liste etape
    } //Fin While recup Cycle

    $res0 .= "<hr><strong class='mt-2'>" . $cpt . " Étapes </strong>";
    $res2 .= "</table></span></form>";
    $res3 .= "</table><br>";
    $cpt = 0;

    ?>

    <div class="container">
        <div class="content p-2">
            <?php
            //Affichage du libellé de la composante
            $reqa = requete($cnx_mysql, "select lib_cmp from composante where cod_cmp='$comp'");
            while ($row = mysqli_fetch_row($reqa)) {
                $lib_comp = $row[0];
            }//fin while lib composante
            ?>
            <a href="comp.php"><span class='bouton_submit'>Retour</span></a>

            <h1 class="has-text-centered mt-2">
                Consultation de la structure des enseignements
            </h1>
            <h2 class="has-text-centered">
                <?php echo $lib_comp; ?> | <?php echo $cod_anu; ?>
            </h2>

            <form class="option" method="post" action="se_apogee-2.php">
                <input type="hidden" name="type" value="tableau">
                <div class="option-recherche" style="padding:0.8em; background-color:#f2f2f2;">
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">INDICATEUR NUMERIQUE DE L'ARBORESCENCE : </span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="numero" value="1" <?php
                                    if ($radio_numero == '1') {
                                        echo "checked";
                                    } ?> /> Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" name="numero" value="0" <?php
                                    if ($radio_numero == '0') {
                                        echo "checked";
                                    } ?> /> Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">
                                LIBELLES DE L’ANNEXE DESCRIPTIVE DU DIPLOME :
                            </span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" name="ladd" value="1" <?php
                                    if ($radio_ladd == '1') {
                                        echo "checked";
                                    } ?> /> Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" name="ladd" value="0" <?php
                                    if ($radio_ladd == '0') {
                                        echo "checked";
                                    }
                                    ?> /> Non
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
                                    if ($radio_charge == '1') {
                                        echo "checked";
                                    } ?> /> Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" name="charge" value="0" <?php
                                    if ($radio_charge == '0') {
                                        echo "checked";
                                    } ?> /> Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="field is-horizontal">
                        <div class="field-label">
                            <span class="label">INFORMATIONS DES EPREUVES :</span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label class="radio">
                                    <input type="radio" class="session" name="epr" value="1" <?php
                                    if ($radio_epr == '1') {
                                        echo "checked";
                                    } ?> /> Oui
                                </label>
                                <label class="radio">
                                    <input type="radio" class="session" name="epr" value="0" <?php
                                    if ($radio_epr == '0') {
                                        echo "checked";
                                    } ?> /> Non
                                </label>
                            </div>
                        </div>
                    </div>
                    <div id="field-session" class="field is-horizontal <?php
                    if ($_SESSION['epr'] != 1) {
                        echo "is-invisible";
                    } ?> ">
                        <div class="field-label">
                            <span style="background-color:#DDDDDD"
                                   class="label p-2">SESSIONS :</span>
                        </div>
                        <div class="field-body">
                            <div class="control">
                                <label style="background-color:#D7E8FE"
                                       class="radio p-2">
                                    <input type="radio" name="cod_ses"
                                           value="1" <?php
                                    if ($radio_ses == '1') {
                                        echo "checked";
                                    } ?> /> 1
                                </label>
                                <label style="background-color:#B7F9B9"
                                       class="radio p-2">
                                    <input type="radio" name="cod_ses"
                                           value="2" <?php
                                    if ($radio_ses == '2') {
                                        echo "checked";
                                    } ?> /> 2
                                </label>
                                <label style="background-color:#F9B7E5"
                                       class="radio p-2">
                                    <input type="radio" name="cod_ses"
                                           value="0" <?php
                                    if ($radio_ses == '0') {
                                        echo "checked";
                                    } ?> /> Unique
                                </label>
                                <label class="radio p-2">
                                    <input type="radio" name="cod_ses"
                                           value="4" <?php
                                    if ($radio_ses == '4') {
                                        echo "checked";
                                    } ?> /> Toutes les sessions
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <?php
                echo $res0;
                echo $res2;
                echo $res;
                echo $res3;
                ?>
                <a href="comp.php"><span class='bouton_submit'>Retour</span></a>
        </div>
    </div>

    <?php
    include "footer.php";
}
?>
