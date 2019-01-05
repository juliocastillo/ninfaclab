<?php

session_start();
$_SESSION['login'];
$userID = $_SESSION['userID'];
$laboratorio = $_SESSION['laboratorio'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}

if (!$id_factura) {
    exit('Solicitud no encontrados...');
}

//abrir conexion
include ("conexion.php");
include ("tools.php");

/*
 * libreria de query
 */
require('repository/facFacturaRepository.php');
require('FRAMEWORK/fpdf/fpdf.php');

$model = new FacFacturaRepository();

$factura = $model->getFactura_id($id_factura);
$facturaDetalle = $model->getFacturaDetalle_id($id_factura);

$pdf = new FPDF('P', 'mm', 'Letter');

/*
 * variables de posicion
 */
$x1 = 20;
$y1 = 20;
$ln = 4;
$font = (object) array(
            'name' => 'Arial',
            'sizeNormal' => 8,
            'sizeTitle1' => 8,
            'sizeTitle2' => 8,
            'sizeText' => 8,
);


$pdf->AddPage();




if ($factura['id_tipocomprobante'] == 1) {
    /*
     * HEADER
     */
    $pdf->SetXY($x1, $y1);
    $pdf->SetFont($font->name, '', $font->sizeTitle1);
    $pdf->Cell(75, 0, $factura['numero_comprobante'], 0, 0, 'R');
    $pdf->ln($ln*3);
    $pdf->Cell(90, 0, $factura['nombre'], 0, 0, 'L');
    $pdf->Cell(0, 0, $factura['fecha_comprobante'], 0, 0);
    $pdf->ln($ln*2);
    $pdf->Cell(90, 0, $factura['direccion'], 0, 0, 'L');
    $pdf->Cell(0, 0,'', 0, 0);
    $pdf->ln($ln*2);
    $pdf->Cell(90, 0, $factura['telefono'], 0, 0, 'L');
    $pdf->ln($ln * 5);

    /*
     * BODY
     */

    foreach ($facturaDetalle as $detalle) {
        $pdf->ln($ln);
        $pdf->Cell(100, 0, $detalle['descripcion']);
        $pdf->Cell(0, 0, $detalle['precio']);
    }

    /*
     * FOOTHER
     */

    $pdf->SetXY($x1, $y1 + 100);
    $pdf->ln($ln);
    $pdf->Cell(85, 0, 'SON: ' . num2letras($factura['venta_total']), 0, 0, 'L');
    $pdf->Cell(25, 0, '', 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, '', 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, '', 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, '', 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, '', 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['venta_total'], 0, 0, 'R');
    $pdf->ln($ln * 5);
} elseif ($factura['id_tipocomprobante'] == 2) {
    /*
     * HEADER
     */
    $pdf->SetXY($x1, $y1);
    $pdf->SetFont($font->name, '', $font->sizeTitle1);
    $pdf->Cell(75, 0, $factura['numero_comprobante'], 0, 0, 'R');
    $pdf->ln($ln*3);
    $pdf->Cell(90, 0, $factura['nombre'], 0, 0, 'L');
    $pdf->Cell(0, 0, $factura['fecha_comprobante'], 0, 0);
    $pdf->ln($ln);
    $pdf->Cell(90, 0, $factura['direccion'], 0, 0, 'L');
    $pdf->Cell(0, 0, $factura['nrc'], 0, 0);
    $pdf->ln($ln);
    $pdf->Cell(90, 0, $factura['municipio'], 0, 0, 'L');
    $pdf->Cell(0, 0, $factura['giro'], 0, 0);
    $pdf->ln($ln);
    $pdf->Cell(90, 0, $factura['departamento'], 0, 0, 'L');
    $pdf->Cell(0, 0, $factura['condicionpago'], 0, 0);
    $pdf->ln($ln * 5);

    /*
     * BODY
     */

    foreach ($facturaDetalle as $detalle) {
        $pdf->ln($ln);
        $pdf->Cell(100, 0, $detalle['descripcion']);
        $pdf->Cell(0, 0, $detalle['precio']);
    }

    /*
     * FOOTHER
     */

    $pdf->SetXY($x1, $y1 + 100);
    $pdf->ln($ln);
    $pdf->Cell(85, 0, 'SON: ' . num2letras($factura['venta_total']), 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['sumas'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['iva_retenido'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['sub_total'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['venta_nosujeta'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['venta_exenta'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->Cell(85, 0, '', 0, 0, 'L');
    $pdf->Cell(25, 0, $factura['venta_total'], 0, 0, 'R');
    $pdf->ln($ln * 5);
}

$pdf->Output();
