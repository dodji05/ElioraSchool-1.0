<?php
//$tranche_expire_sql = "SELECT * FROM `etg_tranche` WHERE Date_Fin_Tranche BETWEEN DATE_SUB( Date_Fin_Tranche, INTERVAL 21 DAY) AND NOW()";
/*$tranche_expire_sql=" SELECT * ,c.id,c.classe FROM `etg_tranche` t,`etg_fraisscolaire` f, `classes` c WHERE (Date_Fin_Tranche BETWEEN DATE_SUB( Date_Fin_Tranche, INTERVAL 21 DAY) AND NOW()) AND (t.id_frais = f.idFrais) and (f.id_classe = c.id)";*/

//$tranche_expire_sql ="select * from eleves where login NOT IN (select idlogin from etg_paiement)";
$tranche_expire_sql = "select DISTINCT (e.login),e.nom,e.prenom,cl.classe,cl.id from eleves e, j_eleves_classes j,classes cl  where ((e.id_eleve NOT IN (select idEleve from etg_paiement)) and  j.login = e.login AND j.id_classe =cl.id) order by cl.classe";
//echo $tranche_expire_sql;

$tranche_expire_rst = mysqli_query($GLOBALS["mysqli"], $tranche_expire_sql);
$nombre_lignes = mysqli_num_rows($tranche_expire_rst);
if ($nombre_lignes != 0) {
?>
<h2> Aucun payement effectué</h2>
<table border='1' cellpadding='2' class='boireaus'>
	<tr>
		<th>Nom et prénom(s)</th>
		<th>Classe</th>
		<th>Tranche</th>
		<th>Montant de la tranche</th>
		<th>Montant payé</th>
		<th>Montant restant</th>
		<th>Relance</th>
	</tr>
<?php
$i=0;
$alt = 1;
while($i < $nombre_lignes){
	$alt = $alt*(-1);
	$montranche= old_mysql_result($tranche_expire_rst,$i,'Nom_Tranche');
	$montanttranche=old_mysql_result($tranche_expire_rst,$i,'Montant_Tranche');;
	$classe=old_mysql_result($tranche_expire_rst,$i,'classe');;
	$mon = old_mysql_result($tranche_expire_rst,$i,'nom');
	$idclasse = old_mysql_result($tranche_expire_rst,$i,'id');
	$idlogin = old_mysql_result($tranche_expire_rst,$i,'login');
	$premon = old_mysql_result($tranche_expire_rst,$i,'prenom');
	//echo $idclasse."<br/>";
	$tranche_expire_sql_el=" SELECT * ,c.id,c.classe FROM `etg_tranche` t,`etg_fraisscolaire` f, `classes` c WHERE (Date_Fin_Tranche BETWEEN DATE_SUB( Date_Fin_Tranche, INTERVAL 21 DAY) AND NOW()) AND (t.id_frais = f.idFrais) and (f.id_classe = c.id) and f.id_classe ='$idclasse'";
	$tranche_expire = mysqli_query($GLOBALS["mysqli"], $tranche_expire_sql_el);
	$Montant_Tranche = old_mysql_result($tranche_expire,0,'Montant_Tranche');
	$Nom_Tranche = old_mysql_result($tranche_expire,0,'Nom_Tranche');
	//echo $tranche_expire_sql_el."<br/>";
	
	echo"<tr class='lig$alt white_hover'>";
		echo"<td>$mon $premon </td>";
		echo"<td>$classe</td>";
		echo"<td>$Nom_Tranche</td>";
		echo"<td>$Montant_Tranche</td>";
		echo"<td>-</td>";
		echo"<td>$Montant_Tranche</td>";
		echo"<td><a href=\"relance_sms.php?type=aucun&ideleve=$idlogin&paye=0&rest=$Montant_Tranche&nom=$mon\" onclick=\"window.open(this.href,'Popup', 'height=200, width=400, top=100, left=100, toolbar=no, menubar=yes, location=no, resizable=yes, scrollbars=no, status=no');return false;\"> 
    Relance</a></td>";
	echo"</tr>";
$i++;}
}
echo"</table>";
?>
<h2>Paiement partiel</h2>
<?php
//$tranche_expire_sql ="select * from eleves where login NOT IN (select idlogin from etg_paiement)";
$tranche_expire_sql = "select DISTINCT (e.login),e.nom,e.prenom,cl.classe,cl.id from eleves e, j_eleves_classes j,classes cl  where ((e.id_eleve IN (select idEleve from etg_paiement)) and  j.login = e.login AND j.id_classe =cl.id) order by cl.classe";
//echo $tranche_expire_sql;
$tranche_expire_rst = mysqli_query($GLOBALS["mysqli"], $tranche_expire_sql);
$nombre_lignes = mysqli_num_rows($tranche_expire_rst);
if ($nombre_lignes != 0) {
?>

<table border='1' cellpadding='2' class='boireaus'>
	<tr>
		<th>Nom et prénom(s)</th>
		<th>Classe</th>
		<th>Tranche</th>
		<th>Montant de la tranche</th>
		<th>Montant payé</th>
		<th>Montant restant</th>
		<th>Relance</th>
	</tr>
<?php
$i=0;
$alt = 1;
while($i < $nombre_lignes){
	$alt = $alt*(-1);
	$montranche= old_mysql_result($tranche_expire_rst,$i,'Nom_Tranche');
	$montanttranche=old_mysql_result($tranche_expire_rst,$i,'Montant_Tranche');;
	$classe=old_mysql_result($tranche_expire_rst,$i,'classe');;
	$mon = old_mysql_result($tranche_expire_rst,$i,'nom');
	$idlogin = old_mysql_result($tranche_expire_rst,$i,'login');
	$idclasse = old_mysql_result($tranche_expire_rst,$i,'id');
	$premon = old_mysql_result($tranche_expire_rst,$i,'prenom');
	//echo $idclasse."<br/>";
	$tranche_expire_sql_el="select sum(Montant) as total, idEleve from etg_paiement p, eleves e where (e.login = p.idLogin AND\n e.id_eleve = p.idEleve and p.idLogin = '$idlogin') group by p.idlogin";
	//echo $tranche_expire_sql_el."<br/>";
	$tranche_expire = mysqli_query($GLOBALS["mysqli"], $tranche_expire_sql_el);
	$Montant_paye = old_mysql_result($tranche_expire,0,'total');
	$Nom_Tranche = old_mysql_result($tranche_expire,0,'Nom_Tranche');
	//echo $tranche_expire_sql_el."<br/>";
	
	$tranche_expire_sql_1=" SELECT * ,c.id,c.classe FROM `etg_tranche` t,`etg_fraisscolaire` f, `classes` c WHERE (Date_Fin_Tranche BETWEEN DATE_SUB( Date_Fin_Tranche, INTERVAL 21 DAY) AND NOW()) AND (t.id_frais = f.idFrais) and (f.id_classe = c.id) and f.id_classe ='$idclasse'";
	//echo $tranche_expire_sql_1."<br/>";
	$tranche_expire_1 = mysqli_query($GLOBALS["mysqli"], $tranche_expire_sql_1);
	$Montant_Tranche = old_mysql_result($tranche_expire_1,0,'Montant_Tranche');
	$Nom_Tranche = old_mysql_result($tranche_expire_1,0,'Nom_Tranche');
	
	echo"<tr class='lig$alt white_hover'>";
		
		if($Montant_Tranche >$Montant_paye ){
			echo"<td>$mon $premon </td>";
			echo"<td>$classe</td>";
			echo"<td>$Nom_Tranche</td>";
			echo"<td>$Montant_Tranche</td>";
			$rest=$Montant_Tranche - $Montant_paye;
			$rest = number_format($rest , 0, ',', ' ');
			echo"<td>$Montant_paye</td>";
			echo"<td>$rest</td>";
			echo"<td><a href=\"relance_sms.php?type=partiel&ideleve=$idlogin&paye=$Montant_paye&rest=$rest&nom=$mon\" onclick=\"window.open(this.href, 'Popup','height=200,width=350, top=100, left=100, toolbar=no, menubar=yes, location=no,resizable=yes, scrollbars=no, status=no');return false;\">Relance</a></td>";
			
		}
		/*else{
			echo"<td>$Montant_Tranche</td>";
			echo"<td>0</td>";
		}*/
		
		//echo"<td>$Montant_Tranche</td>";
		/*echo"<td>$mon $premon </td>";
			echo"<td>$classe</td>";
			echo"<td>$Nom_Tranche</td>";
			echo"<td>$Montant_Tranche</td>";
			$rest=$Montant_Tranche - $Montant_paye;
			$rest = number_format($rest , 0, ',', ' ');
			echo"<td>$Montant_paye</td>";
			echo"<td>$rest</td>";
			echo"<td><a href=\"relance_sms.php?type=partiel&ideleve=$idlogin&paye=$Montant_paye&rest=$rest&nom=$mon\" onclick=\"window.open(this.href, 'Popup','height=200,width=350, top=100, left=100, toolbar=no, menubar=yes, location=no,resizable=yes, scrollbars=no, status=no');return false;\">Relance</a></td>";*/
		
	echo"</tr>";
$i++;}
}
echo"</table>";
?>