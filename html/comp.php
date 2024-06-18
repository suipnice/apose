
<?php //copyright CRI Université Lille 2

#jcg
#init_session(); 
session_start();
/* #jcg */
#FrontController::dispatch();

header("Content-type: text/html; charset=utf-8");
include("frames_header.php");
echo "<link href='style_1.css' rel='stylesheet' type='text/css'>";
echo "<TITLE>SE Apogee</TITLE>";

?>

<script language='JavaScript' type='text/JavaScript'>
	function validate() {
		if (document.formul.Liste_Comp.value == '0')	{
			alert('Veuillez renseigner une composante.');
			document.formul.Liste_Comp.focus();
			return false;
		}	
	}
</script>
<style type='text/css'>
<!--
.Style3 {font-size: 0.6em}
-->
</style>


<?php
if ($_SESSION['authen'] =='ok'){
	
    
	echo "	
	<DIV id='conteneur'>
	<DIV id='centre'><h2> Consultation de la structure des enseignements APOGEE </h2></DIV>";
#JCG	
#	include "Connect.php";
	include "fonctions.php";
	
	connexionapo();
	
         
	echo "<DIV id='centre'><h1>Veuillez sélectionner une composante\n </h1>";
	$sql="select distinct lib_cmp, cod_cmp from composante order by lib_cmp";
	$res=mysql_query($sql);
	
	echo "<form name=formul action=se_apogee.php?action=Arbo method=post onsubmit='return validate();'> <select name=Liste_Comp>\n "	;
	echo "<option value=0 > Sélectionnez une Composante   </option>\n"	;
	while($enr=mysql_fetch_array($res)){
		echo "<option value=" , $enr[1], " >", $enr[0],"</option>\n";
	};//fin while
	echo "</select><input type=submit value=OK></form><br>\n";
	echo "</DIV>";
	echo "<DIV id='centre'>Pour <b>exporter</b> la structure des enseignements obtenue, il vous suffit 
	<ul><li>de sélectionner l'ensemble du document</li>
	<li>de le copier</li>
	<li>et de le coller dans un nouveau document Excel(Microsoft Office) ou Calc (OpenOffice)</li></ul><br></DIV>";
        echo "<DIV id='centre'><i> Données mises à jour 3 fois par jour à partir de la base de production d'APOGEE (APOPROD): 7H/12H/16H<br><br>
            Ne sont consultables que les étapes dont la structure d'enseignement a été modélisée dans APOGEE. <br>Plus d'informations:</i><br></DIV>";
        echo "<DIV id='centre'><p class='button'><a HREF='https://apose.unice.fr/exploitation/Guide_utilisateur.doc'>Guide utilisateur</a></p></DIV><br>";
	
	//echo "<br><A HREF='logout.php'> ->  <u>Se déconnecter </u></a><br><br>\n";
	echo "<span class='bouton'><A HREF='https://apoportail.unice.fr/index/accueil.php'>Apoportail</A></span>\n";
        
	echo "</DIV>";
        
}elseif($_SESSION['authen'] !='ok'){
	session_destroy();
	echo '<meta http-equiv="Refresh" content="0;url=index.php">';
}//fin if session=ok

?>
