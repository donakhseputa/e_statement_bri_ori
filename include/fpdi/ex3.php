<?php

/* ini pakai fpdf_protection  */
/*
require('fpdf_protection.php');
$pdf=new FPDF_Protection();

*/


/* ini pakai FPDI_Protection  */

require('../fpdf/fpdf.php');
require('FPDI_Protection.php');
$pdf=new FPDI_Protection();

$pass = "01130378";
$passadmin = "appdev123456";
$text = 'You can print me but not copy my text.' ." __  ";
$text = $text. $pass ." __";
$text = $text. $passadmin ;



$pdf->SetProtection(array("print"), $pass, $passadmin);
$pdf->AddPage();
$pdf->SetFont('Arial');
$pdf->Write(10, $text);
$pdf->Output();
?>
