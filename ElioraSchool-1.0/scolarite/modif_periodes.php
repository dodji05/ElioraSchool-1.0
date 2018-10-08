<?php
require_once("../lib/initialisations.inc.php");
$id_classe =isset($_POST['id_classe']) ? $_POST['id_classe'] : (isset($_GET['id_classe']) ? $_GET['id_classe'] : NULL);
$id_tranche =isset($_POST['id_tranche']) ? $_POST['id_tranche'] : (isset($_GET['id_tranche']) ? $_GET['id_tranche'] : NULL);
$montant =isset($_POST['mt']) ? $_POST['mt'] : (isset($_GET['mt']) ? $_GET['mt'] : NULL); 

$nb_tranche =isset($_POST['nb_tranche']) ? $_POST['nb_tranche'] : (isset($_GET['nb_tranche']) ? $_GET['nb_tranche'] : NULL);
//print_r($_POST);
$nv_montant_tranche =isset($_POST['nv_montant_tranche']) ? $_POST['nv_montant_tranche'] : (isset($_GET['nv_montant_tranche']) ? $_GET['nv_montant_tranche'] : NULL);
$date_fin_period =isset($_POST['date_fin_period']) ? $_POST['date_fin_period'] : (isset($_GET['date_fin_period']) ? $_GET['date_fin_period'] : NULL);

$nv_montant =isset($_POST['nv_montant']) ? $_POST['nv_montant'] : (isset($_GET['nv_montant']) ? $_GET['nv_montant'] : NULL);
$idfrais=isset($_POST['idfrais']) ? $_POST['idfrais'] : (isset($_GET['idfrais']) ? $_GET['idfrais'] : NULL);
$id_tranche =isset($_POST['id_tranche']) ? $_POST['id_tranche'] : (isset($_GET['id_tranche']) ? $_GET['id_tranche'] : NULL);

print_r($_POST);
echo"<br/>-------------------------------------------------------------<hr/>";
//print_r($montant);
/*echo "id de la classe :".$id_classe."<br/>";
echo "nombre de tranche:".$nb_tranche."<br/>";
echo "montant total : " .$montant ."<br/>";
echo " montant des differents tranches : <br/>";
print_r($montant_tranche);
echo "<br/>date de fin de chaque tranche :<br/>";
print_r($date_fin_period);
echo "<br/>";*/
$somme_tranche=0;
foreach ($nv_montant_tranche as &$value) {
    $somme_tranche += $value;
}
//if ($n)

//echo "somme des tranche :".$somme_tranche ."<br/>";

if($somme_tranche==$nv_montant ) {
$sql_frais = "UPDATE etg_fraisscolaire SET `montant` = '$nv_montant' WHERE idFrais = '$idfrais';";
$reslt1 = mysqli_query($GLOBALS["mysqli"], $sql_frais);
if($reslt1==true){
// MODIFICATION DES TRANCHES
$k=0;
$msg="";
/*echo*/ $nb_tranche = count($nv_montant_tranche);
 while ($k < $nb_tranche) {
            
           //$sql_maj = "UPDATE etg_tranche SET `Montant_Tranche` = '$nv_montant_tranche[$k]', `Date_Fin_Tranche` = NOW() WHERE `etg_tranche`.`id_Tranche` = 18";
			$tmp_tab=explode("/", $date_fin_period[$k]);
            if((!isset($tmp_tab[2]))||(!checkdate($tmp_tab[1], $tmp_tab[0], $tmp_tab[2]))) {
                $msg.="Erreur sur la date de fin de période en période $k<br />";
            }
            else {
                $dte= "'".$tmp_tab[2]."-".$tmp_tab[1]."-".$tmp_tab[0]." 00:00:00'";
            }
			$sql_modif_tranche = "UPDATE  etg_tranche SET Montant_Tranche = $nv_montant_tranche[$k], Date_Fin_Tranche ='$date_fin_period[$k]' WHERE (id_Frais = '$idfrais' AND id_Tranche = '$id_tranche[$k]');";
			mysqli_query($GLOBALS["mysqli"], $sql_modif_tranche);
			// echo "<br/>$sql_modif_tranche <br/>";
          // $register = mysqli_query($GLOBALS["mysqli"], $sql_tranche);
		 //  echo mysql_date_to_unix_timestamp($date_fin_period[$k]);
		 // echo  strftime("%d/%m/%Y", mysql_date_to_unix_timestamp($date_fin_periode[$k]));
            /*if (!$register) {$pb_reg_per = 'yes';}*/
            $k++;
        }
echo"Modifications enregistrées avec succés";

}
else {
// ERREUR SURVENUE DE LA MISE A JOUR
echo "erreur" .$reslt1;
}
}
else{
echo "LA SOMME DES TRANCHES EST DIFFERENTE DU MONTANT TOTAL DE LA SCORALITE";
$msg = "LA SOMME DES TRANCHES EST DIFFERENTE DU MONTANT TOTAL DE LA SCORALITE";
//header("Location: scolarite_tranches.php");
}



// Il n'existe pas la classe dans la table etg_fraissscolrais
// on ajoute un enregistrement

//echo $id_classe;
//print_r($GLOBALS["mysqli"]);

/*
$register = mysqli_query($GLOBALS["mysqli"], $sql_frais);
if ($register){
		if ($somme_tranche==$montant){

			$sql_last = "SELECT (idFrais) FROM etg_fraisscolaire WHERE (id_classe=$id_classe)";
	$test = mysqli_query($GLOBALS["mysqli"],$sql_last);
	// on recupere la valeur du dernier id
	while($lig_class_tmp=mysqli_fetch_object($test)){


		//echo $lig_class_tmp->idFrais."<br/>";
		//$sql_tranche = "SELECT INTO etg_tranche FROM VALUES (NULL,$lig_class_tmp->idFrais,$tranche,'tranche'.$k,NULL,$montant_tranche[$k],$date_fin_period[$k])"
		 $k = 1;
        //$nombre_periode++;
        while ($k <= $nb_tranche) {
            
           
			$tmp_tab=explode("/", $date_fin_period[$k]);
            if((!isset($tmp_tab[2]))||(!checkdate($tmp_tab[1], $tmp_tab[0], $tmp_tab[2]))) {
                $msg.="Erreur sur la date de fin de période en période $k<br />";
            }
            else {
                $dte= "'".$tmp_tab[2]."-".$tmp_tab[1]."-".$tmp_tab[0]." 00:00:00'";
            }
			$sql_tranche = "INSERT INTO etg_tranche  VALUES (NULL,$lig_class_tmp->idFrais,'Tranche$k',NULL,$montant_tranche[$k],$dte)";
			 echo "$sql_tranche <br/>";
           $register = mysqli_query($GLOBALS["mysqli"], $sql_tranche);
		 //  echo mysql_date_to_unix_timestamp($date_fin_period[$k]);
		 // echo  strftime("%d/%m/%Y", mysql_date_to_unix_timestamp($date_fin_periode[$k]));
            /*if (!$register) {$pb_reg_per = 'yes';}*/
  /*          $k++;
        }
     



    }

	header("Location: index.php");
	}
	else
{
	echo " la somme des tranches est different du montant ";
}
	
	//print_r($test);
	//echo $sql_last;
}

*/

?>