<?php
session_start();
$_SESSION['login'];
$userID = $_SESSION['userID'];
$laboratorio = $_SESSION['laboratorio'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}

if (!$id_solicitud) {
    exit('Solicitud no encontrados...');
}

//abrir conexion
include ("conexion.php");

/*
 * libreria de query
 */
require('repository/labSolicitudRepository.php');
require('FRAMEWORK/fpdf/fpdf.php');

$model = new LabSolicitudRepository();

$solicitud = $model->getSolicitud_id($id_solicitud);
$solicitudDetalle = $model->getSolicitudDetalle_id($id_solicitud);

$pdf = new FPDF('P', 'mm', 'Letter');

/*
 * variables de posicion
 */
$x1 = 40;
$y1 = 10;
$x2 = 0;
$y2 = 0;
$ln = 5;
$font = (object) array(
            'name' => 'Arial',
            'sizeNormal' => 14,
            'sizeTitle1' => 14,
            'sizeTitle2' => 12,
            'sizeText' => 11,
);


$pdf->AddPage();

/*
 * HEADER
 */
$pdf->SetFont($font->name, 'B', $font->sizeTitle1);
$pdf->Cell($x1, $y1, $laboratorio);
$pdf->SetFont($font->name, 'B', $font->sizeTitle2);
$pdf->Ln($ln);
$pdf->Cell($x1, $y1, "Orden de exámenes laboratorio");
$pdf->SetFont($font->name, 'B', $font->sizeText);
$pdf->Ln($ln);
$pdf->Cell($x1 - 15, $y1, "No. " . $solicitud['id']);
$pdf->Cell($x1 + 20, $y1, "Fecha: " . $solicitud['fecha_solicitud']);
$pdf->Cell($x1, $y1, "Paciente: " . $solicitud['paciente']);
$pdf->Ln($ln);
$pdf->Cell($x1 + 45, $y1, "Médico: " . $solicitud['medico']);
$pdf->Cell($x1, $y1, "Empresa: " . $solicitud['empresa']);
$pdf->Line(10, 32, 200, 32);
/*
 * BODY
 */
$pdf->Ln($ln);
$pdf->Ln($ln);
$pdf->SetFont($font->name, '', $font->sizeText);
$i = 0;
foreach ($solicitudDetalle as $detalle) {
    $i++;
    $pdf->Cell($x1 - 30, $y1, $i);
    $pdf->Cell($x1 - 20, $y1, $detalle['codigo']);
    $pdf->Cell($x1, $y1, $detalle['prueba']);
    $pdf->Ln($ln);
}

/*
 * FOOTHER
 */

$pdf->Output();
