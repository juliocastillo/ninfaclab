<?php

session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}
//abrir conexion
include ("conexion.php");

//entorno de trabajo
include ("layout.php");

//activar listado de opciones y herramientas de trabajo
include ("llenarlistas.php");
include ("tools.php");

class Vista_form extends Model_form {

    public function __construct() {
        require_once ('./llenarlistas.php');
    }

    public function get_form($args) {

        $diccionario = array(
            'form' => array(
                'FileName' => $args['FileName'],
                'FormTitle' => $args['FormTitle'],
                'id' => $args['id'],
                'abreviatura' => $args['abreviatura'],
                'nombre' => $args['nombre'],
                'margen_sup_titulo' => $args['margen_sup_titulo'],
                'margen_izq_titulo_col1' => $args['margen_izq_titulo_col1'],
                'margen_izq_titulo_col2' => $args['margen_izq_titulo_col2'],
                'interlineado_titulo' => $args['interlineado_titulo'],
                'margen_sup_detalle' => $args['margen_sup_detalle'],
                'margen_izq_detalle_col1' => $args['margen_izq_detalle_col1'],
                'margen_izq_detalle_col2' => $args['margen_izq_detalle_col2'],
                'total_filas_detalle' => $args['total_filas_detalle'],
                'interlineado_detalle' => $args['interlineado_detalle'],
                'margen_sup_totales' => $args['margen_sup_totales'],
                'margen_izq_totales_col1' => $args['margen_izq_totales_col1'],
                'margen_izq_totales_col2' => $args['margen_izq_totales_col2'],
                'interlineado_totales' => $args['interlineado_totales'],
                'margen_sup_leyenda' => $args['margen_sup_leyenda'],
                'ancho_leyenda' => $args['ancho_leyenda'],
                'tblbody' => $args['tblbody']
            )
        );
        /*
         * cargar contenido de archivo
         * para hacer el parse
         */

        $tpl = file_get_contents($args['form']);

        foreach ($diccionario['form'] as $clave => $valor) {
            $tpl = $this->set_var($clave, $valor, $tpl);
        }

        $cbo = new HtmlTamanioletra();
        $lista = $cbo->llenarlista($args['id_tamanio_letra']);
        $tpl = $this->set_var('id_tamanio_letra', $lista, $tpl);

        $cbo = new HtmlTipoletra();
        $lista = $cbo->llenarlista($args['id_tipo_letra']);
        $tpl = $this->set_var('id_tipo_letra', $lista, $tpl);



        print $tpl; //despliega la vista renderizada
    }

    public function set_var($htmlfield, $var, $tpl) {
        /*
         * asignar contenido a las variables en el html
         * solo hacer un reemplazo ya que las variables son únicas.
         */
        return str_replace('{' . $htmlfield . '}', $var, $tpl);
    }

}

class Model_form {

    public function __construct() {
        /*
         * controlador de conexion
         */
        require_once('./conexion.php');
    }

    function get_field_id($id) {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.abreviatura,
                    t01.nombre,
                    t01.id_tamanio_letra,
                    t01.id_tipo_letra,
                    t01.margen_sup_titulo, 	
                    t01.margen_izq_titulo_col1, 	
                    t01.margen_izq_titulo_col2, 	
                    t01.interlineado_titulo, 	
                    t01.margen_sup_detalle, 	
                    t01.margen_izq_detalle_col1, 	
                    t01.margen_izq_detalle_col2, 	
                    t01.total_filas_detalle, 	
                    t01.margen_sup_totales, 	
                    t01.margen_izq_totales_col1, 	
                    t01.margen_izq_totales_col2,
                    t01.interlineado_detalle,
                    t01.interlineado_totales,
                    t01.margen_sup_leyenda,
                    t01.ancho_leyenda
                FROM ctl_tipocomprobante t01 WHERE id='$id'";
        return $db->consulta($sql);
    }

    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.abreviatura,
                    t01.nombre,
                    t02.nombre as tipo_letra,
                    t03.nombre as tamanio_letra,
                    t01.margen_sup_titulo, 	
                    t01.margen_izq_titulo_col1, 	
                    t01.margen_izq_titulo_col2, 	
                    t01.interlineado_titulo, 	
                    t01.margen_sup_detalle, 	
                    t01.margen_izq_detalle_col1, 	
                    t01.margen_izq_detalle_col2, 	
                    t01.total_filas_detalle, 	
                    t01.margen_sup_totales, 	
                    t01.margen_izq_totales_col1, 	
                    t01.margen_izq_totales_col2,
                    t01.interlineado_totales,
                    t01.interlineado_detalle,
                    t01.margen_sup_leyenda,
                    t01.ancho_leyenda
                FROM ctl_tipocomprobante t01
                    LEFT JOIN ctl_tipo_letra t02 ON t02.id = t01.id_tipo_letra
                    LEFT JOIN ctl_tamanio_letra t03 ON t03.id = t01.id_tamanio_letra
                    
                ";
        return $db->consulta($sql);
    }

    function set_form($id) {
        $db = new MySQL();
        $abreviatura = strtoupper($_POST['abreviatura']);
        $nombre = strtoupper($_POST['nombre']);
        $sql = "UPDATE ctl_tipocomprobante SET 
                    abreviatura='$abreviatura',
                    nombre='$nombre',
                    id_tamanio_letra='$_POST[id_tamanio_letra]',
                    id_tipo_letra='$_POST[id_tipo_letra]',
                    margen_sup_titulo='$_POST[margen_sup_titulo]', 	
                    margen_izq_titulo_col1='$_POST[margen_izq_titulo_col1]', 	
                    margen_izq_titulo_col2='$_POST[margen_izq_titulo_col2]', 	
                    interlineado_titulo='$_POST[interlineado_titulo]', 	
                    margen_sup_detalle='$_POST[margen_sup_detalle]', 	
                    margen_izq_detalle_col1='$_POST[margen_izq_detalle_col1]', 	
                    margen_izq_detalle_col2='$_POST[margen_izq_detalle_col2]', 	
                    total_filas_detalle='$_POST[total_filas_detalle]', 	
                    margen_izq_totales_col1='$_POST[margen_izq_totales_col1]', 	
                    margen_izq_totales_col2='$_POST[margen_izq_totales_col2]',
                    interlineado_totales='$_POST[interlineado_totales]',
                    margen_sup_totales='$_POST[margen_sup_totales]',
                    interlineado_detalle='$_POST[interlineado_detalle]',
                    margen_sup_leyenda='$_POST[margen_sup_leyenda]',
                    ancho_leyenda='$_POST[ancho_leyenda]'
                WHERE id='$id'";
        $db->consulta($sql);
    }

    function insert_form() {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $abreviatura = strtoupper($_POST['abreviatura']);
        $sql = "INSERT INTO ctl_tipocomprobante(
                    abreviatura,
                    nombre,
                    id_tamanio_letra,
                    id_tipo_letra,
                    margen_sup_titulo, 	
                    margen_izq_titulo_col1, 	
                    margen_izq_titulo_col2, 	
                    interlineado_titulo, 	
                    margen_sup_detalle, 	
                    margen_izq_detalle_col1, 	
                    margen_izq_detalle_col2, 	
                    total_filas_detalle, 	
                    margen_izq_totales_col1, 	
                    margen_izq_totales_col2,
                    margen_sup_totales,
                    interlineado_totales,
                    interlineado_detalle,
                    margen_sup_leyenda,
                    ancho_leyenda
                    ) 
                VALUE (
                    '$abreviatura',
                    '$nombre',
                    '$_POST[id_tamanio_letra]',
                    '$_POST[id_tipo_letra]',
                    '$_POST[margen_sup_titulo]', 	
                    '$_POST[margen_izq_titulo_col1]', 	
                    '$_POST[margen_izq_titulo_col2]', 	
                    '$_POST[interlineado_titulo]', 	
                    '$_POST[margen_sup_detalle]', 	
                    '$_POST[margen_izq_detalle_col1]', 	
                    '$_POST[margen_izq_detalle_col2]', 	
                    '$_POST[total_filas_detalle]', 	
                    '$_POST[margen_izq_totales_col1]', 	
                    '$_POST[margen_izq_totales_col2],
                    '$_POST[margen_sup_totales],
                    '$_POST[interlineado_totales],
                    '$_POST[interlineado_detalle],
                    '$_POST[margen_sup_leyenda],
                    '$_POST[ancho_leyenda]
                )";
        $db->consulta($sql);
    }

    function make_table($resultado) {
        $db = new MySQL();
        $tblbody = '';
        while ($row = $db->fetch_array($resultado)) {
            $tblbody .= "<tr>" .
                    "<td>" . $row['abreviatura'] . "</td>" .
                    "<td><a href='adminTipocomprobante.php?req=3&id=" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</a></td>" .
                    "<td>" . $row['tipo_letra'] . "</td>" .
                    "<td>" . $row['tamanio_letra'] . "</td>" .
                    "</tr>";
        }
        return $tblbody;
    }

}

extract($_GET);
extract($_POST);

if (!isset($req)) {//ingresar nuevo registro desde cero
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $consulta = $model->get_list_fields();
    $tblbody = $model->make_table($consulta);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminTipocomprobante.html',
        'FileName' => 'adminTipocomprobante.php?req=2',
        'FormTitle' => 'Edicion Tipo Comprobantes',
        'id' => '',
        'abreviatura' => '',
        'nombre' => '',
        'id_tamanio_letra' => 1,
        'id_tipo_letra' => 1,
        'margen_sup_titulo' => '',
        'margen_izq_titulo_col1' => '',
        'margen_izq_titulo_col2' => '',
        'interlineado_titulo' => '',
        'margen_sup_detalle' => '',
        'margen_izq_detalle_col1' => '',
        'margen_izq_detalle_col2' => '',
        'total_filas_detalle' => '',
        'margen_izq_totales_col1' => '',
        'margen_izq_totales_col2' => '',
        'interlineado_detalle' => '',
        'interlineado_totales' => '',
        'margen_sup_totales' => '',
        'margen_sup_leyenda' => '',
        'ancho_leyenda' => '',
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form();
    print "<script>window.location = 'adminTipocomprobante.php'</script>";
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $resultado = $model->get_field_id($id);
    $r = $db->fetch_array($resultado);
    $tblbody = $model->make_table($resultado);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminTipocomprobante.html',
        'FileName' => 'adminTipocomprobante.php?req=4',
        'FormTitle' => 'Edicion Tipo Comprobantes',
        'id' => $r['id'],
        'abreviatura' => $r['abreviatura'],
        'nombre' => $r['nombre'],
        'id_tamanio_letra' => $r['id_tamanio_letra'],
        'id_tipo_letra' => $r['id_tipo_letra'],
        'margen_sup_titulo' => $r['margen_sup_titulo'],
        'margen_izq_titulo_col1' => $r['margen_izq_titulo_col1'],
        'margen_izq_titulo_col2' => $r['margen_izq_titulo_col2'],
        'interlineado_titulo' => $r['interlineado_titulo'],
        'margen_sup_detalle' => $r['margen_sup_detalle'],
        'margen_izq_detalle_col1' => $r['margen_izq_detalle_col1'],
        'margen_izq_detalle_col2' => $r['margen_izq_detalle_col2'],
        'total_filas_detalle' => $r['total_filas_detalle'],
        'margen_izq_totales_col1' => $r['margen_izq_totales_col1'],
        'margen_izq_totales_col2' => $r['margen_izq_totales_col2'],
        'interlineado_detalle' => $r['interlineado_detalle'],
        'interlineado_totales' => $r['interlineado_totales'],
        'margen_sup_totales' => $r['margen_sup_totales'],
        'margen_sup_leyenda' => $r['margen_sup_leyenda'],
        'ancho_leyenda' => $r['ancho_leyenda'],
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'adminTipocomprobante.php'</script>";
}
?>