<?php
session_start();
$_SESSION['login'];
$userID = $_SESSION['userID'];
$laboratorio = $_SESSION['laboratorio'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}

if (!isset($fechaini) && !isset($fechafin)) {
    exit('Solicitud no encontrados...');
}

//abrir conexion
include ("conexion.php");

/*
 * libreria de query
 */
require('repository/repReporteRepository.php');
require('tools.php');
require('FRAMEWORK/fpdf/fpdf.php');
$model = new repReporteRepository();


class PDF extends FPDF
{
// Cabecera de página
function Header()
{
    // Logo
    $this->Image('public/images/lp.png',10,5,50);
    // Arial bold 15
    $this->SetFont('Arial','B',15);
    // Movernos a la derecha
    $this->Cell(80);
    // Título
    $this->Cell(30,35,'Examenes realizados a pacientes de Empresa',0,0,'C');
    // Salto de línea
    $this->Ln(20);
    $this->Line(10, 32, 200, 32);
}

// Pie de página
function Footer()
{
    // Posición: a 1,5 cm del final
    $this->SetY(-15);
    // Arial italic 8
    $this->SetFont('Arial','I',8);
    // Número de página
    $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
}
}

// Creación del objeto de la clase heredada
$pdf = new PDF('P', 'mm', 'Letter');
//$pdf = new FPDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times','',10);
$solicitudes = $model->get_lista_solicitud($fechaini, $fechafin, $id_empresa);
$ln = 5;

foreach ($solicitudes as $solicitud) {
    $solicitudDetalle = $model->getSolicitudDetalle_id($solicitud['id_solicitud']);
    $pdf->Ln($ln);
    $pdf->Cell(40, 10, "ID Solicitud: " . $solicitud['id_solicitud']);
    $pdf->Cell(40 + 20, 10, "Fecha: " . $solicitud['fecha_solicitud']);
    $pdf->Cell(40, 10, "Paciente: " . $solicitud['paciente']);
    $pdf->Ln($ln);
    $pdf->Cell(100, 10, "Médico: " . $solicitud['medico']);
    $pdf->Cell(40, 10, "Empresa: " . $solicitud['empresa']);
    /*
     * BODY
     */
    $pdf->Ln($ln);
    $pdf->Ln($ln);
    $i = 0;
    foreach ($solicitudDetalle as $detalle) {
        $i++;
        $pdf->Cell(10, 0, $i);
        $pdf->Cell(15, 0, $detalle['codigo']);
        $pdf->Cell(125, 0, $detalle['prueba']);
        $pdf->Cell(0, 0, $detalle['precio_prueba'], 0, 0, 'R');
        $pdf->Ln($ln);
    }
        $pdf->SetX(160);
        $pdf->Cell(0, 0, $solicitud['venta_total'],0,0,'R');
    
}
$pdf->Output();
?>
