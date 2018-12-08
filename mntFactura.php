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
    }

    public function get_form($args) {
        
        $diccionario = array(
            'form'                  => array(
            'action'                => $args['action'],
            'FormTitle'             => $args['FormTitle'],
            'id_solicitud'          => $args['id_solicitud'],
            'numero_comprobante'    => $args['numero_comprobante'],
            'fecha_comprobante'     => $args['fecha_comprobante'],
            'nombre'                => $args['nombre'],
            'nombre_comercial'      => $args['nombre_comercial'],
            'direccion'             => $args['direccion'],
            'nrc'                   => $args['nrc'],
            'giro'                  => $args['giro'],
            'departamento'          => $args['departamento'],
            'id_tipocomprobante'    => $args['id_tipocomprobante'],
            'id_condicionpago'      => $args['id_condicionpago'],
            'subtotal'              => $args['subtotal'],
            'venta_gravada'         => $args['venta_gravada'],    
            'iva'                   => $args['iva'],
            'iva_retenido'          => $args['iva_retenido'],     
            'descuento'             => $args['descuento'],
            'venta_total'           => $args['venta_total'],
            'id_estadofactura'      => $args['id_estadofactura'],
            'tblbody'               => $args['tblbody']
            )
        );
        
        /*
         * cargar contenido de archivo
         * para hacer el parse
         */
        $tpl = file_get_contents($args[form]);
        
        /*
         * cargar listado de secciones de laboratorio
         */
        $cbo = new HtmlTipocomprobante();
        $lista = $cbo->llenarlista($args[id_tipocomprobante]);
        $tpl = $this->set_var('id_tipocomprobante', $lista, $tpl);

        $cbo = new HtmlEmpresa();
        $lista = $cbo->llenarlista($args[id_cliente]);
        $tpl = $this->set_var('id_empresa', $lista, $tpl);
        
        $cbo = new HtmlCondicionpago();
        $lista = $cbo->llenarlista($args[id_condicionpago]);
        $tpl = $this->set_var('id_condicionpago', $lista, $tpl);
        
        $cbo = new HtmlMunicipio();
        $lista = $cbo->llenarlista($args[id_municipio]);
        $tpl = $this->set_var('id_municipio', $lista, $tpl);
        
        foreach ($diccionario[form] as $clave => $valor) {
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
        
    function get_totales_comprobante($id_tipocomprobante){
        $db=new MySQL();
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
    
    function actualizar_totales($id_tipocomprobante, $userID = 0){
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
    
    function insertar_form($userID) {
        $db = new MySQL();
        $fecha_comprobante = datetosql($_POST[fecha_comprobante]);
        $venta_total = datetosql($_POST[venta_total]);
        $system_date=date("Y-m-d");
        $sql = "INSERT INTO fac_factura 
            (
                id_solicitud,
                numero_comprobante,
                fecha_comprobante,
                id_tipocomprobante,
                id_condicionpago,
                id_empresa,
                nombre,
                nombre_comercial,
                registro,
                direccion,
                id_municipio,
                subtotal,
                venta_gravada,
                iva,
                iva_retenido,
                descuento,
                venta_total,
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
                '$_POST[nombre_comercial]',
                '$_POST[registro]',
                '$_POST[direccion]',
                '$_POST[id_municipio]',
                '$_POST[subtotal]',
                '$_POST[venta_gravada]',
                '$_POST[iva]',
                '$_POST[iva_retenido]',
                '$_POST[descuento]',
                '$_POST[venta_total]',
                1,
                1,
                NOW(),
                $userID
            )";
        $db->consulta($sql);
        $id_factura = $db->ultimo_id_ingresado();
        
        return $id_factura;
    }
    
    function get_field_id($id_solicitud){
        $db = new MySQL();
        $sql = "SELECT 
                    t01.id_empresa,
                    t02.nombre,
                    t02.nombre_comercial,
                    t02.direccion,
                    t02.registro as nrc,
                    t02.giro,
                    t03.id as id_municipio,
                    t04.nombre as departamento
                FROM lab_solicitud t01 
                LEFT JOIN ctl_empresa t02 ON t02.id = t01.id_empresa
                LEFT JOIN ctl_municipio t03 ON t03.id = t02.id_municipio
                LEFT JOIN ctl_departamento t04 ON t04.id = t03.id_departamento
                WHERE t01.id = $id_solicitud";
        return $db->fetch_array($db->consulta($sql));
    }    
    
    function get_solicitud_factura_id($id_solicitud, $id_factura){
        $db = new MySQL();
        $sql = "SELECT 
                    t01.id as id_solicitud,
                    t02.id as id_factura,
                    t02.id_empresa,
                    DATE_FORMAT(t02.fecha_comprobante,'%d/%m/%Y') as fecha_comprobante,
                    t02.nombre,
                    t02.nombre_comercial,
                    t02.direccion,
                    t02.numero_comprobante
                FROM lab_solicitud t01 
                LEFT JOIN fac_factura t02 ON t02.id_solicitud = t01.id
                LEFT JOIN ctl_empresa t03 ON t03.id = t01.id_empresa
                WHERE t01.id = $id_solicitud AND t02.id = $id_factura";
        return $db->fetch_array($db->consulta($sql));
    }    
    
    function get_field_detalle_id($id_solicitud){
        $db = new MySQL();
        $sql = "
            SELECT 
                1 as cantidad,
                t03.nombre as prueba, 
                t01.precio
            FROM lab_detallesolicitud t01
            LEFT JOIN lab_solicitud   t02 ON t02.id = t01.id_solicitud
            LEFT JOIN ctl_pruebaslab  t03 ON t03.id = t01.id_pruebaslab
            WHERE t02.id = $id_solicitud
            ";
        return $db->consulta($sql);
    }
    
    
    
    function make_table($consulta,$id_tipocomprobante=0){
        $db = new MySQL();        
        $i=0;
        while ($row = $db->fetch_array($consulta)){
            $i++;
            $tblbody .= "<tr>".
                "<td><input type='text' name='cantidad[]' value='".$row['cantidad']."' style='width: 50px'></td>".
                "<td>".$row['prueba']."</td>".
                "<td style='text-align: center'></td>".    
                "<td style='text-align: center'></td>".
                "<td>".$row['precio']."</td>".
                "</tr>";
        }
            $tblbody .= "<tr>".
                "<td style='background-color: gainsboro;' colspan='5'>".'Total'."</td>".
                "</tr>";
        
        
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
    
    function update_item($id_factura,$userID){


//        actualizando comprobante factura o ccf
        $db = new MySQL();
        $sql = "UPDATE fac_factura SET
            id_tipocomprobante='$_POST[id_tipocomprobante]',
            numero_comprobante='$_POST[numero_comprobante]',
            id_empresa='$_POST[id_empresa]', 
            nombre='$_POST[nombre]',
            nombre_comercial='$_POST[nombre_comercial]',
            direccion='$_POST[direccion]',
            venta_total='$venta_total', 
            id_condicionpago='$_POST[id_condicionpago]', 
            iva_retenido='$_POST[iva_retenido]', 
            id_condicionpago='$_POST[id_condicionpago]',
            descuento='$_POST[descuento]',
            date_mod=NOW(),
            user_mod='$userID'
        WHERE id='$id_factura'";
        return $db->consulta($sql);
    }

    function delete_item($id,$id_tipocomprobante){
        
//        borrando item de comprobante factura o ccf
        $db = new MySQL();
        $sql = "DELETE FROM facturas_detalle WHERE id='$id'";
        $db->consulta($sql);
        
         /*
         * actualizar nuevos saldos
         */
        $this->actualizar_totales($id_tipocomprobante);
    }
    
    
}




extract($_GET);
extract($_POST);
isset($_GET['req']) ? $req=$_GET['req'] : $req=0;
//intanciar si no existe solicitud estudio
isset($_GET['id_solicitud']) ? $id_solicitud=$_GET['id_solicitud'] : $id_solicitud=0;
if ($req==0) {//ingresar nuevo registro desde cero
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    $fecha_comprobante = DATE('d/m/Y');  
    
    // traer solicitud
    $solicitud = $model->get_field_id($id_solicitud);
    // traer detalles de la solicitud
    $consulta = $model->get_field_detalle_id($id_solicitud);
    $tblbody  = $model->make_table($consulta);
    
    

    $args = array ( // parametro que se pasaran a la vista
            'form'                  => 'mntFactura.html',
            'action'                => 'mntFactura.php?req=2&id_solicitud='.$id_solicitud,
            'FormTitle'             => 'FACTURACION',
            'numero_comprobante'    => $numero_comprobante,
            'id_tipocomprobante'    => $id_tipocomprobante,
            'fecha_comprobante'     => $fecha_comprobante,
            'id_cliente'            => $solicitud['id_empresa'],
            'nombre'                => $solicitud['nombre'],
            'direccion'             => $solicitud['direccion'],
            'id_municipio'          => $solicitud['id_municpio'],
            'departamento'          => $solicitud['departamento'],
            'nrc'                   => $solicitud['nrc'],
            'giro'                  => $solicitud['giro'],
            'id_condicionpago'      => $id_condicionpago,
            'id_solicitud'          => $id_solicitud,
            'sumas'                 => $sumas,
            'iva_retenido'          => $iva_retenido,
            'sub_total'             => $sub_total,
            'venta_nosujeta'        => $venta_nosujeta,
            'venta_exenta'          => $venta_exenta,
            'venta_total'           => $venta_total,
            'descuento'             => $descuento,
            'tblbody'               => $tblbody
            );
    $vista->get_form($args);

} 
elseif ($req == 2) {//insertar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $id_factura = $model->insertar_form($userID);
    print "<script>window.location = 'mntFactura.php?req=3&id_factura=".$id_factura."&id_solicitud=".$id_solicitud."'</script>";
}
elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
   
    // traer solicitud
    $factura = $model->get_solicitud_factura_id($id_solicitud,$id_factura);
    // traer detalles de la solicitud
    $consulta = $model->get_field_detalle_id($id_solicitud);
    $tblbody  = $model->make_table($consulta);
    

    $args = array ( // parametro que se pasaran a la vista
            'form'                  => 'mntFactura.html',
            'action'                => 'mntFactura.php?req=4&id_factura='.$id_factura.'&id_solicitud='.$id_solicitud,
            'FormTitle'             => 'FACTURACION',
            'numero_comprobante'    => $numero_comprobante,
            'id_tipocomprobante'    => $id_tipocomprobante,
            'fecha_comprobante'     => $fecha_comprobante,
            'id_cliente'            => $solicitud['id_empresa'],
            'nombre'                => $solicitud['nombre'],
            'direccion'             => $solicitud['direccion'],
            'id_municipio'          => $solicitud['id_municpio'],
            'departamento'          => $solicitud['departamento'],
            'nrc'                   => $solicitud['nrc'],
            'giro'                  => $solicitud['giro'],
            'id_condicionpago'      => $id_condicionpago,
            'id_solicitud'          => $id_solicitud,
            'sumas'                 => $sumas,
            'iva_retenido'          => $iva_retenido,
            'sub_total'             => $sub_total,
            'venta_nosujeta'        => $venta_nosujeta,
            'venta_exenta'          => $venta_exenta,
            'venta_total'           => $venta_total,
            'descuento'             => $descuento,
            'tblbody'               => $tblbody
            ); 
    
    $vista->get_form($args);
}

elseif ($req == 4) {//modificar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->update_item($id_factura,$userID);
    print "<script>window.location = 'mntFactura.php?req=3&id_factura=".$id_factura."&id_solicitud=".$id_solicitud."'</script>";
}


elseif ($req == 5) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $rec = $model->get_field_id($id_tipocomprobante);
    $rec_detalle = $model->get_field_detalle_id($id_detalle);
    
    $consulta = $model->get_list_fields($id_tipocomprobante);
    $tbl_detalle = $model->make_table($consulta,$id_tipocomprobante);
    
    
    $args = array ( // parametro que se pasaran a la vista
            'form'          => 'mntFactura.html',
            'action'        => 'mntFactura.php?req=6&id_tipocomprobante='.$id_tipocomprobante.'&id_detalle='.$id_detalle,
            'FormTitle'     => 'LABORATORIOS WÖHLER S.A. DE C.V.',
            'id_tipocomprobante'  =>$id_tipocomprobante,
            'tipocomprobante'=> $rec[tipocomprobante],
            'numero_comprobante'   => $rec[numero_comprobante],
            'fecha_comprobante'=> datetosp($rec[fecha_comprobante]),
            'id_cliente'    => $rec[id_cliente], 
            'id_solicitud'    => $rec[id_solicitud], 
            'venta_total'=> datetosp($rec[venta_total]), 
            'condicion_pago'=> $rec[condicion_pago], 
            'iva_retenido'   => $rec[iva_retenido], 
            'id_estadofactura'      => $rec[id_estadofactura], 
            'id_condicionpago'     => $rec[id_condicionpago],
            'descuento'    => $rec[descuento],
            'cantidad'      => $rec_detalle[cantidad], 
            'id_producto'   => $rec_detalle[id_producto],
            'precio_unit'   => $rec_detalle[precio_unit], 
            'subtotal'=> $rec_detalle[subtotal],
            'iva'=> $rec_detalle[iva], 
            'venta_gravada'=> $rec_detalle[venta_gravada],
            'tbl_detalle'   => $tbl_detalle
            );
    
    $vista->get_form($args);
}

elseif ($req == 6) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->update_item($id_detalle,$id_tipocomprobante);
    print "<script>window.location = 'mntFactura.php?req=3&id_tipocomprobante=".$id_tipocomprobante."'</script>";
}

elseif ($req == 7) {//borrar un registro
    $db = new MySQL();
    $model = new Model_form();
    $model->delete_item($id_detalle,$id_tipocomprobante);
    print "<script>window.location = 'mntFactura.php?req=3&id_tipocomprobante=".$id_tipocomprobante."'</script>";
}


?>

