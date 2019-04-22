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
        $cbo = new HtmlElemento();
        $lista = $cbo->llenarlista($args['id_elemento']);
        $tpl = $this->set_var('id_elemento', $lista, $tpl);

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
                    id_elemento,
                    nombre
                FROM ctl_posibleresultado WHERE id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t03.nombre as prueba,
                    t02.nombre as elemento,
                    t01.nombre as posibleresultado,
                    t01.activo
                FROM ctl_posibleresultado    t01
                LEFT JOIN ctl_elemento       t02 ON t02.id = t01.id_elemento
                LEFT JOIN ctl_pruebaslab     t03 ON t03.id = t02.id_pruebaslab
                ORDER BY t03.nombre, t02.nombre, t01.nombre
                ";
        return $db->consulta($sql);
    }

    function set_form($id) {
        $db = new MySQL();
        $sql = "UPDATE ctl_posibleresultado SET 
                   id_elemento ='$_POST[id_elemento]',
                   nombre ='$_POST[nombre]'
                WHERE id='$id'";
        $db->consulta($sql);
    }

    function insert_form() {
        $db = new MySQL();
        $sql = "INSERT INTO ctl_posibleresultado(
                    nombre,
                    id_elemento,
                    activo
                    ) 
                VALUE (
                    '$_POST[nombre]',
                    '$_POST[id_elemento]',
                    '1'
                )";
        $db->consulta($sql);
    }

    function make_table($consulta) {
        $db = new MySQL();
        $tblbody = '';
        while ($row = $db->fetch_array($consulta)) {
            $tblbody .= "<tr>" .
                    "<td>" . $row['prueba'] . "</td>" .
                    "<td>" . $row['elemento'] . "</td>" .
                    "<td><a href='adminPosibleresultado.php?req=3&id=" . $row['id'] . "'>" . utf8_encode($row['posibleresultado']) . "</a></td>" .
                    "<td>" . $row['activo'] . "</td>" .
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
    if (!isset($id_elemento)) {
        $id_elemento = '000';
    }
    /*
     * declarar parametros para enviar a la vista
     */
    $consulta = $model->get_list_fields();
    $tblbody = $model->make_table($consulta);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminPosibleresultado.html',
        'FileName' => 'adminPosibleresultado.php?req=2',
        'FormTitle' => 'Creacion/Edicion posibles resultados',
        'id' => '',
        'id_elemento' => $id_elemento,
        'nombre' => '',
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form();
    print "<script>window.location = 'adminPosibleresultado.php?id_elemento=".$id_elemento."'</script>";
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
        'form' => 'adminPosibleresultado.html',
        'FileName' => 'adminPosibleresultado.php?req=4',
        'FormTitle' => 'Creacion/Edicion Pruebas lababoratorio',
        'id' => $rec['id'],
        'id_elemento' => $rec['id_elemento'],
        'nombre' => $rec['nombre'],
        'tblbody' => $tblbody
    );
    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'adminPosibleresultado.php?id_elemento=".$id_elemento."'</script>";
}
?>