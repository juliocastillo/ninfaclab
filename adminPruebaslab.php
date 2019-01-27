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
                'codigo' => $args['codigo'],
                'nombre' => $args['nombre'],
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
        $cbo = new HtmlArealab();
        $lista = $cbo->llenarlista($args['id_arealab']);
        $tpl = $this->set_var('id_arealab', $lista, $tpl);

        $cbo = new HtmlFormatosalida();
        $lista = $cbo->llenarlista($args['id_formatosalida']);
        $tpl = $this->set_var('id_formatosalida', $lista, $tpl);



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
                    id,
                    codigo,
                    nombre,
                    id_arealab,
                    id_formatosalida
                FROM ctl_pruebaslab WHERE id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.codigo,
                    t01.nombre,
                    t02.nombre as arealab,
                    t03.nombre as formatosalida
                FROM ctl_pruebaslab         t01
                LEFT JOIN ctl_arealab       t02 ON t02.id = t01.id_arealab
                LEFT JOIN ctl_formatosalida t03 ON t03.id = t01.id_formatosalida
                ORDER BY id";
        return $db->consulta($sql);
    }

    function set_form($id) {
        $db = new MySQL();
        $codigo = strtoupper($_POST['codigo']);
        $nombre = strtoupper($_POST['nombre']);
        $sql = "UPDATE ctl_pruebaslab SET 
                   codigo ='$codigo',
                   nombre ='$nombre',
                   id_arealab ='$_POST[id_arealab]',
                   id_formatosalida ='$_POST[id_formatosalida]'
                WHERE id='$id'";
        $db->consulta($sql);
    }

    function insert_form() {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $codigo = strtoupper($_POST['codigo']);
        $sql = "INSERT INTO ctl_pruebaslab(
                    codigo,
                    nombre,
                    id_arealab,
                    id_formatosalida,
                    activo
                    ) 
                VALUE (
                    '$codigo',
                    '$nombre',
                    '$_POST[id_arealab]',
                    '$_POST[id_formatosalida]',
                    '1'
                )";
        $db->consulta($sql);
    }

    function make_table($consulta) {
        $db = new MySQL();
        $tblbody = '';
        while ($row = $db->fetch_array($consulta)) {
            $tblbody .= "<tr>" .
                    "<td>" . $row['codigo'] . "</td>" .
                    "<td><a href='adminPruebaslab.php?req=3&id=" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</a></td>" .
                    "<td>" . $row['arealab'] . "</td>" .
                    "<td><a onclick='addElementos(" . $row['id'] . ")' href='#'>" . $row['formatosalida'] . "</a></td>" .
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
        'form' => 'adminPruebaslab.html',
        'FileName' => 'adminPruebaslab.php?req=2',
        'FormTitle' => 'Creación/Edición Pruebaslab',
        'id' => '',
        'codigo' => '',
        'nombre' => '',
        'id_arealab' => '',
        'id_formatosalida' => '',
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form();
    print "<script>window.location = 'adminPruebaslab.php'</script>";
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $rec = $model->get_field_id($id);
    $consulta = $model->get_list_fields();
    $tblbody = $model->make_table($consulta);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminPruebaslab.html',
        'FileName' => 'adminPruebaslab.php?req=4',
        'FormTitle' => 'Creación/Edición Pruebas lababoratorio',
        'id' => $rec['id'],
        'codigo' => $rec['codigo'],
        'nombre' => $rec['nombre'],
        'id_arealab' => $rec['id_arealab'],
        'id_formatosalida' => $rec['id_formatosalida'],
        'tblbody' => $tblbody
    );
    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'adminPruebaslab.php'</script>";
}
?>