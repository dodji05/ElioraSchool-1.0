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

$titre_page = "MODULE SCOLARITE : Payement echeance";

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
$tmppaye =0;
$id_eleve =isset($_POST['mt']) ? $_POST['id_eleve'] : (isset($_GET['id_eleve']) ? $_GET['id_eleve'] : NULL);
$el_login =isset($_POST['el_login']) ? $_POST['el_login'] : (isset($_GET['el_login']) ? $_GET['el_login'] : NULL);
$idclasse =isset($_POST['idclasse']) ? $_POST['idclasse'] : (isset($_GET['idclasse']) ? $_GET['idclasse'] : NULL);
$versement =isset($_POST['versement']) ? $_POST['versement'] : (isset($_GET['versement']) ? $_GET['versement'] : NULL);

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
    | <a href='liste_payement'<?php echo insert_confirm_abandon();?>>Liste des payements</a>
	| <a href='recouvrement.php'<?php echo insert_confirm_abandon();?>>Recouvrement</a>
   <!-- | <a href='matieres_csv.php'<?php echo insert_confirm_abandon();?>>Autres frais</a> -->
  
</p>
<!--FIN MENU-->

<?php


// le corps de la page ici
// On va chercher les classes déjà existantes, et on les affiche.
if (isset($_POST["versement"])){
    $ideleve =isset($_POST['ideleve']) ? $_POST['ideleve'] : (isset($_GET['ideleve']) ? $_GET['ideleve'] : NULL);
    $login =isset($_POST['login']) ? $_POST['login'] : (isset($_GET['login']) ? $_GET['login'] : NULL);
    /*echo "fist".$idclasse ;
    print_r($_POST);
    echo"<br/>";*/
    $sommepaye=0;
    foreach ($versement as &$value) {
        $sommepaye += $value;
    }
    //echo $sommepaye;
    $dates = date("Y-m-d H:i:s");
    $sql_paye = "INSERT INTO etg_paiement VALUES(Null,'$ideleve','$login','$dates',  $sommepaye)";
    mysqli_query($GLOBALS["mysqli"], $sql_paye);

}

else {
    $sql_deja ="SELECT sum(Montant) as total FROM etg_paiement WHERE(idEleve=$id_eleve and idLogin='$el_login')";
    //echo $sql_deja;
    $test = mysqli_query($GLOBALS["mysqli"], $sql_deja);
    //print_r($test);
    if($test->num_rows == 1){
        $total = old_mysql_result($test, 0,"total");

    }
    //echo $total;
     $sql="SELECT DISTINCT e.*,jer.*,cl.*,et.*  FROM eleves e, j_eleves_classes j, classes cl, j_eleves_regime jer,etg_fraisscolaire et 
    WHERE (
    j.login = e.login AND
    jer.login = e.login AND
    j.id_classe =cl.id AND
    e.login = '$el_login' AND
    e.id_eleve = '$id_eleve' AND
	et.id_classe = cl.id
    )";
    //echo $sql;
    $calldata = mysqli_query($GLOBALS["mysqli"], $sql);
    //print_r($calldata);
    // $eleve_login = old_mysql_result($calldata, 0, "login");
    $eleve_nom = old_mysql_result($calldata, 0,"nom");
    $eleve_prenom = old_mysql_result($calldata, 0,"prenom");
    $id_classe = old_mysql_result($calldata, 0, "id");
    $classe = old_mysql_result($calldata, 0, "classe");
	$montant = old_mysql_result($calldata, 0, "montant");
	$montant = number_format($montant , 0, ',', ' ');
    echo "<br/><p>Le montant de la scolarite de la classe de $classe est :<b> $montant  FCFA</b></p><br/>";		
    echo "<p>Payement des frais de scolarité pour l'apprenant : <b>".$eleve_nom." ".$eleve_prenom."</b></p>";
    $sql_tranche = "SELECT * FROM etg_fraisscolaire, etg_echeancier,etg_echeancier_tranche where((etg_fraisscolaire.idFrais = etg_echeancier.idFrais) AND (etg_echeancier.idEcheancier = etg_echeancier_tranche.idEcheancier)And( id_classe=$id_classe) AND (idEleve = $id_eleve))";
    echo $sql_tranche;
    $calltranche = mysqli_query($GLOBALS["mysqli"], $sql_tranche);
    $nombreligne = mysqli_num_rows($calltranche);

?>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']?>">
        <input type="hidden" value= "<?php echo $id_eleve ?>" name = "ideleve">
        <input type="hidden" value= "<?php echo $el_login ?>" name = "login">
        <input type="hidden" value= "<?php ?>" name = "">
        <table border="1" class='boireaus' cellpadding='2'summary='Tableau des élèves de la classe'>
            <tr>
                <th>Tranche</th>
                <th>Montant</th>
                <th>Reste à payer</th>
                <th>Date limite</th>
                <th>Somme versée</th>
            </tr>
            <?php
            $i = 0;
            $alt=1;
            $rest_a_payer =array();
            $pt = old_mysql_result($calltranche, 0, "Montant");
            $reste = $pt - $total;
            if($reste == 0 ){
                $rest_a_payer[0] =0;
                for($j=1; $j < $nombreligne; $j++){
                    $rest_a_payer[$j] =old_mysql_result($calltranche, $j, "Montant");
                }
            }
            else if ($reste > 0) {
                $rest_a_payer[0] = $reste;
                for($j=1; $j < $nombreligne; $j++){
                    $rest_a_payer[$j] =old_mysql_result($calltranche, $j, "Montant");
                }
            }
            else{
                $rest_a_payer[0] = 0 ;//old_mysql_result($calltranche, 0, "Montant_Tranche");
               /* echo */$reste1 =($reste)*(-1);
                for($j=1; $j < $nombreligne; $j++){
                    $rst= old_mysql_result($calltranche, $j, "Montant");
                   /* echo "reste".*/$reste = $rst - $reste1."<br/>";
                    if($reste == 0 ){
                        $rest_a_payer[$j] =0;
						$reste1 = 0;
						for($e=$j+1; $e<$nombreligne; $e++){
						$rest_a_payer[$e] =old_mysql_result($calltranche, $e, "Montant");
						}
						break;
                    }
                    else if ($reste > 0) {
                        $rest_a_payer[$j] = $reste;
						//$reste1 = $reste;
						for($e=$j+1; $e<$nombreligne; $e++){
						$rest_a_payer[$e] =old_mysql_result($calltranche, $e, "Montant");
						}
						break;
                    }
					else {
						$reste1 =($reste)*(-1);
						//echo"gil";
						$rest_a_payer[$j]=0;
						for($e=$j+1; $e<$nombreligne; $e++){
						$rest_a_payer[$e] =old_mysql_result($calltranche, $e, "Montant");
						}
					}
					//$reste = 
					$reste = $reste1;
                }
            }
          //  print_r($rest_a_payer);

            while ($i < $nombreligne){
			$alt=$alt*(-1);
                echo "<tr class='lig$alt white_hover'>";
                echo "<td>";
                echo old_mysql_result($calltranche, $i, "Nom_tranches");
                echo "</td>";

                echo "<td>";
                echo number_format(old_mysql_result($calltranche, $i, "Montant") , 0, ',', ' ');
                echo "</td>";

                echo "<td>";
                $rt = @old_mysql_result($calltranche, $i, "Montant");
				$rt = number_format($rt , 0, ',', ' ');
                //print_r($rt);
                /*for($j=0;$j>$i;$j++){
                $t = $rt - $total;
                echo $t;}*/
                //echo number_format($rest_a_payer[$i], 0, ',', ' ');
					echo $rest_a_payer[$i];
                echo "</td>";

                echo "<td>";
                echo old_mysql_result($calltranche, $i, "DateLimites");
                echo "</td>";

                echo "<td>";
				if($rest_a_payer[$i] ==0 ){
					 echo "<input type='text' name='' value='Soldée' size='10' disabled='true'/>";
				} 
				else{
					 echo "<input type='text' name='versement[$i]' value='0' size='10' />";
				}
				
				
               // echo "<input type='text' name='versement[$i]' value='0' size='10' disabled='true'/>";
                //echo $i;
                echo "</td>";
                echo"</tr>";
                $i++;
            }
            ?>
            <tr  align= "center">
                <td valign="center" colspan="5"> <input type="submit" value="Enregistrer"/> </td>
            </tr>
        </table>
    </form>
	<hr/>
	<br/>
<?php

}
if ((!isset($id_eleve)) and (!isset($el_login))){

	$id_eleve = $_POST['ideleve'];
	$el_login = $_POST['login'];
	$sql_list_paye ="SELECT * FROM etg_paiement WHERE(idEleve=$id_eleve and idLogin='$el_login')";
	//echo "1";
}
echo"PAYEMENT EFFECTUE AVEC SUCCESS<br/>";
echo "Voici le  Récapitulatif  de ces paiement :";
$sql_list_paye ="SELECT * FROM etg_paiement WHERE(idEleve=$id_eleve and idLogin='$el_login')";

//echo $sql_list_paye;
   // echo $sql_list_paye;
   $list_paye = mysqli_query($GLOBALS["mysqli"], $sql_list_paye);
   // print_r($list_paye);
    if( $list_paye->num_rows >0){
		$nombrepayement =  $list_paye->num_rows;
		//echo $nombrepayement;
		$i=0;
		$alt = 0;
       ?>
	   <table border="0" class='boireaus' cellpadding='2'summary='Tableau des élèves de la classe'>
	   <tr>
			<th>DATE</th>
			<th>MONTANT VERSE</th>
			<th>ACTION</th>
	   </tr>
	   <?php
	   while ($i < $nombrepayement){
	   $alt=$alt*(-1);
                echo "<tr class='lig$alt white_hover'>";
	 
	   echo"<td>".old_mysql_result($list_paye, $i, "date")."</td>";
	  
	  
	   echo"<td>".number_format(old_mysql_result($list_paye, $i, "Montant"), 0, ',', ' ')."</td>";
	    echo"<td> Modifier</td>";
	    echo "</tr>";
	    $i++;
	   }

    }

// inclusion du footer
require("../lib/footer.inc.php");
?>
