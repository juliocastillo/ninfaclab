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

/*
 * libreria de query
 */
include ("repository/LabSolicitudRepository.php");


class Vista_form extends Model_form {

    public function __construct() {
        require_once ('./llenarlistas.php');
    }

    public function get_form($args) {
        if (isset($args['edad'])) { $edad = $args['edad']; } else { $edad = ''; }
        if (isset($args['id_sexo'])) { $id_sexo = $args['id_sexo']; } else { $id_sexo = 0; }
        if (isset($args['id_paciente'])) { $id_paciente = $args['id_paciente']; } else { $id_paciente = 0; }
        if (isset($args['id_medico'])) { $id_medico = $args['id_medico']; } else { $id_medico = 0; }
        if (isset($args['id_empresa'])) { $id_empresa = $args['id_empresa']; } else { $id_empresa = 0; }
        if (isset($args['id_tipoempresa'])) { $id_tipoempresa = $args['id_tipoempresa']; } else { $id_tipoempresa = 0; }
        if (isset($args['id_pruebaslab'])) { $id_pruebaslab = $args['id_pruebaslab']; } else { $id_pruebaslab = 0; }
        if (isset($args['id_perfil'])) { $id_perfil = $args['id_perfil']; } else { $id_perfil = 0; }
        if (isset($args['id_procedencia'])) { $id_procedencia = $args['id_procedencia']; } else { $id_procedencia = 0; }
        if (isset($args['id_servicio'])) { $id_servicio = $args['id_servicio']; } else { $id_servicio = 0; }
        if (isset($args['id_lugarentrega'])) { $id_lugarentrega = $args['id_lugarentrega']; } else { $id_lugarentrega = 0; }
        if (isset($args['sumas'])) { $sumas = $args['sumas']; } else { $sumas = ''; }
        if (isset($args['descuento'])) { $descuento = $args['descuento']; } else { $descuento = ''; }
        if (isset($args['venta_total'])) { $venta_total = $args['venta_total']; } else { $venta_total = ''; }
        if (isset($args['id_solicitud'])) { $id_solicitud = $args['id_solicitud']; } else { $id_solicitud = ''; }
        
        $diccionario = array(
            'form' => array(
                'FileName'          => $args['FileName'],
                'FormTitle'         => $args['FormTitle'],
                'id_solicitud'      => $id_solicitud,
                'edad'              => $edad,
                'sumas'             => $sumas,
                'descuento'         => $descuento,
                'venta_total'             => $venta_total,
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
        $lista = $cbo->llenarlista($id_sexo);
        $tpl = $this->set_var('id_sexo', $lista, $tpl);

        $cbo = new HtmlPaciente();
        $lista = $cbo->llenarlista($id_paciente);
        $tpl = $this->set_var('id_paciente', $lista, $tpl);

        $cbo = new HtmlMedico();
        $lista = $cbo->llenarlista($id_medico);
        $tpl = $this->set_var('id_medico', $lista, $tpl);

        $cbo = new HtmlEmpresa();
        $lista = $cbo->llenarlista($id_empresa);
        $tpl = $this->set_var('id_empresa', $lista, $tpl);

        $cbo = new HtmlTipoempresa();
        $lista = $cbo->llenarlista($id_tipoempresa);
        $tpl = $this->set_var('id_tipoempresa', $lista, $tpl);

        $cbo = new HtmlPruebaslab();
        $lista = $cbo->llenarlista($id_pruebaslab);
        $tpl = $this->set_var('id_pruebaslab', $lista, $tpl);

        $cbo = new HtmlPerfil();
        $lista = $cbo->llenarlista($id_perfil);
        $tpl = $this->set_var('id_perfil', $lista, $tpl);

        $cbo = new HtmlProcedencia();
        $lista = $cbo->llenarlista($id_procedencia);
        $tpl = $this->set_var('id_procedencia', $lista, $tpl);

        $cbo = new HtmlServicio();
        $lista = $cbo->llenarlista($id_servicio);
        $tpl = $this->set_var('id_servicio', $lista, $tpl);

        $cbo = new HtmlLugarentrega();
        $lista = $cbo->llenarlista($id_lugarentrega);
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

    function get_field_id($id_solicitud) {
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
            t03.id_tipoempresa,
            t01.sumas,
            t01.descuento,
            t01.venta_total
                     FROM lab_solicitud t01
                        LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
                        LEFT JOIN ctl_empresa t03 ON t03.id = t02.id_empresa
                     WHERE t01.id='$id_solicitud'";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_list_fields($id_solicitud) {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id as id_detallesolicitud,
                    t02.id as id_pruebalab,
                    t02.nombre as prueba,
                    t01.precio as precio_prueba
                FROM lab_detallesolicitud  t01
                LEFT JOIN ctl_pruebaslab   t02 ON t02.id = t01.id_pruebaslab
                WHERE t01.id_solicitud = $id_solicitud";
        return $db->consulta($sql);
    }

    function set_form($id_solicitud,$userID) {
        $db = new MySQL();
        $sql = "UPDATE lab_solicitud SET 
                   id_paciente      =$_POST[id_paciente],
                   id_medico        =$_POST[id_medico],
                   sumas            =$_POST[sumas],
                   descuento        =$_POST[descuento],
                   venta_total            =$_POST[venta_total],
                   total_descuento  =$_POST[sumas] - $_POST[venta_total],
                   date_mod         =NOW(),	
                   user_mod         =$userID
                WHERE id=$id_solicitud";
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
                    venta_total,
                    total_descuento,
                    id_estadosolicitud,
                    id_servicio,	
                    date_add,	
                    user_add
                    ) 
                VALUE (
                    NOW(),
                    $_POST[id_paciente],
                    $_POST[id_empresa],
                    $_POST[id_medico],
                    $_POST[id_lugarentrega],
                    $_POST[sumas],
                    $_POST[descuento],
                    $_POST[venta_total],
                    $_POST[sumas] - $_POST[venta_total],
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
        $tblbody = '';
        while ($row = $db->fetch_array($result)){
        $tblbody .= "<tr>".
            "<td><input type='text' name='id_prueba[]' value='" .$row['id_pruebalab'] . "' style='width: 50px;' hidden></td>".  
            "<td>".$row['prueba']."</td>".  
            "<td><input type='text' name='precio_prueba[]' value='" .$row['precio_prueba'] . "' style='width: 75px;'></td>".   
            "<td><input id='remove_item' type='button' value='Eliminar' onclick='deleteDetalle(" .$row['id_detallesolicitud'] . ")'></td>".   
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
//    $consulta = $model->get_list_fields($id_paciente);
    $args = array(// parametro que se pasaran a la vista
        'form'              => 'labSolicitud.html',
        'FileName'          => 'labSolicitud.php?req=2',
        'FormTitle'         => 'Lista de Precios',
        'tblbody'           =>''
    );

    $vista->get_form($args);
} elseif ($req == 2) {//insertar un nuevo registro
    //validar si hay datos correctos
    if (!isset($_POST['id_prueba'])) {
        exit('No hay pruebas seleccionadas para la solicitud');
    }
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    //guardar solicitud padre
    $id_solicitud = $model->insert_form($userID);

    //guardar detalle hijos
    for ($i=0;$i<count($_POST['id_prueba']); $i++) {
        $model->insert_detalle($id_solicitud,$id_prueba[$i],$precio_prueba[$i],$userID);
    }
    print "<script>window.location = 'labSolicitud.php?req=3&id_solicitud=$id_solicitud'</script>";
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $solicitud = $model->get_field_id($id_solicitud);
    $result = $model->get_list_fields($id_solicitud);
    $tblbody = $model->make_table($result);

    $args = array(// parametro que se pasaran a la vista
        'form' => 'labSolicitud.html',
        'FileName'          => 'labSolicitud.php?req=4&id_solicitud='.$id_solicitud,
        'FormTitle'         => 'Mantenimiento de fecha_solicituds',
        'id_solicitud'      => $solicitud['id'],
        'fecha_solicitud'   => $solicitud['fecha_solicitud'],
        'id_paciente'       => $solicitud['id_paciente'],
        'id_medico'         => $solicitud['id_medico'],
        'id_sexo'           => $solicitud['id_sexo'],
        'edad'              => $solicitud['edad'],
        'sumas'             => $solicitud['sumas'],
        'descuento'         => $solicitud['descuento'],
        'venta_total'       => $solicitud['venta_total'],
        'id_empresa'        => $solicitud['id_empresa'],
        'id_tipoempresa'    => $solicitud['id_tipoempresa'],
        'tblbody'           => $tblbody
    );
    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id_solicitud, $userID);
    //guardar detalle hijos
    for ($i=0;$i<count($_POST['id_prueba']); $i++) {
        $model->insert_detalle($id_solicitud,$id_prueba[$i],$precio_prueba[$i],$userID);
    }
    print "<script>window.location = 'labSolicitud.php?req=3&id_solicitud=$id_solicitud'</script>";
}
?>