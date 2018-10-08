<?php

/**
 *
 * exemple de page ElioraSchool pour montrer les fonctionnalités disponibles et les précautions à prendre. (protections contre les attaques classiques)
 * @version $Id: prototype_page_ElioraSchool.php $
 *
 * Copyright 2001, 2010 LesNomsDesDeveloppeurs
 *
 * This file is part of ElioraSchool.
 *
 * ElioraSchool is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * ElioraSchool is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with ElioraSchool; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

 // ********************************************************************************************
//
//                             TITRE DE LA PAGE
//
// ********************************************************************************************

$titre_page = "MODULE SCOLARITE - ETABLISEMENT DE L'ECHEANCE";

// ********************************************************************************************
//
// Si $affiche_connexion est définie, on voit apparaitre la dernière connexion dans l'entête
//
// ********************************************************************************************
$affiche_connexion = 'yes';

// ********************************************************************************************
//
// Position du script dans l'arborescence ElioraSchool : (indispensable)
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
$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_tranche=isset($_POST['id_tranche']) ? $_POST['id_tranche'] : (isset($_GET['id_tranche']) ? $_GET['id_tranche'] : NULL);
$montant=isset($_POST['montant']) ? $_POST['montant'] : (isset($_GET['montant']) ? $_GET['montant'] : NULL);
$chaine_options_classes="";

$paye=0;
$tmppaye =0;//$idfrais = old_mysql_result($res_sql_classes,0, "idFrais");
$id_eleve =isset($_POST['id_eleve']) ? $_POST['id_eleve'] : (isset($_GET['id_eleve']) ? $_GET['id_eleve'] : NULL);
$el_login =isset($_POST['el_login']) ? $_POST['el_login'] : (isset($_GET['el_login']) ? $_GET['el_login'] : NULL);
$idclasse =isset($_POST['idclasse']) ? $_POST['idclasse'] : (isset($_GET['idclasse']) ? $_GET['idclasse'] : NULL);
$versement =isset($_POST['versement']) ? $_POST['versement'] : (isset($_GET['versement']) ? $_GET['versement'] : NULL);

$nb_modalite =isset($_POST['nb_modalite']) ? $_POST['nb_modalite'] : (isset($_GET['nb_modalite']) ? $_GET['nb_modalite'] : NULL);
$idfrais =isset($_POST['idFrais']) ? $_POST['idFrais'] : (isset($_GET['idFrais']) ? $_GET['idFrais'] : NULL);


// ********************************************************************************************
//
//                         Traitement des données - mise en forme
//
// ********************************************************************************************

$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){

    $chaine_options_classes.="<option value=''> -- Veuiller selectionner une classe---</option>";
    while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){



        $chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";



    }
}


$sql_classes= "SELECT id, classe,montant,idFrais FROM classes c,etg_fraisscolaire e WHERE (c.id = e.id_classe) and (c.id =$idclasse)";
$res_sql_classes=mysqli_query($GLOBALS["mysqli"], $sql_classes);
$idfrais = old_mysql_result($res_sql_classes,0, "idFrais");
echo "id frais".$idfrais;
// ********************************************************************************************
//
//                         CSS et js particulier (chargés par le header)
//
// ********************************************************************************************

$javascript_specifique[0] = "path/filename_js_1";
$javascript_specifique[1] = "path/filename_js_2";
$style_specifique[0] = "path/filename_css_1";
$style_specifique[1] = "path/filename_css_2";

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
<br/>
<?php
$sql_classes= "SELECT id, classe,montant,idFrais FROM classes c,etg_fraisscolaire e WHERE (c.id = e.id_classe) and (c.id =$idclasse)";
$res_sql_classes=mysqli_query($GLOBALS["mysqli"], $sql_classes);
$idfrais = old_mysql_result($res_sql_classes,0, "idFrais");
echo $idfrais."frais";
//print_r(old_mysql_result);
//old_mysql_result($call_data, $i, "id"); number_format($Montant , 0, ',', ' ')
echo "<p>Les frais de scolarité de la classe : <b>".old_mysql_result($res_sql_classes,0, "classe")." </b> est de <b>".number_format(old_mysql_result($res_sql_classes,0, "montant"),0, ',', ' '). " FCFA</b></p><br/>";

$sql_eleves = "SELECT nom, prenom, login, id_eleve FROM eleves WHERE (login='$el_login' and id_eleve ='$id_eleve')";
$res_sql_eleves=mysqli_query($GLOBALS["mysqli"], $sql_eleves);
echo "<p>Definissez les modalites de payment de l'apprenant : <b>". old_mysql_result($res_sql_eleves,0, "nom")."  " .old_mysql_result($res_sql_eleves,0, "prenom"). "</b></p>";

if ( $nb_modalite!=""){
$idfrais = old_mysql_result($res_sql_classes,0, "idFrais");
?>
<form enctype="multipart/form-data" method="post" name="formulaire" action="modalites.php">
<input type="hidden" name= "idfrais" value="<?php echo $idfrais?>"/>
<input type="hidden" name= "nb_modalite" value="<?php echo $nb_modalite?>"/>
 <input type="hidden" value= "<?php echo $id_eleve ?>" name = "ideleve">

<table class='boireaus' border='0'>
	<tr>
	<th>&nbsp;</th>
	<th style='padding: 5px;'>Montant de la tranche</th>
	<th style='padding: 5px;' title="La date précisée ici est prise en compte pour les appartenances des élèves à telle classe sur telle période (notamment pour les élèves changeant de classe).
Il n'est pas question ici de verrouiller automatiquement une période de note à la date saisie.">A  payer au<br />tard le</th>
	
	</tr>
<?php
// le corps de la page ici
// On va chercher les classes déjà existantes, et on les affiche.

$k = '1';
	$alt=1;
	$n;
	echo "<p> il/elle a decidé de payer sa scolarité en $nb_modalite modalités comme suit :</p>";
while ($k <= $nb_modalite) {



		/*if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}*/
		$alt=$alt*(-1);

		//$cal[$k] = new Calendrier("formulaire", "date_fin_period_".$k);

		echo "<tr class='lig$alt'>\n";
		echo "<td style='padding: 5px;'>Tranche $k</td>\n";
		/*echo "<td style='padding: 5px;'><input type='text' id='nom_period_$k' name='nom_period[$k]'";
		echo " onchange='changement()'";
		echo " value=\"".$k."\" size='30' /></td>\n";*/
		echo "<td style='padding: 5px;'><input type='text' id='montant_tranche_$k' name='montant_tranche[$k]'";
		echo " onchange='changement()'";
		//echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
		echo " value='' size='10' />";

		//echo "<a href=\"#calend\" onClick=\"".$cal[$k]->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
		//echo img_calendrier_js("date_fin_period_".$k, "img_bouton_date_fin_period_".$k);
		echo "</td>\n";

		echo "<td style='padding: 5px;'><input type='text' id='date_fin_period_$k' name='date_fin_period[$k]'";
		echo " onchange='changement()'";
		echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
		echo " value=\"".strftime("%d/%m/%Y", '1002')."\" size='10' />";
		echo img_calendrier_js("date_conseil_period_".$k, "img_bouton_date_conseil_period_".$k);
		echo "</td>\n";

		echo "</tr>\n";
	$k++;
	}
echo	"<tr><td colspan='3'><center><input type='submit' value='Enregistrer' name='etapefinale' style='margin: 30px 0 30px 0;'/></center></td></tr></table>";
echo "<input type='hidden' name='is_posted' value='yes' />";
echo "<input type='hidden' name='id_classe' value='$id_classe' />";
//echo "<input type='hidden' name='classe' value='$classe' />";
echo "</form>";



}
else{
?>
<form enctype="multipart/form-data" method="post" name="formulaire" action="<?php $_SERVER["PHP_SELF"] ?>">
 <input type="hidden" value= "<?php echo $id_eleve ?>" name = "ideleve">
 <input type="hidden" value= "<?php echo $el_login ?>" name = "login">
    <input type="hidden" value= "<?php echo $idfrais ?>" name = "idfrais">
    <input type="hidden" value= "<?php echo $montant ?>" name = "mt">
<table class='boireaus' border='0'>
<tr class='lig1'>
	<td><label>Choissez le nombre de tranche :</label></td>
	<td><select name='nb_modalite' id='nb_modalite' onchange=\"getEleves(this.value);\" >
		<option value="">Nombre de tranche</option>
		<option value="1">1</option>
		<option value="2">2</option>
		<option value="3">3</option>
		<option value="4">4</option>
		<option value="5">5</option>
		<option value="6">6</option>
		<option value="7">7</option>
		<option value="8">8</option>
		<option value="9">9</option>
		<option value="10">10</option>
		<option value="11">11</option>
		<option value="12">12</option>
		<option value="13">13</option>
		<option value="14">14</option>
		<option value="15">15</option>
		<option value="16">16</option>
		<option value="17">17</option>
		<option value="18">18</option>
		<option value="19">19</option>
		<option value="20">20</option>
	</select><br/></td>
</tr>
</tr>
	<tr class='lig1'>
	<td colspan="2" align="center"><input type="submit" value="Selectionner"/></td>
	
</tr>
</table>

<?php
}

// le corps de la page ici
// On va chercher les classes déjà existantes, et on les affiche.

// inclusion du footer
require("../lib/footer.inc.php");
?>
