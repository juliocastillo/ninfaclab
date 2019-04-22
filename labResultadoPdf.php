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
$solicitudDetalle = $model->getSolicitudDetalle($id_solicitud);

class PDF extends FPDF {

// Cabecera de página
    function Header() {
        // Logo
        $this->Image('public/images/lp.png', 10, 5, 50);
        // Arial bold 15
        $this->SetFont('Arial', 'B', 15);
        // Movernos a la derecha
        $this->Cell(80);
        // Título
        $this->Cell(30, 35, 'Resultados de examenes de laboratorio', 0, 0, 'C');
        // Salto de línea
        $this->Ln(20);
        $this->Line(10, 32, 200, 32);
    }

// Pie de página
    function Footer() {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial', 'I', 8);
        // Número de página
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }

}

// Creación del objeto de la clase heredada
$pdf = new PDF('P', 'mm', 'Letter');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Times', '', 10);
$ln = 5;
$linea = 0;

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


/*
 * BODY
 */

$pdf->Ln($ln);
$linea = $linea + $ln;
$pdf->Cell(40, 10, "ID Solicitud: " . $solicitud['id_solicitud']);
$pdf->Cell($x1 + 20, $y1, "Fecha: " . $solicitud['fecha_solicitud']);
$pdf->Cell($x1, 10, "Paciente: " . $solicitud['paciente']);
$pdf->Ln($ln);
$linea = $linea + $ln;
$pdf->Cell(100, 10, "Médico: " . $solicitud['medico']);
$pdf->Cell($x1, 10, "Empresa: " . $solicitud['empresa']);

$pdf->Ln($ln);
$linea = $linea + $ln;
$pdf->Ln($ln);
$linea = $linea + $ln;
$pdf->SetFont($font->name, '', $font->sizeText);
$i = 0;
foreach ($solicitudDetalle as $detalle) {
    $pdf->Ln($ln);
    $linea = $linea + $ln;
    $pdf->SetFont($font->name, '', $font->sizeText);
    $i++;
    $pdf->Cell(10, 10, $i);
    $pdf->Cell(15, 10, $detalle['codigo']);
    $pdf->Cell(0, 10, $detalle['prueba']);
    $pdf->Ln($ln);
    $linea = $linea + $ln;
    $solicitudDetalle = $model->getSolicitudDetalle_elemento($detalle['id_detallesolicitud']);
    $incluyeantibiograma = 0;
    $pdf->SetFont($font->name, '', 8);
    //TITULOS DE ELEMENTOS
    $pdf->Cell(70, 10, '');
    $pdf->Cell(30, 10, 'RESULTADO');
    $pdf->Cell(20, 10, 'RANGO');
    $pdf->Cell(25, 10, 'UNIDADES');
    foreach ($solicitudDetalle as $r) { //elementos
        if ($linea >= 210) {
            $pdf->AddPage();
            $linea = 0;
        }
        if ($r['estitulo'] != 1) { //elementos de resultado
            $pdf->Ln($ln);
            $linea = $linea + $ln;
            if ($r['fueraderango'] == 1) {
                $pdf->SetFont($font->name, 'B', 8);
            } else {
                $pdf->SetFont($font->name, '', 8);
            }
            $pdf->Cell(70, 10, $r['nombre_elemento']);
            $pdf->Cell(30, 10, $r['resultado']);
            $pdf->Cell(20, 10, $r['intervalo']);
            $pdf->Cell(25, 10, $r['unidades']);
        } else { // elemento de titulo
            if ($r['esantibiograma'] != 1) {
                $pdf->Cell(15, 10, $r['nombre_elemento']);
            } else {
                $incluyeantibiograma = 1;
                $tituloantibiograma = $r['nombre_elemento'];
            }
        }
        // desplegar antibiograma si se ha incluido
        if ($incluyeantibiograma == 1) {
            $microorganismo = $model->lab_resultado_cargar_microorganismo($detalle['id_detallesolicitud']);
            $result_antibiograma = $model->lab_resultado_cargar_antibiograma($detalle['id_detallesolicitud']);
            $pdf->Cell(30, 10, "MICROORGANISMO: ");
            $pdf->Cell(50, 10, $microorganismo['microorganismo']);
            $pdf->Ln($ln);
            $linea = $linea + $ln;
            $pdf->Cell(15, 10, $tituloantibiograma);
            $k = 0;
            $pdf->Ln($ln + 2);
            foreach ($result_antibiograma as $atb) {
                $pdf->Cell(45, 5, $atb['ANTIBIOTIC_ES'], 1, 0, '');
//                $pdf->Cell(15, 5, "", 1, 0,'C');
                $pdf->Cell(15, 5, $atb['categoria'], 1, 0, 'C');
                $pdf->Ln($ln);
                $linea = $linea + $ln;
            }
        }
    }
    $pdf->Ln($ln);
    $linea = $linea + $ln;
}

$pdf->Output();
