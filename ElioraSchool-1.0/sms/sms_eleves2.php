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
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : false);
/* Si on a une r�gion, on proc�de � la requ�te */
if(false !== $id_classe)
{
    /* C�ration de la requ�te pour avoir les d�partements de cette r�gion */
    $sql_eleves = "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login)";
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
        while($lig_class_tmp=mysqli_fetch_object($res_eleves)){
           $liste.="&nbsp;&nbsp;<input class='control-label' type='checkbox' name='eleve[]' value='$lig_class_tmp->nom,$lig_class_tmp->prenom,$lig_class_tmp->telephone'/>
		   <label for name='eleve[]'>$lig_class_tmp->nom  $lig_class_tmp->prenom</label><br/>";

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