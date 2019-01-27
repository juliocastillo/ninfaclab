<?php

session_start();
$_SESSION['login'];
$userID = $_SESSION['userID'];
$iva = $_SESSION['iva'];
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
        
    }

    public function get_form($args) {

//        'numero_comprobante'    => $numero_comprobante,
//            'id_tipocomprobante'    => $id_tipocomprobante,
//            'fecha_comprobante'     => $fecha_comprobante,
//            'id_cliente'            => $solicitud['id_empresa'],
//            'nombre'                => $solicitud['nombre'],
//            'direccion'             => $solicitud['direccion'],
//            'id_municipio'          => $solicitud['id_municpio'],
//            'departamento'          => $solicitud['departamento'],
//            'nrc'                   => $solicitud['nrc'],
//            'giro'                  => $solicitud['giro'],
//            'id_condicionpago'      => $id_condicionpago,
//            'id_solicitud'          => $id_solicitud,
//            'sumas'                 => $sumas,
//            'iva_retenido'          => $iva_retenido,
//            'sub_total'             => $sub_total,
//            'venta_nosujeta'        => $venta_nosujeta,
//            'venta_exenta'          => $venta_exenta,
//            'venta_total'           => $venta_total,
//            'descuento'             => $descuento,
//        
        if (isset($args['numero_comprobante'])) {
            $numero_comprobante = $args['numero_comprobante'];
        } else {
            $numero_comprobante = '';
        }
//        if (isset($args['id_sexo'])) { $id_sexo = $args['id_sexo']; } else { $id_sexo = 0; }
//        if (isset($args['id_paciente'])) { $id_paciente = $args['id_paciente']; } else { $id_paciente = 0; }
//        if (isset($args['id_medico'])) { $id_medico = $args['id_medico']; } else { $id_medico = 0; }
//        if (isset($args['id_empresa'])) { $id_empresa = $args['id_empresa']; } else { $id_empresa = 0; }
//        if (isset($args['id_tipoempresa'])) { $id_tipoempresa = $args['id_tipoempresa']; } else { $id_tipoempresa = 0; }
//        if (isset($args['id_pruebaslab'])) { $id_pruebaslab = $args['id_pruebaslab']; } else { $id_pruebaslab = 0; }
//        if (isset($args['id_perfil'])) { $id_perfil = $args['id_perfil']; } else { $id_perfil = 0; }
//        if (isset($args['id_procedencia'])) { $id_procedencia = $args['id_procedencia']; } else { $id_procedencia = 0; }
//        if (isset($args['id_servicio'])) { $id_servicio = $args['id_servicio']; } else { $id_servicio = 0; }
//        if (isset($args['id_lugarentrega'])) { $id_lugarentrega = $args['id_lugarentrega']; } else { $id_lugarentrega = 0; }
//        if (isset($args['sumas'])) { $sumas = $args['sumas']; } else { $sumas = ''; }
//        if (isset($args['descuento'])) { $descuento = $args['descuento']; } else { $descuento = ''; }
        if (isset($args['comentario'])) { $comentario = $args['comentario']; } else { $comentario = ''; }
        if (isset($args['id_factura'])) { $id_factura = $args['id_factura']; } else { $id_factura = ''; }
        

        $diccionario = array(
            'form' => array(
                'action'                => $args['action'],
                'FormTitle'             => $args['FormTitle'],
                'id_solicitud'          => $args['id_solicitud'],
                'id_factura'            => $id_factura,
                'numero_comprobante'    => $numero_comprobante,
                'fecha_comprobante'     => $args['fecha_comprobante'],
                'nombre'                => $args['nombre'],
                'direccion'             => $args['direccion'],
                'telefono'              => $args['telefono'],
                'nrc'                   => $args['nrc'],
                'giro'                  => $args['giro'],
                'departamento'          => $args['departamento'],
                'id_tipocomprobante'    => $args['id_tipocomprobante'],
                'id_condicionpago'      => $args['id_condicionpago'],
                'id_notaremision'       => $args['id_notaremision'],
                'comentario'            => $comentario,
                'sumas'                 => $args['sumas'],
                'iva_retenido'          => $args['iva_retenido'],
                'sub_total'             => $args['sub_total'],
                'venta_nosujeta'        => $args['venta_nosujeta'],
                'venta_exenta'          => $args['venta_exenta'],
                'venta_total'           => $args['venta_total'],
                'tblbody'               => $args['tblbody']
            )
        );

        /*
         * cargar contenido de archivo
         * para hacer el parse
         */
        $tpl = file_get_contents($args['form']);

        /*
         * cargar listado de secciones de laboratorio
         */
        $cbo = new HtmlTipocomprobante();
        $lista = $cbo->llenarlista($args['id_tipocomprobante']);
        $tpl = $this->set_var('id_tipocomprobante', $lista, $tpl);

        $cbo = new HtmlEmpresa();
        $lista = $cbo->llenarlista($args['id_empresa']);
        $tpl = $this->set_var('id_empresa', $lista, $tpl);

        $cbo = new HtmlCondicionpago();
        $lista = $cbo->llenarlista($args['id_condicionpago']);
        $tpl = $this->set_var('id_condicionpago', $lista, $tpl);

        $cbo = new HtmlMunicipio();
        $lista = $cbo->llenarlista($args['id_municipio']);
        $tpl = $this->set_var('id_municipio', $lista, $tpl);

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

    function get_totales_comprobante($id_tipocomprobante) {
        $db = new MySQL();
        $sql = "SELECT 
                sum(fd.venta_gravada) AS venta_gravada,
                sum(fd.venta_gravada)*.13 AS iva,
               (sum(fd.venta_gravada)+sum(fd.venta_gravada)*.13) AS subtotal,
			   IF(sum(fd.venta_gravada)>=100,sum(fd.venta_gravada)*c.iva_retenido,0) AS iva_retenido,
			   (sum(fd.venta_gravada)+sum(fd.venta_gravada)*.13)-IF(sum(fd.venta_gravada)>=100,sum(fd.venta_gravada)*c.iva_retenido,0) AS venta_total
            FROM facturas_detalle fd
				LEFT JOIN fac_factura f ON fd.id_tipocomprobante = f.id
				LEFT JOIN clientes c ON f.id_cliente = c.id
            WHERE fd.id_tipocomprobante='$id_tipocomprobante'
            GROUP BY id_tipocomprobante
            ";
        return $db->fetch_array($db->consulta($sql));
    }

    function actualizar_totales($id_tipocomprobante, $userID = 0) {
        /*
         * actualizar nuevos saldos
         */
        $db = new MySQL();
        $totales = $this->get_totales_comprobante($id_tipocomprobante);

        $sql = "UPDATE fac_factura SET
            venta_gravada='$totales[venta_gravada]',
            iva='$totales[iva]',
            subtotal='$totales[subtotal]',
            iva_retenido='$totales[iva_retenido]', 
            venta_total='$totales[venta_total]',
            date_mod=NOW(),
            user_mod='$userID'
        WHERE id='$id_tipocomprobante'";
        $db->consulta($sql);
    }

    function insertar_form($userID, $iva) {
        $db = new MySQL();
        $fecha_comprobante = datetosql($_POST['fecha_comprobante']);
        $venta_total = $_POST['venta_total'];
        $system_date = date("Y-m-d");
        $sql = "INSERT INTO fac_factura 
            (
                id_solicitud,
                numero_comprobante,
                fecha_comprobante,
                id_tipocomprobante,
                id_condicionpago,
                id_empresa,
                nombre,
                direccion,
                telefono,
                sumas,
                iva_retenido,
                sub_total,
                venta_nosujeta,
                venta_exenta,
                venta_total,
                iva,
                id_estadofactura,
                activo,
                date_add, 
                user_add
            ) 
            VALUE
            (
                '$_POST[id_solicitud]',
                '$_POST[numero_comprobante]',
                NOW(),
                '$_POST[id_tipocomprobante]',
                '$_POST[id_condicionpago]',
                '$_POST[id_empresa]',
                '$_POST[nombre]',
                '$_POST[direccion]',
                '$_POST[telefono]',
                '$_POST[sumas]',
                '$_POST[iva_retenido]',
                '$_POST[sub_total]',
                '$_POST[venta_nosujeta]',
                '$_POST[venta_exenta]',
                '$_POST[venta_total]',
                $iva,
                1,
                1,
                NOW(),
                $userID
            )";
        $db->consulta($sql);
        $id_factura = $db->ultimo_id_ingresado();
        
        return $id_factura;
    }

    function get_field_id($id_solicitud) {
        $db = new MySQL();
        $sql = "SELECT 
                    t01.id_empresa,
                    t01.sumas,
                    t01.venta_total,
                    t01.total_descuento,
                    t02.nombre,
                    t02.direccion,
                    t02.telefono,
                    t02.nrc,
                    t02.giro,
                    t02.id_tipocomprobante,
                    t03.id as id_municipio,
                    t04.nombre as departamento
                FROM lab_solicitud t01 
                LEFT JOIN ctl_empresa t02 ON t02.id = t01.id_empresa
                LEFT JOIN ctl_municipio t03 ON t03.id = t02.id_municipio
                LEFT JOIN ctl_departamento t04 ON t04.id = t03.id_departamento
                WHERE t01.id = $id_solicitud";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_factura_id($id_factura) {
        $db = new MySQL();
        $sql = "SELECT 
                    t01.id as id_factura,
                    t01.id_solicitud,
                    t01.numero_comprobante,
                    t01.id_empresa,
                    DATE_FORMAT(t01.fecha_comprobante,'%d/%m/%Y') as fecha_comprobante,
                    t01.nombre,
                    t01.direccion,
                    t01.telefono,
                    t01.id_tipocomprobante,
                    t02.id_municipio,
                    t04.nombre as departamento,
                    t02.nrc,
                    t02.giro,
                    id_condicionpago,
                    id_notaremision,
                    comentario,
                    sumas,
                    iva_retenido,
                    sub_total,
                    venta_nosujeta,
                    venta_exenta,
                    venta_total                 
                FROM fac_factura t01 
                LEFT JOIN ctl_empresa t02 ON t02.id = t01.id_empresa
                LEFT JOIN ctl_municipio t03 ON t03.id = t02.id_municipio
                LEFT JOIN ctl_departamento t04 ON t04.id = t03.id_departamento
                WHERE t01.id = $id_factura";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_field_detalle_id($id_solicitud) {
        $db = new MySQL();
        $sql = "
            SELECT 
                1 as cantidad,
                t03.nombre as descripcion, 
                t01.precio
            FROM lab_detallesolicitud t01
            LEFT JOIN lab_solicitud   t02 ON t02.id = t01.id_solicitud
            LEFT JOIN ctl_pruebaslab  t03 ON t03.id = t01.id_pruebaslab
            WHERE t02.id = $id_solicitud
            ";
        return $db->consulta($sql);
    }
    
    function get_factura_detalle_id($id_factura) {
        $db = new MySQL();
        $sql = "
            SELECT 
                1 as cantidad,
                t01.descripcion as descripcion, 
                t01.precio
            FROM fac_detallefactura t01
            WHERE t01.id_factura = $id_factura
            ";
        return $db->consulta($sql);
    }

    function make_table($consulta) {
        $db = new MySQL();
        $i = 0;
        $tblbody = '';
        while ($row = $db->fetch_array($consulta)) {
            $i++;
            $tblbody .= "<tr>".
            "<td><input type='text' name='cantidad[]' value='" .$row['cantidad'] . "' style='width: 50px;'></td>".  
            "<td><input type='text' name='descripcion[]' value='" .$row['descripcion'] . "' style='width: 450px;'></td>".  
            "<td></td>". 
            "<td></td>". 
            "<td><input type='text' name='precio[]' value='" .$row['precio'] . "' style='width: 75px;'></td>".
            "</tr>";
        }
        return $tblbody;
    }

    function get_list_fields($id_tipocomprobante) {
        $db = new MySQL();
        $sql = "SELECT 
                    fd.id,
                    fd.cantidad,
                    fd.precio_unit,
                    fd.subtotal,
                    fd.iva,
                    fd.venta_gravada,
                    p.nombre AS id_producto
                FROM facturas_detalle fd
                LEFT JOIN producto p ON fd.id_producto = p.id
                WHERE id_tipocomprobante='$id_tipocomprobante'";
        return $db->consulta($sql);
    }

    function update_item($id_factura, $userID) {

//        actualizando comprobante factura o ccf
        $db = new MySQL();
        $sql = "UPDATE fac_factura SET
            id_tipocomprobante='$_POST[id_tipocomprobante]',
            numero_comprobante='$_POST[numero_comprobante]',
            id_empresa='$_POST[id_empresa]', 
            nombre='$_POST[nombre]',
            direccion='$_POST[direccion]',
            telefono='$_POST[telefono]',
            comentario='$_POST[comentario]',
            date_mod=NOW(),
            user_mod='$userID'
        WHERE id='$id_factura'";
        return $db->consulta($sql);
    }

    function delete_item($id, $id_tipocomprobante) {

//        borrando item de comprobante factura o ccf
        $db = new MySQL();
        $sql = "DELETE FROM facturas_detalle WHERE id='$id'";
        $db->consulta($sql);

        /*
         * actualizar nuevos saldos
         */
        $this->actualizar_totales($id_tipocomprobante);
    }

    function insert_detalle($id_factura,$precio,$descripcion,$userID) {
        $db = new MySQL();
        $sql = "INSERT INTO fac_detallefactura(
                    id_factura,
                    cantidad,
                    descripcion,
                    precio,
                    date_add,	
                    user_add
                    ) 
                VALUE (
                    '$id_factura',
                    1,
                    '$descripcion',
                    '$precio',
                    NOW(),
                    $userID
                )
               ";
        $db->consulta($sql);
        return;
    }
}

extract($_GET);
extract($_POST);
//intanciar si no existe solicitud estudio
isset($_GET['id_solicitud']) ? $id_solicitud = $_GET['id_solicitud'] : $id_solicitud = 0;
if (!isset($req)) {//ingresar nuevo registro desde cero
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    $fecha_comprobante = DATE('d/m/Y');

    // traer solicitud
    $solicitud = $model->get_field_id($id_solicitud);
    // traer detalles de la solicitud
    $consulta = $model->get_field_detalle_id($id_solicitud);
    $tblbody = $model->make_table($consulta);

    //calculando totales
    if ($solicitud['id_tipocomprobante'] == 1) { //consumidor final
        $sumas = $solicitud['sumas'];
        $iva_retenido = 0;
        $sub_total = $solicitud['sumas'];
        $venta_exenta = 0;
        $venta_total = number_format($sub_total - $venta_exenta, 2);
    } elseif ($solicitud['id_tipocomprobante'] == 2) { //credito fiscal
        $iva_retenido = $solicitud['sumas'] * $iva / 100;
        $sumas = $solicitud['sumas'] - $iva_retenido;
        $sub_total = $solicitud['sumas'];
        $venta_exenta = number_format($sumas * 0.01, 2);
        $venta_total = number_format($sub_total - $venta_exenta, 2);
    }
    $args = array(// parametro que se pasaran a la vista
        'form' => 'facFactura.html',
        'action' => 'facFactura.php?req=2&id_solicitud=' . $id_solicitud,
        'FormTitle' => 'FACTURACION (nuevo)',
        'id_tipocomprobante' => $solicitud['id_tipocomprobante'],
        'fecha_comprobante' => $fecha_comprobante,
        'id_empresa' => $solicitud['id_empresa'],
        'direccion' => $solicitud['direccion'],
        'telefono' => $solicitud['telefono'],
        'nombre' => $solicitud['nombre'],
        'id_municipio' => $solicitud['id_municipio'],
        'departamento' => $solicitud['departamento'],
        'nrc' => $solicitud['nrc'],
        'giro' => $solicitud['giro'],
        'id_condicionpago' => 1,
        'id_notaremision' => 0,
        'id_solicitud' => $id_solicitud,
        'sumas' => $sumas, //sumatoria todo
        'iva_retenido' => $iva_retenido, //13 de la suma
        'sub_total' => $sub_total,
        'venta_nosujeta' => 0,
        'venta_exenta' => $venta_exenta,
        'total_descuento' => $solicitud['total_descuento'],
        'venta_total' => $venta_total,
        'tblbody' => $tblbody
    );
    $vista->get_form($args);
} elseif ($req == 2) {//insertar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $id_factura = $model->insertar_form($userID, $iva);
    //guardar detalle hijos
    for ($i=0;$i<count($_POST['descripcion']); $i++) {
        $model->insert_detalle($id_factura,$precio[$i],$descripcion[$i],$userID);
    }
    print "<script>window.location = 'facFactura.php?req=3&id_factura=" . $id_factura . "'</script>";
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();

    // traer solicitud
    $factura = $model->get_factura_id($id_factura);
    // traer detalles de la solicitud
    $consulta = $model->get_factura_detalle_id($id_factura);
    $tblbody = $model->make_table($consulta);


    $args = array(// parametro que se pasaran a la vista
        'form'                  => 'facFactura.html',
        'action'                => 'facFactura.php?req=4&id_factura=' . $id_factura,
        'FormTitle'             => 'FACTURACION (modificar)',
        'id_empresa'            => $factura['id_empresa'],
        'nombre'                => $factura['nombre'],
        'id_tipocomprobante'    => $factura['id_tipocomprobante'],
        'numero_comprobante'    => $factura['numero_comprobante'],
        'fecha_comprobante'     => $factura['fecha_comprobante'],
        'direccion'             => $factura['direccion'],
        'telefono'              => $factura['telefono'],
        'id_municipio'          => $factura['id_municipio'],
        'departamento'          => $factura['departamento'],
        'nrc'                   => $factura['nrc'],
        'giro'                  => $factura['giro'],
        'id_condicionpago'      => $factura['id_condicionpago'],
        'id_solicitud'          => $factura['id_solicitud'],
        'id_factura'            => $id_factura,
        'id_notaremision'       => 0,
        'sumas'                 => $factura['sumas'],
        'iva_retenido'          => $factura['iva_retenido'],
        'comentario'            => $factura['comentario'],
        'sub_total'             => $factura['sub_total'],
        'venta_nosujeta'        => $factura['venta_nosujeta'],
        'venta_exenta'          => $factura['venta_exenta'],
        'venta_total'           => $factura['venta_total'],
        'tblbody'               => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 4) {//modificar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->update_item($id_factura,$userID);
    //guardar detalle hijos
//    for ($i=0;$i<count($_POST['descripcion']); $i++) {
//        $model->update_detalle($id_factura,$precio[$i],$descripcion[$i],$userID);
//    }
    print "<script>window.location = 'facFactura.php?req=3&id_factura=" . $id_factura . "'</script>";
}
    
?>

