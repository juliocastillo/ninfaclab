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
                'precio' => $args['precio'],
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
        $cbo = new HtmlTipoempresa();
        $lista = $cbo->llenarlista($args['id_tipoempresa']);
        $tpl = $this->set_var('id_tipoempresa', $lista, $tpl);

        $cbo = new HtmlPruebaslab();
        if ($args['id_pruebaslab'] != "") {
            $lista = $cbo->llenarlista($args['id_pruebaslab']);
        } else {
            $lista = $cbo->llenarlista_sinprecios($args['id_tipoempresa'], $args['id_pruebaslab']);
        }
        $tpl = $this->set_var('id_pruebaslab', $lista, $tpl);



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
                    precio,
                    id_tipoempresa,
                    id_pruebaslab
                FROM mnt_precio_tipoempresa_pruebaslab WHERE id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_list_fields($lista) {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.precio,
                    t02.nombre as tipoempresa,
                    CONCAT(t03.codigo,' ', t03.nombre) as pruebaslab
                FROM mnt_precio_tipoempresa_pruebaslab  t01
                LEFT JOIN ctl_tipoempresa               t02 ON t02.id = t01.id_tipoempresa
                LEFT JOIN ctl_pruebaslab                t03 ON t03.id = t01.id_pruebaslab
                WHERE t02.id = $lista
                ORDER BY t03.codigo";
        return $db->consulta($sql);
    }

    function set_form($id) {
        $db = new MySQL();
        $pruebalab = $_POST[id_pruebaslab][0];
        $sql = "UPDATE mnt_precio_tipoempresa_pruebaslab SET 
                   precio ='$_POST[precio]',
                   id_tipoempresa ='$_POST[id_tipoempresa]',
                   id_pruebaslab ='$pruebalab'
                WHERE id='$id'";
        $db->consulta($sql);
    }

    function insert_form() {
        $db = new MySQL();
        foreach ($_POST[id_pruebaslab] as $prueba) { //guardar multiple prueba
            $sql = "INSERT INTO mnt_precio_tipoempresa_pruebaslab(
                        precio,
                        id_tipoempresa,
                        id_pruebaslab
                        ) 
                    VALUE (
                        '$_POST[precio]',
                        '$_POST[id_tipoempresa]',
                        '$prueba'
                    ) ON DUPLICATE KEY UPDATE precio='$_POST[precio]'";
            $db->consulta($sql);
        } //fin for each
    }

    function make_table($consulta) {
        $db = new MySQL();
        $tblbody = "";
        while ($row = $db->fetch_array($consulta)) {
            $tblbody .= "<tr>" .
                    "<td>" . $row['pruebaslab'] . "</td>" .
                    "<td>" . $row['precio'] . "</td>" .
                    "<td style=' text-align: center;'><a href='mntPrecios.php?req=3&id=" . $row['id'] . "'>" . "<img src='public/images/carpabiertat.gif' alt='abrir' height='25' width='25'>" . "</a></td>" .
                    "</tr>";
        }
        return $tblbody;
    }

}

extract($_GET);
extract($_POST);
if (!isset($id_tipoempresa)) {
    $id_tipoempresa = 1;
}
if (!isset($req)) {//ingresar nuevo registro desde cero
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $consulta = $model->get_list_fields($id_tipoempresa);
    $tblbody = $model->make_table($consulta);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'mntPrecios.html',
        'FileName' => 'mntPrecios.php?req=2',
        'FormTitle' => 'Lista de Precios',
        'id' => '',
        'precio' => '',
        'id_tipoempresa' => $id_tipoempresa,
        'id_pruebaslab' => '',
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    $model->insert_form();
    $consulta = $model->get_list_fields($_POST['id_tipoempresa']);
    $tblbody = $model->make_table($consulta);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'mntPrecios.html',
        'FileName' => 'mntPrecios.php?req=2',
        'FormTitle' => 'Lista de Precios',
        'id' => '',
        'precio' => '',
        'id_tipoempresa' => $_POST['id_tipoempresa'],
        'id_pruebaslab' => '',
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $rec = $model->get_field_id($id);
    $consulta = $model->get_list_fields($rec['id_tipoempresa']);
    //$tblbody = $model->make_table($consulta);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'mntPrecios.html',
        'FileName' => 'mntPrecios.php?req=4',
        'FormTitle' => 'Mantenimiento de precios',
        'id' => $rec['id'],
        'precio' => $rec['precio'],
        'id_tipoempresa' => $rec['id_tipoempresa'],
        'id_pruebaslab' => $rec['id_pruebaslab'],
        'tblbody' => ""
    );
    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'mntPrecios.php'</script>";
}
?>