<?php
/**
 * $Id$
*/
?>

<!-- ************************* -->
<!-- Début du corps de la page -->
<!-- ************************* -->
<?php
		maintien_de_la_session();
	?>

	<!-- Témoin de contact du serveur -->
	<?php
		if($tbs_aff_temoin_check_serveur=='y') {
			temoin_check_srv();
		}
	?>
	
	<ol>
		<?php

			if((getSettingAOui('active_mod_alerte'))&&(in_array($_SESSION['statut'], array('professeur', 'administrateur', 'scolarite', 'cpe', 'autre')))) {
				if(check_mae($_SESSION['login'])) {
					if(isset($_SERVER['SCRIPT_NAME'])) {
						// Pour éviter de faire apparaitre le témoin de message sur des pages présentées lors des conseils de classe:
						$tab_pages_temoin_fixe_messagerie_exclu=array(
																	// La page de saisie de message elle-même:
																	// sinon on a le témoin clignotant alors qu'on est en train de cocher "lu",
																	// en train de rédiger une réponse,...
																	"/mod_alerte/form_message.php",
																	// Dans Gérer mon compte: les tests alerte perturbent le changement de mot de passe
																	"/utilisateurs/mon_compte.php",
																	// Les pages que l'on affiche pendant un conseil de classe
																	// On n'a pas le temps pendant les conseils de cocher "lu"
																	"/visualisation/",
																	"/bulletin/bull_index.php",
																	"/saisie/saisie_avis",
																	"/prepa_conseil/index3.php",
																	"/prepa_conseil/edit_limite.php",
																	// Dans les pages qui font de gros traitements par tranches
																	// avec submit automatique via JavaScript
																	"/responsables/maj_import3.php",
																	"/cahier_texte_2/archivage_cdt.php",
																	"/utilitaires/",
																	"/gestion/accueil_sauve.php",
																	// Dans les pages d'initialisation de l'année
																	"/init",
																	"/edt_organisation/edt_init");
						$cpt_tab_pages_temoin_fixe_messagerie_exclu=0;
						for($loop=0;$loop<count($tab_pages_temoin_fixe_messagerie_exclu);$loop++) {
							if(preg_match("@$tab_pages_temoin_fixe_messagerie_exclu[$loop]@", $_SERVER['SCRIPT_NAME'])) {
								$cpt_tab_pages_temoin_fixe_messagerie_exclu++;
								break;
							}
						}
						if($cpt_tab_pages_temoin_fixe_messagerie_exclu==0) {
						//echo "<li class='ligne_premier_menu'>".affichage_temoin_messages_recus()."</li>";
						}
						else {
							// Il se passe un truc bizarre sur /utilisateurs/mon_compte.php dans le cas où on doit changer de mot de passe
							/*
							echo "
		<li class='ligne_premier_menu'>".affichage_temoin_messages_recus("header_seul")."
		</li>
							";
							*/
						}
					}
				}
			}

		/*	if (count($tbs_premier_menu)) {
				foreach ($tbs_premier_menu as $value) {
					if ("$value[texte]"!="") {
						echo "
	<li class='ligne_premier_menu'>
		<a href='$value[lien]'".insert_confirm_abandon().">
			<img src='$value[image]' alt='$value[alt]' title='$value[title]' class='icone16' />
			<span class='menu_bandeau'>
				&nbsp;$value[texte]
			</span>
		</a>
	</li>
						";
						//var_dump($tbs_premier_menu);
					}
				}
				unset($value);
			}*/
		?>
	</ol>


	<!-- titre de la page -->
		<h1><?php //echo $titre_page; ?></h1>
<!-- Main navbar -->
	<div class="navbar navbar-inverse">
		<div class="navbar-header">
			 <a class="navbar-brand" href="#"><h4><?php echo $titre_page; ?></h4></a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
				<li><a class="sidebar-mobile-main-toggle"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			<ul class="nav navbar-nav">
				<li><a class="sidebar-control sidebar-main-toggle hidden-xs"><i class="icon-paragraph-justify3"></i></a></li>
			</ul>

			<ul class="nav navbar-nav navbar-right">
			<li>
					<a href="<?php echo $tbs_premier_menu[0]['lien']?>">
						<i class="icon-home"></i> <?php echo $tbs_premier_menu[0]['texte']?>
						<span class="visible-xs-inline-block position-right">Icon link</span>
					</a>						
				</li>
				<li>
					<!-- statut utilisateur -->
					<a href='#'>
		<?php
		
			if (count($tbs_statut)) {
				foreach ($tbs_statut as $value) {
					echo $value['texte'];
					if (count($donnees_enfant)) {
						foreach ($donnees_enfant as $value2) {
							echo "1
				
						".$value2['nom']." (<em>".$value2['classe']."</em>)
							";
						}
						unset($value2);
					}
					echo "
			
					";
				}
				unset($value);
			}
		?>
			</li>
			</a>
			<li>
					
							<?php echo affichage_temoin_messages_recus()?>
				</li>
				<li>	

				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
						<img src="assets/images/image.png" alt="">
						<span><?php echo $tbs_nom_prenom; ?></span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="<?php echo $tbs_premier_menu[1]['lien']?>"> <i class="icon-user-plus"></i> <?php echo $tbs_premier_menu[1]['texte']?></a></li>
						<!--<li><a href="#"><i class="icon-coins"></i> My balance</a></li>
						<li><a href="#"><span class="badge badge-warning pull-right">58</span> <i class="icon-comment-discussion"></i> Messages</a></li>
						<li class="divider"></li>
						<li><a href="#"><i class="icon-cog5"></i> Account settings</a></li>-->
						<li><a href="<?php echo $tbs_premier_menu[2]['lien']?>"> <i class="icon-switch2"></i> <?php echo $tbs_premier_menu[2]['texte']?> </a></li>
						

					</ul>
				</li>
			</ul>
		</div>
	</div>
	<!-- /main navbar -->
	<div class="page-content">
	<!-- Main sidebar -->
			<div class="sidebar sidebar-main">
				<div class="sidebar-content">

					<!-- User menu -->
					<div class="sidebar-user">
						<div class="category-content">
							<div class="media">
								<a href="#" class="media-left"><img src="assets/images/image.png" class="img-circle img-sm" alt=""></a>
								<div class="media-body">
									<span class="media-heading text-semibold">VictoriaTTTTT Baker</span>
									
								</div>

								<div class="media-right media-middle">
									<ul class="icons-list">
										<li>
											<a href="#"><i class="icon-cog3"></i></a>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<!-- /user menu -->


					<!-- Main navigation -->
					<?php
						    /**
     *
     * @global string
     * @global string
     * @param type $tab
     * @param type $niveau
     */
	function ligne_menu_barre($tab,$niveau) {
		global $gepiPath, $themessage;

            $afficheTitle='';
            if (isset ($tab['title']) && $tab['title'] !='') {
              $afficheTitle= ' title="'.$tab['title'].'"';
            }

		if(isset($tab['sous_menu'])) {
			echo "<li";
			/*if($niveau==1) {
				echo " class='li_inline'";
			}
			else {
				echo " class='plus'";
			}*/
			echo ">\n";

			// éventuellement le lien peut être vide
			if ($tab['lien']=="") {
				echo $tab['texte']."\n";
			}
			elseif (mb_substr($tab['lien'],0,4) == 'http') {
				echo "<a href=\"".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle;
				if(isset($tab['target'])) {
					echo " target='".$tab['target']."'";
				}
				echo ">".$tab['texte']."</a>\n";
			}
			else {
				echo "<a href=\"$gepiPath".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle;
				if(isset($tab['target'])) {
					echo " target='".$tab['target']."'";
				}
				echo ">".$tab['texte']."</a>\n";
			}

			echo "<ul class='niveau".$tab['niveau_sous_menu']."'>\n";
			for($i=0;$i<count($tab['sous_menu']);$i++) {
				ligne_menu_barre($tab['sous_menu'][$i], $tab['niveau_sous_menu']);
			}
			echo "</ul>\n";
			echo "</li>\n";
		}
		else {
			echo "<li";
			if($niveau==1) {
				echo " class='li_inline'";
			}
			echo ">";

			// éventuellement le lien peut être vide
			if ($tab['lien']=="") {
				echo $tab['texte']."\n";
			}
			elseif (mb_substr($tab['lien'],0,4) == 'http') {
				echo "<a href=\"".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle;
				if(isset($tab['target'])) {
					echo " target='".$tab['target']."'";
				}
				echo ">".$tab['texte']."</a>\n";
			}
			else {
				echo "<a href=\"$gepiPath".$tab['lien']."\"".insert_confirm_abandon().$afficheTitle;
				if(isset($tab['target'])) {
					echo " target='".$tab['target']."'";
				}
				echo ">".$tab['texte']."</a>";
			}
			echo "</li>\n";
		}
	}
					?>
					<div class="sidebar-category sidebar-category-visible">
						<div class="category-content no-padding">
							<ul class="navigation navigation-main navigation-accordion">

								<!-- Main -->
								<?php if (count($tbs_menu_admin)) : ?>
									<?php foreach ($tbs_menu_admin as $value) { if ("$value[li]"!="") { ?>
									<?php echo $value['li']; ?>
									<?php }} unset($value); ?>
								<?php endif ?>
								<!-- menu prof -->
<?php
	if (count($tbs_menu_prof)>0) {
		echo "<div id='menu_barre'>\n";
		echo "<ul class='niveau1'>\n";
		foreach($tbs_menu_prof as $key => $value) {
			ligne_menu_barre($value,1);
		}
		echo "</ul>\n";
		echo "</div>\n";
	}
?>

<!-- menu admin -->


<!-- menu scolarité -->
<?php if (count($tbs_menu_scol)) : ?>

			<?php foreach ($tbs_menu_scol as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>

<?php endif ?>

<!-- menu cpe -->
<?php if (count($tbs_menu_cpe)) : ?>

			<?php foreach ($tbs_menu_cpe as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>
		
<?php endif ?>

<!-- menu responsable -->
<?php if (count($tbs_menu_responsable)) : ?>

	
			<?php foreach ($tbs_menu_responsable as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>	
	

<?php endif ?>

<!-- menu eleve -->
<?php if (count($tbs_menu_eleve)) : ?>

			<?php foreach ($tbs_menu_eleve as $value) { if ("$value[li]"!="") { ?>
			<?php echo $value['li']; ?>
			<?php }} unset($value); ?>
		
<?php endif ?>

								<!-- /main -->

							</ul>
						</div>
					</div>
					<!-- /main navigation -->

				</div>
			</div>
			<!-- /main sidebar -->
	

<!-- Début bandeau -->
<!-- Initialisation du bandeau à la bonne couleur -->
	<div id='bandeau' class="<?php echo $tbs_modif_bandeau.' '.$tbs_degrade_entete.' '.$tbs_modif_bandeau.'_'.$tbs_degrade_entete; ?>">

<a href="#contenu" class="invisible"> Aller au contenu </a>

	<!-- Page title, access rights -->
	<!-- User name, status, main matter, home, logout, account management -->

<div class="bandeau_colonne">
	<!-- Bouton rétrécir le bandeau 
		<a class='change_taille_gd' href="#" onclick="modifier_taille_bandeau();change_mode_header('y', '<?php //echo $tbs_bouton_taille;?>');return false;">
			<img src="<?php //echo $tbs_bouton_taille;?>/images/up.png" alt='Cacher le bandeau' title='Cacher le bandeau' />
		</a>-->
	<!-- Bouton agrandir le bandeau 
		<a class='change_taille_pt' href="#" onclick="modifier_taille_bandeau();change_mode_header('n', '<?php //echo $tbs_bouton_taille;?>');return false;">
			<img src="<?php //echo $tbs_bouton_taille;?>/images/down.png" alt='Afficher le bandeau' title='Afficher le bandeau' />
		</a>-->

	

	<!-- Dernière connexion -->
		<?php
			if ($tbs_last_connection!=""){
				echo "<div class=\"bs-callout bs-callout-info\">
				<p>
					$tbs_last_connection
				</p>
				</div>
				";
			}
		?>

	<!-- numéro de version	 -->
		<p class="rouge"> 
			<?php echo $tbs_version_gepi; ?>
		</p>
</div>

<div class="bandeau_colonne" id="bd_colonne_droite">
	<!-- Nom prénom 
		<p id='bd_nom' title="<?php //echo $tbs_nom_prenom_statut;?>">
			<?php //echo $tbs_nom_prenom; ?>
		</p>-->

	<!-- statut utilisateur -->
		
	<!-- sépare les 2 menus -->

	<!-- menu contact	 
		<ol id="bandeau_menu_deux">
		<?php
			/*if (count($tbs_deux_menu)) {
				foreach ($tbs_deux_menu as $value) {
					if ("$value[texte]"!="") {
						// Là le (js) insert_confirm_abandon() est inutile parce que c'est une ouverture dans une autre fenêtre
						echo "
	<li class='ligne_deux_menu'>
		<a href='$value[lien]' $value[onclick] title=\"Nouvelle fenêtre\">
			$value[texte]
		</a>
	</li>
						";
					}
				}
				unset($value);
			}
		*/?>
		</ol>-->

</div>

</div>

<?php

	echo '<!--[if lt IE 7]>
<script type=text/javascript>
	// Fonction destinée à remplacer le "li:hover" pour IE 6
	sfHover = function() {
		var sfEls = document.getElementById("menu_barre").getElementsByTagName("li");
		for (var i=0; i<sfEls.length; i++) {
			sfEls[i].onmouseover = function() {
				this.className = this.className.replace(new RegExp(" sfhover"), "");
				this.className += " sfhover";
			}
			sfEls[i].onmouseout = function() {
				this.className = this.className.replace(new RegExp(" sfhover"), "");
			}
		}
	}
	if (window.attachEvent) window.attachEvent("onload", sfHover);
</script>

<style type="text/css">#menu_barre li {
	width: 164px;
}
</style>
<![endif]-->
';


?>



<!-- fil d'ariane -->

<div class="breadcrumb-line">

						<ul class="breadcrumb">
							<?php
  if (isset($messageEnregistrer) && $messageEnregistrer !="" ){
	affiche_ariane(TRUE,$messageEnregistrer);
  }else{
	if(isset($_SESSION['ariane']) && (count($_SESSION['ariane']['lien'])>1)){
	  affiche_ariane();
	}
  }
?>
						</ul>

						
					</div>
<!-- fin fil d'ariane -->


<!-- message -->
<?php
			if ($tbs_msg !="") {
?>
	<p class='headerMessage bold<?php if(isset($post_reussi) && $post_reussi) echo " vert" ;?>'>
<?php
		echo $tbs_msg;
?>

	</p>
<?php
			}
	//debug_var();
	echo "<div id='temoin_messagerie_non_vide' style='position:fixed; right:1em; top:300px;'></div>\n";
	

?>

