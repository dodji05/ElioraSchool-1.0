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


/* On récupère l'identifiant de la région choisie. */
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : false);
/* Si on a une région, on procède à la requête */
if(false !== $id_classe)
{
    /* Cération de la requête pour avoir les départements de cette région */
    $sql_eleves = "SELECT DISTINCT e.* FROM eleves e, j_eleves_classes c WHERE (c.id_classe = '$id_classe' AND e.login = c.login)";
	 $res_eleves = mysqli_query($GLOBALS["mysqli"], $sql_eleves);
   /* $connexion = mysql_connect($serveur, $admin, $mdp);
    mysql_select_db($base, $connexion);
    $rech_dept = mysql_query($sql2, $connexion);
    /* Un petit compteur pour les départements */
    /*$nd = 0;*/
    /* On crée deux tableaux pour les numéros et les noms des départements */
    /*$code_dept = array();
    $nom_dept = array();*/
    /* On va mettre les numéros et noms des départements dans les deux tableaux */
   
    /* Maintenant on peut construire la liste déroulante */
    $liste = "";
	//$liste .="<fieldset><legend>La liste des éleves</legend><br />";
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
   
    /* Affichage de la liste déroulante */
    echo($liste);
}
/* Sinon on retourne un message d'erreur */
else
{
    echo("<p>Une erreur s'est produite. La région sélectionnée comporte une donnée invalide.</p>\n");
}
?>