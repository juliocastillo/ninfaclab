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
            'apellido' => $args['apellido'],
            'usuario_login' => $args['usuario_login'],
            'usuario_password' => '',
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
        
        $cbo=new HtmlRol();
        $lista=$cbo->llenarlista($args['id_rol']);
        $tpl = $this->set_var('id_rol', $lista, $tpl);
        
        
                
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
                    t01.id,
                    t01.nombre,
                    t01.apellido,
                    t01.usuario_login,
                    t01.id_rol
                FROM ctl_usuario t01 
                WHERE t01.id=$id";
        return $db->fetch_array($db->consulta($sql));
    }
    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.nombre,
                    t01.apellido,
                    t01.usuario_login,
                    t02.nombre as rol
                FROM ctl_usuario t01 
                    LEFT JOIN ctl_rol t02 ON t02.id = t01.id_rol
                WHERE t01.id!=1
                ORDER BY id";
        return $db->consulta($sql);
    }
    function set_form($id) {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $apellido = strtoupper($_POST['apellido']);
        $usuario_login = strtolower($_POST['usuario_login']);
        $sql = "UPDATE ctl_usuario SET 
                   nombre ='$nombre',
                   apellido ='$apellido',
                   usuario_login ='$usuario_login',
                   id_rol ='$_POST[id_rol]'
                WHERE id=$id";
        $db->consulta($sql);
    }
    function insert_form() {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $apellido = strtoupper($_POST['apellido']);
        $usuario_login = strtolower($_POST['usuario_login']);
        $usuario_password = md5($usuario_password);
        $sql = "INSERT INTO ctl_usuario(
                    nombre,
                    apellido,
                    usuario_login,
                    usuario_password,
                    id_rol
                    ) 
                VALUE (
                    '$nombre',
                    '$apellido',
                    '$usuario_login',
                    '$usuario_password',
                    '$_POST[id_rol]'
                )";
        $db->consulta($sql);
    }
    
    function make_table($consulta){
        $db = new MySQL();
        while ($row = $db->fetch_array($consulta)){
        $tblbody .= "<tr>".
            "<td><a href='adminUsuario.php?req=3&id=".$row['id']."'>".$row['usuario_login']."</td>".  
            "<td>".$row['nombre']."</td>".  
            "<td>".$row['apellido']."</td>".  
            "<td>".$row['rol']."</td>".  
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
            'form'              => 'adminUsuario.html',
            'FileName'          => 'adminUsuario.php?req=2',
            'FormTitle'         => 'Creación/Edición Usuario',
            'id'                => '',
            'nombre'            => '',
            'apellido'          => '',
            'id_rol'            => '',
            'usuario_login'     => '',
            'usuario_password'  => '',
            'tblbody'           => $tblbody
            );
    
    $vista->get_form($args);
} 
elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form($nombre);
    print "<script>window.location = 'adminUsuario.php'</script>";    
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
            'form'              => 'adminUsuario.html',
            'FileName'          => 'adminUsuario.php?req=4',
            'FormTitle'         => 'Creación/Edición Usuario',
            'id'                => $rec['id'],
            'nombre'            => $rec['nombre'],
            'apellido'          => $rec['apellido'],
            'id_rol'            => $rec['id_rol'],
            'usuario_login'     => $rec['usuario_login'],
            'usuario_password'  => $rec['usuario_password'],        
            'tblbody'           => $tblbody
            );
    $vista->get_form($args);
}

elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'adminUsuario.php'</script>";    
}

?>