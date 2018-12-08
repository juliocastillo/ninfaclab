<?php
session_start();
$_SESSION['login'];
$_SESSION['userID'];
$_SESSION['username'];
$userID     = $_SESSION['userID'];
$username   = $_SESSION['username'];

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
            'mensaje' => $args['mensaje'],
            'usuario' => $args['usuario'],
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
                    mensaje,
                    id_usuario
                FROM tool_mailer WHERE id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }
    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.mensaje,
                    date_format(t01.fecha,'%d-%m-%Y %H:%i:%s') fecha,
                    CONCAT(t02.nombre, ' ', t02.apellido) as usuario,
                    if (t01.activo=1,'Pendiente','Cerrado') as activo
                FROM tool_mailer   t01
                LEFT JOIN ctl_usuario t02 ON t02.id = t01.id_usuario
                ORDER BY id DESC";
        return $db->consulta($sql);
        
    }
    function set_form($id, $activo) {
        $db = new MySQL();
        if ($activo=='Pendiente'){
            $activo = 0;
        } else {
            $activo = 1;
        }
        $sql = "UPDATE tool_mailer SET 
                   activo = $activo
                WHERE id='$id'";
        $db->consulta($sql);
    }
    function insert_form() {
        $userID = $_SESSION['userID'];
        $db = new MySQL();
        $sql = "INSERT INTO tool_mailer(
                    mensaje,
                    fecha,
                    id_usuario,
                    activo
                    ) 
                VALUE (
                    '$_POST[mensaje]',
                    NOW(),
                    $userID,
                    1    
                    )";
        $db->consulta($sql);
    }
    
    function make_table($consulta){
        $db = new MySQL();
        while ($row = $db->fetch_array($consulta)){
        $tblbody .= "<tr>".
            "<td><a href='toolMailer.php?req=3&id=".$row['id']."&activo=".$row['activo']."'>".$row['activo']."</td>".  
            "<td>".$row['fecha']."</td>".  
            "<td>".$row['usuario']."</td>".  
            "<td style='width:250px;'><textarea rows='3' cols='25px'>".$row['mensaje']."</textarea></td>".  
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
            'form'      => 'toolMailer.html',
            'FileName'  => 'toolMailer.php?req=2',
            'FormTitle' => 'Sistema de notificacion de eventos',
            'id'        => '',
            'mensaje'   => '',
            'usuario'   => $username,
            'tblbody'   => $tblbody
            );
    
    $vista->get_form($args);
} 
elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form($nombre);
    print "<script>window.location = 'toolMailer.php'</script>";
}
elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * cambiar activo = false
     */
    $model->set_form($id,$activo);
    

    /*
     * declarar parametros para enviar a la vista
     */
    $consulta = $model->get_list_fields();
    $tblbody = $model->make_table($consulta);
    
    
    $args = array ( // parametro que se pasaran a la vista
            'form'      => 'toolMailer.html',
            'FileName'  => 'toolMailer.php?req=2',
            'FormTitle' => 'Sistema de notificacion de eventos',
            'id'        => '',
            'mensaje'   => '',
            'usuario'   => $username,
            'tblbody'   => $tblbody
            );
    
    $vista->get_form($args);
}

?>