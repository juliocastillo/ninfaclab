<?php
session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login']!=true){
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
            'FileName'=>$args['FileName'],
            'FormTitle'=>$args['FormTitle'],
            'id'=>$args['id'],
            'nombre' => $args['nombre'],
            'tblbody'=>$args['tblbody']
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
    function get_field_id($id){
        $db = new MySQL();
        $sql = "SELECT
                    id,
                    nombre
                FROM ctl_tipoempresa WHERE id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }
    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT
                    id,
                    nombre
                FROM ctl_tipoempresa 
                ORDER BY id";
        return $db->consulta($sql);
    }
    function set_form($id) {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $sql = "UPDATE ctl_tipoempresa SET 
                   nombre ='$_POST[nombre]'
                WHERE id='$id'";
        $db->consulta($sql);
    }
    function insert_form() {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $sql = "INSERT INTO ctl_tipoempresa(
                    nombre) 
                VALUE (
                    '$_POST[nombre]')";
        $db->consulta($sql);
    }
    
    function make_table($consulta){
        $db = new MySQL();
        while ($row = $db->fetch_array($consulta)){
        $tblbody .= "<tr>".
            "<td><a href='adminTipoempresa.php?req=3&id=".$row['id']."'>".$row['nombre']."</td>".  
            "</tr>";
        }
        return $tblbody;
    }
}



extract($_GET);
extract($_POST);

if (!$req) {//ingresar nuevo registro desde cero
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $consulta = $model->get_list_fields();
    $tblbody = $model->make_table($consulta);
    $args = array ( // parametro que se pasaran a la vista
            'form' => 'adminTipoempresa.html',
            'FileName' => 'adminTipoempresa.php?req=2',
            'FormTitle' => 'Creación/Edición Tipo empresa',
            'id'=> '',
            'nombre' => '',
            'tblbody' => $tblbody
            );
    
    $vista->get_form($args);
} 
elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form($nombre);
    print "<script>window.location = 'adminTipoempresa.php'</script>";    
}
elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $rec = $model->get_field_id($id);
    $consulta = $model->get_list_fields();
    $tblbody = $model->make_table($consulta);
    $args = array ( // parametro que se pasaran a la vista
            'form' => 'adminTipoempresa.html',
            'FileName' => 'adminTipoempresa.php?req=4',
            'FormTitle' => 'Creación/Edición Tipo empresa',
            'id'=> $rec['id'],
            'nombre' => $rec['nombre'],
            'tblbody' => $tblbody
            );
    $vista->get_form($args);
}

elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'adminTipoempresa.php'</script>";    
}

?>