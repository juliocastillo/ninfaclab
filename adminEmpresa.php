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
include ("entity/Ctl_empresa.php");

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
                'nombre'            => $r['nombre'],
                'nombre_comercial'  => $r['nombre_comercial'],
                'nrc'               => $r['nrc'],
                'nit'               => $r['nit'],
                'telefono'          => $r['telefono'],
                'correo'            => $r['correo'],
                'direccion'         => $r['direccion'],
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
               
        $cbo=new HtmlEmpresa();
        $lista=$cbo->llenarlista($r['id_empresa']);
        $tpl = $this->set_var('id_empresa', $lista, $tpl);
        
        $cbo=new HtmlMunicipio();
        $lista=$cbo->llenarlista($r['id_municipio']);
        $tpl = $this->set_var('id_municipio', $lista, $tpl);
        
        $cbo=new HtmlZona();
        $lista=$cbo->llenarlista($r['id_zona']);
        $tpl = $this->set_var('id_zona', $lista, $tpl);

        $cbo=new HtmlTipoempresa();
        $lista=$cbo->llenarlista($r['id_tipoempresa']);
        $tpl = $this->set_var('id_tipoempresa', $lista, $tpl);

        $cbo=new HtmlTipocomprobante();
        $lista=$cbo->llenarlista($r['id_tipocomprobante']);
        $tpl = $this->set_var('id_tipocomprobante', $lista, $tpl);
        
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
if ($_POST['exento']){ $exento = 1;} else { $exento = 0;}
if ($_POST['activo']){ $activo = 1;} else { $activo = 0;}

if (!isset($id)) {//ingresar nuevo registro desde cero
    $vista = new Form();
    $args = array ( // parametro que se pasaran a la vista
            'form'                  => 'adminEmpresa.html',
            'FileName'              => 'adminEmpresa.php',
            'FormTitle'             => 'Creación/Edición de empresas',
            'id'                    => '',
            'nombre'                => '',
            'nombre_comercial'      => '',               
            'nrc'                   => '',
            'nit'                   => '',
            'telefono'              => '',
            'correo'                => '',
            'id_municipio'          => '',
            'zona'                  => '',
            'id_tipoempresa'        => '',
            'id_tipocomprobante'    => '',
            );
    
    $vista->get_form($args);
}
elseif ($id=='') {//ingresar un nuevo registro
    $empresa = new Ctl_empresa();

    $empresa->setNombre(strtoupper($nombre));
    $empresa->setNombre_comercial(strtoupper($nombre_comercial));
    $empresa->setRegistro($nrc);
    $empresa->setNit($nit);
    $empresa->setTelefono($telefono);
    $empresa->setCorreo($correo);
    $empresa->setDireccion($direccion);
    $empresa->setId_municipio($id_municipio);
    $empresa->setExento($exento);
    $empresa->setId_zona($id_zona);
    $empresa->setId_tipoempresa($id_tipoempresa);
    $empresa->setId_tipocomprobante($id_tipocomprobante);
    $empresa->setActivo($activo);

    $empresa->commit();
    
    print "<script>window.location = 'adminEmpresa.php'</script>";    
}
else {//mostrar para modificar registro
    $empresa = new Ctl_empresa($id);
    
    $empresa->setNombre(strtoupper($nombre));
    $empresa->setNombre_comercial(strtoupper($nombre_comercial));
    $empresa->setRegistro($nrc);
    $empresa->setNit($nit);
    $empresa->setTelefono($telefono);
    $empresa->setCorreo($correo);
    $empresa->setDireccion($direccion);
    $empresa->setId_municipio($id_municipio);
    $empresa->setExento($exento);
    $empresa->setId_zona($id_zona);
    $empresa->setId_tipoempresa($id_tipoempresa);
    $empresa->setId_tipocomprobante($id_tipocomprobante);
    $empresa->setActivo($activo);
    
    $empresa->commit();
    print "<script>window.location = 'adminEmpresa.php'</script>";    
}
?>