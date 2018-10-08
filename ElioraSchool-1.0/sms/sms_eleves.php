<?php
/**
 * Code qui sera aeeplé par un objet XHR et qui
 * retournera la liste déroulante des départements
 * correspondant à la région sélectionnée.
 */
/* Paramètres de connexion */
$serveur = "localhost";
$admin   = "root";
$mdp     = "";
$base    = "regions";

/* On récupère l'identifiant de la région choisie. */
$idr = isset($_GET['idr']) ? $_GET['idr'] : false;
/* Si on a une région, on procède à la requête */
if(false !== $idr)
{
    /* Cération de la requête pour avoir les départements de cette région */
    $sql2 = "SELECT `id_departement`, `departement`".
            " FROM `departement`".
            " WHERE `id_region` = ". $idr ."".
            " ORDER BY `id_departement`;";
    $connexion = mysql_connect($serveur, $admin, $mdp);
    mysql_select_db($base, $connexion);
    $rech_dept = mysql_query($sql2, $connexion);
    /* Un petit compteur pour les départements */
    $nd = 0;
    /* On crée deux tableaux pour les numéros et les noms des départements */
    $code_dept = array();
    $nom_dept = array();
    /* On va mettre les numéros et noms des départements dans les deux tableaux */
    while(false != ($ligne_dept = mysql_fetch_assoc($rech_dept)))
    {
        $code_dept[] = $ligne_dept['id_departement'];
        $nom_dept[]  = $ligne_dept['departement'];
        $nd++;
    }
    /* Maintenant on peut construire la liste déroulante */
    $liste = "";
    $liste .= '<select name="departement" id="departement">'."\n";
    for($d = 0; $d < $nd; $d++)
    {
        $liste .= '  <option value="'. $code_dept[$d] .'">'. htmlentities($nom_dept[$d]) .' ('. $code_dept[$d] .')</option>'."\n";
    }
    $liste .= '</select>'."\n";
    /* Un petit coup de balai */
    mysql_free_result($rech_dept);
    /* Affichage de la liste déroulante */
    echo($liste);
}
/* Sinon on retourne un message d'erreur */
else
{
    echo("<p>Une erreur s'est produite. La région sélectionnée comporte une donnée invalide.</p>\n");
}
?>