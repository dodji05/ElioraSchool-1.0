<?php
require_once("../lib/initialisations.inc.php");
// Resume session
$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
    header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
    die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
}


/* On r�cup�re l'identifiant de la r�gion choisie. */
$id_prof=isset($_POST['id_prof']) ? $_POST['id_prof'] : (isset($_GET['id_prof']) ? $_GET['id_prof'] : false);
/* Si on a une r�gion, on proc�de � la requ�te */
if(false !== $id_prof)
{
    /* Cr�ation de la requ�te pour avoir les d�partements de cette r�gion */
    //$sql_eleves = "SELECT DISTINCT e.tarifHoraire FROM  etg_salaire_prof e WHERE (e.id_professeur = '$id_prof')";
	$sql_eleves ="SELECT g.description description, g.id groupe_id, jgp.*,c.classe classe, c.id classe_id FROM j_groupes_professeurs jgp, j_groupes_classes jgc, groupes g, classes c WHERE (jgp.login = '$id_prof' and g.id = jgp.id_groupe and jgc.id_groupe = jgp.id_groupe and c.id = jgc.id_classe) order by c.classe";
	 $res_eleves = mysqli_query($GLOBALS["mysqli"], $sql_eleves);
   /* $connexion = mysql_connect($serveur, $admin, $mdp);
    mysql_select_db($base, $connexion);
    $rech_dept = mysql_query($sql2, $connexion);
    /* Un petit compteur pour les d�partements */
    /*$nd = 0;*/
    /* On cr�e deux tableaux pour les num�ros et les noms des d�partements */
    /*$code_dept = array();
    $nom_dept = array();*/
    /* On va mettre les num�ros et noms des d�partements dans les deux tableaux */
   
    /* Maintenant on peut construire la liste d�roulante */
    $liste = "";
	//$liste .="<fieldset><legend>La liste des �leves</legend><br />";
    //$liste .= '<select name="departement" id="departement">'."\n";
	if(mysqli_num_rows($res_eleves)>0){
		$oo=1;
        while($lig_class_tmp=mysqli_fetch_object($res_eleves)){
          /* $liste.="&nbsp;&nbsp;<input class='control-label' type='checkbox' name='eleve[]' value='$lig_class_tmp->nom,$lig_class_tmp->prenom,$lig_class_tmp->telephone'/>$lig_class_tmp->nom  $lig_class_tmp->prenom<br/>";*/
		  $liste .="<input type ='hidden' name=group_id[] value='$lig_class_tmp->groupe_id'/>";
		  $liste .= "<label>Taux horaire pour la classe de $lig_class_tmp->classe ($lig_class_tmp->description)</label>";
		  $liste .="<input type ='number' name=TH[] id='TH[$oo]' size='5' value='$lig_class_tmp->TauxHoraire'/>";
		  $liste .="<label>Nombre d heure effectu�e </label>";
		  $liste .="<input type ='number' name='NBE[]' id='NBE[$oo]' size='5' value='' onblur=\"calcul($oo);\"/>";
		  $liste .="<label>Total </label>";
		  $liste .= "<input type ='number' name='TOTAL[]' id='TOTAL[$oo]' size='5' value='0' onchange=\"calcultotal($oo);\"/><br/>";
		 // $liste .= $lig_class_tmp->tarifHoraire;
		 $oo++;

        }
    }
	else{
		echo"<h2> Aucun eleve dans cette classe</h2>";
	}
   // $liste .="</fieldset>";
    /* Un petit coup de balai */
   
    /* Affichage de la liste d�roulante */
    echo($liste);
}
/* Sinon on retourne un message d'erreur */
else
{
    echo("<p>Une erreur s'est produite. La r�gion s�lectionn�e comporte une donn�e invalide.</p>\n");
}
?>