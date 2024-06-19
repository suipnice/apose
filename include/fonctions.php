<?php

include "param.php";


// ---------------------------------------------------
// Fonction d'authentification auprès d'un serveur CAS
// entrée :
// sortie : $_SESSION['login']=	$usernameCAS;
// ---------------------------------------------------
function authentification_CAS()
{
    // import de la librairie CAS
    require_once ("CAS.php");
    // import des paramètres du serveur CAS
    global $connexionCAS;
    global $logoutCas;

    // initialisation phpCAS
    if ($connexionCAS != "active") {
        $config_CAS_host = CAS_HOST;
        $config_CAS_portNumber = CAS_PORT;
        $config_CAS_URI = CAS_URI;
        $CASCnx = phpCAS::client(CAS_VERSION_2_0, $config_CAS_host, $config_CAS_portNumber, $config_CAS_URI);
        $connexionCAS = "active";
    }

    if ($logoutCas == 1) {
        phpCAS::logout();
    }

    // authentificationCAS (redirection vers la page d'authentification de CAS)
    phpCAS::setNoCasServerValidation();
    phpCAS::forceAuthentication();

    //L'utilisateur a été correctement identifié
    $usernameCAS = phpCAS::getUser();


    //on lance l'identification LDAP avec le UID de la personne
    $statut = identification_LDAP($usernameCAS);

    return $statut;

}

// ---------------------------------------------------
// Fonction d'identification auprés d'un serveur LDAP
// parametre entrée : $login : uid
// parametre sortie : $edupersonprimaryaffiliation : statut de la personne qui se connecte
// ---------------------------------------------------
function identification_LDAP($login)
{

    $baseDN = LDAP_BASE_DN;
    $ldapServer = LDAP_SERVEUR;
    $ldapServerPort = LDAP_PORT;

    /* mot clef de la recherche*/
    $keyword = $login;

    // Connexion au serveur
    $conn = ldap_connect($ldapServer);

    /* 2ème étape : on effectue une liaison au serveur, ici de type "anonyme"
     * pour une recherche permise par un accès en lecture seule
     */
    // On dit qu'on utilise LDAP V3, sinon la V2 par défaut est utilisé
    // et le bind ne passe pas.
    ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);


    // Instruction de liaison.
    // Connexion anonyme
    $bindServerLDAP = ldap_bind($conn, "cn=manager,dc=unice,dc=fr", "c@Rm@br");

    /* 3ème étape : on effectue une recherche anonyme, avec le dn de base */
    $query = "(&(uid=" . $keyword . "))";
    $result = ldap_search($conn, $baseDN, $query);

    // Lecture du resultat
    $info = ldap_get_entries($conn, $result);

    for ($i = 0; $i < $info["count"]; $i++) {

        $uid = $info[$i]["uid"][0];
        $supannetuid = $info[$i]["supannetuid"][0];
        $mail = $info[$i]["mail"][0];
        $username = $info[$i]["cn"][0];
        $edupersonprimaryaffiliation = $info[$i]["edupersonprimaryaffiliation"][0];

        $val = $uid . "|" . $mail . "|" . $username . "|" . $edupersonprimaryaffiliation;

        $_SESSION['login'] = $uid;
        $_SESSION['supannetuid'] = $supannetuid;
        $_SESSION['mail'] = $mail;
        $_SESSION['username'] = $username;
        $_SESSION['statut'] = $edupersonprimaryaffiliation;
    }

    /* 4ème étape : cloture de la session LDAP */
    ldap_close($conn);

    return $edupersonprimaryaffiliation;
}

function connexion_oracle($user_oracle = USER_ORACLE, $passwd_oracle = PASSWD_ORACLE, $base_oracle = BASE_ORACLE)
{
    $cnxoracle = oci_connect($user_oracle, $passwd_oracle, $base_oracle);
    if ($cnxoracle == false)
        die("Connexion $base_oracle impossible " . $e['message'] . "\n");
    else {
        return $cnxoracle;
    }
}


function connexion_mysql($base_mysql = MYSQL_BASE_DATAS, $hote_mysql = HOTE_MYSQL, $user_mysql = USER_MYSQL, $passwd_mysql = PASSWD_MYSQL)
{
    $link = mysqli_connect($hote_mysql, $user_mysql, $passwd_mysql, $base_mysql);
    /* Vérification de la connexion */
    if (mysqli_connect_errno()) {
        printf("Échec de la connexion : %s\n", mysqli_connect_error());
        exit();
    } else {
        return $link;
    }
}

function requete($cnx_mysql, $libreq, $debug = 0)
{

    $req = mysqli_query($cnx_mysql, $libreq);
    if ($debug) {
        echo $libreq . '<br><br>';
    }

    if ($req) {
        return $req;
    } else {
        $erreur = "\r\nErreur requete\r\n";
        $erreur .= $libreq . '\r\n' . MYSQLI_ERROR($cnx_mysql) . '\n'; #pour le debug

        echo $erreur;
        die('UNE ERREUR A ETE RENCONTREE\n ');
        return false;
    }
}



#fonctions
#Recuperation des listes d'elp pour une version d'etape
function etp_lse($cnx_mysql, $cod_etp_cible, $cod_vrs_vet)
{

    $reqetp_lse = "select * from vet_regroupe_lse, liste_elp where cod_etp='$cod_etp_cible' and cod_vrs_vet='$cod_vrs_vet' and vet_regroupe_lse.cod_lse=liste_elp.cod_lse";
    debug("recuperation des listes elp pour une version etape <br>" . $reqetp_lse);
    $req = requete($cnx_mysql, $reqetp_lse);
    while ($r = mysqli_fetch_assoc($req))
        $res[] = $r['cod_lse'];
    return $res;
}

#Recuperation des elp fils d'une liste d'une version d'etape
function cherche_elp_fils($cnx_mysql, $nbchg, $entetes, $cod_lse, $niveau, $type = "tableau", $numero = 0, $lib_niveau_initial = "", $res_tablo = array())
{
    //GLOBAL $apogee;
    $res = "";
    $tabulation1 = "";
    $tabulation2 = "";
    $c1 = "";
    $c2 = "";
    $ladd = $_POST["ladd"];//Libelle Annexe Descriptive du Diplome
    $charge = $_POST["charge"];//charge d'enseignement
    $epr = $_POST["epr"];//affichage epreuve
    $ses = $_POST["cod_ses"];//affichage session

    if ($type == "tableau") {
        for ($i = 1; $i < $niveau; $i++) {
            $tabulation1 .= "&nbsp;&nbsp;&nbsp;";
            $tabulation2 .= "";
        }
        $t1 = "</td>\n<td>";
    } else {
        $t1 = "";
        $tabulation1 = "";
        $res .= "<ul type=none>";
        $c1 = "[";
        $c2 = "]";
    }

    $etp = $_SESSION['cod_etp_cible'];
    $cod_vrs_vet = $_SESSION['cod_vrs_vet'];
    $cod_anu = $_POST['cod_anu'];
    $cycle = $_POST['cycle'];

    $req = requete($cnx_mysql, "SELECT lse_regroupe_elp.cod_elp, table_elp.lib_elp, table_elp.cod_nel, table_elp.cod_pel, table_elp.nbr_crd_elp,
  table_elp_nbetu.nb_etu_ip,
  table_elp.lib_elp_lng,
    table_elp_nbetu.cod_etp
  FROM table_elp
  INNER JOIN lse_regroupe_elp ON table_elp.cod_elp = lse_regroupe_elp.cod_elp
  LEFT OUTER JOIN table_elp_nbetu ON table_elp.cod_elp = table_elp_nbetu.cod_elp
                    AND table_elp_nbetu.cod_anu = '$cod_anu'
                    AND table_elp_nbetu.cod_etp = '$etp'
            AND table_elp_nbetu.cod_vrs_etp = '$cod_vrs_vet'
  WHERE lse_regroupe_elp.cod_lse = '$cod_lse'
  order by table_elp.lib_elp");


    $i = 0;

    while ($r = mysqli_fetch_assoc($req)) {

        $cod_elp = $r['cod_elp'];
        $cod_nel = $r['cod_nel'];
        $cod_pel = $r['cod_pel'];
        $nbr_crd_elp = $r['nbr_crd_elp'];

        //Recuperation nb IP de l'elp
        $reqnbip = requete($cnx_mysql, "SELECT table_elp_nbetu.nb_etu_ip from table_elp_nbetu where table_elp_nbetu.cod_elp = '$cod_elp' and table_elp_nbetu.cod_etp = '$etp' and table_elp_nbetu.cod_vrs_etp = '$cod_vrs_vet'");
        while ($rnbip = mysqli_fetch_assoc($reqnbip)) {
            $elp_nbetu = $rnbip['nb_etu_ip'];
        }

        // avec lib ADD
        if ($ladd == "1") {
            $i++;
            //Ne faire apparaitre l'ADD que pour les élements SEM et UE
            if ($cod_nel == "UE" or $cod_nel == "SE") {
                $lib_elp = $r['lib_elp'] . "<br><font size=-1,5>" . $r['lib_elp_lng'] . "</font>";
            } else {
                $lib_elp = $r['lib_elp'];
            }
        } else { // sans ADD
            $i++;
            $lib_elp = $r['lib_elp'];
        }//Fin if ladd

        if ($type <> "tableau")
            $tabulation1 = "";
        if ($type == "tableau")
            $res .= "<tr><td>";
        else
            $res .= "<li>";

        #voir si fils
        $req2 = requete($cnx_mysql, "select t1.cod_lse,t2.cod_typ_lse,t1.nbr_min_elp_obl_chx,t1.nbr_max_elp_obl_chx
                from elp_regroupe_lse as t1
                inner join liste_elp as t2 on t1.cod_lse=t2.cod_lse WHERE cod_elp='$cod_elp'");
        $nb_fils = mysqli_num_rows($req2);
        if ($nb_fils > 0) {
            $desc = 1;
            $g1 = "<b><big>";
            $g2 = "</big></b>";
        } else {
            $desc = 0;
            $g1 = "";
            $g2 = "";
        }

        // sous une autre forme if($numero and $nb_fils>0) $lib_niveau = $lib_niveau_initial."$i"; else $lib_niveau="";
        if ($numero)
            $lib_niveau = "$niveau.$i";
        else
            $lib_niveau = "";
        if ($type == "tableau") {
            //Recup info Charge
            $affcharge = '';
            if ($charge == "1") {
                //Recup info Charge
                $rescharge = mysqli_query($cnx_mysql, "select distinct elp_chg_typ_heu.COD_TYP_HEU, elp_chg_typ_heu.NBR_HEU_ELP FROM elp_chg_typ_heu WHERE cod_anu = '$cod_anu' and cod_elp='$cod_elp' order by elp_chg_typ_heu.COD_TYP_HEU;");
                if (mysqli_num_rows($rescharge) == 0) {
                    $affcharge = $affcharge . "<td></td>";
                    for ($n = 0; $n < $nbchg; $n++) {
                        //$affcharge=$affcharge."<td></td>";
                    }
                } else {
                    $index = 0;
                    $affcharge = $affcharge . "<td>";
                    while ($enrcharge = mysqli_fetch_array($rescharge)) {
                        /*while (strcmp($enrcharge['COD_TYP_HEU'],$entetes[$index]) != 0) {
                                $affcharge=$affcharge."<td></td>";
                                $index = $index + 1;
                              }*/

                        $affcharge = $affcharge . $enrcharge['COD_TYP_HEU'] . " = " . $enrcharge['NBR_HEU_ELP'] . " ";
                        $index = $index + 1;

                    }
                    $affcharge = $affcharge . "</td>";
                    /*for (;$index < $nbchg; $index++){
                           $affcharge=$affcharge."<td></td>";
                         }*/
                }
            }

            $res .= "$tabulation1 $lib_niveau $g1$lib_elp$g2 $tabulation2 $t1 $c1$cod_elp$c2 $t1 $cod_nel $t1 $cod_pel $t1 $nbr_crd_elp $t1 $elp_nbetu $affcharge";
        } else {
            $res .= "$tabulation1 $lib_niveau $g1$lib_elp$g2";
        }

        $lib_liste_filles = "";
        $t_liste_lse_filles = array();

        while ($r2 = mysqli_fetch_assoc($req2)) {
            $t_liste_lse_filles[] = $r2;
            $lib_liste_filles .= "[" . implode("|", $r2) . "]";
        }

        $cod_elp_regroupe = "";
        $l = array(/*$decalage,*/ $niveau, $cod_lse, $cod_elp, $cod_nel, $nbr_crd_elp, $lib_elp, $cod_elp_regroupe, $nb_fils, $lib_liste_filles);
        $res_tablo[] = $l;

        // desc = 1 si il y a des fils/filles
        if ($desc == 1) {
            foreach ($t_liste_lse_filles as $k => $r2) {
                $max = $r2['nbr_max_elp_obl_chx'];
                $min = $r2['nbr_min_elp_obl_chx'];
                $cod_lse_aff = $r2['cod_lse'];
                if ($type == "tableau")
                    $res .= " $t1<i>$cod_lse_aff</i>$t1";
                if ($max > 1)
                    $pluriel = "s";
                else
                    $pluriel = "";
                $card = "&nbsp;";
                if ($min and $min == $max)
                    $card = " $max élèment$pluriel à choisir";
                if ($min and $min < $max)
                    $card = " de $min à $max élèment$pluriel à choisir";
                $res .= $card;
                if ($numero and $type <> "tableau")
                    $res .= ":";
                if ($type == "tableau")
                    $res .= "</td></tr>";
                else
                    $res .= "</li>";

                // AFFICHAGE SESSIONS  paramètres : cod_ses=1 (session 1) cod_ses=2 (session 2) cod_ses=3 (session  unique) cod_ses=4 (toutes les sessions)
                if ($_SESSION['epr'] == '1') {
                    if ($_SESSION['cod_ses'] == '4') {//Affichage de toutes les sessions
                        $critsess = '';
                    } else {
                        $critsess = "and cod_ses='" . $_SESSION['cod_ses'] . "'";
                    }

                    //Recherche d'epreuve pour l'element
                    $reqepr = mysqli_query($cnx_mysql, "select epreuve.cod_epr, epreuve.lib_epr, epreuve.cod_nep, epreuve.cod_tep, epr_sanctionne_elp.cod_ses from epr_sanctionne_elp, epreuve
          where epr_sanctionne_elp.cod_epr=epreuve.cod_epr
          " . $critsess . "
          and epr_sanctionne_elp.cod_elp='" . $cod_elp . "'
          order by epr_sanctionne_elp.cod_ses, epreuve.lib_epr, epreuve.cod_epr");
                    while ($repr = mysqli_fetch_array($reqepr)) {
                        if ($repr[4] == '1') {
                            $bgcolor = "#D7E8FE";
                        } else {
                            if ($repr[4] == '2') {
                                $bgcolor = "#B7F9B9";
                            } else {
                                if ($repr[4] == '0') {
                                    $bgcolor = "#F9B7E5";
                                }
                            }
                        }
                        $res .= "<tr><td bgcolor=" . $bgcolor . ">" . $tabulation1 . $tabulation1 . "&nbsp;&nbsp;&nbsp;&nbsp;" . $repr[1] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[0] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[2] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[3] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[4] . "</td></tr>";
                    }
                }//Fin If Affichage des sessions

                // montage pdf-csv
                if ($type == "webip")
                    $res_tablo = cherche_elp_fils($cnx_mysql, $nbchg, $entetes, $r2['cod_lse'], $niveau + 1, $type, $numero, $lib_niveau, $res_tablo);
                else
                    $res .= cherche_elp_fils($cnx_mysql, $nbchg, $entetes, $r2['cod_lse'], $niveau + 1, $type, $numero, $lib_niveau) . "";
            }
        }

        if ($desc == 0) {
            if ($type == "tableau")
                $res .= "$t1&nbsp;$t1&nbsp;</td></tr>";
            else
                $res .= "";
            if ($_SESSION['epr'] == '1') {
                if ($_SESSION['cod_ses'] == '4') {//Affichage de toutes les sessions
                    $critsess = '';
                } else {
                    $critsess = "and epr_sanctionne_elp.cod_ses='" . $_SESSION['cod_ses'] . "'";
                }
                //Recherche d'epreuve pour l'element
                $reqepr = mysqli_query($cnx_mysql, "select epreuve.cod_epr, epreuve.lib_epr, epreuve.cod_nep, epreuve.cod_tep, epr_sanctionne_elp.cod_ses from epr_sanctionne_elp, epreuve
        where epr_sanctionne_elp.cod_epr=epreuve.cod_epr
        " . $critsess . "
        and epr_sanctionne_elp.cod_elp='" . $cod_elp . "'
        order by epr_sanctionne_elp.cod_ses, epreuve.lib_epr, epreuve.cod_epr");

                while ($repr = mysqli_fetch_array($reqepr)) {
                    if ($repr[4] == '1') {
                        $bgcolor = "#D7E8FE";
                    } else {
                        if ($repr[4] == '2') {
                            $bgcolor = "#B7F9B9";
                        } else {
                            if ($repr[4] == '0') {
                                $bgcolor = "#F9B7E5";
                            }
                        }
                    }
                    $res .= "<tr><td bgcolor=" . $bgcolor . ">" . $tabulation1 . $tabulation1 . "&nbsp;&nbsp;&nbsp;&nbsp;" . $repr[1] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[0] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[2] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[3] . "</td><td bgcolor=" . $bgcolor . ">" . $repr[4] . "</td></tr>";
                }
            }//Fin If Affichage des sessions
        }

    }

    if ($type == "webip") {
        return $res_tablo;
    }
    if ($type == "tableau") {
        $res .= "";
    } else {
        $res .= "</ul>";
    }
    return $res;

    mysqli_close($cnx_mysql);
}

/**
 * Debug mixed values
 *
 * @param mixed $value
 * @return string
 */
function debug($value)
{
    if (APP_MODE_DEBUG == "YES") {
        echo '<pre style="background-color:#EDF1F3;border-color:#9AAAB4;border-style:solid;border-width:1px;margin:10px 6px 10px 10px;padding:4pt;text-align:left;">';
        if ($value) {
            if (is_array($value) or is_object($value)) {
                print_r($value);
            } else {
                echo $value;
            }
        } else {
            echo 'Aucun enregistrement';
        }
        echo '</pre>';
    }
}


?>