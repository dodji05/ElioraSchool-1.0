<!DOCTYPE html>
<html xml:lang="fr" lang="fr">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Limitless - Responsive Web Application Kit by Eugene Kopyov</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/icons/icomoon/styles.css" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/minified/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/minified/core.min.css" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/minified/components.min.css" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/minified/colors.min.css" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/minified/grid.css" rel="stylesheet" type="text/css">
	<link href="./templates/limitless/assets/css/minified/doc.css" rel="stylesheet" type="text/css">
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="./templates/limitless/assets/js/core/libraries/jquery.min.js"></script>
	<script type="text/javascript" src="./templates/limitless/assets/js/core/libraries/bootstrap.min.js"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript" src="./templates/limitless/assets/js/core/app.js"></script>
	<!-- /theme JS files -->

</head>

<body onload="show_message_deconnexion();<?php if($tbs_charger_observeur) echo $tbs_charger_observeur;?>">
	<?php include('templates/origine/bandeau_template.php');?>
	<?php
	if (count($afficheAccueil->message_admin)){
		foreach ($afficheAccueil->message_admin as $value) {
			if ($value != "") {
?>
	<p class="rouge center">
		<?php echo $value; ?>
	</p>
<?php
			}
		}
		unset ($value);
	}
?>
<!-- Alertes sécurité -->
<?php
	if ($afficheAccueil->alert_sums>0) {
?>		
	  <div class="bs-callout bs-callout-danger">
	  <p>
		Alertes sécurité (niveaux cumulés) : <?php echo "<b>".$afficheAccueil->alert_sums."</b>"; ?> (
		<a href='gestion/security_panel.php'>Panneau de contrôle</a>)
	  </p>
	  </div>
<?php
	}
?>
<!-- Référencement	-->

<?php
	if (count($afficheAccueil->referencement)) {
	  foreach ($afficheAccueil->referencement as $value) {
?>
		<p class='referencement'>
		Votre établissement n'est pas référencé parmi les utilisateurs de ElioraSchool.
		<span>
			<br />
			<a href="javascript:ouvre_popup_reference('<?php echo $value['lien'];?>')" title="<?php echo $value['titre'];?>">
				<?php echo $value['titre']; ?>
			</a>
		</span>
		</p>
<?php
	  }
	  unset($value);
	}
?>

<!-- messages de sécurité -->
<?php
	if (count($afficheAccueil->probleme_dir)) {
	echo "<div class=\"bs-callout bs-callout-danger\">";
	  foreach ($afficheAccueil->probleme_dir as $value) {
?>		
		
		<p>
			<?php echo $value; ?>
		</p>

<?php
	  }
	  unset($value);
	  echo "</div>";
	}
?>
	

	<!-- Page container -->
	<div class="page-container">

		<!-- Page content -->
					
			<!-- Main content -->
			<div class="content-wrapper">
					<!-- erreurs d'affectation d'élèves -->


	
	<a id="contenu" class="invisible">Début de la page</a>

<!-- Signalements d'erreurs d'affectations -->
<?php
	if((isset($afficheAccueil->signalement))&&($afficheAccueil->signalement!="")) {
?>
	  <div class='infobulle_corps' style='text-align:center; margin: 3em; padding:0.5em; color:red; border: 1px dashed red;'>
		<?php echo $afficheAccueil->signalement; ?>
	  </div>

<?php
	}
?>
<!-- Actions à effectuer -->
<?php

	if((getSettingValue('active_cahiers_texte')=='y')&&(getSettingValue('GepiCahierTexteVersion')=='2')) {
        if(!file_exists("./temp/info_jours.js")) {
			creer_info_jours_js();
			if(!file_exists("./temp/info_jours.js")) {
                $sql="SELECT * FROM infos_actions WHERE titre='Fichier info_jours.js absent'";
                $test_info_jours = mysqli_query($mysqli, $sql);
                if($test_info_jours->num_rows == 0) {
                    enregistre_infos_actions("Fichier info_jours.js absent","Le fichier info_jours.js destiné à tenir compte des jours ouvrés dans les saisies du cahier de textes n'est pas renseigné.\nVous pouvez le renseigner en <a href='$gepiPath/edt_organisation/admin_horaire_ouverture.php?action=visualiser'>saisissant ou re-validant les horaires d'ouverture</a> de l'établissement.","administrateur",'statut');
                }
            }
        } else {
            $sql="SELECT * FROM infos_actions WHERE titre='Fichier info_jours.js absent'";
            $test_info_jours = mysqli_query($mysqli, $sql);
            if($test_info_jours->num_rows > 0) {
				while($lig_action=$test_info_jours->fetch_object($test_info_jours)) {
					del_info_action($lig_action->id);
				}
            }
        }
    }

	affiche_infos_actions();
?>
<!-- messagerie-->
<?php
	if(in_array($_SESSION['statut'], array('administrateur', 'professeur', 'cpe', 'scolarite'))) {
		if($gepiVersion!="master") {
			$gepiVersionConnue=getPref($_SESSION['login'], 'gepiVersionConnue', '');
			if($gepiVersionConnue!=$gepiVersion) {
				$message_nouvelle_version_gepi=afficher_message_nouvelle_version_gepi();
			}
		}
	}

	if(in_array($_SESSION['statut'], array('professeur', 'cpe', 'scolarite', 'responsable', 'eleve'))) {
		//echo "<div align='center'>".afficher_les_evenements()."</div>";
		$liste_evenements=afficher_les_evenements();
	}

	if(($_SESSION['statut']=='professeur')&&(getSettingAOui('active_mod_abs_prof'))) {
		$message_remplacements_confirmes=affiche_remplacements_confirmes($_SESSION['login']);
		$message_remplacements_proposes=affiche_remplacements_en_attente_de_reponse($_SESSION['login']);
	}

	if((getSettingAOui('active_mod_abs_prof'))&&
		((($_SESSION['statut']=="administrateur")||
		(($_SESSION['statut']=="scolarite")&&(getSettingAOui('AbsProfAttribuerRemplacementScol')))||
		(($_SESSION['statut']=="cpe")&&(getSettingAOui('AbsProfAttribuerRemplacementCpe')))))) {
		$message_remplacements_a_valider=test_reponses_favorables_propositions_remplacement();
	}

	if((getSettingAOui('active_mod_abs_prof'))&&
		($_SESSION['statut']=="eleve")) {
		$message_remplacements=affiche_remplacements_eleve($_SESSION['login']);
	}

	if((getSettingAOui('active_mod_abs_prof'))&&
		($_SESSION['statut']=="responsable")) {

		$message_remplacements="";
		$tab_eleves_en_responsabilite=get_enfants_from_resp_login($_SESSION['login'], 'avec_classe', "yy");
		for($loop=0;$loop<count($tab_eleves_en_responsabilite);$loop+=2) {
			$tmp_remplacements=affiche_remplacements_eleve($tab_eleves_en_responsabilite[$loop]);
			if($tmp_remplacements!="") {
				$message_remplacements.="<p class='bold'>".$tab_eleves_en_responsabilite[$loop+1]."</p>".$tmp_remplacements;
			}
		}
	}

	if(getSettingAOui('active_mod_disc_pointage')) {
		$affichage_pointages="";
		if(($_SESSION['statut']=='eleve')&&(getSettingAOui('disc_pointage_aff_totaux_ele'))) {
			$pointages_ele_courant=retourne_tab_html_pointages_disc($_SESSION['login']);
			if($pointages_ele_courant!="") {
				$affichage_pointages.="<div class=\"postit\">".$pointages_ele_courant."</div>";
			}
		}
		elseif(($_SESSION['statut']=='responsable')&&(getSettingAOui('disc_pointage_aff_totaux_resp'))) {
			$tab_ele_resp=get_enfants_from_resp_login($_SESSION['login'], 'avec_classe', "yy");
			for($loop=0;$loop<count($tab_ele_resp);$loop+=2) {
				$pointages_ele_courant=retourne_tab_html_pointages_disc($tab_ele_resp[$loop]);
				if($pointages_ele_courant!="") {
					$affichage_pointages.="<div class=\"postit\"><p><strong>".get_nom_prenom_eleve($tab_ele_resp[$loop])."</strong></p>".$pointages_ele_courant."</div>";
				}
			}
		}
	}

	$chaine_tableaux_page_accueil="";
	if (in_array($_SESSION['statut'], array("scolarite", "administrateur"))) {
		/*if(getPref($_SESSION['login'], "accueil_tableau_ouverture_periode", "y")!="n") {
			$sql="SELECT * FROM classes c, periodes p WHERE c.id=p.id_classe LIMIT 1;";
			$test=mysqli_query($mysqli, $sql);
			if(mysqli_num_rows($test)>0) {
				if(acces("/bulletin/verrouillage.php", $_SESSION['statut'])) {
					$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'ouverture/verrouillage des périodes.";
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.=chaine_title_explication_verrouillage_periodes();
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.="Vous pouvez supprimer l'affichage de ce tableau dans 'Gérer mon compte'.\"><a href='$gepiPath/bulletin/verrouillage.php' title=\"Modifier l'ouverture/verrouillage des périodes.\" style='color:black;'><strong>Tableau de l'ouverture/verrouillage des périodes</strong>&nbsp;<img src='$gepiPath/images/edit16.png' class='icone16' alt='Modifier' /></a>";
				}
				else {
					$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'ouverture/verrouillage des périodes.";
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.=chaine_title_explication_verrouillage_periodes();
					$chaine_tableaux_page_accueil.="\n\n";
					$chaine_tableaux_page_accueil.="Vous pouvez supprimer l'affichage de ce tableau dans 'Gérer mon compte'.\"><strong>Tableau de l'ouverture/verrouillage des périodes</strong>";
				}
				$chaine_tableaux_page_accueil.=affiche_tableau_periodes_ouvertes();
				$chaine_tableaux_page_accueil.="</div>";
			}
		}*/

		/*if((getSettingAOui('active_bulletins'))&&
		((getSettingValue("acces_app_ele_resp")=="manuel")||
		(getSettingValue("acces_app_ele_resp")=="manuel_individuel"))&&
		(getPref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", "y")!="n")) {
			if(acces("/classes/acces_appreciations.php", $_SESSION['statut'])) {
				$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe.\n\nVous pouvez supprimer cet affichage dans 'Gérer mon compte'.\"><a href='$gepiPath/classes/acces_appreciations.php' title=\"Modifier l'accès aux appréciations et avis.\" style='color:black;'><strong>Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe</strong>&nbsp;<img src='$gepiPath/images/edit16.png' class='icone16' alt='Modifier' /></a>";
			}
			else {
				$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe.\n\nVous pouvez supprimer cet affichage dans 'Gérer mon compte'.\"><strong>Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe</strong>";
			}
			$chaine_tableaux_page_accueil.=affiche_tableau_acces_ele_parents_appreciations_et_avis_bulletins();
			$chaine_tableaux_page_accueil.="</div>";
		}*/
	}
	elseif(($_SESSION['statut']=='professeur')&&
		(getSettingAOui('active_bulletins'))&&
		((getSettingValue("acces_app_ele_resp")=="manuel")||
		(getSettingValue("acces_app_ele_resp")=="manuel_individuel"))&&
		(is_pp($_SESSION['login']))&&
		(getPref($_SESSION['login'], "accueil_tableau_acces_app_bull_ele_resp", "y")!="n")) {

		// Chemin d'ouverture à revoir...
		if(getSettingAOui('GepiAccesRestrAccesAppProfP')) {
			$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe.\n\nVous pouvez supprimer cet affichage dans 'Gérer mon compte'.\"><a href='$gepiPath/classes/acces_appreciations.php' title=\"Modifier l'accès aux appréciations et avis.\" style='color:black;'><strong>Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe</strong>&nbsp;<img src='$gepiPath/images/edit16.png' class='icone16' alt='Modifier' /></a>";
		}
		else {
			$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe.\n\nVous pouvez supprimer cet affichage dans 'Gérer mon compte'.\"><strong>Tableau de l'accès parents/élève aux appréciations et avis du conseil de classe</strong>";
		}
		$chaine_tableaux_page_accueil.=affiche_tableau_acces_ele_parents_appreciations_et_avis_bulletins("pp");
		$chaine_tableaux_page_accueil.="</div>";
	}


	if((getSettingAOui('abs2_afficher_alerte_nj'))&&(in_array($_SESSION['statut'], array('cpe')))) {
		// Que pour les CPE pour le moment

		// Autres paramètres à implémenter: envoi de mail tous les tel jour de la semaine ou tel jour du mois (à la connexion du premier cpe, ou scolarite ou administrateur)
		// Renseigner un setting avec la date du dernier envoi... calculer la date de l'envoi suivant au cas où... ou renseigner une date d'envoi suivant... et si on dépasse, on envoie.

		// Paramétrage des jours d'affichage non encore implémenté
		//$tab_jours_affichage_alerte_nj=array(1,2,3,4,5); // strftime("%u")
		$tab_jours_affichage_alerte_nj=array(0,1,2,3,4,5,6); // strftime("%u")
		if(in_array(strftime("%u"), $tab_jours_affichage_alerte_nj)) {
			$lignes_alerte_abs2_nj=abs2_afficher_tab_alerte_nj();
			if($lignes_alerte_abs2_nj!="") {
				$chaine_tableaux_page_accueil.="<div align='center' style='font-size:xx-small; margin:0.5em;' title=\"Tableau des absences non justifiées depuis un certain temps.\">";
				$chaine_tableaux_page_accueil.=$lignes_alerte_abs2_nj;
				$chaine_tableaux_page_accueil.="</div>";
			}
		}
	}

	if ((count($afficheAccueil->message))||
	($chaine_tableaux_page_accueil!="")||
	((isset($liste_evenements))&&($liste_evenements!=""))||
	((isset($message_remplacements))&&($message_remplacements!=""))||
	((isset($message_remplacements_proposes))&&($message_remplacements_proposes!=""))||
	((isset($message_remplacements_a_valider))&&($message_remplacements_a_valider!=""))||
	((isset($message_remplacements_confirmes))&&($message_remplacements_confirmes!=""))||
	((isset($affichage_pointages))&&($affichage_pointages!=""))||
	((isset($message_nouvelle_version_gepi))&&($message_nouvelle_version_gepi!=""))) :
?>

	<div class="panneau_affichage">
		<div class="panneau_liege">
			<?php if ($_SESSION['statut'] == "administrateur"): ?>
			<div style="position:absolute;width:30px;">
				<a href="./messagerie/index.php"><img src="./images/add_message.png" alt="Ajouter un message" title="Ajouter un message"/></a>
			</div> 
			<?php endif ?>
			<div class="panneau_coingh"></div>
			<div class="panneau_coindh"></div>
			<div class="panneau_haut"></div>
			<div class="panneau_droite"></div>
			<div class="panneau_gauche"></div>
			<div class="panneau_coingb"></div>
			<div class="panneau_coindb"></div>
			<div class="panneau_bas"></div>
			<div class="panneau_centre">
				<?php 
				if((isset($message_nouvelle_version_gepi))&&($message_nouvelle_version_gepi!="")) {
					echo "<div class='postit' title=\"Votre ElioraSchool a été mis à jour.\">".$message_nouvelle_version_gepi."</div>";
				}

				if((isset($liste_evenements))&&($liste_evenements!="")) {
					echo "<div class='postit' title=\"Événements à venir (définis) pour vos classes.\">".$liste_evenements."</div>";
				}

				if(isset($message_remplacements_confirmes)) {
					echo $message_remplacements_confirmes;
				}

				if(isset($message_remplacements_proposes)) {
					echo $message_remplacements_proposes;
				}

				if(isset($message_remplacements_a_valider)) {
					echo $message_remplacements_a_valider;
				}

				if((isset($message_remplacements))&&($message_remplacements!="")) {
					echo $message_remplacements;
				}

				if((isset($affichage_pointages))&&($affichage_pointages!="")) {
					echo $affichage_pointages;
				}

				if (count($afficheAccueil->message)) :
					foreach ($afficheAccueil->message as $value) : 
				?>
				<div class="postit"><?php
					if(acces_messagerie($_SESSION['statut'])) {
						if((isset($value['statuts_destinataires']))&&($value['statuts_destinataires']!="_")) {
							echo "<div style='float:right; width:16' title=\"Éditer/modifier le message.\"><a href='$gepiPath/messagerie/index.php?id_mess=".$value['id']."'><img src='images/edit16.png' class='icone16' /></a></div>";
						}
					}
					echo $value['message'];
				?></div>
				<?php
				endforeach;
				endif;
				?>
				<?php
					unset ($value); 
					echo $chaine_tableaux_page_accueil;
				?>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>

	<?php endif; ?>
<!-- <div id='messagerie'> -->

	<!--	</div> -->
<?php /* } */ ?>
<?php

	if ($_SESSION['statut'] =="professeur") {
?>
		<p class='bold'>
		  <a href='accueil_simpl_prof.php'>
			Interface graphique
		  </a>
		</p>
<?php
	}
?>
<!-- début corps menu	-->



				<!-- Page header -->
				<div class="page-header">
					<div class="page-header-content">
						<div class="page-title">
							<h4><i class="icon-arrow-left52 position-left"></i> <span class="text-semibold">Starters</span> - 2 Columns</h4>
						</div>

						<div class="heading-elements">
							<a href="#" class="btn btn-labeled btn-labeled-right bg-blue heading-btn">Button <b><i class="icon-menu7"></i></b></a>
						</div>
					</div>

					<div class="breadcrumb-line">
						<ul class="breadcrumb">
							<li><a href="index.html"><i class="icon-home2 position-left"></i> Home</a></li>
							<li><a href="2_col.html">Starters</a></li>
							<li class="active">2 columns</li>
						</ul>

						<ul class="breadcrumb-elements">
							<li><a href="#"><i class="icon-comment-discussion position-left"></i> Link</a></li>
							<li class="dropdown">
								<a href="#" class="dropdown-toggle" data-toggle="dropdown">
									<i class="icon-gear position-left"></i>
									Dropdown
									<span class="caret"></span>
								</a>

								<ul class="dropdown-menu dropdown-menu-right">
									<li><a href="#"><i class="icon-user-lock"></i> Account security</a></li>
									<li><a href="#"><i class="icon-statistics"></i> Analytics</a></li>
									<li><a href="#"><i class="icon-accessibility"></i> Accessibility</a></li>
									<li class="divider"></li>
									<li><a href="#"><i class="icon-gear"></i> All settings</a></li>
								</ul>
							</li>
						</ul>
					</div>
				</div>
				<!-- /page header -->


				<!-- Content area -->
				<div class="content">
				
		<!-- menu	général -->

	<?php
	if (count($afficheAccueil->titre_Menu)) {
	  foreach ($afficheAccueil->titre_Menu as $newEntreeMenu) {
	  echo "<div class='panel panel-flat'>";
      if ($newEntreeMenu->texte!='bloc_invisible') {
?>
		
						<div class="panel-heading bas-etg">
							<h5 class="panel-title"><img src="<?php echo $newEntreeMenu->icone['chemin'] ?>" alt="<?php echo $newEntreeMenu->icone['alt'] ?>" /> - <?php echo $newEntreeMenu->texte ?></h5>
						</div>
							  <div class="row">
								
						
						
					<?php
					
		if ($newEntreeMenu->texte=="Votre flux RSS") {
?>
		  
<?php
		  if ($afficheAccueil->canal_rss["mode"]==1) {
?>
			<h3 class="colonne ie_gauche flux_rss" title="A utiliser avec un lecteur de flux rss" onclick="changementDisplay('divuri', 'divexpli')" >
			  Votre uri pour les cahiers de textes
			</h3>
			<p class="colonne ie_droite vert">
			  <span id="divexpli" style="display: block;">
				<?php echo $afficheAccueil->canal_rss['expli']; ?>
			  </span>
			  <span id="divuri" style="display: none;">
			  <?php
				if(!isset($afficheAccueil->canal_rss_plus)) {
			  ?>
				<a href="<?php echo $afficheAccueil->canal_rss['lien']; ?>" onclick="window.open(this.href, '_blank'); return false;" >
				  <?php echo $afficheAccueil->canal_rss['texte']; ?>
				</a>
			  <?php
				}
				else {
					echo $afficheAccueil->canal_rss_plus;
				}
			  ?>
			  </span>
			</p>

<?php
		  }else if ($afficheAccueil->canal_rss["mode"]==2){
?>
			<h3 class="colonne ie_gauche">
			  Votre uri pour les cahiers de textes
			</h3>
			<p class="colonne ie_droite vert">
			  Veuillez la demander à l'administration de votre établissement.
			</p>
<?php
		  }
?>
		 
<?php
		}else{
		  if (count($afficheAccueil->menu_item)) {
			foreach ($afficheAccueil->menu_item as $newentree) {
			
				
			  if ($newentree->indexMenu==$newEntreeMenu->indexMenu) {
			
?>
			

<?php
				  if ($newentree->titre=="Sauvegarde de la base") {
?>
	<div class="div_tableau cellule_1">
		<form enctype="multipart/form-data" action="gestion/accueil_sauve.php" method="post" id="formulaire" >
			<p>
				<?php
					echo add_token_field();
				?>
				<input type='hidden' name='action' value='system_dump' />
				<input type="submit" value="Lancer une sauvegarde de la base de données" />
			</p>
		</form>
		<p class='small'>
			Les répertoires "documents" (<em>contenant les documents joints aux cahiers de texte</em>) et "photos" (<em>contenant les photos du trombinoscope</em>) ne seront pas sauvegardés.<br />
			Un outil de sauvegarde spécifique se trouve en bas de la page <a href='./gestion/accueil_sauve.php#zip'>gestion des sauvegardes</a>.
		</p>
	</div>
<?php
			  }else{
?>

				 <!--Insertion 2-->
				
        <div class="col-xs-6 col-md-4"><a href="<?php echo mb_substr($newentree->chemin,1) ?>"><?php echo $newentree->titre ?></a></div>
		 <div class="col-xs-12 col-md-8"><?php echo $newentree->expli ?></div>
				
				
				
<?php
			  }
			// echo "</table>";
			//	echo"</div>";
?>
			
<?php
			  }
			}
			}
			unset($newentree);
		  }
		}
		//echo "</table>";
	  echo "</div>";
	  echo "</div>";}
	  unset($newEntreeMenu);
	}
    
?>

<!-- début RSS	-->
		
<!-- fin RSS	-->




					<!-- Simple panel -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Simple panel</h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
						</div>

						<div class="panel-body">
							<h6 class="text-semibold">Start your development with no hassle!</h6>
							<p class="content-group">Common problem of templates is that all code is deeply integrated into the core. This limits your freedom in decreasing amount of code, i.e. it becomes pretty difficult to remove unnecessary code from the project. Limitless allows you to remove unnecessary and extra code easily just by removing the path to specific LESS file with component styling. All plugins and their options are also in separate files. Use only components you actually need!</p>

							<h6 class="text-semibold">What is this?</h6>
							<p class="content-group">Starter kit is a set of pages, useful for developers to start development process from scratch. Each layout includes base components only: layout, page kits, color system which is still optional, bootstrap files and bootstrap overrides. No extra CSS/JS files and markup. CSS files are compiled without any plugins or components. Starter kit was moved to a separate folder for better accessibility.</p>

							<h6 class="text-semibold">How does it work?</h6>
							<p>You open one of the starter pages, add necessary plugins, uncomment paths to files in components.less file, compile new CSS. That's it. I'd also recommend to open one of main pages with functionality you need and copy all paths/JS code from there to your new page, it's just faster and easier.</p>
						</div>
					</div>
					<!-- /simple panel -->


					<!-- Table -->
					<div class="panel panel-flat">
						<div class="panel-heading">
							<h5 class="panel-title">Basic table</h5>
							<div class="heading-elements">
								<ul class="icons-list">
			                		<li><a data-action="collapse"></a></li>
			                		<li><a data-action="close"></a></li>
			                	</ul>
		                	</div>
	                	</div>

	                	<div class="panel-body">
	                		Starter pages include the most basic components that may help you start your development process - basic grid example, panel, table and form layouts with standard components. Nothing extra.
	                	</div>

						<div class="table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>#</th>
										<th>First Name</th>
										<th>Last Name</th>
										<th>Username</th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td>1</td>
										<td>Eugene</td>
										<td>Kopyov</td>
										<td>@Kopyov</td>
									</tr>
									<tr>
										<td>2</td>
										<td>Victoria</td>
										<td>Baker</td>
										<td>@Vicky</td>
									</tr>
									<tr>
										<td>3</td>
										<td>James</td>
										<td>Alexander</td>
										<td>@Alex</td>
									</tr>
									<tr>
										<td>4</td>
										<td>Franklin</td>
										<td>Morrison</td>
										<td>@Frank</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<!-- /table -->


					<!-- Grid -->
					<div class="row">
						<div class="col-md-6">

							<!-- Horizontal form -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Horizontal form</h5>
									<div class="heading-elements">
										<ul class="icons-list">
					                		<li><a data-action="collapse"></a></li>
					                		<li><a data-action="close"></a></li>
					                	</ul>
				                	</div>
			                	</div>

								<div class="panel-body">
									<form class="form-horizontal" action="#">
										<div class="form-group">
											<label class="control-label col-lg-2">Text input</label>
											<div class="col-lg-10">
												<input type="text" class="form-control">
											</div>
										</div>

										<div class="form-group">
											<label class="control-label col-lg-2">Password</label>
											<div class="col-lg-10">
												<input type="password" class="form-control">
											</div>
										</div>

				                        <div class="form-group">
				                        	<label class="control-label col-lg-2">Select</label>
				                        	<div class="col-lg-10">
					                            <select name="select" class="form-control">
					                                <option value="opt1">Basic select</option>
					                                <option value="opt2">Option 2</option>
					                                <option value="opt3">Option 3</option>
					                                <option value="opt4">Option 4</option>
					                                <option value="opt5">Option 5</option>
					                                <option value="opt6">Option 6</option>
					                                <option value="opt7">Option 7</option>
					                                <option value="opt8">Option 8</option>
					                            </select>
				                            </div>
				                        </div>

										<div class="form-group">
											<label class="control-label col-lg-2">Textarea</label>
											<div class="col-lg-10">
												<textarea rows="5" cols="5" class="form-control" placeholder="Default textarea"></textarea>
											</div>
										</div>

										<div class="text-right">
											<button type="submit" class="btn btn-primary">Submit form <i class="icon-arrow-right14 position-right"></i></button>
										</div>
									</form>
								</div>
							</div>
							<!-- /horizotal form -->

						</div>

						<div class="col-md-6">

							<!-- Vertical form -->
							<div class="panel panel-flat">
								<div class="panel-heading">
									<h5 class="panel-title">Vertical form</h5>
									<div class="heading-elements">
										<ul class="icons-list">
					                		<li><a data-action="collapse"></a></li>
					                		<li><a data-action="close"></a></li>
					                	</ul>
				                	</div>
			                	</div>

								<div class="panel-body">
									<form action="#">
										<div class="form-group">
											<label>Text input</label>
											<input type="text" class="form-control">
										</div>

				                        <div class="form-group">
				                        	<label>Select</label>
				                            <select name="select" class="form-control">
				                                <option value="opt1">Basic select</option>
				                                <option value="opt2">Option 2</option>
				                                <option value="opt3">Option 3</option>
				                                <option value="opt4">Option 4</option>
				                                <option value="opt5">Option 5</option>
				                                <option value="opt6">Option 6</option>
				                                <option value="opt7">Option 7</option>
				                                <option value="opt8">Option 8</option>
				                            </select>
				                        </div>

										<div class="form-group">
											<label>Textarea</label>
											<textarea rows="4" cols="4" class="form-control" placeholder="Default textarea"></textarea>
										</div>

										<div class="text-right">
											<button type="submit" class="btn btn-primary">Submit form <i class="icon-arrow-right14 position-right"></i></button>
										</div>
									</form>
								</div>
							</div>
							<!-- /vertical form -->

						</div>
					</div>
					<!-- /grid -->


					<!-- Footer -->
					<div class="footer text-muted">
						&copy; 2015. <a href="#">Limitless Web App Kit</a> by <a href="http://themeforest.net/user/Kopyov" target="_blank">Eugene Kopyov</a>
					</div>
					<!-- /footer -->

				</div>
				<!-- /content area -->

			</div>
			<!-- /main content -->

		</div>
		<!-- /page content -->

	</div>
	<!-- /page container -->
<!-- Alarme sonore -->
<?php
	echo joueAlarme();
?>
<!-- Fin alarme sonore -->

<div id="alert_cache" style="z-index:2000;
							display:none;
							position:absolute;
							top:0px;
							left:0px;
							background-color:#000000;
							width:200px;
							height:200px;"> &nbsp;</div>
<div id="alert_entete" style="z-index:2000;
								display:none;
								position:absolute;"><img   src="./images/alerte_entete.png" alt="alerte" /></div>
<div id="alert_popup" style="z-index:2000;
								text-align:justify;
								width:600px;
								height:130px;
								border:1px solid black;
								background-color:white;
								padding-top:10px;
								padding-left:20px;
								padding-right:20px;
								display:none;
								position:absolute;
								background-image:url('./images/degrade_noir.png');
								background-repeat:repeat-x;
								background-position: left bottom;">
	<div id="alert_message"></div>
	<div id="alert_button" style="margin:5px auto;width:90px;">
		<div id="alert_bouton_ok" style="float:left;"><img src="./images/bouton_continue.png" alt="ok" /></div>
	</div>
</div>

<?php
	include("./lib/footer_tab_infobulle.php");
?>

</body>
</html>