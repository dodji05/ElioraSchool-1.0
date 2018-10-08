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

$titre_page = "MODULE SCOLARITE";

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
    header("Location: /logout.php?auto=1");
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
    header("Location: ../logout.php?auto=1");
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
    header("Location: ../logout.php?auto=2");
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
$id_prof=isset($_POST['id_prof']) ? $_POST['id_prof'] : (isset($_GET['id_prof']) ? $_GET['id_prof'] : NULL);
$nbre_heure=isset($_POST['nbre_heure']) ? $_POST['nbre_heure'] : (isset($_GET['nbre_heure']) ? $_GET['nbre_heure'] : NULL);
$horaire=isset($_POST['horaire']) ? $_POST['horaire'] : (isset($_GET['horaire']) ? $_GET['horaire'] : NULL);
$total=isset($_POST['total']) ? $_POST['total'] : (isset($_GET['total']) ? $_GET['total'] : NULL);

// ********************************************************************************************
//
//                         Traitement des données - mise en forme
//
// ********************************************************************************************

$chaine_profs="";
$sql="SELECT * FROM utilisateurs where statut='professeur' ORDER BY auth_mode;";



$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){

    $chaine_profs.="<option value='vide'> -- Veuiller selectionner un professeur---</option>";
    while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){



        $chaine_profs.="<option value='$lig_class_tmp->login'>$lig_class_tmp->nom"." "."$lig_class_tmp->prenom  </option>\n";



    }
}
// ********************************************************************************************
//
//                         CSS et js particulier (chargés par le header)
//
// ********************************************************************************************

$javascript_specifique[0] = "paie_professeurs/horaire_xhr";
/*$javascript_specifique[1] = "path/filename_js_2";
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
ici la liste des nemuns
<!--FIN MENU-->
<?php 
if (($id_prof=='vide') or ($id_prof== NULL)) {
//echo"bonjour";
//print_r($_POST);

?>
<div class="container">
    <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="chgdept" class="form-horizontal">
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Professeurs :</label>
            <div class="col-sm-3">
                <?php echo "<select name='id_prof' id='id_classe' onchange=\"getEleves(this.value);\"  class = \"form-control\">\n";
                echo $chaine_profs;
                echo "</select>\n";?>
            </div>
            
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">Salaire horaire :</label>
            <div class="col-sm-3">
                <span id="BlocHoraires" name='horaire'></span>
            </div>
            <label for="inputEmail3" class="col-sm-2 control-label">Montant total :</label>
            <div class="col-sm-3">
                <input type="number" class="control-label" name='total' id='total'>
            </div>
        </div>
		<div class="form-group">
<br/>
    <div class="col-sm-8">
	<input class="btn btn-lg btn-primary btn-block"type="submit" value="Envoyer"/>
	</div>
</div>	
    </form>
</div>

<?php 
}
else{
//print_r($_POST);
$TOTAL = $_POST['TOTAL'];
$NBE = $_POST['NBE'];
$group_id =$_POST['group_id'];
//print_r($_SESSION);
echo"<br/>";
//print_r($GLOBALS["mysqli"]);
$payeur = $_SESSION['nom']." ".$_SESSION['prenom']; 
$sql="INSERT INTO `etg_payement_salaire_prof` (`id_professeur`, `date`, `agent`,`idPayement_Salaire_Prof`) VALUES ('$id_prof', CURRENT_TIMESTAMP, '$payeur', NULL)";
//echo "<br/>".$sql;
$res_eleves = mysqli_query($GLOBALS["mysqli"], $sql);
//var_dump($GLOBALS["mysqli"]);
//print_r($GLOBALS["mysqli"]->insert_id);
$id_paiement = $GLOBALS["mysqli"]->insert_id;
/*echo "<br/>Le dernier ID inséré dans est le id".mysqli_insert_id($cnx);
echo "<br/> $res_eleves";*/
if ($res_eleves==1) {}
/*$sql_insert = "INSERT INTO `etg_payement_salaire_prof` (`id_professeur`, `nbreHeure`, `montantpercu`, `date`, `agent`, `idPayement_Salaire_Prof`) VALUES ('$id_prof', '$nbre_heure', '$total', 'NOW()', '$payeur', NULL)";*/
for($i=0;$i<count($TOTAL);$i++){
echo "<br/>";
$sql_insert = "INSERT INTO etg_ligne_payement_salaire_prof (`id_ligne`, `id_paiement`, `group_id`,`NbreHeure`, `TotalRecu`) VALUES (NULL, '$id_paiement', '$group_id[$i]', '$NBE[$i]', '$TOTAL[$i]');";
//echo $sql_insert."<br/>";
mysqli_query($GLOBALS["mysqli"], $sql_insert);

}

//echo $sql_insert."<br/>";
echo "-----------------------------------------------------------------------------------------------------";
}
?>

<?php


// le corps de la page ici
// On va chercher les classes déjà existantes, et on les affiche.

// inclusion du footer
require("../lib/footer.inc.php");
?>
