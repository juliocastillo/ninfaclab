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

$r = $model->getTipocomprobante_id($factura['id_tipocomprobante']);

$pdf = new FPDF('P', 'mm', 'Legal');

/*
 * variables de posicion
 */

$tipo = array(// parametro que se pasaran a la vista
    'id' => $r['id'],
    'abreviatura' => $r['abreviatura'],
    'nombre' => $r['nombre'],
    'tamanio_letra' => $r['tamanio_letra'],
    'tipo_letra' => $r['tipo_letra'],
    'margen_sup_titulo' => $r['margen_sup_titulo'],
    'margen_izq_titulo_col1' => $r['margen_izq_titulo_col1'],
    'margen_izq_titulo_col2' => $r['margen_izq_titulo_col2'],
    'interlineado_titulo' => $r['interlineado_titulo'],
    'margen_sup_detalle' => $r['margen_sup_detalle'],
    'margen_izq_detalle_col1' => $r['margen_izq_detalle_col1'],
    'margen_izq_detalle_col2' => $r['margen_izq_detalle_col2'],
    'total_filas_detalle' => $r['total_filas_detalle'],
    'interlineado_detalle' => $r['interlineado_detalle'],
    'margen_izq_totales_col1' => $r['margen_izq_totales_col1'],
    'margen_izq_totales_col2' => $r['margen_izq_totales_col2'],
    'interlineado_totales' => $r['interlineado_totales'],
    'margen_sup_totales' => $r['margen_sup_totales'],
    'margen_sup_leyenda' => $r['margen_sup_leyenda'],
    'ancho_leyenda' => $r['ancho_leyenda'],
);

/* margenes para titulo */
$x1 = $tipo['margen_izq_titulo_col1'];
$y1 = $tipo['margen_sup_titulo'];
$x2 = $tipo['margen_izq_titulo_col2'];
$ln = $tipo['interlineado_titulo'];
$ln1 = $tipo['interlineado_detalle'];
$ln2 = $tipo['interlineado_totales'];

/* margenes para detalle */
$x3 = $tipo['margen_izq_detalle_col1'];
$y2 = $tipo['margen_sup_detalle'];
$x4 = $tipo['margen_izq_detalle_col2'];

/* margenes para totales */
$x5 = $tipo['margen_izq_totales_col1'];
$y3 = $tipo['margen_sup_totales'];
$x6 = $tipo['margen_izq_totales_col2'];

/* margen leyenda */
$y4 = $tipo['margen_sup_leyenda'];
$l = $tipo['ancho_leyenda'];

$font = (object) array(
            'name' => $tipo['tipo_letra'],
            'sizeNormal' => $r['tamanio_letra'],
);
$pdf->SetFont($font->name, '', $font->sizeNormal);
$pdf->AddPage();

if ($factura['id_tipocomprobante'] == 1) { //CONSUMIDOR FINAL
    /*
     * HEADER
     */
    $pdf->ln($ln);
    $pdf->SetXY($x1, $y1);
    $pdf->Cell($x2, $y1 - 15, $factura['numero_comprobante'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['nombre'], 0, 0, 'L');
    $pdf->SetX($x2);
    $pdf->Cell($x2, $y1, $factura['dia'] . '            ' . mes_en_letras($factura['mes']) . '           ' . $factura['anio']);
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['direccion'], 0, 0, 'L');
    $pdf->Cell($x2, $y1, '');
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['telefono'], 0, 0, 'L');

    /*
     * BODY
     */

    $pdf->SetXY($x3, $y1 + $y2);
    $pdf->ln($ln1);
    foreach ($facturaDetalle as $detalle) {
        $pdf->SetX($x3);
        $pdf->Cell($x3, $y1 + $y2, $detalle['descripcion']);
        $pdf->SetX($x4);
        $pdf->Cell($x4, $y1 + $y2, $detalle['precio']);
        $pdf->ln($ln1);
    }
    if ($factura['comentario'] != '') {
        $pdf->SetXY($x3, $y4);
        $pdf->MultiCell($l,3, $factura['comentario']);
    }
    /*
     * FOOTHER
     */
    $pdf->ln($ln1);
    $pdf->SetXY($x5, $y1 + $y2 + $y3);
    $pdf->Cell($x5, $y1 + $y2 + $y3, 'SON: ' . num2letras($factura['venta_total']));
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['sumas']);
    $pdf->ln($ln1);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, '');
    $pdf->ln($ln1);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, '');
    $pdf->ln($ln1);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, '');
    $pdf->ln($ln1);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, '');
    $pdf->ln($ln1);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['venta_total']);
} elseif ($factura['id_tipocomprobante'] == 2) { // CREDITO FISCAL
    /*
     * HEADER
     */
    $pdf->ln($ln);
    $pdf->SetXY($x1, $y1);
    $pdf->Cell($x2, $y1 - 15, $factura['numero_comprobante'], 0, 0, 'R');
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['nombre'], 0, 0, 'L');
    $pdf->SetX($x2);
    $pdf->Cell($x2, $y1, $factura['dia'] . '            ' . mes_en_letras($factura['mes']) . '           ' . $factura['anio'], 0, 0);
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['direccion'], 0, 0, 'L');
    $pdf->SetX($x2);
    $pdf->Cell($x2, $y1, $factura['nrc']);
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['municipio'], 0, 0, 'L');
    $pdf->SetX($x2);
    $pdf->Cell($x2, $y1, $factura['giro']);
    $pdf->ln($ln);
    $pdf->SetX($x1);
    $pdf->Cell($x1, $y1, $factura['departamento'], 0, 0, 'L');
    $pdf->SetX($x2);
    $pdf->Cell($x2, $y1, $factura['condicionpago']);


    /*
     * BODY
     */

    $pdf->SetXY($x3, $y1 + $y2);
    $pdf->ln($ln1);
    foreach ($facturaDetalle as $detalle) {
        $pdf->SetX($x3);
        $pdf->Cell($x3, $y1 + $y2, $detalle['descripcion']);
        $pdf->SetX($x4);
        $pdf->Cell($x4, $y1 + $y2, $detalle['precio']);
        $pdf->ln($ln1);
    }
    if ($factura['comentario'] != '') {
        $pdf->SetXY($x3, $y4);
        $pdf->MultiCell($l,3, $factura['comentario']);
    }
    /*
     * FOOTHER
     */
    $pdf->ln($ln2);
    $pdf->SetXY($x5, $y1 + $y2 + $y3);
    $pdf->Cell($x5, $y1 + $y2 + $y3, 'SON: ' . num2letras($factura['venta_total']));
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['sumas']);
    $pdf->ln($ln2);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['iva_retenido']);
    $pdf->ln($ln2);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['sub_total']);
    $pdf->ln($ln2);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['venta_nosujeta']);
    $pdf->ln($ln2);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['venta_exenta']);
    $pdf->ln($ln2);
    $pdf->SetX($x6);
    $pdf->Cell($x6, $y1 + $y2 + $y3, $factura['venta_total']);
}

$pdf->Output();
