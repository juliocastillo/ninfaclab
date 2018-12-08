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

/* Entidad */
include ("entity/Mnt_paciente.php");

class Form {
    public function __construct() {
        require_once ('./llenarlistas.php'); 
    }

    public function get_form($r) {
        $diccionario = array(
            'form' => array(
                'FileName'          => $r['FileName'],
                'FormTitle'         => $r['FormTitle'],
                'id'                => $r['id'],
                'nombres'           => $r['nombres'],
                'apellidos'         => $r['apellidos'],
                'registro'          => $r['registro'],
                'edad'              => $r['edad'],
                'direccion'         => $r['direccion'],
                'telefono'          => $r['telefono'],
            )
        );
        /*
         * cargar contenido de archivo
         * para hacer el parse
         */

        $tpl = file_get_contents($r['form']);
        foreach ($diccionario['form'] as $clave => $valor) {
            $tpl = $this->set_var($clave, $valor, $tpl);
        }
               
        $cbo=new HtmlSexo();
        $lista=$cbo->llenarlista($r['id_sexo']);
        $tpl = $this->set_var('id_sexo', $lista, $tpl);
        
        $cbo=new HtmlEmpresa();
        $lista=$cbo->llenarlista($r['id_empresa']);
        $tpl = $this->set_var('id_empresa', $lista, $tpl);
       
        $cbo=new HtmlPaciente();
        $lista=$cbo->llenarlista($r['id_paciente']);
        $tpl = $this->set_var('id_paciente', $lista, $tpl);
       
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



$db = new MySQL();
extract($_POST);
if ($_POST['activo']){ $activo = 1;} else { $activo = 0;}

if (!isset($id)) {//ingresar nuevo registro desde cero
    $vista = new Form();
    $args = array ( // parametro que se pasaran a la vista
            'form'          => 'adminPaciente.html',
            'FileName'      => 'adminPaciente.php',
            'FormTitle'     => 'Creación/Edición de pacientes',
            'id'            => '',
            'nombres'       => '',
            'apellidos'     => '',               
            'registro'      => '',
            'edad'          => '',
            'id_sexo'       => '',
            'direccion'     => '',
            'telefono'      => '',
            'id_empresa'    => '',
            );
    $vista->get_form($args);
}
else {//mostrar para modificar registro
    $paciente = new Mnt_paciente($id);
    
    $paciente->setNombres(strtoupper($nombres));
    $paciente->setApellidos(strtoupper($apellidos));
    $paciente->setRegistro($registro);
    $paciente->setEdad($edad);
    $paciente->setDireccion($direccion);
    $paciente->setTelefono($telefono);
    $paciente->setId_sexo($id_sexo);
    $paciente->setId_empresa($id_empresa);
    $r = $paciente->setActivo($activo);
    $paciente->commit();
    print "<script>window.location = 'adminPaciente.php'</script>";    
}
?>