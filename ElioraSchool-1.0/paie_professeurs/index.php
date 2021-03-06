<?php

/**
 *
 * exemple de page GEPI pour montrer les fonctionnalités disponibles et les précautions à prendre. (protections contre les attaques classiques)
 * @version $Id: prototype_page_gepi.php $
 *
 * Copyright 2001, 2010 LesNomsDesDeveloppeurs
 *
 * This file is part of GEPI.
 *
 * GEPI is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * GEPI is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with GEPI; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

 // ********************************************************************************************
//
//                             TITRE DE LA PAGE
//
// ********************************************************************************************

$titre_page = "MODULE PAIE PROFESSEURS";

// ********************************************************************************************
//
// Si $affiche_connexion est définie, on voit apparaitre la dernière connexion dans l'entête
//
// ********************************************************************************************
$affiche_connexion = 'yes';

// ********************************************************************************************
//
// Position du script dans l'arborescence GEPI : (indispensable)
// à la racine                         -> $niveau_arbo = 0
// dans un sous-dossier             -> $niveau_arbo = 1
// dans un sous-sous-dossier         -> $niveau_arbo = 2
// dans un sous-sous-sous-dossier     -> $niveau_arbo = 3 (valeur maxi)
//
// ********************************************************************************************
$niveau_arbo = 1;

// ********************************************************************************************
//
//                 Initialisations files 
//
// ********************************************************************************************

// initialisations (indispensables)
// 
// 1. Filtrage de $_GET, $_POST, $_SERVER, $COOKIE contre injections SQL
// 2. Filtrage de $_GET et $_POST contre injections XSS
// 3. Filtrage de $_FILES pour interdire certaines extensions

// on peut définir la liste des fichiers autorisés au téléchargement - filtrage de $_FILES (optionnel)
$AllowedFilesExtensions = array("txt", "png");

require_once("../lib/initialisations.inc.php");

$resultat_session = $session_gepi->security_check();

if ($resultat_session == 'c') {
    header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
    die();
} else if ($resultat_session == '0') {
    header("Location: ./logout.php?auto=1");
    die();
}


// initialisation pour pouvoir utiliser Propel (optionnel)
include("../lib/initialisationsPropel.inc.php");

// ********************************************************************************************
//
//                 inclusions personnelles (optionnel)
//
// ********************************************************************************************

//require_once("./mesfonctions.php");

// ********************************************************************************************
//
//                 Vérification de l'authentification - (indispensable)
//
// ********************************************************************************************

$resultat_session = $session_gepi->security_check();
if ($resultat_session == 'c') {
   header("Location:utilisateurs/mon_compte.php?change_mdp=yes&retour=accueil#changemdp");
   die();
} else if ($resultat_session == '0') {
    header("Location: ../../logout.php?auto=1");
    die();
}

// ********************************************************************************************
//
// Sécurité  - (indispensable)
// ajout d'une ligne du style suivant dans 'sql/data_gepi.sql' et 'utilitaires/updates/access_rights.inc.php'
// INSERT INTO droits VALUES ('/edt_organisation/verifier_edt.php', 'V', 'F', 'F', 'F', 'F', 'F', 'F', 'vérifier la table edt_cours', '');
//
// ********************************************************************************************

if (!checkAccess()) {
    header("Location: ../../logout.php?auto=2");
    die();
}
/**
* Fonctions utiles uniquement pour l'administrateur
*/
if($_SESSION['statut']=='administrateur') {
    include_once("../lib/share-admin.inc.php");
}

// ********************************************************************************************
//
//                         Initialisation des variables
//
// ********************************************************************************************

// Toutes les variables $_GET et $_POST sont traitées par InputFilter ou HTMLPurifier (permet de se prémunir des injections XSS)
// Le choix du filtre se fait par l'intermédiaire du champ 'filtrage_html' dans la table 'setting' - à ajouter ou modifier directement dans la table pour le personnaliser
// valeurs prises par 'filtrage_html' : 'input_filter', 'htmlpurifier' ou 'pas_de_filtrage_html'.
// par défaut, on utilise le filtre 'htmlpurifier'

/*$MyLogin = isset($_GET["MyLogin"]) ? $_GET["MyLogin"] : (isset($_POST["MyLogin"]) ? $_POST["MyLogin"] : NULL);
$MyMessage = "";
$MyMessage2 = "";*/
$_SESSION['gepiPath']=$gepiPath;
/*$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_tranche=isset($_POST['id_tranche']) ? $_POST['id_tranche'] : (isset($_GET['id_tranche']) ? $_GET['id_tranche'] : NULL);
$montant=isset($_POST['montant']) ? $_POST['montant'] : (isset($_GET['montant']) ? $_GET['montant'] : NULL);
$chaine_options_classes="";*/

// ********************************************************************************************
//
//                         Traitement des données - mise en forme
//
// ********************************************************************************************
/*
$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){

    $chaine_options_classes.="<option value=''> -- Veuiller selectionner une classe---</option>";
    while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){



        $chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";



    }
}*/
// ********************************************************************************************
//
//                         CSS et js particulier (chargés par le header)
//
// ********************************************************************************************

/*$javascript_specifique[0] = "path/filename_js_1";
$javascript_specifique[1] = "path/filename_js_2";
$style_specifique[0] = "path/filename_css_1";
$style_specifique[1] = "path/filename_css_2";*/

// ======================= Pour utiliser 'scriptaculous' (basé sur prototype)
$utilisation_scriptaculous = 'ok';    
$scriptaculous_effet = 'effects,dragdrop';

// ======================= Pour utiliser 'prototype window' (basé sur prototype)
$utilisation_win = 'oui';

// ********************************************************************************************
//
//                         Affichage de la page
//
// ********************************************************************************************

// inclusion du header
require_once("../lib/header.inc.php");
include_once("../lib/header_template.inc.php");
?>
<!-- MENU HAUT DE PAGE-->
<p class=bold><a href="../accueil_admin.php"<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
    | <a href="scolarite_tranches.php"<?php echo insert_confirm_abandon();?>>Definir les frais de scolarité pour une classe</a>
    | <a href='payement_frais.php'<?php echo insert_confirm_abandon();?>>Effectuer un payement </a>
    | <a href='matieres_categories.php'<?php echo insert_confirm_abandon();?>>Liste des payements</a>
    | <a href='matieres_csv.php'<?php echo insert_confirm_abandon();?>>Autres frais</a>
  
</p>
<!--FIN MENU-->

<?php

$sql_prof="SELECT * FROM utilisateurs where statut='professeur' ORDER BY auth_mode;";
$test_sql_prof=mysqli_query($GLOBALS["mysqli"], $sql_prof);
?>
<table class='boireaus' cellpadding='3' summary='Tableau des utilisateurs'>
	<tr>
		<th>Nom et prénom</th>
		<th>matière(s) enseignée(s)</th>
		<th>classe(s)</td>
		<th>tarifs horaires</th>
	</tr>
<?php
$nb_prof = mysqli_num_rows($test_sql_prof);
if($nb_prof>0) {
		$k = 0;
		while ($k < $nb_prof) {
			echo "<tr>";
				echo "<td>".old_mysql_result($test_sql_prof, $k, "nom")." ".old_mysql_result($test_sql_prof, $k, "prenom")."</td>";
				echo "<td>";
				$user_login = old_mysql_result($test_sql_prof, $k, "login");
				//echo "SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres";
					$call_matieres = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM j_professeurs_matieres j WHERE j.id_professeur = '$user_login' ORDER BY ordre_matieres");
    $nb_mat = mysqli_num_rows($call_matieres);
    $i = 0;
	$ii=0;
    while ($i < $nb_mat) {
        $user_matiere_id = old_mysql_result($call_matieres, $i, "id_matiere");
		//echo "SELECT matiere FROM matieres WHERE matiere='$user_matiere_id'<br />\n";
        //$user_matiere[$k] = old_mysql_result(mysql_query("SELECT matiere FROM matieres WHERE matiere='$user_matiere_id'"),0);
		$sql="SELECT matiere FROM matieres WHERE matiere='$user_matiere_id';";
		$res_test_matiere=mysqli_query($GLOBALS["mysqli"], $sql);
		if(mysqli_num_rows($res_test_matiere)>0) {
			$user_matiere[$ii] = old_mysql_result($res_test_matiere,0);
			$ii++;
		}
		else {
			echo "<span style='color:red;'>Anomalie:</span> La matière '$user_matiere_id' n'existe plus mais reste asociée à '$user_login'.<br />Recréez la matière (<i>puis supprimez la proprement si nécessaire</i>)<br />\n";
		}
		$i++;
    }
				echo "</td>";
			echo"</tr>";
			$k++;
		}

}
echo "</table>";
// le corps de la page ici
// On va chercher les classes déjà existantes, et on les affiche.

// inclusion du footer
require("../lib/footer.inc.php");
?>
