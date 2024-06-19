<?php
session_start();
if ($_SESSION['authen'] != 'ok') {
    session_destroy();
    echo '<meta http-equiv="Refresh" content="0;url=index.php">';
} else {
    include "include/fonctions.php";
    include "header.php";
    $link = connexion_mysql();
    ?>
    <div class="container">
        <div class="box mt-6">
            <div class="content">
                <h1 class="has-text-centered">
                    Consultation de la structure des enseignements APOGEE
                </h1>

                <div style="border: 1px solid gray"
                     class="container is-max-desktop p-4 mt-6">
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
                                            //recuperation des annees
                                            $sql = "select cod_anu from annee_uni";
                                            $res = mysqli_query($link, $sql);
                                            $i = 0;
                                            while ($enr = mysqli_fetch_array($res)) {
                                                $i++;
                                                echo "<option value=" . $enr[0] . " >" . $enr[0] . "</option>\n";
                                            }
                                            ;
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
                                        <select name="Liste_Comp"
                                                id="Liste_Comp" required="">
                                            <option value="">
                                                Sélectionnez une composante</option>
                                            <?php
                                            //recuperation des composantes
                                            $sql = "select distinct lib_cmp, cod_cmp from composante order by lib_cmp";
                                            $res = mysqli_query($link, $sql);
                                            while ($enr = mysqli_fetch_array($res)) {
                                                echo "<option value=" . $enr[1] . " >" . $enr[0] . "</option>\n";
                                            }
                                            ;
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
                                        <input class="bouton_submit" type="submit"
                                               value="Consulter">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>

                </div>
                <div class="block container is-max-desktop mt-3 mb-3">
                    Pour <strong>exporter</strong> la structure des enseignements
                    obtenue, il vous suffit :
                    <ul>
                        <li>de sélectionner l'ensemble du document
                            <span class="icon has-text-info"><i
                                  class="fas fa-mouse-pointer"></i></span></li>
                        <li>de le copier
                            <span class="icon has-text-info"><i
                                  class="fas fa-copy"></i></span></li>
                        <li>et de le coller dans un nouveau document
                            Excel (Microsoft Office) ou Calc (OpenOffice)
                            <span class="icon has-text-info"><i
                                  class="fas fa-file-excel"></i></span></li>
                    </ul>

                    <p><a href="./documentation/Guide_utilisateur_apose.pdf"
                          target="_blank"><i
                        class="fas fa-book"></i>&nbsp;Accéder
                        à la documentation (PDF)</a>
                    </p>
                </div>
                <div class="block has-text-centered pt-3">
                    <hr />
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
    include "footer.php";
}
?>