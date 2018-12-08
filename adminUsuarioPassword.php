<?php

session_start();
$_SESSION['login'];
$_SESSION['userID'];
$userID = $_SESSION['userID'];

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
                'password_actual' => $args['password_actual'],
                'nuevo_password' => $args['nuevo_password'],
                'confirme_password' => $args['confirme_password'],
                'message' => $args['message'],
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

    function password_exist($id, $password_actual) {
        $db = new MySQL();
        $usuario_password = md5($password_actual);
        $sql = "SELECT
                    *
                FROM ctl_usuario t01 
                WHERE t01.id=$id AND usuario_password = '$usuario_password'";

        return $db->fetch_array($db->consulta($sql));
    }

    function set_form($id) {
        $db = new MySQL();
        $usuario_password = md5($_POST[nuevo_password]);
        $sql = "UPDATE ctl_usuario SET 
                   usuario_password ='$usuario_password'
                WHERE id=$id";
        $db->consulta($sql);
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
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminUsuarioPassword.html',
        'FileName' => 'adminUsuarioPassword.php?req=2',
        'FormTitle' => 'Cambiar password',
        'id' => $userID,
        'password_actual' => '',
        'nuevo_password' => '',
        'confirme_password' => ''
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();

    //verificar claves iguales
    if ($_POST['nuevo_password'] === $_POST['confirme_password']) {
        $result = $model->password_exist($id, $_POST['password_actual']);
        if ($result) {
            $model->set_form($id);
            print "<script>window.location = 'index.php'</script>";
        } else {
            $args = array(// parametro que se pasaran a la vista
                'form' => 'adminUsuarioPassword.html',
                'FileName' => 'adminUsuarioPassword.php?req=2',
                'FormTitle' => 'Cambiar password',
                'id' => $userID,
                'password_actual' => '',
                'nuevo_password' => '',
                'confirme_password' => '',
                'message' => 'Parámetros no válidos, No se pudo cambiar el password'
            );
            $vista->get_form($args);
        }
    } else {
         $args = array(// parametro que se pasaran a la vista
                'form' => 'adminUsuarioPassword.html',
                'FileName' => 'adminUsuarioPassword.php?req=2',
                'FormTitle' => 'Cambiar password',
                'id' => $userID,
                'password_actual' => '',
                'nuevo_password' => '',
                'confirme_password' => '',
                'message' => 'Las claves no son identicas, No se pudo cambiar el password'
            );
            $vista->get_form($args);
    }
}
?>