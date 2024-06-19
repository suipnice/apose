<?php

echo "\n*****************************************************************************";
echo "";

//die("recup apogee stoppé.");
// SCRIPT DE RECUPERATION DES DONNEES APOGEE
error_reporting(E_ALL);
require "../include/fonctions.php";


function query_table_etape($an)
{
    $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC, APOGEE.INS_ADM_ETP.COD_CMP,Count(APOGEE.INS_ADM_ETP.COD_IND) AS Nb_Etu
FROM APOGEE.INS_ADM_ETP, APOGEE.ETAPE, APOGEE.VERSION_ETAPE
where APOGEE.INS_ADM_ETP.ETA_IAE='E'
and APOGEE.INS_ADM_ETP.ETA_PMT_IAE='P'
and APOGEE.INS_ADM_ETP.COD_ETP = APOGEE.ETAPE.COD_ETP
and APOGEE.INS_ADM_ETP.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP
and APOGEE.INS_ADM_ETP.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
and APOGEE.INS_ADM_ETP.COD_ANU='$an'
group by APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC, APOGEE.INS_ADM_ETP.COD_CMP";
    return $query;
}

function query_etape($an)
{
    $query = "SELECT APOGEE.VDI_FRACTIONNER_VET.COD_ETP,APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC, APOGEE.VERSION_ETAPE.COD_CMP, '$an' as COD_ANU, APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET, APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET
FROM APOGEE.VDI_FRACTIONNER_VET,APOGEE.VERSION_ETAPE,APOGEE.ETAPE
where APOGEE.VDI_FRACTIONNER_VET.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP
and APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
and APOGEE.VERSION_ETAPE.COD_ETP=APOGEE.ETAPE.COD_ETP
and APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET>='$an'
and APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET<='$an'
GROUP BY APOGEE.VDI_FRACTIONNER_VET.COD_ETP, APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC, APOGEE.VERSION_ETAPE.COD_CMP, APOGEE.VERSION_ETAPE.COD_ESI, APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET, APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET";
    return $query;
}


function query_annee_uni_apo()
{
    $query = "SELECT COD_ANU
FROM APOGEE.ANNEE_UNI
where ETA_ANU_IAE='O'";
    return $query;
}

function query_annee_uni_apo_ouverte()
{
    $query = "select cod_anu
from annee_uni
where cod_anu>=(select cod_anu from annee_uni where eta_anu_iae='O')";
    return $query;
}

function query_epreuve()
{
    $query = "SELECT COD_EPR, LIB_EPR, COD_NEP, COD_TEP
FROM APOGEE.EPREUVE";
    return $query;
}

function query_epr_sanctionne_elp()
{
    $query = "SELECT COD_ELP, COD_EPR, COD_SES
FROM APOGEE.EPR_SANCTIONNE_ELP where TEM_SUS_EPR_SES = 'N'";
    return $query;
}

function query_composante()
{
    $query = "SELECT COD_CMP, LIB_CMP, INT_1_EDI_DIP_CMP
FROM APOGEE.COMPOSANTE WHERE TEM_EN_SVE_CMP = 'O'
and cod_rne_cmp is not null";
    //AND APOGEE.COMPOSANTE.COD_NAT_CMP = 'J'";
    return $query;
}

function query_table_etape_nbetu($an)
{
    $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET,APOGEE.VERSION_ETAPE.LIB_WEB_VET,Count(APOGEE.INS_ADM_ETP.COD_IND) AS Nb_Etu
FROM APOGEE.INS_ADM_ETP, APOGEE.VERSION_ETAPE
where APOGEE.INS_ADM_ETP.ETA_IAE='E'
and APOGEE.INS_ADM_ETP.ETA_PMT_IAE='P'
and APOGEE.INS_ADM_ETP.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP
and APOGEE.INS_ADM_ETP.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
and APOGEE.INS_ADM_ETP.COD_ANU = '$an'
group by APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET,APOGEE.VERSION_ETAPE.LIB_WEB_VET";
    return $query;
}

function query_table_elp_nbetu($an)
{
    $query = "SELECT APOGEE.IND_CONTRAT_ELP.COD_ANU,APOGEE.IND_CONTRAT_ELP.COD_ETP,APOGEE.IND_CONTRAT_ELP.COD_VRS_VET,APOGEE.IND_CONTRAT_ELP.COD_ELP,Count(APOGEE.IND_CONTRAT_ELP.COD_IND) AS nb_etu_ip
FROM APOGEE.IND_CONTRAT_ELP
where APOGEE.IND_CONTRAT_ELP.COD_ANU = '$an'
and APOGEE.IND_CONTRAT_ELP.TEM_PRC_ICE='N'
group by APOGEE.IND_CONTRAT_ELP.COD_ANU,APOGEE.IND_CONTRAT_ELP.COD_ETP,APOGEE.IND_CONTRAT_ELP.COD_VRS_VET,APOGEE.IND_CONTRAT_ELP.COD_ELP";
    return $query;
}

function query_vet_regr_lse()
{
    $query = "select COD_ETP,COD_VRS_VET,COD_LSE,NBR_MAX_ELP_OBL_CHX_VET,NBR_MIN_ELP_OBL_CHX_VET
    from APOGEE.vet_regroupe_lse
    WHERE DAT_FRM_REL_LSE_VET is null ";
    return $query;
}

function query_elp_regroupe_lse()
{
    $query = "select COD_ELP,COD_LSE,NBR_MAX_ELP_OBL_CHX,NBR_MIN_ELP_OBL_CHX
 from APOGEE.elp_regroupe_lse
 where DAT_FRM_REL_LSE_ELP is null";
    return $query;
}
function query_listes()
{
    $query = "select COD_LSE,COD_TYP_LSE,ETA_LSE,LIC_LSE,LIB_LSE
from APOGEE.liste_elp ";
    return $query;
}
function query_lse_regroupe_elp()
{
    $query = "select COD_LSE,COD_ELP
from APOGEE.lse_regroupe_elp";
    return $query;
}

function query_table_elp()
{
    $query = "select APOGEE.ELEMENT_PEDAGOGI.COD_ELP,
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
  from APOGEE.ELEMENT_PEDAGOGI LEFT JOIN APOGEE.ELP_LIBELLE ON APOGEE.ELEMENT_PEDAGOGI.COD_ELP=APOGEE.ELP_LIBELLE.COD_ELP
  where APOGEE.ELEMENT_PEDAGOGI.TEM_SUS_ELP = 'N'
  and APOGEE.ELEMENT_PEDAGOGI.ETA_ELP='O'
  and (APOGEE.ELP_LIBELLE.COD_LNG='FRAN'
  or APOGEE.ELP_LIBELLE.COD_LNG IS NULL)";
    return $query;
}

function query_table_elp_charge_ens($an)
{
    $query = "SELECT APOGEE.ELP_CHARGE_ENS.COD_ELP, APOGEE.ELP_CHARGE_ENS.COD_ANU, APOGEE.ELP_CHARGE_ENS.nbr_heu_cm_elp, APOGEE.ELP_CHARGE_ENS.nbr_heu_td_elp, APOGEE.ELP_CHARGE_ENS.nbr_heu_tp_elp
FROM APOGEE.ELP_CHARGE_ENS
where APOGEE.ELP_CHARGE_ENS.COD_ANU = '$an'
and APOGEE.ELP_CHARGE_ENS.TEM_CAL_CHG = 'O'";
    return $query;
}

function query_table_charge_typ_ens($an)
{
    $query = "SELECT APOGEE.ELP_CHG_TYP_HEU.COD_ELP,APOGEE.ELP_CHG_TYP_HEU.COD_ANU, APOGEE.ELP_CHG_TYP_HEU.COD_TYP_HEU, APOGEE.ELP_CHG_TYP_HEU.NBR_HEU_ELP
    FROM APOGEE.ELP_CHG_TYP_HEU
    WHERE
    APOGEE.ELP_CHG_TYP_HEU.COD_ANU='$an'";
    return $query;
}

function query_table_typ_heure()
{
    $query = "SELECT APOGEE.TYPE_HEURE.COD_TYP_HEU,APOGEE.TYPE_HEURE.LIC_TYP_HEU, APOGEE.TYPE_HEURE.NUM_ORD_TYP_HEU
    FROM APOGEE.TYPE_HEURE
    WHERE
    APOGEE.TYPE_HEURE.TEM_EN_SVE_TYP_HEU = 'O'";
    return $query;
}

function recup_simple($cnx_mysql, $cnx_oracle, $nom_table_mysql, $lib_query)
{
    //fonction de debug, verifier si debug YES dans param.php
    debug("table " . $nom_table_mysql . "<br><br>" . $lib_query);

    set_time_limit(600); //10min
    $cursor = oci_parse($cnx_oracle, $lib_query);
    $result = oci_execute($cursor);
    $nrows = oci_fetch_all($cursor, $result);

    if ($cursor and $result) {
        $result = oci_execute($cursor);
        requete($cnx_mysql, "lock tables $nom_table_mysql write");
        $i = 0;

        while ($row = oci_fetch_object($cursor)) {
            $sql = "'";
            $i++;

            foreach ($row as $cle => $valeur) {
                $valeur = str_replace(",", ".", $valeur);
                $sql .= str_replace("'", "\\'", $valeur) . "','";
            }
            $sql = substr($sql, 0, -2); /* Enleve les 2 caracteres à la fin */
            $sql = utf8_encode($sql);

            $req_insert_sql = "INSERT into " . $nom_table_mysql . " values(" . $sql . ")";

            requete($cnx_mysql, $req_insert_sql);
        }

        requete($cnx_mysql, "unlock tables");
    } else {
        die("Erreur Requete ORACLE \n$lib_query\n" . print_r(oci_error($cursor)) . "\n");
    }
}

echo "\r\n<br>";
echo date("Y-m-d H:i:s") . "<br>Connexion a mysql ...";
$cnx_mysql = connexion_mysql();
echo "<br>Connexion a oracle ...";
$cnx = connexion_oracle();

requete($cnx_mysql, "delete from annee_uni");
recup_simple($cnx_mysql, $cnx, "annee_uni", query_annee_uni_apo_ouverte());

//echo "<br>selection des années ouvertes<br>";
$reqa = "select cod_anu from annee_uni";
$resa = mysqli_query($cnx_mysql, $reqa);

$tab_annees = array();

while ($enra = mysqli_fetch_array($resa)) {
    $tab_annees[] = $enra["cod_anu"];
}
//echo "<br>annees selectionnees";
//print_r($tab_annees);
//echo "<br>";

//echo "<br>table etape<br>";
requete($cnx_mysql, "delete from etape");
foreach ($tab_annees as $key => $value) {
    //echo "<br>table etape ".$value."<br>";
    recup_simple($cnx_mysql, $cnx, "etape", query_etape($value));
}

//echo "<br>table compsante<br>";
requete($cnx_mysql, "delete from composante");
recup_simple($cnx_mysql, $cnx, "composante", query_composante());


//echo "<br>table table_etape_apo<br>";
requete($cnx_mysql, "delete from table_etape_apo");
foreach ($tab_annees as $key => $value) {
    //echo "<br>table table_etape_apo ".$value."<br>";
    recup_simple($cnx_mysql, $cnx, "table_etape_apo", query_table_etape($value));
}

//echo "<br>table epreuve<br>";
requete($cnx_mysql, "delete from epreuve");
recup_simple($cnx_mysql, $cnx, "epreuve", query_epreuve());


//echo "<br>table epr_sanctionne_elp<br>";
requete($cnx_mysql, "delete from epr_sanctionne_elp");
recup_simple($cnx_mysql, $cnx, "epr_sanctionne_elp", query_epr_sanctionne_elp());

//echo "<br>table table_etape_nbetu<br>";
requete($cnx_mysql, "delete from table_etape_nbetu");
foreach ($tab_annees as $key => $value) {
    //echo "<br>table table_etape_nbetu ".$value."<br>";
    recup_simple($cnx_mysql, $cnx, "table_etape_nbetu", query_table_etape_nbetu($value));
}

//echo "<br>table table_elp<br>";
requete($cnx_mysql, "delete from table_elp");
recup_simple($cnx_mysql, $cnx, "table_elp", query_table_elp());

//echo "<br>table table_elp_nbetu<br>";
requete($cnx_mysql, "delete from table_elp_nbetu");
foreach ($tab_annees as $key => $value) {
    //echo "<br>table table_elp_nbetu ".$value."<br>";
    recup_simple($cnx_mysql, $cnx, "table_elp_nbetu", query_table_elp_nbetu($value));
}

//echo "<br>table vet_regroupe_lse<br>";
#Pour la SE on ne peut que tout recuperer (impossible de qualifer cod_etp recursivement)
requete($cnx_mysql, "delete from vet_regroupe_lse");
recup_simple($cnx_mysql, $cnx, "vet_regroupe_lse", query_vet_regr_lse());

//echo "<br>table elp_regroupe_lse<br>";
requete($cnx_mysql, "delete from elp_regroupe_lse");
recup_simple($cnx_mysql, $cnx, "elp_regroupe_lse", query_elp_regroupe_lse());

//echo "<br>table liste_elp<br>";
requete($cnx_mysql, "delete from liste_elp");
recup_simple($cnx_mysql, $cnx, "liste_elp", query_listes());

//echo "<br>table lse_regroupe_elp<br>";
requete($cnx_mysql, "delete from lse_regroupe_elp");
recup_simple($cnx_mysql, $cnx, "lse_regroupe_elp", query_lse_regroupe_elp());

//echo "<br>table elp_chg_typ_heu<br>";
//modif oct. 2014 suite patch apogee 4.50
requete($cnx_mysql, "delete from elp_chg_typ_heu");
foreach ($tab_annees as $key => $value) {
    //echo "<br>table elp_chg_typ_heu ".$value."<br>";
    recup_simple($cnx_mysql, $cnx, "elp_chg_typ_heu", query_table_charge_typ_ens($value));
}

//echo "<br>table type_heure<br>";
//modif oct. 2014 suite patch apogee 4.50
requete($cnx_mysql, "delete from type_heure");
recup_simple($cnx_mysql, $cnx, "type_heure", query_table_typ_heure());

echo "<br> script ok";

OCILogoff($cnx);
mysqli_close($cnx_mysql);
