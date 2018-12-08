<?php

session_start();
$_SESSION['login'];
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
                'FileName'          => $args['FileName'],
                'FormTitle'         => $args['FormTitle'],
                'id'                => $args['id'],
                'fecha_solicitud'   => $args['fecha_solicitud'],
                'edad'              => $args['edad'],
                'sumas'             => $args['sumas'],
                'descuento'         => $args['descuento'],
                'total'             => $args['total'],
                'tblbody'           =>$args['tblbody']
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

        $cbo = new HtmlSexo();
        $lista = $cbo->llenarlista($args['id_sexo']);
        $tpl = $this->set_var('id_sexo', $lista, $tpl);

        $cbo = new HtmlPaciente();
        $lista = $cbo->llenarlista($args['id_paciente']);
        $tpl = $this->set_var('id_paciente', $lista, $tpl);

        $cbo = new HtmlMedico();
        $lista = $cbo->llenarlista($args['id_medico']);
        $tpl = $this->set_var('id_medico', $lista, $tpl);

        $cbo = new HtmlEmpresa();
        $lista = $cbo->llenarlista($args['id_empresa']);
        $tpl = $this->set_var('id_empresa', $lista, $tpl);

        $cbo = new HtmlTipoempresa();
        $lista = $cbo->llenarlista($args['id_tipoempresa']);
        $tpl = $this->set_var('id_tipoempresa', $lista, $tpl);

        $cbo = new HtmlPruebaslab();
        $lista = $cbo->llenarlista($args['id_pruebaslab']);
        $tpl = $this->set_var('id_pruebaslab', $lista, $tpl);

        $cbo = new HtmlPerfil();
        $lista = $cbo->llenarlista($args['id_perfil']);
        $tpl = $this->set_var('id_perfil', $lista, $tpl);

        $cbo = new HtmlProcedencia();
        $lista = $cbo->llenarlista($args['id_procedencia']);
        $tpl = $this->set_var('id_procedencia', $lista, $tpl);

        $cbo = new HtmlServicio();
        $lista = $cbo->llenarlista($args['id_servicio']);
        $tpl = $this->set_var('id_servicio', $lista, $tpl);

        $cbo = new HtmlLugarentrega();
        $lista = $cbo->llenarlista($args['id_lugarentrega']);
        $tpl = $this->set_var('id_lugarentrega', $lista, $tpl);

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
            date_format(t01.fecha_solicitud,'%d-%m-%Y %H:%i:%s') fecha_solicitud, 
            t01.id_paciente, 
            t01.id_medico,
            t01.id_lugarentrega,
            t01.id_servicio,
            t02.nombres, 
            t02.apellidos,
            t02.edad,
            t02.id_sexo,
            t02.id_empresa,
            t01.sumas,
            t01.descuento,
            t01.total
                     FROM lab_solicitud t01
                        LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
                     WHERE t01.id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_list_fields($id_solicitud) {
        $db = new MySQL();
        $sql = "SELECT
                    t02.id,
                    t02.nombre as prueba,
                    t01.precio as precio_prueba
                FROM lab_detallesolicitud  t01
                LEFT JOIN ctl_pruebaslab   t02 ON t02.id = t01.id_pruebaslab
                WHERE t01.id_solicitud = $id_solicitud";
        return $db->consulta($sql);
    }

    function set_form($id_solicitud) {
        $db = new MySQL();
        $pruebalab = $_POST[id_medico][0];
        $sql = "UPDATE lab_solicitud SET 
                   fecha_solicitud ='$_POST[fecha_solicitud]',
                   id_paciente ='$_POST[id_paciente]',
                   sumas ='$_POST[sumas]',
                   descuento ='$_POST[descuento]',
                   total ='$_POST[total]',
                   id_medico ='$pruebalab'
                WHERE id='$id_solicitud'";
        $db->consulta($sql);
    }

    function insert_form($userID) {
        $db = new MySQL();
        $sql = "INSERT INTO lab_solicitud(
                    fecha_solicitud,
                    id_paciente,
                    id_empresa,
                    id_medico,
                    id_lugarentrega,
                    sumas,	
                    descuento,	
                    total,	
                    id_estadosolicitud,
                    id_servicio,	
                    date_add,	
                    user_add
                    ) 
                VALUE (
                    NOW(),
                    '$_POST[id_paciente]',
                    '$_POST[id_empresa]',
                    '$_POST[id_medico]',
                    '$_POST[id_lugarentrega]',
                    '$_POST[sumas]',
                    '$_POST[descuento]',
                    '$_POST[total]',
                    1,
                    '$_POST[id_servicio]',
                    NOW(),
                    $userID
                )";
        $db->consulta($sql);

        return $db->ultimo_id_ingresado();
    }

    function insert_detalle($id_solicitud,$id_prueba,$precio,$userID) {
        $db = new MySQL();
        $sql = "INSERT INTO lab_detallesolicitud(
                        id_solicitud,	
                        id_pruebaslab,
                        precio,
                        id_estadosolicitud,
                        date_add,	
                        user_add
                    ) 
                VALUE (
                    '$id_solicitud',
                    '$id_prueba',
                    '$precio',
                    1,
                    NOW(),
                    $userID
                )
                ON DUPLICATE KEY UPDATE 
                            precio='$precio',
                            date_mod=NOW(),
                            user_mod=$userID
               ";
        $db->consulta($sql);

        return;
    }
    
    function make_table($result){
        $db = new MySQL();
        while ($row = $db->fetch_array($result)){
        $tblbody .= "<tr>".
            "<td><input type='text' name='id_prueba[]' value='" .$row['id'] . "' style='width: 50px;' hidden></td>".  
            "<td>".$row['prueba']."</td>".  
            "<td><input type='text' name='precio_prueba[]' value='" .$row['precio_prueba'] . "' style='width: 75px;'></td>".   
            "<td><input id='remove_item' type='button' value='Qutar' onclick='removeDetalle()'></td>".   
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
//    $consulta = $model->get_list_fields($id_paciente);
    $args = array(// parametro que se pasaran a la vista
        'form'              => 'labSolicitud.html',
        'FileName'          => 'labSolicitud.php?req=2',
        'FormTitle'         => 'Lista de Precios',
        'id'                => '',
        'fecha_solicitud'   => '',
        'sumas'             => 0,
        'descuento'         => 0,
        'total'             => 0,
        'tblbody'           =>''
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    //validar si hay datos correctos
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();

    //guardar solicitud padre
    $id_solicitud = $model->insert_form($userID);

    //guardar detalle hijos
    for ($i=0;$i<count($_POST['id_prueba']); $i++) {
        $model->insert_detalle($id_solicitud,$id_prueba[$i],$precio_prueba[$i],$userID);
    }
    $args = array(// parametro que se pasaran a la vista
        'form'              => 'labSolicitud.html',
        'FileName'          => 'labSolicitud.php?req=2',
        'FormTitle'         => 'Lista de Precios',
        'id'                => '',
        'sumas'             => 0,
        'descuento'         => 0,
        'total'             => 0,
        'fecha_solicitud'   => '',
        'tblbody'           =>''
    );
    $vista->get_form($args);
    
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $rec = $model->get_field_id($id_solicitud);
    $result = $model->get_list_fields($id_solicitud);
    $tblbody = $model->make_table($result);

    $args = array(// parametro que se pasaran a la vista
        'form' => 'labSolicitud.html',
        'FileName'          => 'labSolicitud.php?req=4&id_solicitud='.$id_solicitud,
        'FormTitle'         => 'Mantenimiento de fecha_solicituds',
        'id'                => $rec['id'],
        'fecha_solicitud'   => $rec['fecha_solicitud'],
        'id_paciente'       => $rec['id_paciente'],
        'id_medico'         => $rec['id_medico'],
        'id_sexo'           => $rec['id_sexo'],
        'edad'              => $rec['edad'],
        'sumas'             => $rec['sumas'],
        'descuento'         => $rec['descuento'],
        'total'             => $rec['total'],
        'id_empresa'        => $rec['id_empresa'],
        'tblbody'           => $tblbody
    );
    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id_solicitud);
    //guardar detalle hijos
    for ($i=0;$i<count($_POST['id_prueba']); $i++) {
        $model->insert_detalle($id_solicitud,$id_prueba[$i],$precio_prueba[$i],$userID);
    }
    print "<script>window.location = 'labSolicitud.php'</script>";
}
?>