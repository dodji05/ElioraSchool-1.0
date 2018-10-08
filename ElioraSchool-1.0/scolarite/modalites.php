<?php
require_once("../lib/initialisations.inc.php");
$id_classe = isset($_POST['idclasse']) ? $_POST['idclasse'] : (isset($_GET['idclasse']) ? $_GET['iidclasse'] : NULL);
$id_tranche = isset($_POST['id_tranche']) ? $_POST['id_tranche'] : (isset($_GET['id_tranche']) ? $_GET['id_tranche'] : NULL);
$montant = isset($_POST['mt']) ? $_POST['mt'] : (isset($_GET['mt']) ? $_GET['mt'] : NULL);

//$nb_tranche = isset($_POST['nb_tranche']) ? $_POST['nb_tranche'] : (isset($_GET['nb_tranche']) ? $_GET['nb_tranche'] : NULL);
//print_r($_POST);
$montant_tranche = isset($_POST['montant_tranche']) ? $_POST['montant_tranche'] : (isset($_GET['montant_tranche']) ? $_GET['montant_tranche'] : NULL);
$date_fin_period = isset($_POST['date_fin_period']) ? $_POST['date_fin_period'] : (isset($_GET['date_fin_period']) ? $_GET['date_fin_period'] : NULL);

$nb_modalite = isset($_POST['nb_modalite']) ? $_POST['nb_modalite'] : (isset($_GET['nb_modalite']) ? $_GET['nb_modalite'] : NULL);

$idEleve = isset($_POST['ideleve']) ? $_POST['ideleve'] : (isset($_GET['ideleve']) ? $_GET['ideleve'] : NULL);
$idfrais = isset($_POST['idfrais']) ? $_POST['idfrais'] : (isset($_GET['idfrais']) ? $_GET['idfrais'] : NULL);
print_r($_POST);
//$date_fin_period =isset($_POST['date_fin_period']) ? $_POST['date_fin_period'] : (isset($_GET['date_fin_period']) ? $_GET['date_fin_period'] : NULL);
//print_r($montant);
/*echo "id de la classe :".$id_classe."<br/>";
echo "nombre de tranche:".$nb_tranche."<br/>";
echo "montant total : " .$montant ."<br/>";
echo " montant des differents tranches : <br/>";
print_r($montant_tranche);
echo "<br/>date de fin de chaque tranche :<br/>";
print_r($date_fin_period);
echo "<br/>";*/
$somme_tranche = 0;
foreach ($montant_tranche as &$value) {
    $somme_tranche += $value;
}
//if ($n)

//echo "somme des tranche :".$somme_tranche;

$sql_verifi = "SELECT * FROM etg_echeancier WHERE idEleve =$idEleve";
//echo $sql_verifi;
$verification = mysqli_query($GLOBALS["mysqli"], $sql_verifi);
$nombresultats = mysqli_num_rows($verification);
if ($nombresultats > 0) {
// On modifie les données

    echo "L'echancier pour cette eleve est deja defini :";
} else {
// Il n'existe pas la classe dans la table etg_fraissscolrais
// on ajoute un enregistrement
    $sql_frais = "INSERT INTO etg_echeancier(idEleve,idFrais) values($idEleve,$idfrais)";
    echo $sql_frais;
//echo $id_classe;
//print_r($GLOBALS["mysqli"]);
    $register = mysqli_query($GLOBALS["mysqli"], $sql_frais);
    echo "register" . $register;
    echo "montant total" . $somme_tranche."<br/>";

    $sql_montant = "SELECT montant FROM etg_fraisscolaire WHERE idFrais =$idfrais";
    $montant_res = mysqli_query($GLOBALS["mysqli"], $sql_montant);
    echo  $sql_montant;
    $montant =  old_mysql_result($montant_res,0, "montant");

    echo "montant " . $montant."<br/>";
//echo $sql_verifi;
    $verification = mysqli_query($GLOBALS["mysqli"], $sql_verifi);
    if ($somme_tranche == $montant) {
        if ($register==1) {

            $sql_last = "SELECT (idEcheancier) FROM etg_echeancier WHERE (	(idEleve=$idEleve) and (idFrais=$idfrais))";
           // echo "Dernier ID" . $sql_last;
            $test = mysqli_query($GLOBALS["mysqli"], $sql_last);
            print_r($test);
            // on recupere la valeur du dernier id
            while ($lig_class_tmp = mysqli_fetch_object($test)) {

                echo $lig_class_tmp->idEcheancier."<br/>";
                //$sql_tranche = "SELECT INTO etg_tranche FROM VALUES (NULL,$lig_class_tmp->idFrais,$tranche,'tranche'.$k,NULL,$montant_tranche[$k],$date_fin_period[$k])"
                $k = 1;
                //$nombre_periode++;
                while ($k <= $nb_modalite) {


                    $tmp_tab = explode("/", $date_fin_period[$k]);
                    if ((!isset($tmp_tab[2])) || (!checkdate($tmp_tab[1], $tmp_tab[0], $tmp_tab[2]))) {
                        $msg .= "Erreur sur la date de fin de période en période $k<br />";
                    } else {
                        $dte = "'" . $tmp_tab[2] . "-" . $tmp_tab[1] . "-" . $tmp_tab[0] . " 00:00:00'";
                    }
                    $sql_tranche = "INSERT INTO etg_echeancier_tranche  VALUES (NULL,$lig_class_tmp->idEcheancier,'Tranche$k',$montant_tranche[$k],$dte)";
                   // echo "$sql_tranche <br/>";
                    $register = mysqli_query($GLOBALS["mysqli"], $sql_tranche);
                    //  echo mysql_date_to_unix_timestamp($date_fin_period[$k]);
                    // echo  strftime("%d/%m/%Y", mysql_date_to_unix_timestamp($date_fin_periode[$k]));
                    /*if (!$register) {$pb_reg_per = 'yes';}*/
                    $k++;
                }


            }

            // header("Location: index.php");
        } else {
            echo " la somme des tranches est different du montant ";
            header("location:" . $_SERVER['HTTP_REFERER']);
        }

        //print_r($test);
        //echo $sql_last;
    }


}

?>