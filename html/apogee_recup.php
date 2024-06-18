<?php
//die("recup apogee stoppé.");
#SCRIPT DE RECUPERATION DES DONNEES APOGEE
error_reporting(E_ALL ^ E_NOTICE);

$nom_fic_param = "param.php";
if(!is_file($nom_fic_param)) die("$nom_fic_param - Fichier de parametres manquants\n"); else include($nom_fic_param);


function connexion_oracle($user_oracle=USER_ORACLE,$passwd_oracle=PASSWD_ORACLE,$base_oracle=BASE_ORACLE)
{

  echo "connexion a la base Oracle : " . $base_oracle . "\n";
  $cnxoracle = oci_connect($user_oracle, $passwd_oracle,$base_oracle);
    if ($cnxoracle == false) die("Connexion $base_oracle impossible ". $e['message'] ."\n");
    else return $cnxoracle; 
}


function connexion_mysql($base_mysql=MYSQL_BASE_DATAS,$hote_mysql=HOTE_MYSQL,$user_mysql=USER_MYSQL,$passwd_mysql=PASSWD_MYSQL)
{
echo "user Mysql=$user_mysql\n";
echo "bdd_mysql=$base_mysql\n";
$cnxmysql=mysql_connect($hote_mysql,$user_mysql,$passwd_mysql) or die ("Connexion MySQL impossible ". mysql_error()."\n") ;
$bdd = mysql_select_db($base_mysql,$cnxmysql) or die ("Base de donnees ".$base_mysql." INTROUVABLE:" . mysql_error()."\n"); 
return $cnxmysql;
}


function query_table_etape($an)
{ $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC, APOGEE.INS_ADM_ETP.COD_CMP,Count(APOGEE.INS_ADM_ETP.COD_IND) AS Nb_Etu
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
{ $query = "SELECT APOGEE.VDI_FRACTIONNER_VET.COD_ETP,APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET, APOGEE.ETAPE.LIC_ETP, APOGEE.VERSION_ETAPE.LIB_WEB_VET, APOGEE.ETAPE.COD_CYC, APOGEE.VERSION_ETAPE.COD_CMP, APOGEE.VERSION_ETAPE.COD_ESI, APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET, APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET 
FROM APOGEE.VDI_FRACTIONNER_VET,APOGEE.VERSION_ETAPE,APOGEE.ETAPE
where APOGEE.VDI_FRACTIONNER_VET.COD_ETP = APOGEE.VERSION_ETAPE.COD_ETP 
and APOGEE.VDI_FRACTIONNER_VET.COD_VRS_VET = APOGEE.VERSION_ETAPE.COD_VRS_VET
and APOGEE.VERSION_ETAPE.COD_ETP=APOGEE.ETAPE.COD_ETP
and APOGEE.VDI_FRACTIONNER_VET.DAA_FIN_RCT_VET>='$an'
and APOGEE.VDI_FRACTIONNER_VET.DAA_DEB_RCT_VET<='$an'" ;
 return $query;
//Cod_ESI dans le select afin de remplir artificiellement la colonne Cod_anu
}


function query_annee_uni_apo()
{ $query = "SELECT COD_ANU
FROM APOGEE.ANNEE_UNI
where ETA_ANU_IAE='O'";
 return $query;
}

function query_epreuve()
{ $query = "SELECT COD_EPR, LIB_EPR, COD_NEP, COD_TEP
FROM APOGEE.EPREUVE";
 return $query;
}

function query_epr_sanctionne_elp()
{ $query = "SELECT COD_ELP, COD_EPR, COD_SES
FROM APOGEE.EPR_SANCTIONNE_ELP where TEM_SUS_EPR_SES = 'N'";
 return $query;
}

function query_composante()
{ $query = "SELECT COD_CMP, LIB_CMP, INT_1_EDI_DIP_CMP
FROM APOGEE.COMPOSANTE WHERE TEM_EN_SVE_CMP = 'O'";
//AND APOGEE.COMPOSANTE.COD_NAT_CMP = 'J'";
 return $query;
}


function query_ind_contrat_elp1($comp,$an)
{ $query = "SELECT APOGEE.IND_CONTRAT_ELP.COD_ANU, APOGEE.IND_CONTRAT_ELP.COD_ETP, APOGEE.IND_CONTRAT_ELP.COD_VRS_VET, APOGEE.IND_CONTRAT_ELP.COD_ELP, APOGEE.INDIVIDU.COD_ETU
FROM APOGEE.IND_CONTRAT_ELP, APOGEE.INDIVIDU 
where APOGEE.IND_CONTRAT_ELP.COD_ANU='$an'
and APOGEE.IND_CONTRAT_ELP.COD_IND = APOGEE.INDIVIDU.COD_IND
and COD_FEX is not null
and TEM_PRC_ICE='N'
and APOGEE.IND_CONTRAT_ELP.COD_ETP LIKE '$comp%'";
 return $query;
}

function query_ins_adm_etp($an)
{ $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INDIVIDU.COD_ETU, APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET, APOGEE.INS_ADM_ETP.TEM_IAE_PRM, APOGEE.INS_ADM_ETP.COD_CMP, APOGEE.INS_ADM_ETP.COD_CGE
FROM APOGEE.INS_ADM_ETP, APOGEE.INDIVIDU 
where APOGEE.INS_ADM_ETP.ETA_IAE='E' 
and APOGEE.INS_ADM_ETP.ETA_PMT_IAE='P' 
and APOGEE.INS_ADM_ETP.COD_IND = APOGEE.INDIVIDU.COD_IND 
and APOGEE.INS_ADM_ETP.COD_ANU='$an'";
 return $query;
}

function query_table_etape_nbetu($an)
{ $query = "SELECT APOGEE.INS_ADM_ETP.COD_ANU,APOGEE.INS_ADM_ETP.COD_ETP,APOGEE.INS_ADM_ETP.COD_VRS_VET,APOGEE.VERSION_ETAPE.LIB_WEB_VET,Count(APOGEE.INS_ADM_ETP.COD_IND) AS Nb_Etu
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
{ $query = "SELECT APOGEE.IND_CONTRAT_ELP.COD_ANU,APOGEE.IND_CONTRAT_ELP.COD_ETP,APOGEE.IND_CONTRAT_ELP.COD_VRS_VET,APOGEE.IND_CONTRAT_ELP.COD_ELP,Count(APOGEE.IND_CONTRAT_ELP.COD_IND) AS nb_etu_ip
FROM APOGEE.IND_CONTRAT_ELP 
where APOGEE.IND_CONTRAT_ELP.COD_ANU = '$an' 
and APOGEE.IND_CONTRAT_ELP.TEM_PRC_ICE='N'
group by APOGEE.IND_CONTRAT_ELP.COD_ANU,APOGEE.IND_CONTRAT_ELP.COD_ETP,APOGEE.IND_CONTRAT_ELP.COD_VRS_VET,APOGEE.IND_CONTRAT_ELP.COD_ELP";
 return $query;
}

function query_vet_regr_lse()
{	$query = "select COD_ETP,COD_VRS_VET,COD_LSE,NBR_MAX_ELP_OBL_CHX_VET,NBR_MIN_ELP_OBL_CHX_VET 
	from APOGEE.vet_regroupe_lse 
	WHERE DAT_FRM_REL_LSE_VET is null ";
	return $query;
}

function query_elp_regroupe_elp()
{ $query = "select COD_LSE,COD_ELP_FILS,ETA_ELP_FILS,ETA_LSE,COD_TYP_LSE,COD_ELP_PERE,ETA_ELP_PERE,TEM_SUS_ELP_PERE,TEM_SUS_ELP_FILS
 from APOGEE.elp_regroupe_elp 
 where DATE_FERMETURE_LIEN is null";
 return $query;
}
function query_elp_regroupe_lse()
{$query = "select COD_ELP,COD_LSE,NBR_MAX_ELP_OBL_CHX,NBR_MIN_ELP_OBL_CHX
 from APOGEE.elp_regroupe_lse
 where DAT_FRM_REL_LSE_ELP is null";
 return $query;
}
function query_listes()
{ $query = "select COD_LSE,COD_TYP_LSE,ETA_LSE,LIC_LSE,LIB_LSE 
from APOGEE.liste_elp ";
  return $query;
}
function query_lse_regroupe_elp()
{ $query = "select COD_LSE,COD_ELP 
from APOGEE.lse_regroupe_elp";
  return $query;
}

function query_table_elp()
{ $query = "select APOGEE.ELEMENT_PEDAGOGI.COD_ELP, 
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
{ $query = "SELECT APOGEE.ELP_CHARGE_ENS.COD_ELP, APOGEE.ELP_CHARGE_ENS.COD_ANU, APOGEE.ELP_CHARGE_ENS.nbr_heu_cm_elp, APOGEE.ELP_CHARGE_ENS.nbr_heu_td_elp, APOGEE.ELP_CHARGE_ENS.nbr_heu_tp_elp
FROM APOGEE.ELP_CHARGE_ENS 
where APOGEE.ELP_CHARGE_ENS.COD_ANU = '$an' 
and APOGEE.ELP_CHARGE_ENS.TEM_CAL_CHG = 'O'";
 return $query;
}

function query_table_charge_typ_ens($an)
{$query = "SELECT APOGEE.ELP_CHG_TYP_HEU.COD_ELP,APOGEE.ELP_CHG_TYP_HEU.COD_ANU, APOGEE.ELP_CHG_TYP_HEU.COD_TYP_HEU, APOGEE.ELP_CHG_TYP_HEU.NBR_HEU_ELP
    FROM APOGEE.ELP_CHG_TYP_HEU
    WHERE
    APOGEE.ELP_CHG_TYP_HEU.COD_ANU='$an'";
return $query;
}

function query_table_typ_heure()
{$query = "SELECT APOGEE.TYPE_HEURE.COD_TYP_HEU,APOGEE.TYPE_HEURE.LIC_TYP_HEU, APOGEE.TYPE_HEURE.NUM_ORD_TYP_HEU
    FROM APOGEE.TYPE_HEURE
    WHERE
    APOGEE.TYPE_HEURE.TEM_EN_SVE_TYP_HEU = 'O'";
return $query;
}



FUNCTION requete($libreq, $debug=0){
 $req = mysql_query($libreq);
 if ($debug) {
   echo $libreq . '\n';
 }
# IF (!MYSQL_ERROR())

 IF ($req)
  RETURN $req;
  ELSE  {
  $erreur= "\r\nErreur requete\r\n";
  $erreur.=$libreq.'\r\n'.MYSQL_ERROR().'\n'; #pour le debug
  //ERROR_LOG('Apogee_recup - ERREUR REQUETE \r\n$erreur',1,'xxx@xxxx');
  //ECHO $erreur;
  echo $erreur;
  DIE('UNE ERREUR A ETE RENCONTREE\n ');
  RETURN FALSE;
  }
}

FUNCTION recup_simple($cnx_oracle,$nom_table_mysql,$lib_query, $debug=0)
{ 
  echo "debug $nom_table_mysql $debug \n";
  //echo "\n\n".$lib_query."\n";
  set_time_limit(600); //10min
  //log_action( date("H:i:s")." $nom_table_mysql - Nbre de rangees:");
  $cursor = OCIParse($cnx_oracle, $lib_query);
  $result = @OCIExecute($cursor);
  //echo "resultat requete Oracle : $result \n";  

  $nrows = @OCIFetchStatement($cursor,$results);
  
   echo "$nrows rows fetched\n";
  
  //var_dump($results);
  
  if($cursor and $result)
  { $result = @OCIExecute($cursor); //log_action( "- Effectuees:");
    requete("lock tables $nom_table_mysql write"); $i=0;
    while (@OCIFetchInto($cursor, $values, OCI_NUM+OCI_RETURN_NULLS))
    { 
      $sql="'";$i++;
      foreach ($values as $cle => $valeur) {$valeur = str_replace(",",".",$valeur);$sql .= str_replace("'","\\'",$valeur)."','" ;}
      $sql = substr($sql,0,-2) ; /* Enleve les 2 caracteres à la fin */
	  $sql=utf8_encode($sql);
	    //echo "replace into $nom_table_mysql values($sql)\n";
	  requete("INSERT into $nom_table_mysql values($sql)", $debug);
    }
    requete("unlock tables");
  }
  else
  {die("Erreur Requete ORACLE \n$lib_query\n" .print_r(OCIError($cursor))."\n");
  }
}

//activation du debogage oracle


$cnx_mysql = connexion_mysql();

$cnx = connexion_oracle(); 

//requete ("delete from etat_import");
//Recuperation année universitaire
requete("delete from annee_uni");
//recup_simple($cnx,"annee_uni",query_annee_uni_apo(),1);
recup_simple($cnx,"annee_uni",query_annee_uni_apo());

$reqa="select cod_anu from annee_uni";
$resa=mysql_query($reqa);
while($enra=mysql_fetch_array($resa)){
	$an=$enra[0];
}

// zone de debug


requete("delete from ins_adm_etp");
recup_simple($cnx,"ins_adm_etp",query_ins_adm_etp($an));
//recup_simple($cnx,"ins_adm_etp",query_ins_adm_etp($an),1);
$anMoins=$an-1;
recup_simple($cnx,"ins_adm_etp",query_ins_adm_etp($anMoins));
//recup_simple($cnx,"ins_adm_etp",query_ins_adm_etp($anMoins),1);

requete("delete from etape");
recup_simple($cnx,"etape",query_etape($an));
//recup_simple($cnx,"etape",query_etape($an),1);
$req = "Update etape
Set cod_anu = '$an'";
$cur = mysql_query($req);

requete("delete from composante");
recup_simple($cnx,"composante",query_composante());
//recup_simple($cnx,"composante",query_composante(),1);

requete("delete from table_etape_apo");
recup_simple($cnx,"table_etape_apo",query_table_etape($an));
//recup_simple($cnx,"table_etape_apo",query_table_etape($an),1);

requete("delete from epreuve");
recup_simple($cnx,"epreuve",query_epreuve());
//recup_simple($cnx,"epreuve",query_epreuve(),1);

requete("delete from epr_sanctionne_elp");
recup_simple($cnx,"epr_sanctionne_elp",query_epr_sanctionne_elp());
//recup_simple($cnx,"epr_sanctionne_elp",query_epr_sanctionne_elp(),1);

requete("delete from table_etape_nbetu");
recup_simple($cnx,"table_etape_nbetu",query_table_etape_nbetu($an));
//recup_simple($cnx,"table_etape_nbetu",query_table_etape_nbetu($an),1);

requete("delete from table_elp");
recup_simple($cnx,"table_elp",query_table_elp());
//recup_simple($cnx,"table_elp",query_table_elp(),1);

requete("delete from table_elp_nbetu");
recup_simple($cnx,"table_elp_nbetu",query_table_elp_nbetu($an));
//recup_simple($cnx,"table_elp_nbetu",query_table_elp_nbetu($an),1);

#Pour la SE on ne peut que tout recuperer (impossible de qualifer cod_etp recursivement)
requete("delete from vet_regroupe_lse");
recup_simple($cnx,"vet_regroupe_lse",query_vet_regr_lse());
//recup_simple($cnx,"vet_regroupe_lse",query_vet_regr_lse(),1);

//requete("delete from elp_regroupe_elp");
//recup_simple($cnx,"elp_regroupe_elp",query_elp_regroupe_elp());

requete("delete from elp_regroupe_lse");
recup_simple($cnx,"elp_regroupe_lse",query_elp_regroupe_lse());
//recup_simple($cnx,"elp_regroupe_lse",query_elp_regroupe_lse(),1);

requete("delete from liste_elp");
recup_simple($cnx,"liste_elp",query_listes());
//recup_simple($cnx,"liste_elp",query_listes(),1);

requete("delete from lse_regroupe_elp");
recup_simple($cnx,"lse_regroupe_elp",query_lse_regroupe_elp());
//recup_simple($cnx,"lse_regroupe_elp",query_lse_regroupe_elp(),1);

//requete("delete from elp_charge_ens");
//recup_simple($cnx,"elp_charge_ens",query_table_elp_charge_ens($an));
//recup_simple($cnx,"elp_charge_ens",query_table_elp_charge_ens($an),1);

//modif oct. 2014 suite patch apogee 4.50
requete("delete from ELP_CHG_TYP_HEU");
recup_simple($cnx,"ELP_CHG_TYP_HEU",query_table_charge_typ_ens($an));
//recup_simple($cnx,"ELP_CHG_TYP_HEU",query_table_charge_typ_ens($an),1);

//modif oct. 2014 suite patch apogee 4.50
requete("delete from TYPE_HEURE");
recup_simple($cnx,"TYPE_HEURE",query_table_typ_heure());
//recup_simple($cnx,"TYPE_HEURE",query_table_typ_heure(),1);
 


requete("delete from ind_contrat_elp");
$req0="select cod_cmp from composante";
$res0=mysql_query($req0);
while($enr0=mysql_fetch_array($res0)){
	$comp = $enr0[0];
	recup_simple($cnx,"ind_contrat_elp",query_ind_contrat_elp1($comp,$an));
	//recup_simple($cnx,"ind_contrat_elp",query_ind_contrat_elp1($comp,$an),1);
};

OCILogoff($cnx);
mysql_close();

?>
