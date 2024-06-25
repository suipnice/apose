<?php
/**
 * ApoSE comp.php
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

session_start();
if (isset($_SESSION["authen"]) === false or $_SESSION["authen"] !== 'ok') {
    session_destroy();
    echo '<meta http-equiv="Refresh" content="0;url=index.php">';
} else {
    include "../include/fonctions.php";
    include "../include/header.php";
    $link = connexionMysql();
    ?>
    <div class="container">
        <div class="box mt-6">
            <div class="content">
                <h1 class="has-text-centered">
                    Consultation de la structure des enseignements APOGEE
                </h1>

                <div style="border: 1px solid gray" class="container is-max-desktop p-4 mt-6">
                    <p class="has-text-centered">
                        Veuillez sélectionner l’année et une composante :
                    </p>

                    <form name="formul" action="se_apogee.php" method="post">
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label" for="cod_anu">Année</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="select">
                                        <select name="cod_anu" id="cod_anu">
                                            <?php
                                            // Recuperation des annees.
                                            $sql = "SELECT cod_anu FROM annee_uni";
                                            $res = mysqli_query($link, $sql);
                                            $i = 0;
                                            while (
                                                is_array(
                                                    $enr = mysqli_fetch_array($res)
                                                ) === true
                                            ) {
                                                $i++;
                                                echo "<option value='" . $enr[0] . "'
                                                    >" . $enr[0] . "</option>";
                                            }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="field is-horizontal">
                            <div class="field-label is-normal">
                                <label class="label" for="Liste_Comp">
                                    Composante</label>
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="select">
                                        <select name="Liste_Comp" id="Liste_Comp" required="">
                                            <option value="">
                                                Sélectionnez une composante</option>
                                            <?php
                                            // Recuperation des composantes.
                                            $sql = "SELECT DISTINCT lib_cmp, cod_cmp
                                                    FROM composante
                                                    ORDER BY lib_cmp";
                                            $res = mysqli_query($link, $sql);
                                            while (
                                                is_array(
                                                    $enr = mysqli_fetch_array($res)
                                                ) === true
                                            ) {
                                                echo "<option value='" . $enr[1] . "'
                                                     >" . $enr[0] . "</option>";
                                            }

                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>


                        <div class="field is-horizontal">
                            <div class="field-label">
                                <!-- Left empty for spacing -->
                            </div>
                            <div class="field-body">
                                <div class="field">
                                    <div class="control">
                                        <input class="button is-primary"
                                            type="submit" value="Consulter">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="block container is-max-desktop mt-3 mb-3">
                    <strong>Nouveau !</strong>
                    Pour <strong>exporter</strong> la structure des enseignements
                    obtenue, il vous suffit :
                    <ul>
                        <li>de cliquer sur le bouton
                            <span class="icon has-text-info">
                                <i class="fas fa-clipboard" aria-hidden="true"></i>
                            </span>
                            Copier le tableau
                        </li>
                        <li>et de le coller dans un document
                            Excel (Microsoft Office) ou Calc (OpenOffice)
                            <span class="icon has-text-info"><i class="fas fa-file-excel" aria-hidden="true"></i></span>
                        </li>
                    </ul>

                    <p><a href="./documentation/Guide_utilisateur_apose.pdf" target="_blank" class="button is-ghost"><i
                                class="fas fa-book" aria-hidden="true"></i>&nbsp;Accéder
                            à la documentation (PDF)</a>
                    </p>
                </div>
                <div class="block has-text-centered pt-3">
                    <hr>
                    <p>
                        <em>Données mises à jour 3 fois par jour à partir de la base
                            de production d’APOGEE (APOPROD) : 7H30/12H30/16H30.<br>
                            Ne sont consultables que les étapes dont la structure
                            d’enseignement a été modélisée dans APOGEE.</em>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php
    include "../include/footer.php";
}//end if authen == OK
?>