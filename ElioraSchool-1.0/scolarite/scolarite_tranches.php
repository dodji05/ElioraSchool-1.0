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

$titre_page = "MODULE SCOLARITE | DEFINIR LES FRAIS ";

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
    header("Location: ../logout.php?auto=1");
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
if ($_SESSION['statut'] == 'administrateur') {
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
$_SESSION['gepiPath'] = $gepiPath;
$id_classe = isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_tranche = isset($_POST['id_tranche']) ? $_POST['id_tranche'] : (isset($_GET['id_tranche']) ? $_GET['id_tranche'] : NULL);
$montant = isset($_POST['montant']) ? $_POST['montant'] : (isset($_GET['montant']) ? $_GET['montant'] : NULL);
$action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : NULL);
$chaine_options_classes = "";


//$chaine_options_classes="";
$sql = "SELECT id, classe FROM classes ";

// ********************************************************************************************
//
//                         Traitement des données - mise en forme
//
// ********************************************************************************************


$res_class_tmp = mysqli_query($GLOBALS["mysqli"], $sql);
if (mysqli_num_rows($res_class_tmp) > 0) {

    $chaine_options_classes .= "<option value=''> -- Veuiller selectionner une classe---</option>";
    while ($lig_class_tmp = mysqli_fetch_object($res_class_tmp)) {


        $chaine_options_classes .= "<option value='$lig_class_tmp->id'>$lig_class_tmp->classe</option>\n";


    }
}
// ********************************************************************************************
//
//                         CSS et js particulier (chargés par le header)
//
// ********************************************************************************************

$style_specifique[] = "lib/DHTMLcalendar/calendarstyle";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar";
$javascript_specifique[] = "lib/DHTMLcalendar/lang/calendar-fr";
$javascript_specifique[] = "lib/DHTMLcalendar/calendar-setup";

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

<p class=bold><a href="../accueil_admin.php"<?php echo insert_confirm_abandon(); ?>><img src='../images/icons/back.png'
                                                                                         alt='Retour'
                                                                                         class='back_link'/> Retour</a>
    | <a href="scolarite_tranches.php"<?php echo insert_confirm_abandon(); ?>>Definir les frais de scolarité pour une
        classe</a>
    | <a href='payement_frais.php'<?php echo insert_confirm_abandon(); ?>>Effectuer un payement </a>
    | <a href='liste_payement'<?php echo insert_confirm_abandon(); ?>>Liste des payements</a>
    | <a href='recouvrement.php'<?php echo insert_confirm_abandon(); ?>>Recouvrement</a>
    | <a href='recouvrement2.php'<?php echo insert_confirm_abandon(); ?>>Recouvrement2</a>
    <!-- | <a href='matieres_csv.php'<?php echo insert_confirm_abandon(); ?>>Autres frais</a> -->

</p>

<?php
if ($action == 'modif'){
$sql = "SELECT  classe FROM classes where id=" . $id_classe;
if ($id_classe != "" and $id_tranche != "" and ($montant != "" and $montant != "")){
// affichage du formulaires pour inserer le montant des tranches

if (isset($_GET['etape']) and ($_GET['etape'] == 'final'))
{
    // fait tes trucs ici  etape='final'

    echo "enregistrement des donnes";
}
else {
$sql_verifi = "SELECT * FROM etg_fraisscolaire WHERE id_classe =$id_classe";
$verification = mysqli_query($GLOBALS["mysqli"], $sql_verifi);
$nombresultats = mysqli_num_rows($verification);
if ($nombresultats > 0)
{
    echo "<br/><p>VOUS AVEZ DEJA DEFINI LES FRAIS DE SCOLARITE POUR CETTE CLASSE</p><br/>";
    $old_montant = old_mysql_result($verification, 0, 'montant');
    $idfrais = old_mysql_result($verification, 0, 'idFrais');

    ?>
    <form method="post" action="modif_periodes.php">
        <input type="hidden" value="<?php echo $idfrais ?>" name="idfrais"/>
        <table class='boireaus padd_et_bordg'>
            <tr>
                <td><label> Montant total: </label></td>
                <td colspan="2"><input type="text" name="nv_montant" value="<?php echo $old_montant ?>"/></td>
            </tr>
            <?php
            $sql_tranche_modif = "SELECT * FROM etg_tranche t,etg_fraisscolaire f WHERE ((t.id_frais =f.idFrais) AND( f.idfrais =$idfrais))";
            //echo $sql_tranche_modif;
            $tranche_old = mysqli_query($GLOBALS["mysqli"], $sql_tranche_modif);
            //print_r($tranche_old);
            $tranche_old_results = mysqli_num_rows($tranche_old);
            $k = 0;
            $alt = 1;
            while ($k < $tranche_old_results) {
                //echo $k;

                $nom_tranche = old_mysql_result($tranche_old, $k, 'Nom_Tranche');
                $Montant_tranche = old_mysql_result($tranche_old, $k, 'Montant_Tranche');
                $Date_Fin_Tranche = old_mysql_result($tranche_old, $k, 'Date_Fin_Tranche');
                $id_tranche = old_mysql_result($tranche_old, $k, 'id_Tranche');
                /*if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}*/
                $alt = $alt * (-1);

                //$cal[$k] = new Calendrier("formulaire", "date_fin_period_".$k);

                echo "<tr class='lig$alt'>\n";
                echo "<td style='padding: 5px;'>$nom_tranche <input type='hidden' name=id_tranche[$k] value = '$id_tranche'/></td>\n";
                /*echo "<td style='padding: 5px;'><input type='text' id='nom_period_$k' name='old_mysql_result($verification,0,'idFrais');'";
                echo " onchange='changement()'";
                echo " value=\"".$k."\" size='30' /></td>\n";*/
                echo "<td style='padding: 5px;'><input type='text' id='montant_tranche_$k' name='nv_montant_tranche[$k]'";
                echo " onchange='changement()'";
                //echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
                echo " value='$Montant_tranche' size='10' />";

                //echo "<a href=\"#calend\" onClick=\"".$cal[$k]->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
                //echo img_calendrier_js("date_fin_period_".$k, "img_bouton_date_fin_period_".$k);
                echo "</td>\n";

                echo "<td style='padding: 5px;'><input type='text' id='date_fin_period_$k' name='date_fin_period[$k]'";
                echo " onchange='changement()'";
                echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
                echo " value=\"" . strftime($Date_Fin_Tranche, '1002') . "\" size='10' />";
                echo img_calendrier_js("date_conseil_period_" . $k, "img_bouton_date_conseil_period_" . $k);
                echo "</td>\n";

                echo "</tr>\n";
                $k++;

            }
            echo "<tr><td colspan='3'><center><input type='submit' value='Enregistrer' name='etapefinale' style='margin: 30px 0 30px 0;'/></center></td></tr>";
            echo "<input type='hidden' name='is_posted' value='yes' />";
            //echo "<input type='hidden' name='id_classe' value='$id_classe' />";
            //echo "<input type='hidden' name='classe' value='$classe' />";
            ?>
        </table>
    </form>
    <?php
}
else
{
$sql_class = "SELECT  classe FROM classes where id=" . $id_classe;
$res_class = mysqli_query($GLOBALS["mysqli"], $sql_class);
if (mysqli_num_rows($res_class) > 0) {

    while ($lig_class_tmp = mysqli_fetch_object($res_class)) {

        $classe = $lig_class_tmp->classe;

    }
}
echo "<br/><b>Classe : $classe</b>";
//number_format($Montant , 0, ',', ' ');

echo " <p> Le montant de la scolarite est de :<b> " . number_format($montant, 0, ',', ' ') . " FCFA</b> et est payable en <b>" . $id_tranche . " tranche(s)</b></p>";
?>
<form enctype="multipart/form-data" method="post" name="formulaire" action="periodes.php">
    <input type="hidden" name="mt" value="<?php echo $montant ?>"/>
    <input type="hidden" name="nb_tranche" value="<?php echo $id_tranche ?>"/>
    <table class='boireaus' border='0'>
        <tr>
            <th>&nbsp;</th>
            <th style='padding: 5px;'>Montant de la tranche</th>
            <th style='padding: 5px;' title="La date précisée ici est prise en compte pour les appartenances des élèves à telle classe sur telle période (notamment pour les élèves changeant de classe).
                        Il n'est pas question ici de verrouiller automatiquement une période de note à la date saisie.">
                A payer au<br/>tard le
            </th>

        </tr>
        <?php
        // le corps de la page ici
        // On va chercher les classes déjà existantes, et on les affiche.
        $k = '1';
        $alt = 1;
        $n;
        while ($k <= $id_tranche) {
            /*if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}*/
            $alt = $alt * (-1);
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
            echo " value=\"" . strftime("%d/%m/%Y", '1002') . "\" size='10' />";
            echo img_calendrier_js("date_conseil_period_" . $k, "img_bouton_date_conseil_period_" . $k);
            echo "</td>\n";

            echo "</tr>\n";
            $k++;
        }
        echo "<tr><td colspan='3'><center><input type='submit' value='Enregistrer' name='etapefinale' style='margin: 30px 0 30px 0;'/></center></td></tr></table>";
        echo "<input type='hidden' name='is_posted' value='yes' />";
        echo "<input type='hidden' name='id_classe' value='$id_classe' />";
        echo "<input type='hidden' name='classe' value='$classe' />";
        echo "</form>";

        }
        }
        }
        else {
// etape1 :  affiche du formulaire pour afficher le nombre de tranches
            ?>
            <br/>
            <h2> Définissez le montant de la scolarité et le nombre de tranche de payement</h2>
            <form action="<?php $_SERVER["PHP_SELF"] ?>" method="post" id="chgdept">
                <table border="0" class='boireaus'>
                    <tr class='lig-1'>
                        <td><label> Classe : </label></td>
                        <td><?php echo "<select name='id_classe' id='id_classe' onchange=\"getEleves(this.value);\" >\n";
                            echo $chaine_options_classes;
                            echo "</select>\n"; ?><br/></td>
                    </tr>
                    <tr class='lig1'>
                        <td><label>Nombre de tranche de paiement :</label></td>
                        <td><select name='id_tranche' id='id_tranche' onchange=\"getEleves(this.value);\">
                                <option value="">Selectionnez le nombre de tranche</option>
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
                            </select><br/></td>
                    </tr>
                    <tr class='lig-1'>
                        <td><label> Montant de la scolarité : </label></td>
                        <td><input type="text" name="montant"></td>
                    </tr>
                    <tr class='lig1'>
                        <td colspan="2" align="center"><input type="submit" value="valider"/></td>

                    </tr>

                </table>


            </form>
            <?php
        }
        }
        elseif ($action == 'view') {
        ///    echo "visuo";
            $sql_verifi = "SELECT * FROM etg_fraisscolaire WHERE id_classe =$id_classe";
            echo  $sql_verifi;
        $verification = mysqli_query($GLOBALS["mysqli"], $sql_verifi);
        $nombresultats = mysqli_num_rows($verification);
        if ($nombresultats > 0) {

            $old_montant = old_mysql_result($verification, 0, 'montant');
            $idfrais = old_mysql_result($verification, 0, 'idFrais');
        ?>
            <table class='boireaus padd_et_bordg'>
                <tr>
                    <td><label> Montant total: </label></td>
                    <td colspan="2"><input type="text" name="nv_montant" value="<?php echo $old_montant ?>"/></td>
                </tr>
                <?php
                $sql_tranche_modif = "SELECT * FROM etg_tranche t,etg_fraisscolaire f WHERE ((t.id_frais =f.idFrais) AND( f.idfrais =$idfrais))";
                //echo $sql_tranche_modif;
                $tranche_old = mysqli_query($GLOBALS["mysqli"], $sql_tranche_modif);
                //print_r($tranche_old);
                $tranche_old_results = mysqli_num_rows($tranche_old);
                $k = 0;
                $alt = 1;
                while ($k < $tranche_old_results) {
                    //echo $k;

                    $nom_tranche = old_mysql_result($tranche_old, $k, 'Nom_Tranche');
                    $Montant_tranche = old_mysql_result($tranche_old, $k, 'Montant_Tranche');
                    $Date_Fin_Tranche = old_mysql_result($tranche_old, $k, 'Date_Fin_Tranche');
                    $id_tranche = old_mysql_result($tranche_old, $k, 'id_Tranche');
                    /*if ($nom_periode[$k] == '') {$nom_periode[$k] = "période ".$k;}*/
                    $alt = $alt * (-1);

                    //$cal[$k] = new Calendrier("formulaire", "date_fin_period_".$k);

                    echo "<tr class='lig$alt'>\n";
                    echo "<td style='padding: 5px;'>$nom_tranche <input type='hidden' name=id_tranche[$k] value = '$id_tranche'/></td>\n";
                    /*echo "<td style='padding: 5px;'><input type='text' id='nom_period_$k' name='old_mysql_result($verification,0,'idFrais');'";
                    echo " onchange='changement()'";
                    echo " value=\"".$k."\" size='30' /></td>\n";*/
                    echo "<td style='padding: 5px;'>". $Montant_tranche;

                    //echo "<a href=\"#calend\" onClick=\"".$cal[$k]->get_strPopup('../lib/calendrier/pop.calendrier.php', 350, 170)."\"><img src=\"../lib/calendrier/petit_calendrier.gif\" border=\"0\" alt=\"Petit calendrier\" /></a>\n";
                    //echo img_calendrier_js("date_fin_period_".$k, "img_bouton_date_fin_period_".$k);
                    echo "</td>\n";

                    echo "<td style='padding: 5px;'><input type='text' id='date_fin_period_$k' name='date_fin_period[$k]'";
                    echo " onchange='changement()'";
                    echo " onKeyDown=\"clavier_date(this.id,event);\" AutoComplete=\"off\"";
                    echo " value=\"" . strftime($Date_Fin_Tranche, '1002') . "\" size='10' />";
                    echo img_calendrier_js("date_conseil_period_" . $k, "img_bouton_date_conseil_period_" . $k);
                    echo "</td>\n";

                    echo "</tr>\n";
                    $k++;

                }
                echo "<tr><td colspan='3'><center><input type='submit' value='Enregistrer' name='etapefinale' style='margin: 30px 0 30px 0;'/></center></td></tr>";
                echo "<input type='hidden' name='is_posted' value='yes' />";
                //echo "<input type='hidden' name='id_classe' value='$id_classe' />";
                //echo "<input type='hidden' name='classe' value='$classe' />";
                ?>
            </table>
            <?php
        }



        }
        else
            {
                ?>
                <br/>
                <h2> Définissez le montant de la scolarité et le nombre de tranche de payement</h2>
                <form action="<?php $_SERVER["PHP_SELF"] ?>?action=modif" method="post" id="chgdept">
                    <table border="0" class='boireaus'>
                        <tr class='lig-1'>
                            <td><label> Classe : </label></td>
                            <td><?php echo "<select name='id_classe' id='id_classe' onchange=\"getEleves(this.value);\" >\n";
                                echo $chaine_options_classes;
                                echo "</select>\n"; ?><br/></td>
                        </tr>
                        <tr class='lig1'>
                            <td><label>Nombre de tranche de paiement :</label></td>
                            <td><select name='id_tranche' id='id_tranche' onchange=\"getEleves(this.value);\">
                                    <option value="">Selectionnez le nombre de tranche</option>
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
                                </select><br/></td>
                        </tr>
                        <tr class='lig-1'>
                            <td><label> Montant de la scolarité : </label></td>
                            <td><input type="text" name="montant"></td>
                        </tr>
                        <tr class='lig1'>
                            <td colspan="2" align="center"><input type="submit" value="valider"/></td>

                        </tr>

                    </table>


                </form>
                <?php

            }
        // inclusion du footer
        require("../lib/footer.inc.php");
        ?>
