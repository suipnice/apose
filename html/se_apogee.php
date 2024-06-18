<?php //copyright CRI Université Lille 2
session_start(); session_name();
header("Content-type: text/html; charset=utf-8");
include("frames_header.php");
echo "<link href='style_1.css' rel='stylesheet' type='text/css'>";
echo "<TITLE>SE Apogee</TITLE>";

?>

<?php

if ($_SESSION['authen'] =='ok'){

echo "<DIV id=conteneur>";
//header("Content-type: text/html; charset=utf-8");
#fonctions
#Recuperation des listes d'elp pour une version d'etape
function etp_lse($cod_etp_cible,$cod_vrs_vet)
{ //GLOBAL $apogee;
  $req=requete("select * from vet_regroupe_lse, liste_elp where cod_etp='$cod_etp_cible' and cod_vrs_vet='$cod_vrs_vet' and vet_regroupe_lse.cod_lse=liste_elp.cod_lse");
  while ($r=mysql_fetch_assoc($req)) $res[]=$r['cod_lse'];
  return $res;
}
#Recuperation des elp fils d'une liste d'une version d'etape
function cherche_elp_fils($nbchg,$entetes,$cod_lse,$niveau,$type="tableau",$numero=0,$lib_niveau_initial="",$res_tablo="")
{ //GLOBAL $apogee;
	$res="";
	$tabulation1="";
	$tabulation2="";
	$c1="";
	$c2="";
	$ladd=$_POST["ladd"];//Libelle Annexe Descriptive du Diplome
	$charge=$_POST["charge"];//charge d'enseignement
	$epr=$_POST["epr"];//affichage epreuve
	$ses=$_POST["cod_ses"];//affichage session

	if($type=="tableau"){
		for ($i=1;$i<$niveau;$i++){
			$tabulation1.="&nbsp;&nbsp;&nbsp;";$tabulation2.="";
		}
    	$t1="</td>\n<td>";
	}else{
  		$t1=""; $tabulation1=""; $res.="<ul type=none>"; $c1="["; $c2="]"; 
	}
	$etp=$_SESSION['cod_etp_cible'];
        $cod_vrs_vet=$_SESSION['cod_vrs_vet'];
	$cod_anu=$_POST['cod_anu'];
	$cycle=$_POST['cycle'];

	
	/*$req=requete("SELECT 
lse_regroupe_elp.cod_elp,
table_elp.lib_elp,
table_elp.cod_nel,
table_elp.cod_pel, 
table_elp.nbr_crd_elp,
table_elp_nbetu.cod_etp,
table_elp_nbetu.nb_etu_ip,
table_elp.lib_elp_lng,
table_elp.nbr_vol_elp,
table_elp.cod_vol_elp
FROM table_elp, lse_regroupe_elp, table_elp_nbetu
WHERE
lse_regroupe_elp.cod_lse = '$cod_lse'
AND table_elp.cod_elp = lse_regroupe_elp.cod_elp
AND table_elp.cod_elp = table_elp_nbetu.cod_elp
AND table_elp_nbetu.cod_etp = '$etp'
AND table_elp_nbetu.cod_vrs_etp = '$cod_vrs_vet'
AND table_elp_nbetu = '$cod_anu'
order by cod_elp");*/
        //echo $cod_vrs_vet;
        $req=requete("SELECT lse_regroupe_elp.cod_elp, table_elp.lib_elp, table_elp.cod_nel, table_elp.cod_pel, table_elp.nbr_crd_elp,
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
      /*  $req=requete("SELECT lse_regroupe_elp.cod_elp,table_elp.lib_elp,cod_nel,cod_pel, nbr_crd_elp,
	table_elp_nbetu.nb_etu_ip as nb_etu_ip,
	table_elp.lib_elp_lng,
        table_elp_nbetu.cod_etp
        // nbr_vol_elp,
        // cod_vol_elp 
	FROM table_elp
	INNER JOIN lse_regroupe_elp ON table_elp.cod_elp = lse_regroupe_elp.cod_elp
	LEFT OUTER JOIN table_elp_nbetu ON table_elp.cod_elp = table_elp_nbetu.cod_elp
                                      AND table_elp_nbetu.cod_anu = '$cod_anu'
                                      AND table_elp_nbetu.cod_etp = '$etp'
				      AND table_elp_nbetu.cod_vrs_etp = '$cod_vrs_vet'
	WHERE lse_regroupe_elp.cod_lse = '$cod_lse'
	order by cod_elp");*/
        
//vardump($req);

  $i=0;
 
  while ($r=mysql_fetch_assoc($req)){
      
$cod_elp=$r['cod_elp'];$cod_nel=$r['cod_nel'];$cod_pel=$r['cod_pel']; $nbr_crd_elp=$r['nbr_crd_elp'];
//Recuperation nb IP de l'elp
                // $reqnbip=requete("SELECT table_elp_nbetu.nb_etu_ip from table_elp_nbetu where table_elp_nbetu.cod_elp = '$cod_elp'" );
                 $reqnbip=requete("SELECT table_elp_nbetu.nb_etu_ip from table_elp_nbetu where table_elp_nbetu.cod_elp = '$cod_elp' and table_elp_nbetu.cod_etp = '$etp' and table_elp_nbetu.cod_vrs_etp = '$cod_vrs_vet'" );
                 while ($rnbip=mysql_fetch_assoc($reqnbip)){
                     $elp_nbetu=$rnbip['nb_etu_ip'];
                     //$elp_nbetu=$cod_vrs_vet;
                 }
// avec lib ADD 
    	if ($ladd=="Oui"){
		 $i++;
                                  
		 //Ne faire apparaitre l'ADD que pour les élements SEM et UE
		 if ($cod_nel=="UE" or $cod_nel=="SE"){
			 $lib_elp=$r['lib_elp']."<br><font size=-1,5>".$r['lib_elp_lng']."</font>";
                         
		 }else{
			 $lib_elp=$r['lib_elp'];
		 }
                 //Fin if nature=SEM ou UE
                 //mars 2014: suppression affichage volume d'heure
		 
                 //$elp_nbetu=$r['nb_etu_ip'];
                 		 /* if ($r['nbr_vol_elp']=='0'){
			$nbr_vol_elp="";
		 }else{
			$nbr_vol_elp=$r['nbr_vol_elp']." ".$r['cod_vol_elp'];
		 }*/
	}
// sans ADD
        else{	
		 //$i++;$cod_elp=$r['cod_elp']; $lib_elp=$r['lib_elp']; $cod_nel=$r['cod_nel']; $cod_pel=$r['cod_pel']; $nbr_crd_elp=$r['nbr_crd_elp'];$elp_nbetu=$r['nb_etu_ip'];
                 $i++;
                 $lib_elp=$r['lib_elp'];
                 
                 //$elp_nbetu=$r['nb_etu_ip'];
		/* if ($r['nbr_vol_elp']=='0'){
			$nbr_vol_elp="";
		 }else{
			$nbr_vol_elp=$r['nbr_vol_elp']." ".$r['cod_vol_elp'];
		 }*/
	}//Fin if ladd

//  	$lib_elp=utf8_encode($lib_elp);

    if($type<>"tableau") $tabulation1="";
    if($type=="tableau") $res.= "<tr><td>"; else $res.="<li>";
    
    #voir si fils
    $req2=requete("select t1.cod_lse,t2.cod_typ_lse,t1.nbr_min_elp_obl_chx,t1.nbr_max_elp_obl_chx
    from elp_regroupe_lse as t1
    inner join liste_elp as t2 on t1.cod_lse=t2.cod_lse WHERE cod_elp='$cod_elp'");
    $nb_fils = mysql_num_rows($req2);
    if($nb_fils>0) {$desc=1; $g1="<b><big>"; $g2="</big></b>";} else {$desc=0; $g1=""; $g2="";}
    // sous une autre forme if($numero and $nb_fils>0) $lib_niveau = $lib_niveau_initial."$i"; else $lib_niveau="";
    if($numero) $lib_niveau="$niveau.$i"; else $lib_niveau="";
    if($type=="tableau"){
	//Recup info Charge
		$affcharge='';
		if($charge=="Oui"){
		  
	//Recup info Charge
                $rescharge=mysql_query("select distinct ELP_CHG_TYP_HEU.COD_TYP_HEU, ELP_CHG_TYP_HEU.NB_HEU FROM ELP_CHG_TYP_HEU WHERE cod_anu = '$cod_anu' and cod_elp='$cod_elp' order by ELP_CHG_TYP_HEU.COD_TYP_HEU;");
	if(mysql_num_rows($rescharge)==0){
                            for ($n=0;$n<$nbchg;$n++){
                            $affcharge=$affcharge."<td></td>";}
	}else{
	
	  $index = 0;
	  while($enrcharge=mysql_fetch_array($rescharge)){
	    while (strcmp($enrcharge['COD_TYP_HEU'],$entetes[$index]) != 0) {
	      $affcharge=$affcharge."<td></td>";
	      $index = $index + 1;
	    }
	    $affcharge=$affcharge."<td>".$enrcharge['NB_HEU']."</td>";
	    $index = $index + 1;
	  }
	  for (;$index < $nbchg; $index++) { $affcharge=$affcharge."<td></td>";}
		}
}

    	// $res .= "$tabulation1 $lib_niveau $g1$lib_elp$g2 $tabulation2 $t1 $c1$cod_elp$c2 $t1 $cod_nel $t1 $cod_pel $t1 $nbr_crd_elp $t1 $elp_nbetu $t1 $nbr_vol_elp $affcharge";
        $res .= "$tabulation1 $lib_niveau $g1$lib_elp$g2 $tabulation2 $t1 $c1$cod_elp$c2 $t1 $cod_nel $t1 $cod_pel $t1 $nbr_crd_elp $t1 $elp_nbetu $affcharge";
    }else{
	    $res .= "$tabulation1 $lib_niveau $g1$lib_elp$g2";
	}
    $lib_liste_filles="";$t_liste_lse_filles=array();
    while ($r2=mysql_fetch_assoc($req2))
    { $t_liste_lse_filles[]=$r2;
      $lib_liste_filles.="[".implode("|",$r2)."]";
    }
    /*$decalage = str_repeat(" ",$niveau);*/
	$cod_elp_regroupe="";
    $l=array(/*$decalage,*/$niveau,$cod_lse,$cod_elp,$cod_nel,$nbr_crd_elp,$lib_elp,$cod_elp_regroupe,$nb_fils,$lib_liste_filles);
    $res_tablo[]=$l;
// desc = 1 si il y a des fils/filles	
    if($desc==1) 
	foreach($t_liste_lse_filles as $k=>$r2)
    //while ($r2=mysql_fetch_assoc($req2))
    { $max=$r2['nbr_max_elp_obl_chx']; $min=$r2['nbr_min_elp_obl_chx']; $cod_lse_aff=$r2['cod_lse'];
      if($type=="tableau") $res.=" $t1<i>$cod_lse_aff</i>$t1";
      if ($max>1) $pluriel="s"; else $pluriel="";
      $card="&nbsp;";
      if ($min and $min==$max)$card=" $max élèment$pluriel à choisir";
      if ($min and $min<$max)$card=" de $min à $max élèment$pluriel à choisir";
      $res.=$card;
      if($numero and $type<>"tableau") $res.=":";
      if($type=="tableau") $res.= "</td></tr>"; else $res.="</li>";
      
      // AFFICHAGE SESSIONS  paramètres : cod_ses=1 (session 1) cod_ses=2 (session 2) cod_ses=3 (session  unique) cod_ses=4 (toutes les sessions)
		if($_SESSION['epr']=='Oui'){
			if($_SESSION['cod_ses']=='4'){//Affichage de toutes les sessions
		   		$critsess='';
			}else{
				$critsess="and cod_ses='".$_SESSION['cod_ses']."'";
			}
                        
		   //Recherche d'epreuve pour l'element
			$reqepr=mysql_query("select epreuve.cod_epr, epreuve.lib_epr, epreuve.cod_nep, epreuve.cod_tep, epr_sanctionne_elp.cod_ses from epr_sanctionne_elp, epreuve
			where epr_sanctionne_elp.cod_epr=epreuve.cod_epr
			".$critsess."
			and epr_sanctionne_elp.cod_elp='".$cod_elp."'
			order by epr_sanctionne_elp.cod_ses, epreuve.lib_epr, epreuve.cod_epr");
  			while ($repr=mysql_fetch_array($reqepr)){
				if($repr[4]=='1'){
					$bgcolor="#D7E8FE";
				}else{
					if($repr[4]=='2'){
						$bgcolor="#B7F9B9";
					}else{
						if($repr[4]=='0'){
							$bgcolor="#F9B7E5";
						}
					}
				}
				$res.="<tr><td bgcolor=".$bgcolor.">".$tabulation1.$tabulation1."&nbsp;&nbsp;&nbsp;&nbsp;".$repr[1]."</td><td bgcolor=".$bgcolor.">".$repr[0]."</td><td bgcolor=".$bgcolor.">".$repr[2]."</td><td bgcolor=".$bgcolor.">".$repr[3]."</td><td bgcolor=".$bgcolor.">".$repr[4]."</td></tr>";
                                
			}
		}//Fin If Affichage des sessions
  // montage pdf-csv
      if($type=="webip")
        $res_tablo = cherche_elp_fils($nbchg,$entetes,$r2['cod_lse'],$niveau+1,$type,$numero,$lib_niveau,$res_tablo);
      else
        $res.= cherche_elp_fils($nbchg,$entetes,$r2['cod_lse'],$niveau+1,$type,$numero,$lib_niveau)."";
     }
     if($desc==0) {if($type=="tableau") $res.= "$t1&nbsp;$t1&nbsp;</td></tr>"; else $res.="";
		if($_SESSION['epr']=='Oui'){
			if($_SESSION['cod_ses']=='4'){//Affichage de toutes les sessions
		   		$critsess='';
			}else{
				$critsess="and epr_sanctionne_elp.cod_ses='".$_SESSION['cod_ses']."'";
			}		
                        //Recherche d'epreuve pour l'element
			$reqepr=mysql_query("select epreuve.cod_epr, epreuve.lib_epr, epreuve.cod_nep, epreuve.cod_tep, epr_sanctionne_elp.cod_ses from epr_sanctionne_elp, epreuve
			where epr_sanctionne_elp.cod_epr=epreuve.cod_epr
			".$critsess."
			and epr_sanctionne_elp.cod_elp='".$cod_elp."'
			order by epr_sanctionne_elp.cod_ses, epreuve.lib_epr, epreuve.cod_epr");
			
			while ($repr=mysql_fetch_array($reqepr)){
				if($repr[4]=='1'){
					$bgcolor="#D7E8FE";
				}else{
					if($repr[4]=='2'){
						$bgcolor="#B7F9B9";
					}else{
						if($repr[4]=='0'){
							$bgcolor="#F9B7E5";
						}
					}
				}
				$res.="<tr><td bgcolor=".$bgcolor.">".$tabulation1.$tabulation1."&nbsp;&nbsp;&nbsp;&nbsp;".$repr[1]."</td><td bgcolor=".$bgcolor.">".$repr[0]."</td><td bgcolor=".$bgcolor.">".$repr[2]."</td><td bgcolor=".$bgcolor.">".$repr[3]."</td><td bgcolor=".$bgcolor.">".$repr[4]."</td></tr>";
			}
		}//Fin If Affichage des sessions
	 }

   }
   if($type=="webip") return $res_tablo;
   if($type=="tableau") $res.=""; else $res.="</ul>";
   return $res;
   mysql_close;
}


function requete($libreq,$base=MYSQL_BASE_DATAS){
#JCG
# MYSQL_SELECT_DB ("se_apogee") or die("$base n'est pas une base connue\n"); # selection de la base
$req = MYSQL_QUERY ($libreq);
IF (!MYSQL_ERROR())
 RETURN $req;
 ELSE {
  $info_erreur= "<pre>\r\nErreur requête\r\n";
  $info_erreur.=$libreq."\r\n<br>".MYSQL_ERROR()."<br></pre>\n"; #pour le debug
  $info_erreur.= print_r(debug_backtrace(),true);
  echo $info_erreur;
 // ERROR_LOG("ERREUR REQUETE \n$info_erreur",1,"");
  DIE(" UNE ERREUR A ETE DETECTEE ET SIGNALEE PAR EMAIL. MERCI DE RENOUVELER VOTRE DEMANDE ULTERIEUREMENT.");
  RETURN FALSE;
 }
} 

function connexion_mysql($base_mysql,$hote_mysql=HOTE_MYSQL,$user_mysql=USER_MYSQL,$passwd_mysql=PASSWD_MYSQL)
{
/*$hote_mysql="localhost";
$user_mysql="root";
$base_mysql="apogee";
$cnxmysql=mysql_connect($hote_mysql, $user_mysql) or die ("Connexion MySQL impossible ".mysql_error()."\n") ;
 $bdd = mysql_select_db($base_mysql,$cnxmysql) or die ("Base de donnees ".$base." INTROUVABLE \n"); 
 return $cnxmysql;*/
 include "fonctions.php";
connexionapo();

}

#actions
//header("Content-type: text/html; charset=utf-8");
//$annee="2006";

$nom_fic_param = "param.php";
if(!is_file($nom_fic_param)) die("$nom_fic_param - Fichier de parametres manquants\n"); else include($nom_fic_param);

$cnx_mysql = connexion_mysql(MYSQL_BASE_APOGEE);

$action=$_GET["action"];

$pdf="";
$res="";
$res2a="";//Premiere partie du document
$res0="";//Affichage du nb etape
$res2="";
$res3="";//Liste des Etapes non Modélisées dans Apogée
$res4="";//Fin de document
$adresse="se_apogee.php";
$cpt=0;


if ($action=="Arbo"){ 
	$comp=$_POST["Liste_Comp"];
        //Recupération de l'année universitaire en cours
	$req0="select cod_anu from annee_uni";
	$res0=mysql_query($req0);
	while($enr0=mysql_fetch_array($res0)){
		$cod_anu=$enr0[0];
	}//Fin recup annee univ
	$_POST['cod_anu']= $cod_anu; #JCG : pas passe en argument
	/*$cod_anu=$_POST['cod_anu']; */
	//$cycle=$_POST['cycle'];
	//echo $cod_anu;
	//$res2.="<form method=\"post\" action=\"comp.php\"><input type=\"submit\" value=\"Retour\"></form>\n";
	
	$res2a.="<span class='bouton'><A HREF=comp.php>retour</A></span>\n";
	//$res2.="<A HREF=javascript:self.history.back()>retour</A>";
	$res2a.="<DIV id='centre'><h2> Consultation de la structure des enseignements  ";
	//Affichage du libellé de la composante
	$req=requete("select lib_cmp from composante where cod_cmp='$comp'");
	while ($row = mysql_fetch_row($req)) {
		$lib_comp=$row[0];
	}//fin while lib composante
	$res2a.="$lib_comp</h2>\n";
        $res2a.="<p class = 'a'>(source APOGEE)</p></div><br><br>";
	
#JCG	$sqllistan="select cod_anu from annee_uni_apo_tt order by cod_anu desc";
	/*$sqllistan="select cod_anu from annee_uni order by cod_anu desc";
	$reslistan=mysql_query($sqllistan);

	$res2a.= "<form name=list_an action=?action=Arbo&list=ok method=post>";
	$res2a.= "Sélectionnez l'année universitaire : <select name=cod_anu OnChange=\"submit();\">";
	while($enrlistan=mysql_fetch_array($reslistan)){
		if($enrlistan[0]==$_POST['cod_anu']){
			$res2a.= "<option value='$enrlistan[0]' selected>".$enrlistan[0]."</option>\n";
		}else{
			$res2a.= "<option value='$enrlistan[0]'>".$enrlistan[0]."</option>\n";
		}
	}//fin While Recup Annee pour liste*/
	//$res2a.= "</select>";
        $res2a.="Année universitaire: " .$cod_anu. "\n";
	$res2a.= "<input type=hidden name=Liste_Comp value='".$_POST['Liste_Comp']."'>";
	$res2a.= "</form>";
	
	
	  /*if(is_user_admin($id_util)) $jointure="left"; else */
        $jointure="inner";
	/*  $req=requete("select distinct t2.lib_etp,t1.cod_etp,t1.cod_vrs_vet from se_apogee.vet_regroupe_lse as t1
	  $jointure join etape as t2 on t1.cod_etp=t2.cod_etp and t1.cod_vrs_vet=t2.cod_vrs_vet
	  where cod_cmp='$comp'
	  order by t1.cod_etp");
	
	  $req=requete("select distinct t2.lib_etp,t1.cod_etp,t1.cod_vrs_vet ,t3.nb_etu
	  from se_apogee.vet_regroupe_lse as t1 
	  inner join etape as t2 on t1.cod_etp=t2.cod_etp and t1.cod_vrs_vet=t2.cod_vrs_vet
	  left join table_etape_nbetu as t3 on t2.cod_anu=t3.cod_anu and t2.cod_etp=t3.cod_etp and t2.cod_vrs_vet=t3.cod_vrs_vet
	  where cod_cmp='$comp'
	  order by t1.cod_etp, t1.cod_vrs_vet");
	*/
//Memorisation et Prise en compte du choix des boutons radios lors du retour
	if (isset($_POST['numero'])){
		$radio_numero=$_POST['numero'];
	}else{
		$radio_numero='0';
	}
	if (isset($_POST['ladd'])){
		$radio_ladd=$_POST['ladd'];
	}else{
		$radio_ladd='Non';
	}
	if (isset($_POST['charge'])){
		$radio_charge=$_POST['charge'];
	}else{
		$radio_charge='Non';
	}
	if (isset($_SESSION['epr'])){
		$radio_epr=$_SESSION['epr'];
	}else{
		$radio_epr='Non';
	}
	if (isset($_SESSION['cod_ses'])){
		$radio_ses=$_SESSION['cod_ses'];
	}else{
		$radio_ses='1';
	}
//echo "Session ",$radio_ses;
		$res2.="<form method=\"post\" action=\"{$adresse}?action=Arbo2\"><span class='tablo'><table>";
//		$res2.="<tr><td width=400>TYPE D'EDITION:</td><td><input type=\"radio\" name=\"type\" value=\"normale\" ></td><td width=60>Normale</td>\n";
//		$res2.="<td><input type=\"radio\" name=\"type\" value=\"tableau\" checked></td><td width=60>Tableau</td>\n";
		$res2.="<input type=\"hidden\" name=\"type\" value=\"tableau\" >\n";
		$res2.="<tr><td width=400>INDICATEUR NUMERIQUE DE L'ARBORESENCE :</TD>\n";
if($radio_numero=='1'){
		$res2.="<td><input type=\"radio\" name=\"numero\" value=\"1\" checked></td><td width=60>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"numero\" value=\"0\"></td><td width=60>Non</td></tr>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"numero\" value=\"1\"></td><td width=60>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"numero\" value=\"0\" checked></td><td width=60>Non</td></tr>\n";
}
		$res2.="<tr><td>AVEC LES LIBELLES DE L'ANNEXE DESCRIPTIVE DU DIPLOME :</TD>\n";
if($radio_ladd=='Oui'){
		$res2.="<td><input type=\"radio\" name=\"ladd\" value=\"Oui\" checked></td><td>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"ladd\" value=\"Non\" ></td><td>Non</td></tr>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"ladd\" value=\"Oui\"></td><td>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"ladd\" value=\"Non\" checked></td><td>Non</td></tr>\n";
}
		$res2.="<tr><td>AVEC LES CHARGES D'ENSEIGNEMENTS :</TD>\n";
if($radio_charge=='Oui'){
		$res2.="<td><input type=\"radio\" name=\"charge\" value=\"Oui\" checked></td><td>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"charge\" value=\"Non\"></td><td>Non</td></tr>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"charge\" value=\"Oui\"></td><td>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"charge\" value=\"Non\" checked></td><td>Non</td></tr>\n";
}
		$res2.="<tr><td>AVEC LES INFORMATIONS EPREUVES :</TD>\n";
if($radio_epr=='Oui'){
		$res2.="<td><input type=\"radio\" name=\"epr\" value=\"Oui\" onclick=\"document.getElementById('epr').style.display='block';\"; checked></td><td>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"epr\" value=\"Non\" onclick=\"document.getElementById('epr').style.display='none';\"></td><td>Non</td></tr>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"epr\" value=\"Oui\" onclick=\"document.getElementById('epr').style.display='block';\"></td><td>Oui</td>\n";
		$res2.="<td><input type=\"radio\" name=\"epr\" value=\"Non\" onclick=\"document.getElementById('epr').style.display='none';\"; checked></td><td>Non</td></tr>\n";
}
		$res2.="</table>\n";
if($radio_epr=='Oui'){
		$res2.="<div id=epr style=\"display:block\">";
}else{
		$res2.="<div id=epr style=\"display:none\">";
}
		$res2.="<table><tr><td width=400 bgcolor=#D5D5D5 rowspan=2>SESSION :</TD>\n";
if($radio_ses=='1'){
		$res2.="<td><input type=\"radio\" name=\"cod_ses\" value=\"1\" checked></td><td width=60 bgcolor=#D7E8FE>1</td>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"cod_ses\" value=\"1\"></td><td width=60 bgcolor=#D7E8FE>1</td>\n";
}
if($radio_ses=='2'){
		$res2.="<td><input type=\"radio\" name=\"cod_ses\" value=\"2\" checked></td><td width=60 bgcolor=#B7F9B9>2</td>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"cod_ses\" value=\"2\"></td><td width=60 bgcolor=#B7F9B9>2</td>\n";
}
if($radio_ses=='0'){
		$res2.="<td><input type=\"radio\" name=\"cod_ses\" value=\"0\" checked></td><td width=60 bgcolor=#F9B7E5>Unique</td></tr>\n";
}else{
		$res2.="<td><input type=\"radio\" name=\"cod_ses\" value=\"0\"></td><td width=60 bgcolor=#F9B7E5>Unique</td></tr>\n";
}
if($radio_ses=='4'){
		$res2.="<tr><td><input type=\"radio\" name=\"cod_ses\" value=\"4\" checked></td><td colspan=5>Toutes les sessions</td></tr></table></div>\n";
}else{				
		$res2.="<tr><td><input type=\"radio\" name=\"cod_ses\" value=\"4\"></td><td colspan=5>Toutes les sessions</td></tr></table></div>\n";
}		
		$res2.="<hr><h3>Liste des années d'études disponibles sur APOGEE:</h3><table>\n";

		$res3.="<hr><h3>Liste des années d'études non modélisées sur APOGEE:</h3><ul>
		<table border=1 cellspacing=0 cellpadding=0>\n";
	$rescycle=mysql_query("SELECT distinct(etape.cod_cyc) from etape 
	where etape.cod_cmp='".$_POST['Liste_Comp']."' 
	AND etape.DAA_DEB_RCT_VET<='".$_POST['cod_anu']."' 
	AND etape.DAA_FIN_RCT_VET>='".$_POST['cod_anu']."'
	order by cod_cyc");

	while($enrcycle=mysql_fetch_array($rescycle)){
		$req=requete("SELECT etape.cod_etp,etape.cod_vrs_vet,etape.lic_etp,etape.lib_etp, etape.cod_cyc, etape.cod_cmp, etape.cod_anu,cod_lse,DAA_DEB_RCT_VET,DAA_FIN_RCT_VET
			FROM etape 
			LEFT JOIN vet_regroupe_lse 
			ON (etape.cod_vrs_vet = vet_regroupe_lse.cod_vrs_vet) 
			AND (etape.cod_etp = vet_regroupe_lse.cod_etp)
			GROUP BY etape.cod_etp, etape.cod_vrs_vet, etape.lic_etp, etape.lib_etp, etape.cod_cyc, etape.cod_cmp, etape.cod_anu, etape.DAA_DEB_RCT_VET, etape.DAA_FIN_RCT_VET
			HAVING etape.cod_cmp='".$_POST['Liste_Comp']."' 
			AND etape.DAA_DEB_RCT_VET<='".$_POST['cod_anu']."' 
			AND etape.DAA_FIN_RCT_VET>='".$_POST['cod_anu']."'
			and etape.cod_cyc='".$enrcycle[0]."'
			ORDER BY etape.lib_etp");

		$res2.="<tr><td colspan=2 BGCOLOR=#85F7E8><a name=cyc".$enrcycle[0]."></a><font size=+0.5>Cycle ".$enrcycle[0]."</font></td></tr>\n";
		$res3.="<tr><td colspan=2 BGCOLOR=#E1B5F7><font size=+0.5>Cycle ".$enrcycle[0]."</font></td></tr>\n";
//echo $enrcycle[0];

		while($r=mysql_fetch_assoc($req)){
			$cpt=$cpt+1;
			$cod_etp=$r['cod_etp']; $lib_etp=$r['lib_etp']; $cod_vrs_vet=$r['cod_vrs_vet'];$deb_rec=$r['DAA_DEB_RCT_VET']; $fin_rec=$r['DAA_FIN_RCT_VET']; 
			if($r['cod_lse']!=''){
				$res2.="<tr><td><a name=$cod_etp$cod_vrs_vet></a><input type=\"radio\" name=\"RefEtp\" value=\"$cod_etp|$cod_vrs_vet|$comp|$cod_anu|".$enrcycle[0]."\" OnClick=\"submit();\"></td>\n";
				$res2.="<input type=hidden name=cod_anu value=$cod_anu>\n";
				$res2.="<input type=hidden name=cycle value=".$enrcycle[0].">\n";
				$res2.="<td> $lib_etp ($cod_etp / $cod_vrs_vet) <b>-- Recr. $deb_rec/$fin_rec</b>";
				//-- Nombre d'étudiants inscrits : $nb_etu</td></tr>\n";
			}else{
				$res3.="<tr>";
				$res3.="<td> $lib_etp ($cod_etp / $cod_vrs_vet)<b> -- Recr. $deb_rec/$fin_rec</b>";
				// -- Nombre d'étudiants inscrits : $nb_etu</td></tr>\n";
			}
			//Recup nb etu
			$sqletu="select nb_etu from table_etape_apo
			where table_etape_apo.cod_etp='$cod_etp'
			and table_etape_apo.cod_vrs_vet='$cod_vrs_vet'
			and cod_anu='".$_POST['cod_anu']."'
			and cod_cmp='".$_POST['Liste_Comp']."'";
			//echo $sqletu;
			$resetu=mysql_query($sqletu);
			if(mysql_num_rows($resetu)==0){
				echo "</td></tr>";
			}else{
				while($enretu=mysql_fetch_array($resetu)){
					if($r['cod_lse']!=''){
						$res2.=" -- Nombre d'étudiants inscrits : <b> $enretu[0]</b></td></tr>\n";
					}else{
						$res3.=" -- Nombre d'étudiants inscrits :<b> $enretu[0]</b></td></tr>\n";
					}
				}
			}
	
		}//fin while liste etape
	}//Fin While recup Cycle
	$res0.= "<h3>".$cpt." Etapes </h3>\n";
	$res2.="</table></span></form>\n";
	$res3.="</table></ul><br>\n";
	$cpt=0;
	//en remplacement de $res2.="</table></span><input type=\"submit\" value=\"Valider\"></form>\n";
	//  echo $res2;
	$res4.="<span class='bouton'><A HREF=comp.php>retour</A></span><br>";
	//$res4.="<br><A HREF='logout.php> ->  <u>Se déconnecter </u></a><br><br>\n";
}

//fin if action=Arbo





if (!empty($_POST) and $action=="Arbo2") {

	$RefEtp=$_POST["RefEtp"];
	$type=$_POST["type"];
	$numero=$_POST["numero"];
	$ladd=$_POST["ladd"];
	$charge=$_POST["charge"];
	$_SESSION['cod_ses']=$_POST["cod_ses"];
	$_SESSION['epr']=$_POST["epr"];
	$cod_anu=$_POST["cod_anu"];
	$cycle=$_POST["cycle"];
	}


if ($action=="Arbo2" and $RefEtp)
{ if($RefEtp)list($cod_etp_cible,$cod_vrs_vet,$comp,$cod_anu,$cycle) = explode("|",$RefEtp);
  if($type=="webip")
  {
    $pdf=0; $nom_fic = CHEMIN_PUBLIC."pdf/".$cod_etp_cible.".csv";
    $fic=fopen($nom_fic,"w");
  }
  
  
  //echo $cycle;
  //$cod_etp_cible="DLMD03";
  $_SESSION['cod_etp_cible']=$cod_etp_cible;
  $_SESSION['cod_vrs_vet']=$cod_vrs_vet;
  $r=mysql_fetch_assoc(requete("select * from etape where cod_etp='$cod_etp_cible' and cod_vrs_vet='$cod_vrs_vet'"));
  $lib_etp=$r['lib_etp'];
 $res2.="<form method=\"post\" action=\"{$adresse}?action=Arbo#cyc$cycle\">
 <input type=\"hidden\" name=\"Liste_Comp\" value=\"$comp\">
 <input type=\"hidden\" name=\"cod_anu\" value=\"$cod_anu\">
 <input type=\"hidden\" name=\"numero\" value=\"$numero\">
 <input type=\"hidden\" name=\"ladd\" value=\"$ladd\">
 <input type=\"hidden\" name=\"charge\" value=\"$charge\">
 <input type=\"submit\" value=\"Retour\"></form>\n";
  $res2.="<table border=0 cellspacing=0 cellpadding=0 width=90%><tr><td rowspan=3>
  <h3>Etat de la modélisation APOGEE<br>$cod_etp_cible $cod_vrs_vet -- $lib_etp <br>-- $cod_anu --</h3></td>";
  	if($_SESSION['epr']=='Oui'){
 		if($_SESSION['cod_ses']=='1'){
			$bgcolor="#D7E8FE";
			$lib_ses=$_SESSION['cod_ses'];
			$res2.="<td bgcolor=".$bgcolor." align=center><h3>Epreuve SESSION ".$lib_ses."</h3></td>";
		}else{
			if($_SESSION['cod_ses']=='2'){
				$bgcolor="#B7F9B9";
				$lib_ses=$_SESSION['cod_ses'];
				$res2.="<td bgcolor=".$bgcolor." align=center><h3>Epreuve SESSION ".$lib_ses."</h3></td>";
			}else{
				if($_SESSION['cod_ses']=='0'){
					$bgcolor="#F9B7E5";
					$lib_ses='Unique';
					$res2.="<td bgcolor=".$bgcolor." align=center><h3>Epreuve SESSION ".$lib_ses."</h3></td>";
				}else{
					if($_SESSION['cod_ses']=='4'){
						$lib_ses='Unique';
						$res2.="<td align=center bgcolor=#D7E8FE><h3x>Epreuve SESSION 1</h3x></td></tr>
						<tr><td align=center bgcolor=#B7F9B9><h3x>Epreuve SESSION 2</h3x></td></tr>
						<tr><td align=center bgcolor=#F9B7E5><h3x>Epreuve SESSION Unique</h3x></td>\n";
					}
				}
			}
		}
  }
  $res2.="</tr></table>";

  $t_etp_lse = etp_lse($cod_etp_cible,$cod_vrs_vet);
  
  //  $res2.="<a href=\"{$adresse}?action=Arbo2&numero=$numero&type=$type&cod_etp_cible=$cod_etp_cible&cod_vrs_vet=$cod_vrs_vet&pdf=1\">[Impression]</a>";
  $res2.="<form method=\"post\" action=\"{$adresse}?action=Arbo#cyc$cycle\">
  <input type=\"hidden\" name=\"Liste_Comp\" value=\"$comp\">
  <input type=\"hidden\" name=\"cod_anu\" value=\"$cod_anu\">
  <input type=\"hidden\" name=\"numero\" value=\"$numero\">
  <input type=\"hidden\" name=\"ladd\" value=\"$ladd\">
  <input type=\"hidden\" name=\"charge\" value=\"$charge\">";
  $res_tablo[]=array(/*"tabulation",*/"niveau","cod_lse","cod_elp","cod_nel","nbr_crd_elp","lib_elp","vol_hor","coeff","cod_elp_regroupe","nb_fils","lib_liste_filles");
  if($charge=='Oui'){
            $resentetes=mysql_query("select TYPE_HEURE.COD_TYP_HEU, TYPE_HEURE.NUM_ORD_TYP_HEU from TYPE_HEURE;");
            $nbchg=mysql_num_rows($resentetes);            
            if (mysql_num_rows($resentetes) == 0 ){
           	$libcharge="";   
           }else {
              	$index=0;
              	while($rowchg= mysql_fetch_array($resentetes)){
                   $libcharge.="<td BGCOLOR=#cccccc align=\"center\"><b>Charges Ens<br>".$rowchg['COD_TYP_HEU']."</b></td>";
                   $entetes[$index] = $rowchg['COD_TYP_HEU'];
                   $index=$index+1;
                   }
        	}
}else{
            $libcharge="";
            $nbchg=0;
  }
  
  foreach ($t_etp_lse as $k=>$cod_lse)
  { $niveau=1;
  $req=requete("select liste_elp.cod_lse, liste_elp.lib_lse, vet_regroupe_lse.nbr_max_elp_obl_chx, vet_regroupe_lse.nbr_min_elp_obl_chx
FROM vet_regroupe_lse,liste_elp 
where vet_regroupe_lse.cod_lse = liste_elp.cod_lse 
and liste_elp.cod_lse='$cod_lse'");
  while ($row = mysql_fetch_row($req)) {
   $lib_liste=$row[1]; $max=$row[2];$min=$row[3];
   	if (!empty($max)){
   		$lib_liste.=" (Liste à choix $max $min)";
   	}
   }
    $res2.="<hr>$cod_lse : $lib_liste <br>\n";
    if($type=="tableau")
    {
      $res2.="<table border=1 align=\"center\">\n";
      if($_SESSION['epr']=='Oui'){
	  	$libses="<br>/Session";
	}else{
		$libses="";
	} 
       // suppression colonne Volume (reunion reso sco mars 2014)
       //$res2.="<tr class=bg2><td BGCOLOR=#cccccc align=\"center\"><b>Libellé</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Code</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Nature</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Période</b></td><td BGCOLOR=#cccccc align=\"center\"><b>ECTS$libses</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Nb IP</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Volume</b></td>".$libcharge."<td BGCOLOR=#cccccc align=\"center\"><b>Code liste</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Observations</b></td></tr>\n";
       $res2.="<tr class=bg2><td BGCOLOR=#cccccc align=\"center\"><b>Libellé</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Code</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Nature</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Période</b></td><td BGCOLOR=#cccccc align=\"center\"><b>ECTS$libses</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Nb IP</b></td>".$libcharge."<td BGCOLOR=#cccccc align=\"center\"><b>Code liste</b></td><td BGCOLOR=#cccccc align=\"center\"><b>Observations</b></td></tr>\n";

    }
    $res_tmp = cherche_elp_fils($nbchg,$entetes,$cod_lse,$niveau,$type,$numero,"",$res_tablo);
    if($type=="webip")
    {  $res_tablo = $res_tmp;
    }
    else $res2.=$res_tmp;
    if($type=="tableau") $res2.= "</table>";
   }
   if($type=="webip")
   {  foreach($res_tablo as $k=>$l) fputs($fic,'"'.implode('";"',$l).'"'."\r\n");
      $res2.="<pre>".file_get_contents($nom_fic)."</pre><hr>".ahref("Télécharger le fichier texte",URL_INTRADROIT."pdf/$cod_etp_cible.csv");
      fclose($fic);
   }
   if (!$pdf)
   {  //$res2.="<br><a href=\"?action=Arbo\">[RETOUR]</a>";
      //if($type<>"webip") $res2.="<a href=\"{$adresse}?action=Arbo2&numero=$numero&type=$type&cod_etp_cible=$cod_etp_cible&cod_vrs_vet=$cod_vrs_vet&pdf=1\">[Impression]</a>";
   }
$res2.="<input type=\"submit\" value=\"Retour\">";
$res2.="</form>";


}

//Fin If Arbo2  -- fin affichage detaillee d'une etape


echo $res2a;
//echo $res0;
echo $res2;
echo $res;
echo $res3;
echo $res4;
echo "</div>";
}elseif($_SESSION['authen'] !='ok'){
	session_destroy();
	echo '<meta http-equiv="Refresh" content="0;url=index.php">';
}//fin if session=ok

?>
