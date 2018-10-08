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

$titre_page = "Module SMS";

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
require_once("../lib/envoi_SMS.inc.php");
// Resume session
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

$_SESSION['gepiPath']=$gepiPath;
$MyLogin = isset($_GET["MyLogin"]) ? $_GET["MyLogin"] : (isset($_POST["MyLogin"]) ? $_POST["MyLogin"] : NULL);
$MyMessage = "";
$MyMessage2 = "";

$id_classe=isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);

// ********************************************************************************************
//
//                         Traitement des données - mise en forme
//
// ********************************************************************************************

//
/*$call_data = mysqli_query($GLOBALS["mysqli"], "SELECT classe FROM classes WHERE id = '$id_classe'");
$classe = old_mysql_result($call_data, 0, "classe");
$periode_query = mysqli_query($GLOBALS["mysqli"], "SELECT * FROM periodes WHERE id_classe = '$id_classe'");*/
$chaine_options_classes="";
$sql="SELECT id, classe FROM classes ORDER BY classe";



$res_class_tmp=mysqli_query($GLOBALS["mysqli"], $sql);
if(mysqli_num_rows($res_class_tmp)>0){

    $chaine_options_classes.="<option value='vide'> -- Veuiller selectionner une classe---</option>";
    while($lig_class_tmp=mysqli_fetch_object($res_class_tmp)){



        $chaine_options_classes.="<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";



    }
}

// Exemple d'une requête SQL traditionnelle


// ********************************************************************************************
//
//                         CSS et js particulier (chargés par le header)
//
// ********************************************************************************************

$javascript_specifique[0] = "sms/eleves_xhr";
/*$javascript_specifique[1] = "path/filename_js_2";*/
$style_specifique[0] = "sms/dga_sms";
/*$style_specifique[1] = "path/filename_css_2";**/

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

<p class=bold><a href="../accueil_admin.php"<?php echo insert_confirm_abandon();?>><img src='../images/icons/back.png' alt='Retour' class='back_link'/> Retour</a>
    | <a href="modify_matiere.php"<?php echo insert_confirm_abandon();?>>Ajouter matière</a>
    | <a href='matieres_param.php'<?php echo insert_confirm_abandon();?>>Paramétrage de plusieurs matières par lots</a>
    | <a href='matieres_categories.php'<?php echo insert_confirm_abandon();?>>Editer les catégories de matières</a>
    | <a href='matieres_csv.php'<?php echo insert_confirm_abandon();?>>Importer un CSV de la liste des matières</a>
    | <a href='../gestion/admin_nomenclatures.php'<?php echo insert_confirm_abandon();?>>Gérer les nomenclatures</a>
    <?php
    if(acces("/gestion/gerer_modalites_election_enseignements.php", $_SESSION['statut'])) {
        echo " | <a href='../gestion/gerer_modalites_election_enseignements.php' title=\"Gérer les modalités d'élection des enseignements.\">Modalités d'élection enseignements</a>";
    }
    ?>
</p>
<?php
/*$affiche_debug=debug_var();
print_r($affiche_debug);*/

// le corps de la page ici
echo $MyMessage;
echo $MyMessage2;

?>
<br/>
<br/>
<div class="container">
<form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="chgdept" class="form-horizontal">
<div class="form-group">
<label for="inputEmail3" class="col-sm-1 control-label">Classe :</label>
    <div class="col-sm-3">
      <?php echo "<select name='id_classe' id='id_classe' onchange=\"getEleves(this.value);\"  class = \"form-control\">\n";
                echo $chaine_options_classes;
            echo "</select>\n";?>
    </div>
	<label for="inputEmail3" class="col-sm-1 control-label">Motif :</label>
    <div class="col-sm-3">
      <select name="motif" class = "form-control" > 
				<option> Retard</option>
				<option>Devoir</option>
				<option>paye</option>
				<option>Scolarite</option>
			</select>
    </div>
  
</div>
<div class="form-group">

    <div class="col-sm-4">
     <span id="BlocEleves"></span><br />
    </div>
	
    <div class="col-sm-4">
	<br/>
        <label>Votre message</label><textarea class = "form-control" name="msg_sms" cols="30" rows="5"></textarea>
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
//traiteme   
if ((isset($_POST["eleve"]))&&(isset($_POST["msg_sms"])))
{
    $eleve = $_POST["eleve"];
    $msg_sms = $_POST["msg_sms"];
    //print_r ($eleve);
    //echo count($eleve);
    $listDestinataire ="";
    $tab_num = array();
    $listNum="";
    $nb_tel = count($eleve);
    echo $nb_tel;
    $i=1;
    foreach($eleve as $valeur){
        $info = explode("," ,$valeur);
        /*echo "nom : ".$info[0];
        echo "  prenom : ".$info[1];
        echo "  telephone: ".$info[2];
        echo " || ";*/
        $listDestinataire .= "nom : ".$info[0]."  prenom : ".$info[1]."  telephone: ".$info[2]." || ";
        $tab_num[]=$info[2].",";
        if ($i < ($nb_tel)){
            $listNum .= $info[2].",";
        }
        if ($i== $nb_tel){
            $listNum .= $info[2];
        }
        $i++;
    }
   /* $objet="SMSING";
    echo $listDestinataire;
    echo"<br/>voici le message: <br/>"; */
   // echo $msg_sms;
    //echo $listNum;
    //check_token();
    $tab_to=explode(',' ,$listNum);
    $t_retour=envoi_SMS($tab_to,str_replace('\n',"\n",$_POST['msg_sms']),true);
    ?>
    <p class="center">
        ---------------------------------------------------------------------<br /><br />
        </p>
        <p class="center" style="color:<?php if ($t_retour['retour']=='OK') echo 'blue'; else echo 'red'; ?>">

        Bilan du test : 
    <?php
        if ($t_retour['retour']=='OK') echo "Message bien envoyé."; else echo "Erreur : ".$t_retour['retour'];
    ?>
        </p>
        <br /><br />
        <p class="center">Ce qui a été envoyé au prestataire : </p>
        <div  style="font-size: small; padding: 2em; background-color: white; margin-left: 25%; margin-right: 25%; width: 50%; white-space:pre-wrap;"><?php echo $t_retour['envoi'] ?></div>
        <br />
        <br />
        <p class="center">Réponse retournée par le prestataire :  </p>
        <div  style="font-size: small;  padding: 2em; background-color: white; margin-left: 25%; margin-right: 25%; width: 50%; white-space:pre-wrap;"><?php echo $t_retour['reponse'] ?></div>
        </p>
    <?php
        }
    ?>


<?php
    


/*//echo $sql;
echo "<select name='id_classe' id='id_classe' onchange=\"confirm_changement_classe(change, '$themessage');\">\n";
echo $chaine_options_classes;
echo "</select>\n";*/
// inclusion du footer
require("../lib/footer.inc.php");
?>