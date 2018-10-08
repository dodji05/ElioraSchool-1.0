<?php
require_once(dirname(__FILE__).'/../impression/class_pdf.php');

class PDF extends FPDF
{
// En-tête
function Header()
{
    $X_entete_etab='5';
    $caractere_utilse='DejaVu'; // caractère utilisé dans le document
    $affiche_logo_etab='1'; // affiché le logo de l'établissement
    $entente_mel='0'; // afficher dans l'entête le mel de l'établissement
    $entente_tel='0'; // afficher dans l'entête le téléphone de l'établissement
    $entente_fax='0'; // afficher dans l'entête le fax de l'établissement
    $L_max_logo='75'; // Longeur maxi du logo
    $H_max_logo='75'; // hauteur maxi du logo

    $avec_adresse_responsable=1;

    // bloc responsable parents
    $active_bloc_adresse_parent=$avec_adresse_responsable;
    $X_parent=110; $Y_parent=40;

    //information année
    $gepiYear = getSettingValue('gepiYear');
    $annee_scolaire = $gepiYear;
    $X_cadre_eleve = '130';

    // cadre note
    $titre_du_cadre='Relevé de notes du ';
    $largeur_cadre_matiere='50';
    $texte_observation='Observations:';
    $cadre_titre='0'; // affiche le cadre autour du titre ici: "relevé de notes..."
    $largeur_cadre_note_global = '200'; //largeur du cadre note global nom matiere | note | observation
    $hauteur_dun_regroupement='4'; // hauteur de la cellule regroupement

    $hauteur_du_titre = '4.5';
    //$largeur_cadre_note = '95';
    $largeur_cadre_note_si_obs = '95';
    // Sinon, on prend $largeur_cadre_note_global moins l'espace déjà utilisé pour la colonne matière.

    $X_cadre_note = '5';

    // cadre des signature
    $hauteur_cachet = '30'; // hauteur des signatures





}

// Pied de page
function Footer()
{
    // Positionnement à 1,5 cm du bas
    $this->SetY(-15);
    // Police Arial italique 8
    $this->SetFont('Times','',8);
    // Numéro de page
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}


// Instanciation de la classe dérivée
/*$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',12);
for($i=1;$i<=40;$i++)
    $pdf->Cell(0,10,'Impression de la ligne numéro '.$i,0,1);
$pdf->Output();
*/

$pdf=new rel_PDF("P","mm","A4");
/*$pdf->SetTopMargin(TopMargin);
$pdf->SetRightMargin(RightMargin);
$pdf->SetLeftMargin(LeftMargin);
$pdf->SetAutoPageBreak(true, BottomMargin);*/
$pdf->Cell(0,10,'Impression',0,1);

// Couleur des traits
$pdf->SetDrawColor(0,0,0);

// Caractéres utilisée
$caractere_utilse = 'DejaVu';


$releve_affiche_formule=getSettingValue("releve_affiche_formule") ? getSettingValue("releve_affiche_formule") : "y";
$releve_formule_bas=getSettingValue("releve_formule_bas") ? getSettingValue("releve_formule_bas") : "";

$releve_affiche_tel=getSettingValue("releve_affiche_tel") ? getSettingValue("releve_affiche_tel") : "n";
$releve_affiche_fax=getSettingValue("releve_affiche_fax") ? getSettingValue("releve_affiche_fax") : "n";
$releve_affiche_mail=getSettingValue("releve_affiche_mail") ? getSettingValue("releve_affiche_mail") : "n";
$gepiSchoolFax=getSettingValue("gepiSchoolFax");
$gepiSchoolTel=getSettingValue("gepiSchoolTel");
$gepiSchoolEmail=getSettingValue('gepiSchoolEmail');

$pdf->Output();
?>
