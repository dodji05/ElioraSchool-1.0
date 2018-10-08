<?php
  echo "<table border='1' cellpadding='2' class='boireaus'  summary='Tableau des élèves de la classe'>\n";
    echo "<tr>\n";
    echo "<th><p>Identifiant</p></th>\n";
    $csv.="Identifiant;";

    $ajout_param_lien="";
    if(isset($motif_rech)){$ajout_param_lien.="&amp;motif_rech=$motif_rech";}
    if(isset($mode_rech_nom)){$ajout_param_lien.="&amp;mode_rech_nom=$mode_rech_nom";}
    if(isset($mode_rech_prenom)){$ajout_param_lien.="&amp;mode_rech_prenom=$mode_rech_prenom";}
    //if((isset($mode_rech_champ))&&(isset($champ_rech))) {$ajout_param_lien.="&amp;mode_rech_champ=$mode_rech_champ&amp;champ_rech=$champ_rech";}
    if(isset($mode_rech)) {$ajout_param_lien.="&amp;mode_rech=$mode_rech";}
    //if(isset($mode_rech_elenoet)) {$ajout_param_lien.="&amp;mode_rech_elenoet=$mode_rech_elenoet";}
    //if(isset($mode_rech_ele_id)) {$ajout_param_lien.="&amp;mode_rech_ele_id=$mode_rech_ele_id";}
    //if(isset($mode_rech_no_gep)) {$ajout_param_lien.="&amp;mode_rech_no_gep=$mode_rech_no_gep";}
    // 20130607
    //if(isset($motif_rech_mef)) {$ajout_param_lien.="&amp;motif_rech_mef=$motif_rech_mef";}
    //if(isset($motif_rech_etab)) {$ajout_param_lien.="&amp;motif_rech_etab=$motif_rech_etab";}

    echo "<th><p><a href='index.php?order_type=nom,prenom&amp;quelles_classes=$quelles_classes";
    echo $ajout_param_lien;
    echo "'>Nom Prénom</a></p></th>\n";
    $csv.="Nom Prénom;";
    $csv.="Date sortie;";

    echo "<th><p><a href='index.php?order_type=sexe,nom,prenom&amp;quelles_classes=$quelles_classes";
    echo $ajout_param_lien;
    echo "'>Sexe</a></p></th>\n";
    $csv.="Sexe;"
    ;
    echo "<th><p><a href='index.php?order_type=naissance,nom,prenom&amp;quelles_classes=$quelles_classes";
    echo $ajout_param_lien;
    echo "'>Date de naissance</a></p></th>\n";
    $csv.="Date de naissance;";

    echo "<th><p><a href='index.php?order_type=regime,nom,prenom&amp;quelles_classes=$quelles_classes";
    echo $ajout_param_lien;
    echo "'>Régime</a></p></th>\n";
    $csv.="Régime;";

    if (($quelles_classes == 'na')||($quelles_classes == 'dse')) {
        echo "<th><p>Classe</p></th>\n";
    } else {
        echo "<th><p>";
        if($_SESSION['statut'] != 'professeur') {
            echo "<a href='index.php?order_type=classe,nom,prenom&amp;quelles_classes=$quelles_classes";
            echo $ajout_param_lien;
            echo "'>Classe</a>";
        }
        else{
            echo "Classe";
        }
        echo "</p></th>\n";
    }
    $csv.="Classe;";

    // 20130607
    //echo "<th><p>MEF</p></th>\n";

//    echo "<th><p>Classe</p></th>";
    echo "<th><p>Enseign.<br />suivis</p></th>\n";
    //$csv.=";";
    echo "<th><p>".ucfirst(getSettingValue("gepi_prof_suivi"))."</p></th>\n";
    $csv.=ucfirst(getSettingValue("gepi_prof_suivi")).";";

    //if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
    if($_SESSION['statut']=="administrateur") {
        echo "<th><p><input type='submit' value='Supprimer' onclick=\"return confirmlink(this, 'La suppression d\'un élève est irréversible et entraîne l\'effacement complet de toutes ses données (notes, appréciations, ...). Etes-vous sûr de vouloir continuer ?', 'Confirmation de la suppression')\" /></p></th>\n";
    }
    elseif($_SESSION['statut']=="scolarite") {
        echo "<th><p><span title=\"La suppression n'est possible qu'avec un compte administrateur\">Supprimer</span></p></th>\n";
    }
    //$csv.=";";

    if (getSettingValue("active_module_trombinoscopes")=='y') {
        if($_SESSION['statut']=="professeur") {
            if (getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes') {
                echo "<th><p><input type='submit' value='Téléverser les photos' name='bouton1' /></th>\n";
            }
        }
        else{
            echo "<th><p><input type='submit' value='Téléverser les photos' name='bouton1' /></th>\n";
        }
    }
    //$csv.=";";

    echo "</tr>\n";
    $csv.="\r\n";

    if(!isset($tab_eleve)){
        $nombreligne = mysqli_num_rows($calldata);
    }
    else{
        $nombreligne = count($tab_eleve);
    }
    //echo "\$nombreligne=$nombreligne<br />";
/*
    echo "<p>Total : $nombreligne éleves</p>\n";
    echo "<p>Remarque : le login ne permet pas aux élèves de se connecter à Gepi. Il sert simplement d'identifiant unique.</p>\n";
*/

    $acces_class_const=acces("/classes/classes_const.php", $_SESSION['statut']);

    $tab_mef=get_tab_mef();
    $acces_associer_eleve_mef=acces("/mef/associer_eleve_mef.php", $_SESSION['statut']);

    $i = 0;
    $alt=1;
    while ($i < $nombreligne){
        if(!isset($tab_eleve[$i])){
            $eleve_login = old_mysql_result($calldata, $i, "login");
            $eleve_nom = old_mysql_result($calldata, $i, "nom");
            $eleve_prenom = old_mysql_result($calldata, $i, "prenom");
            $eleve_sexe = old_mysql_result($calldata, $i, "sexe");
            $eleve_naissance = old_mysql_result($calldata, $i, "naissance");
            $elenoet = old_mysql_result($calldata, $i, "elenoet");
            $date_sortie_elv = old_mysql_result($calldata, $i, "date_sortie");
            // 20130607
            $mef_code = old_mysql_result($calldata, $i, "mef_code");
            if($quelles_classes=='no_regime') {
                $eleve_regime = "-";
                $eleve_doublant =  "-";
            }
            else {
                $eleve_regime =  old_mysql_result($calldata, $i, "regime");
                $eleve_doublant =  old_mysql_result($calldata, $i, "doublant");
            }
        }
        else{
            $eleve_login = $tab_eleve[$i]["login"];
            $eleve_nom = $tab_eleve[$i]["nom"];
            $eleve_prenom = $tab_eleve[$i]["prenom"];
            $eleve_sexe = $tab_eleve[$i]["sexe"];
            $eleve_naissance = $tab_eleve[$i]["naissance"];
            $elenoet =  $tab_eleve[$i]["elenoet"];
            $eleve_regime =  $tab_eleve[$i]["regime"];
            $eleve_doublant =  $tab_eleve[$i]["doublant"];
            //$date_sortie_elv = old_mysql_result($calldata, $i, "date_sortie");
            $date_sortie_elv = $tab_eleve[$i]["date_sortie"];
            // 20130607
            $mef_code = $tab_eleve[$i]["mef_code"];
        }

        $call_classe = mysqli_query($GLOBALS["mysqli"], "SELECT n.classe, n.id FROM j_eleves_classes c, classes n WHERE (c.login ='$eleve_login' and c.id_classe = n.id) order by c.periode DESC");
        $eleve_classe = @old_mysql_result($call_classe, 0, "classe");
        $eleve_id_classe = @old_mysql_result($call_classe, 0, "id");
        $pas_de_classe="n";
        if ($eleve_classe == '') {
            $eleve_classe = "<font color='red'>N/A</font>";
            $eleve_classe_csv = "N/A";
            $pas_de_classe="y";
        }
        else {
            $eleve_classe_csv = $eleve_classe;
        }

        $call_suivi = mysqli_query($GLOBALS["mysqli"], "SELECT u.* FROM utilisateurs u, j_eleves_professeurs s WHERE (s.login ='$eleve_login' and s.professeur = u.login and s.id_classe='$eleve_id_classe')");
        if(mysqli_num_rows($call_suivi)==0){
            $eleve_profsuivi_nom = "";
            $eleve_profsuivi_prenom = "";
        }
        else{
            $eleve_profsuivi_nom = @old_mysql_result($call_suivi, 0, "nom");
            $eleve_profsuivi_prenom = @old_mysql_result($call_suivi, 0, "prenom");
        }

        if ($eleve_profsuivi_nom == '') {
            if(($acces_class_const)&&($eleve_id_classe!="")) {
                $gepi_prof_suivi=retourne_denomination_pp($eleve_id_classe);
                $eleve_profsuivi_nom = "<a href='../classes/classes_const.php?id_classe=".$eleve_id_classe."' title=\"Définir le ".$gepi_prof_suivi."\"><font color='red'>N/A</font></a>";
            }
            else {
                $eleve_profsuivi_nom = "<font color='red'>N/A</font>";
            }
            $info_pp=$eleve_profsuivi_nom;

            $eleve_profsuivi_nom_csv = "N/A";
        }
        else {
            $eleve_profsuivi_nom_csv = $eleve_profsuivi_nom;
            $info_pp=casse_mot($eleve_profsuivi_nom,"maj")." ".casse_mot($eleve_profsuivi_prenom,"majf2");
        }
        //$delete_login = 'delete_'.$eleve_login;

        //========================================
        // Début de l'affichage de la ligne élève:
        $alt=$alt*(-1);
        echo "<tr class='lig$alt white_hover'>\n";

        echo "<td><p>" . $eleve_login . "</p></td>\n";
        $csv.="$eleve_login;";

        echo "<td>";

        if($_SESSION['statut']=='administrateur') {$avec_lien="y";}
        else {$avec_lien="n";}
        $lien_image_compte_utilisateur=lien_image_compte_utilisateur($eleve_login, "eleve", "", $avec_lien);
        if($lien_image_compte_utilisateur!="") {
            $correspondance_sso=temoin_compte_sso($eleve_login);
            if($correspondance_sso!="") {
                echo "<div style='float:right; width: 16px'>".$correspondance_sso."</div>";
            }
            echo "<div style='float:right; width: 16px'>".$lien_image_compte_utilisateur."</div>";
        }

        if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')||($_SESSION['statut']=='autre')||
            (($_SESSION['statut']=='cpe')&&(getSettingAOui('GepiAccesTouteFicheEleveCpe')))||
            (($_SESSION['statut']=='cpe')&&(is_cpe($_SESSION['login'],'',$eleve_login)))||
            (($_SESSION['statut']=='professeur')&&(is_pp($_SESSION['login'],"",$eleve_login))&&(getSettingAOui('GepiAccesGestElevesProfP')))||
            ((getSettingAOui('GepiAccesPPTousElevesDeLaClasse'))&&(is_pp($_SESSION['login'], $quelles_classes)))) {
            echo "<p><a href='modify_eleve.php?eleve_login=$eleve_login&amp;quelles_classes=$quelles_classes&amp;order_type=$order_type";
            if(isset($motif_rech)){echo "&amp;motif_rech=$motif_rech";}
            if(isset($mode_rech)){echo "&amp;mode_rech=$mode_rech";}
            echo "'>$eleve_nom $eleve_prenom</a>";
        }
        else {
            echo "$eleve_nom $eleve_prenom";
        }
        $csv.="$eleve_nom $eleve_prenom;";

        if ($date_sortie_elv!=0) {
             echo "<br/>";
             echo "<span class=\"red\"><b>Sortie le ".affiche_date_sortie($date_sortie_elv)."</b></span>";

            $csv.=$date_sortie_elv;
        }
        echo "</p></td>\n";
        $csv.=";";

        // Sexe
        echo "<td><p>$eleve_sexe</p></td>\n";
        $csv.="$eleve_sexe;";

        // Naissance
        echo "<td><p>".affiche_date_naissance($eleve_naissance)."</p></td>\n";
        $csv.=affiche_date_naissance($eleve_naissance).";";

        // Régime
        echo "<td><p>";
        if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
            echo "<a href='#' onclick=\"afficher_changement_regime('$eleve_login', '$eleve_regime') ;return false;\">";
            echo "<span id='regime_$eleve_login'>";
            echo $eleve_regime;
            echo "</span>";
            echo "</a>";
        }
        else {
            echo $eleve_regime;
        }
        echo "</p></td>\n";
        $csv.="$eleve_regime;";

        // Classe(s)
        if(($_SESSION['statut']=='administrateur')&&($pas_de_classe!="y")) {
            echo "<td><p><a href='../classes/classes_const.php?id_classe=$eleve_id_classe'>$eleve_classe</a></p></td>\n";
        }
        else {
            if(acces('/classes/ajout_eleve_classe.php', $_SESSION['statut'])) {
                echo "<td><p><a href=\"javascript:affiche_ajout_ele_clas('$eleve_login')\" title=\"Inscrire $eleve_nom $eleve_prenom dans une classe.\">$eleve_classe</a></p></td>\n";
            }
            else {
                echo "<td><p>$eleve_classe</p></td>\n";
            }
        }
        $csv.="$eleve_classe_csv;";

        // MEF
        /*echo "<td><p style='font-size:x-small;'>";
        if($acces_associer_eleve_mef) {
            echo "<a href='../mef/associer_eleve_mef.php?type_selection=nom_eleve&amp;nom_eleve=".$eleve_nom."' target='_blank'>";
        }
        if(isset($tab_mef[$mef_code])) {
            echo $tab_mef[$mef_code]['designation_courte'];
        }
        else {
            echo $mef_code;
        }
        if($acces_associer_eleve_mef) {
            echo "</a>";
        }
        echo "</p></td>\n";        */
        //$csv.=";";

        // Enseignements suivis
        echo "<td>";
        if(acces('/classes/eleve_options.php', $_SESSION['statut'])) {
            echo "<p><a href='../classes/eleve_options.php?login_eleve=".$eleve_login."&amp;id_classe=$eleve_id_classe&amp;quitter_la_page=y' target='_blank'><img src='../images/icons/chercher.png' width='16' height='16' alt='Enseignements suivis' title='Enseignements suivis' /></a></p>";
        }
        else {
            //https://127.0.0.1/steph/gepi-1.6.0/eleves/visu_eleve.php?ele_login=aubreev&onglet=enseignements
            echo "<p><a href='../eleves/visu_eleve.php?ele_login=".$eleve_login."&onglet=enseignements' target='_blank'><img src='../images/icons/chercher.png' width='16' height='16' alt='Enseignements suivis' title='Enseignements suivis' /></a></p>";
        }
        echo "</td>\n";
        //$csv.=";";

        // Professeur principal
        // 20130802
        if(($_SESSION['statut']=='administrateur')||($_SESSION['statut']=='scolarite')) {
            echo "<td><p>";
            echo "<a href='#' onclick=\"afficher_changement_prof_suivi('$eleve_login') ;return false;\">";
            echo "<span id='prof_suivi_$eleve_login'>";
            echo $info_pp;
            echo "</span>";
            echo "</a>";
            echo "</p></td>\n";
        }
        else {
            echo "<td><p>$info_pp</p></td>\n";
        }
        $csv.="$info_pp;";

        //if(($_SESSION['statut']=="administrateur")||($_SESSION['statut']=="scolarite")){
        if($_SESSION['statut']=="administrateur") {
            //echo "<td><p><center><INPUT TYPE=CHECKBOX NAME='$delete_login' VALUE='yes' /></center></p></td></tr>\n";
            //echo "<td><p align='center'><input type='checkbox' name='$delete_login' value='yes' /></p></td>\n";
            echo "<td><p align='center'><input type='checkbox' name='delete_eleve[]' value='$eleve_login' /></p></td>\n";
        }
        elseif($_SESSION['statut']=="scolarite") {
            echo "<td><p align='center'><span title=\"La suppression n'est possible qu'avec un compte administrateur\">-</span></p></td>\n";
        }

        if ((getSettingValue("active_module_trombinoscopes")=='y')&&
            ((($_SESSION['statut']=="professeur")&&(getSettingValue("GepiAccesGestPhotoElevesProfP")=='yes'))||
                ($_SESSION['statut']!="professeur"))) {

            //echo "<td style='white-space: nowrap;'><input name='photo[$i]' type='file' />\n";
            echo "<td style='white-space: nowrap; text-align:center;'>\n";

            //echo "<input name='photo[$i]' type='file' />\n";

            // Dans le cas du multisite, on préfère le login pour afficher les photos
            $nom_photo_test = (isset ($multisite) AND $multisite == 'y') ? $eleve_login : $elenoet;
            echo "<input type='hidden' name='quiestce[$i]' value=\"$nom_photo_test\" />\n";

            $photo=nom_photo($elenoet);
            $temoin_photo="";
            if($photo){
                echo "<div style='width: 32px; height: 32px; float:right;'>";

                $titre="$eleve_nom $eleve_prenom";

                $texte="<div align='center'>\n";
                $texte.="<img src='".$photo."' width='150' alt=\"$eleve_nom $eleve_prenom\" />";
                $texte.="<br />\n";
                $texte.="</div>\n";

                $temoin_photo="y";

                $tabdiv_infobulle[]=creer_div_infobulle('photo_'.$eleve_login,$titre,"",$texte,"",14,0,'y','y','n','n');

                echo "<a href='".$photo."' target='_blank' onmouseover=\"delais_afficher_div('photo_$eleve_login','y',-20,20,500,40,30);\">";

                echo "<img src='../mod_trombinoscopes/images/";
                if($eleve_sexe=="F") {
                    echo "photo_f.png";
                }
                else{
                    echo "photo_g.png";
                }
                echo "' width='32' height='32'  align='middle' border='0' alt='photo présente' title='photo présente' />";
                echo "</a>";

                echo "</div>";
            }


            if($nom_photo_test=="") {
                // Dans le cas multisite, le login élève est forcément renseigné
                echo "<span style='color:red'>Elenoet non renseigné</span>";
            }
            else {
                //echo "<span id='span_file_$i'></span>";
                echo "<span id='span_file_$i'>";
                //echo "<a href='javascript:add_file_upload($i)'><img src='../images/ico_edit16plus.png' width='16' height='16' alt='Choisir un fichier à uploader' /></a>";
                // Pour que si JavaScript est désactivé, on ait quand même le champ FILE
                echo "<input name='photo[$i]' type='file' />\n";
                echo "</span>";
            }

            echo "</td>\n";
        }

        echo "</tr>\n";
        $csv.="\r\n";

        $i++;
    }
    echo "</table>\n";
    echo "<p>Total : $nombreligne élève";
    if($nombreligne>1) {echo "s";}


?>
