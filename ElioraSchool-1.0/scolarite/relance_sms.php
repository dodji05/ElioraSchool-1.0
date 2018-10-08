<?php
  /*****************************************
  *  Constantes et variables
  relance_sms.php?ideleve=$idlogin&paye=$Montant_paye&rest=$rest
  $mon
  *****************************************/
 require_once("../lib/initialisations.inc.php");
 require_once("../lib/envoi_SMS.inc.php");
$idLogin=isset($_POST['ideleve']) ? $_POST['ideleve'] : (isset($_GET['ideleve']) ? $_GET['ideleve'] : NULL);
$paye=isset($_POST['paye']) ? $_POST['paye'] : (isset($_GET['paye']) ? $_GET['paye'] : NULL);
$rest=isset($_POST['rest']) ? $_POST['rest'] : (isset($_GET['rest']) ? $_GET['rest'] : NULL);
$mon=isset($_POST['nom']) ? $_POST['nom'] : (isset($_GET['nom']) ? $_GET['nom'] : NULL);
  /*****************************************
  *  Vérification du formulaire
  *****************************************/
  // Si le tableau $_POST existe alors le formulaire a été envoyé
  if(!empty($_POST))
  {
    // Le login est-il rempli ?
    if(empty($_POST['msg_sms']))
    {
      $message = ' Veuillez saisir le message !';
    }
	else{
	//echo $idLogin;
		$sql="SELECT DISTINCT e.* FROM eleves e where(e.login = '$idLogin')";
		$tranche_expire_1 = mysqli_query($GLOBALS["mysqli"], $sql);
		$tel = old_mysql_result($tranche_expire_1,0,'telephone');
		$tel = "+229".$tel;
		//echo $tel."<br/>";
		 $tab_to=explode(',' ,$tel);
		// print_r($tab_to);
		$t_retour=envoi_SMS($tab_to,str_replace('\n',"\n",$_POST['msg_sms']),true);
	}
   
  }
 // print_r($_POST);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" >
  <head>
  	<link rel="SHORTCUT ICON" href="/ElioraSchool-1.0/favicon.ico" />

    <title>RELANCE SMS</title>
	<link rel="stylesheet" type="text/css" href="/ElioraSchool-1.0/edt_effets/themes/default.css" media="all" />
<link rel="stylesheet" type="text/css" href="/ElioraSchool-1.0/edt_effets/themes/alphacube.css" media="all" />
<link rel="stylesheet" type="text/css" href="/ElioraSchool-1.0/css/style.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/ElioraSchool-1.0/css/style_imprime.css" media="print" />

  </head>
  <body>
    <?php if(!empty($message)) : ?>
      <p><?php echo $message; ?></p>
    <?php endif; ?>
	<h4 align="center"> Relance SMS </h4>
    <form action="<?php echo htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES); ?>" method="post">
     
         <textarea rows="5" cols="45" name="msg_sms">Salut M/Mme/Mlle <?php echo $mon ?> nous vous infornons que pour la tranche1 de la scolarité vous avez payé <?php echo number_format($paye, 0, ',', ' ');?> FCFA et vous rester devoir payé <?php echo $rest ?> FCFA.Merci
		</textarea><br/>
		<center><input type="submit" name="submit" value="Envoi Sms" /></center>
    </form>
  </body>
</html>