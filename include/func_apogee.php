<?php
/**
 * ApoSE func_apogee.php
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


/**
 * Connect to an Oracle database
 *
 * @param mixed $user_oracle   Utilisateur BDD Oracle
 * @param mixed $passwd_oracle MDP BDD Oracle
 * @param mixed $base_oracle   Base Oracle
 *
 * @return resource Oracle connection identifier
 */
function connexionOracle(
    $user_oracle = USER_ORACLE,
    $passwd_oracle = PASSWD_ORACLE,
    $base_oracle = BASE_ORACLE
) {
    $cnxoracle = oci_connect($user_oracle, $passwd_oracle, $base_oracle);
    if ($cnxoracle !== false) {
        return $cnxoracle;
    }

    $err = oci_error();
    die("Connexion $base_oracle impossible " . $err['message'] . "\n");

}//end connexionOracle()


/**
 * [Description for queryTableEtape]
 *
 * @param string $year Année universitaire
 *
 * @return [type]
 */
function queryTableEtape($year)
{
    $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,
    APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET,
    APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET,
    APOGEE.ETAPE.COD_CYC, APOGEE.INS_ADM_ETP.COD_CMP,
    Count(APOGEE.INS_ADM_ETP.COD_IND) AS Nb_Etu
FROM APOGEE.INS_ADM_ETP, APOGEE.ETAPE, APOGEE.VERSION_ETAPE
WHERE APOGEE.INS_ADM_ETP.ETA_IAE='E'
AND APOGEE.INS_ADM_ETP.ETA_PMT_IAE='P'
AND APOGEE.INS_ADM_ETP.COD_ETP = APOGEE.ETAPE.COD_ETP
AND APOGEE.INS_ADM_ETP.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP
AND APOGEE.INS_ADM_ETP.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
AND APOGEE.INS_ADM_ETP.COD_ANU='$year'
GROUP BY APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,
         APOGEE.INS_ADM_ETP.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP,
         APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC,
         APOGEE.INS_ADM_ETP.COD_CMP";
    return $query;

}


/**
 * [Description for queryEtape]
 *
 * @param string $year Année universitaire
 *
 * @return [type]
 */
function queryEtape($year)
{
    $query = "SELECT APOGEE.VDI_FRACTIONNER_VET.COD_ETP,
    APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP,
    APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC,
    APOGEE.VERSION_ETAPE.COD_CMP, '$year' AS COD_ANU,
    APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET,
    APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET
FROM APOGEE.VDI_FRACTIONNER_VET,APOGEE.VERSION_ETAPE,APOGEE.ETAPE
WHERE APOGEE.VDI_FRACTIONNER_VET.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP
AND APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
AND APOGEE.VERSION_ETAPE.COD_ETP=APOGEE.ETAPE.COD_ETP
AND APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET>='$year'
AND APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET<='$year'
GROUP BY APOGEE.VDI_FRACTIONNER_VET.COD_ETP,
         APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET,
         APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET,
         APOGEE.ETAPE.COD_CYC, APOGEE.VERSION_ETAPE.COD_CMP,
         APOGEE.VERSION_ETAPE.COD_ESI,
         APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET,
         APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET";
    return $query;

}


/**
 * [Description for queryAnneeUniApo]
 *
 * @return string requete
 */
function queryAnneeUniApo()
{
    $query = "SELECT COD_ANU
FROM APOGEE.ANNEE_UNI
where ETA_ANU_IAE='O'";
    return $query;

}


/**
 * Récupère dans Apogée l'année courante, la suivante, ansi que le nombre
 * d'années précédentes indiquées par le param NB_PREV_YEAR
 *
 * @return string La requete a fournir à Oracle
 */
function queryAnneeUniApoOuverte()
{
    $NB_PREV_YEAR = NB_PREV_YEAR;
    $query = "SELECT cod_anu
    FROM annee_uni
    WHERE cod_anu + $NB_PREV_YEAR >=(
        SELECT cod_anu FROM annee_uni WHERE eta_anu_iae='O')";
    return $query;

}


/**
 * [Description for queryEpreuve]
 *
 * @return [type]
 */
function queryEpreuve()
{
    $query = "SELECT COD_EPR, LIB_EPR, COD_NEP, COD_TEP
FROM APOGEE.EPREUVE";
    return $query;

}


/**
 * [Description for queryEprSanctionneElp]
 *
 * @return [type]
 */
function queryEprSanctionneElp()
{
    $query = "SELECT COD_ELP, COD_EPR, COD_SES
    FROM APOGEE.EPR_SANCTIONNE_ELP
    WHERE TEM_SUS_EPR_SES = 'N'";
    return $query;

}


/**
 * [Description for queryComposante]
 *
 * @return [type]
 */
function queryComposante()
{
    $query = "SELECT COD_CMP, LIB_CMP, INT_1_EDI_DIP_CMP
    FROM APOGEE.COMPOSANTE WHERE TEM_EN_SVE_CMP = 'O'
    AND cod_rne_cmp IS NOT NULL";
    // AND APOGEE.COMPOSANTE.COD_NAT_CMP = 'J'";
    return $query;

}


/**
 * [Description for queryTableEtapeNbetu]
 *
 * @param string $year Année universitaire
 *
 * @return [type]
 */
function queryTableEtapeNbetu($year)
{
    $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,
    APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET,
    APOGEE.VERSION_ETAPE.LIB_WEB_VET,
    Count(APOGEE.INS_ADM_ETP.COD_IND) AS Nb_Etu
FROM APOGEE.INS_ADM_ETP, APOGEE.VERSION_ETAPE
WHERE APOGEE.INS_ADM_ETP.ETA_IAE='E'
AND APOGEE.INS_ADM_ETP.ETA_PMT_IAE='P'
AND APOGEE.INS_ADM_ETP.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP
AND APOGEE.INS_ADM_ETP.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
AND APOGEE.INS_ADM_ETP.COD_ANU = '$year'
GROUP BY APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,
         APOGEE.INS_ADM_ETP.COD_VRS_VET,
         APOGEE.VERSION_ETAPE.LIB_WEB_VET";
    return $query;

}


/**
 * [Description for queryTableElpNbetu]
 *
 * @param string $year Année universitaire
 *
 * @return [type]
 */
function queryTableElpNbetu($year)
{
    $query = "SELECT APOGEE.IND_CONTRAT_ELP.COD_ANU,
    APOGEE.IND_CONTRAT_ELP.COD_ETP,APOGEE.IND_CONTRAT_ELP.COD_VRS_VET,
    APOGEE.IND_CONTRAT_ELP.COD_ELP,
    Count(APOGEE.IND_CONTRAT_ELP.COD_IND) AS nb_etu_ip
FROM APOGEE.IND_CONTRAT_ELP
WHERE APOGEE.IND_CONTRAT_ELP.COD_ANU = '$year'
AND APOGEE.IND_CONTRAT_ELP.TEM_PRC_ICE='N'
GROUP BY APOGEE.IND_CONTRAT_ELP.COD_ANU,
         APOGEE.IND_CONTRAT_ELP.COD_ETP,
         APOGEE.IND_CONTRAT_ELP.COD_VRS_VET,
         APOGEE.IND_CONTRAT_ELP.COD_ELP";
    return $query;

}


/**
 * [Description for queryVetRegrLse]
 *
 * @return [type]
 */
function queryVetRegrLse()
{
    $query = "SELECT COD_ETP, COD_VRS_VET, COD_LSE,
        NBR_MAX_ELP_OBL_CHX_VET, NBR_MIN_ELP_OBL_CHX_VET
    FROM APOGEE.vet_regroupe_lse
    WHERE DAT_FRM_REL_LSE_VET IS NULL ";
    return $query;

}


/**
 * [Description for queryElpRegroupeLse]
 *
 * @return [type]
 */
function queryElpRegroupeLse()
{
    $query = "SELECT COD_ELP,COD_LSE,NBR_MAX_ELP_OBL_CHX,NBR_MIN_ELP_OBL_CHX
 FROM APOGEE.elp_regroupe_lse
 WHERE DAT_FRM_REL_LSE_ELP IS NULL";
    return $query;

}


/**
 * [Description for queryListes]
 *
 * @return [type]
 */
function queryListes()
{
    $query = "SELECT COD_LSE,COD_TYP_LSE,ETA_LSE,LIC_LSE,LIB_LSE
FROM APOGEE.liste_elp ";
    return $query;

}


/**
 * [Description for queryLseRegroupeElp]
 *
 * @return [type]
 */
function queryLseRegroupeElp()
{
    $query = "SELECT COD_LSE,COD_ELP
FROM APOGEE.lse_regroupe_elp";
    return $query;

}


/**
 * [Description for queryTableElp]
 *
 * @return [type]
 */
function queryTableElp()
{
    $query = "SELECT APOGEE.ELEMENT_PEDAGOGI.COD_ELP,
  APOGEE.ELEMENT_PEDAGOGI.LIC_ELP,
  APOGEE.ELEMENT_PEDAGOGI.LIB_ELP,
  APOGEE.ELEMENT_PEDAGOGI.COD_NEL,
  APOGEE.ELEMENT_PEDAGOGI.COD_PEL,
  APOGEE.ELEMENT_PEDAGOGI.TEM_ADI,
  APOGEE.ELEMENT_PEDAGOGI.TEM_ADO,
  APOGEE.ELEMENT_PEDAGOGI.NBR_CRD_ELP,
  APOGEE.ELP_LIBELLE.LIB_ELP_LNG,
  APOGEE.ELEMENT_PEDAGOGI.NBR_VOL_ELP,
  APOGEE.ELEMENT_PEDAGOGI.COD_VOL_ELP,
  APOGEE.ELEMENT_PEDAGOGI.TEM_MCC_ELP
  FROM APOGEE.ELEMENT_PEDAGOGI
  LEFT JOIN APOGEE.ELP_LIBELLE
  ON APOGEE.ELEMENT_PEDAGOGI.COD_ELP=APOGEE.ELP_LIBELLE.COD_ELP
  WHERE APOGEE.ELEMENT_PEDAGOGI.TEM_SUS_ELP = 'N'
  AND APOGEE.ELEMENT_PEDAGOGI.ETA_ELP='O'
  AND (APOGEE.ELP_LIBELLE.COD_LNG='FRAN'
  OR APOGEE.ELP_LIBELLE.COD_LNG IS NULL)";
    return $query;

}


/**
 * [Description for queryTableElpChargeEns]
 *
 * @param string $year Année universitaire
 *
 * @return [type]
 */
function queryTableElpChargeEns($year)
{
    $query = "SELECT APOGEE.ELP_CHARGE_ENS.COD_ELP,
    APOGEE.ELP_CHARGE_ENS.COD_ANU,
    APOGEE.ELP_CHARGE_ENS.nbr_heu_cm_elp,
    APOGEE.ELP_CHARGE_ENS.nbr_heu_td_elp,
    APOGEE.ELP_CHARGE_ENS.nbr_heu_tp_elp
FROM APOGEE.ELP_CHARGE_ENS
WHERE APOGEE.ELP_CHARGE_ENS.COD_ANU = '$year'
AND APOGEE.ELP_CHARGE_ENS.TEM_CAL_CHG = 'O'";
    return $query;

}


/**
 * [Description for queryTableChargeTypEns]
 *
 * @param string $year Année universitaire
 *
 * @return [type]
 */
function queryTableChargeTypEns($year)
{
    $query = "SELECT COD_ELP,
        COD_ANU,
        COD_TYP_HEU,
        NBR_HEU_ELP AS NB_HEU_ELP
    FROM APOGEE.ELP_CHG_TYP_HEU
    WHERE COD_ANU='$year'";
    return $query;

}


/**
 * [Description for queryTableTypHeure]
 *
 * @return [type]
 */
function queryTableTypHeure()
{
    $query = "SELECT APOGEE.TYPE_HEURE.COD_TYP_HEU,
        APOGEE.TYPE_HEURE.LIC_TYP_HEU,
        APOGEE.TYPE_HEURE.NUM_ORD_TYP_HEU
    FROM APOGEE.TYPE_HEURE
    WHERE APOGEE.TYPE_HEURE.TEM_EN_SVE_TYP_HEU = 'O'";
    return $query;

}


/**
 * [Description for recupSimple]
 *
 * @param mysqli $cnx_mysql       Connexion à la base Mysql
 * @param mixed  $cnx_oracle      Connexion à la base Oracle
 * @param mixed  $nom_table_mysql Nom de la table
 * @param string $lib_query       Requete SQL
 *
 * @return [type]
 */
function recupSimple(
    $cnx_mysql,
    $cnx_oracle,
    $nom_table_mysql,
    $lib_query
) {
    // Fonction de debug, verifier si debug YES dans param.php.
    debug("table " . $nom_table_mysql . "<br><br>" . $lib_query);

    // 10min
    set_time_limit(600);
    $cursor = oci_parse($cnx_oracle, $lib_query);
    $result = oci_execute($cursor);
    oci_fetch_all($cursor, $result);

    if ($cursor !== false and is_array($result) === true) {
        $result = oci_execute($cursor);
        requete($cnx_mysql, "lock tables $nom_table_mysql write");

        while (is_object($row = oci_fetch_object($cursor)) === true) {
            $sql = "'";
            $keys = [];
            foreach ($row as $cle => $valeur) {
                $valeur = str_replace(",", ".", $valeur);

                // Colonnes qui doivent numériques plutôt que chaine vide ''
                $num_cols = ["NBR_VOL_ELP", "NB_HEU_ELP"];
                if (in_array($cle, $num_cols) and $valeur === '') {
                    $valeur = 0;
                }

                // Colonnes qui doivent être NULL plutot que ''
                if (in_array($cle, ["NB_HEU"]) and $valeur === '') {
                    /* Retire "'" à la fin */
                    $sql = substr($sql, 0, -1);
                    $sql .= "NULL,'";
                } else {
                    $sql .= str_replace("'", "\\'", $valeur) . "','";
                }
                $keys[] = $cle;
            }
            // Enleve les 2 caracteres à la fin (,').
            $sql = substr($sql, 0, -2);
            $keys = implode(",", $keys);

            $req_insert_sql = "INSERT INTO " . $nom_table_mysql . "
                               VALUES(" . $sql . ")";

            requete($cnx_mysql, $req_insert_sql);
        }

        requete($cnx_mysql, "unlock tables");
    } else {
        $e = oci_error();
        echo "cursor = '$cursor' // result = $result";
        die(
            "Erreur Requete ORACLE \n$lib_query\n
            " . print_r($e['message']) . "\n"
        );
    }//end if
}
