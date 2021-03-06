<?php
require('fpdf.php');

class PDF extends FPDF
{
// Page header
function Header()
{
    // Logo
    $this->Image('tutorial/logo.png',10,6,30);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Move to the right
    $this->Cell(80);
    // Title
    $this->Cell(50,10,'Travel Media',1,0,'C');
    // Line break
    $this->Ln(30);
}

// Page footer
function Footer()
{
    // Position at 1.5 cm from bottom
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Page number
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

$cookie = $_COOKIE['plan'];
$cookie = stripslashes($cookie);
$plans = json_decode($cookie, true);

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Your travel plan is follows:');
$pdf->Ln(10);
foreach($plans as $key => &$val){
	$pdf->Ln(15);
	$pdf->Cell(80,10,$key);	
	$pdf->Ln(10);
	foreach($val as $key1 => &$val1){
		$pdf->Cell(80,10,$key1);	
		$pdf->Cell(80,10,$val1);
		$pdf->Ln(10);	
	}
}

$pdf->Output();
?>

