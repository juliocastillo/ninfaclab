<!--Clase para registrar consultas-->
<!--creado por Julio Castillo-->
<?php

class Model {

    public function __construct() {
        
    }

    function busqueda_producto($producto) {
        $db = new MySQL();
        $sql = "
            SELECT 
                id, 
                codigo, 
                id_grupo, 
                nombre, 
                presentacion, 
                CONCAT(nombre,' ',presentacion) AS nombre_producto,
                precio_cls_iva, 
                precio_clc_iva, 
                precio_pc_iva, 
                vineta 
            FROM producto WHERE nombre LIKE '%$producto%'
        ";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function busqueda_producto_id($id_producto) {
        $db = new MySQL();
        $sql = "
            SELECT 
                id, 
                codigo, 
                id_grupo, 
                nombre, 
                presentacion, 
                CONCAT(nombre,' ',presentacion) AS nombre_producto,
                precio_cls_iva, 
                precio_clc_iva, 
                precio_pc_iva, 
                vineta,
                precio_costo
            FROM producto WHERE id='$id_producto%'
        ";
        /*
         * devuelve el arreglo
         */

        return $db->fetch_array($db->consulta($sql));
    }

    function busqueda_cliente($cliente) {
        $db = new MySQL();
        $sql = "
            SELECT 
                id, 
                codigo, 
                nrc, 
                nombre, 
                nombre_comercial, 
                CONCAT(nombre,' ',nombre_comercial) AS nombre_cliente_comercial,
                giro, 
                nit, 
                dir, 
                dir2, 
                tel, 
                dep, 
                mun, 
                venta_a_cta, 
                iva_retenido
            FROM clientes WHERE nombre LIKE '%$cliente%'
        ";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function get_lista_entrada($finicio, $ffin) {
        $db = new MySQL();
        $sql = "
            SELECT 
                e.id,
                t.tipo_entrada,
                e.n_entrada,
                DATE_FORMAT(e.fecha_entrada,'%d/%m/%Y') fecha_entrada,
                e.id_producto,
                p.nombre AS nombre_producto,
                e.lote, 
                p.presentacion, 
                e.cantidad, 
                e.comentario 
            FROM  
            entrada_producto_terminado e
            LEFT JOIN tipo_entrada t ON e.tipo_entrada = t.id
            LEFT JOIN producto p ON e.id_producto = p.id
            
            WHERE fecha_entrada >= '$finicio' AND fecha_entrada <= '$ffin'";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function get_lista_factura($finicio, $ffin, $n_documento, $tipo_documento = '') {
        $db = new MySQL();
        if ($finicio != "" && $ffin != "") {
            $cadenawhere = "e.fecha_documento>='$finicio' AND e.fecha_documento<='$ffin'";
        } else {
            $cadenawhere = "e.numero_comprobante = '$n_documento'";
        }
        if ($tipo_documento != "") {
            $cadenawhere .= ' AND e.tipo_documento=' . $tipo_documento;
        }

        $sql = "
            SELECT 
                t01.id as id_factura,
                DATE_FORMAT(t01.fecha_comprobante,'%d/%m/%Y %h:%m:%s') fecha,
                t01.nombre,
                t01.numero_comprobante as numero_comprobante,
                t02.nombre as tipo_comprobante,
                t01.id_estadofactura
            FROM  fac_factura t01
                LEFT JOIN ctl_tipocomprobante t02 ON t02.id = t01.id_tipocomprobante
            ORDER BY t01.fecha_comprobante ASC
            ";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function get_lista_solicitud($id_solicitud) {
        $db = new MySQL();
        $sql = "
            SELECT 
                t01.id as id_solicitud,
                DATE_FORMAT(t01.fecha_solicitud,'%d/%m/%Y %h:%m:%s') fecha_solicitud,
                CONCAT(t02.nombres, ' ',t02.apellidos) as nombre_paciente,
                t02.id_sexo,
                t02.edad
            FROM  lab_solicitud t01
            LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
            WHERE t01.id = $id_solicitud
            ORDER BY t01.fecha_solicitud ASC
            ";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    
    function get_grupo_edad($edad) {
        $edad_dias = $edad * 365;
        $db = new MySQL();
        $sql = "
            SELECT id as id_grupoedad
            FROM ctl_grupoedad 
            WHERE $edad_dias >= edad_minima_dias and  $edad_dias <= edad_maxima_dias
            ";
        /*
         * devuelve el arreglo
         */

        return $db->fetch_array($db->consulta($sql));
    }
    
    
    function get_lista_detallesolicitud($id_solicitud) {
        $db = new MySQL();
        $sql = "
            SELECT 
                t01.id as id_solicitud,
                t03.id as id_detallesolicitud,
                DATE_FORMAT(t01.fecha_solicitud,'%d/%m/%Y %h:%m:%s') fecha_solicitud,
                CONCAT(t02.nombres, ' ',t02.apellidos) as nombre_paciente,
                t03.id_pruebaslab,
                t04.nombre as pruebalab,
                t03.id_estadosolicitud
            FROM lab_solicitud t01
            LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
            LEFT JOIN lab_detallesolicitud t03 ON t03.id_solicitud = t01.id
            LEFT JOIN ctl_pruebaslab t04 ON t04.id = t03.id_pruebaslab
            WHERE t01.id = $id_solicitud
            ORDER BY t01.fecha_solicitud ASC
            ";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    // obtener un listado general de productos con sus respectivos saldos
    function get_lista_inventario($finicio, $ffin) {
        $db = new MySQL();
        $sql = "SELECT
                  p.id,
                  p.nombre,
                  IF(pts.cant_entrada is not null,pts.cant_entrada,0) AS cant_entrada_ant,
                  IF(fas.cant_salida is not null,fas.cant_salida,0) AS cant_salida_ant,
                  IF(pts.cant_entrada is not null,pts.cant_entrada,0)-IF(fas.cant_salida is not null,fas.cant_salida,0) AS saldo_anterior,
                  IF(pt.cant_entrada is not null, pt.cant_entrada,0) AS cant_entrada,
                  IF(fa.cant_salida is not null,fa.cant_salida,0) AS cant_salida,
                  IF(pts.cant_entrada is not null,pts.cant_entrada,0)-IF(fas.cant_salida is not null,fas.cant_salida,0)+
                    IF(pt.cant_entrada is not null, pt.cant_entrada,0) - IF(fa.cant_salida is not null,fa.cant_salida,0) AS saldo_actual

                FROM producto p
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_entrada
                            FROM entrada_producto_terminado
                            WHERE fecha_entrada >= '$finicio' AND fecha_entrada <= '$ffin' GROUP BY id_producto) pt ON pt.id_producto = p.id
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_salida
                            FROM facturas_detalle fd, facturacion f
                            WHERE fd.id_documento = f.id AND
                                  fecha_documento >= '$finicio' AND fecha_documento <= '$ffin' AND fd.estado = 'A' GROUP BY fd.id_producto) fa ON fa.id_producto = p.id
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_entrada
                            FROM entrada_producto_terminado
                            WHERE fecha_entrada < '$finicio' GROUP BY id_producto) pts ON pts.id_producto = p.id
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_salida
                            FROM facturas_detalle fd, facturacion f
                            WHERE fd.id_documento = f.id AND
                                  fecha_documento < '$finicio' AND fd.estado = 'A' GROUP BY fd.id_producto) fas ON fas.id_producto = p.id
                ORDER BY p.id_grupo,p.nombre";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    //obtiene el detalle de movimientos por producto
    function get_lista_kardex($id_producto, $finicio, $ffin) {
        $db = new MySQL();
        $sql = "
                SELECT
                    *
                FROM
                    (SELECT
                      pt.fecha_entrada as fecha,
                      pt.tipo_entrada AS tipo_documento,
                      CONCAT(pt.n_entrada) n_documento,
                      CONCAT('LOTE:',pt.lote) as proveedor,
                      p.id AS producto,
                      'E' AS tipo,
                      IF(pt.cant_entrada is not null, pt.cant_entrada,0) AS cant_entrada,
                      pt.precio_unit AS precio_entrada,
                      0 AS cant_salida,
                      0 AS precio_salida
                     FROM producto p
                       LEFT JOIN (SELECT t.tipo_entrada,e.n_entrada, e.fecha_entrada, e.lote, e.id_producto,e.cantidad AS cant_entrada, e.precio_unit
                                   FROM entrada_producto_terminado e
                                        LEFT JOIN tipo_entrada t ON t.id = e.tipo_entrada
                                   WHERE e.id_producto = '$id_producto' AND 
                                        fecha_entrada >= '$finicio' AND fecha_entrada <= '$ffin') pt ON pt.id_producto = p.id
                    WHERE p.id = '$id_producto'
                    UNION
                    SELECT
                      fa.fecha_documento AS fecha,
                      fa.tipo_documento,
                      fa.n_documento,
                      CONCAT(fa.cliente,'<br>',fa.nombre_comercial) AS proveedor,
                      p.id AS producto,
                      'S' AS tipo,
                      0 AS cant_entrada,
                      0 AS precio_entrada,
                      IF(fa.cant_salida is not null,fa.cant_salida,0) AS cant_salida,
                      fa.precio_unit AS precio_salida
                    FROM producto p
                      LEFT JOIN (SELECT t.tipo_documento,f.n_documento,f.fecha_documento,f.id_cliente,c.nombre AS cliente,c.nombre_comercial,fd.id_producto,fd.cantidad AS cant_salida, fd.precio_unit
                                  FROM facturas_detalle fd,
                                       facturacion f
                                       LEFT JOIN clientes c ON c.id = f.id_cliente
                                       LEFT JOIN tipo_documento t ON t.id = f.tipo_documento
                                  WHERE fd.id_producto = '$id_producto' AND fd.id_documento = f.id AND
                                        fecha_documento >= '$finicio' AND fecha_documento <= '$ffin'  AND fd.estado = 'A') fa ON fa.id_producto = p.id
                    WHERE p.id = '$id_producto') a
                WHERE
                    a.fecha is not null
                ORDER BY a.fecha ASC, a.tipo ASC
                ";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function get_existencias() {
        $db = new MySQL();
        $sql = "SELECT
                  p.id,
                  p.nombre,
                  IF(pt.cant_entrada is not null, pt.cant_entrada,0) AS cant_entrada,
                  IF(fa.cant_salida is not null,fa.cant_salida,0) AS cant_salida,
                  IF(pt.cant_entrada is not null, pt.cant_entrada,0) - IF(fa.cant_salida is not null,fa.cant_salida,0) AS saldo_actual

                FROM producto p
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_entrada
                            FROM entrada_producto_terminado GROUP BY id_producto) pt ON pt.id_producto = p.id
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_salida
                            FROM facturas_detalle fd, facturacion f
                            WHERE fd.id_documento = f.id GROUP BY fd.id_producto) fa ON fa.id_producto = p.id
                ORDER BY p.id_grupo,p.nombre";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function get_buscar_existencias($producto) {
        $db = new MySQL();
        $sql = "SELECT
                  p.id,
                  p.nombre AS nombre_producto,
                  IF(pt.cant_entrada is not null, pt.cant_entrada,0) AS cant_entrada,
                  IF(fa.cant_salida is not null,fa.cant_salida,0) AS cant_salida,
                  IF(pt.cant_entrada is not null, pt.cant_entrada,0) - IF(fa.cant_salida is not null,fa.cant_salida,0) AS saldo_actual

                FROM producto p
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_entrada
                            FROM entrada_producto_terminado GROUP BY id_producto) pt ON pt.id_producto = p.id
                  LEFT JOIN (SELECT id_producto,SUM(cantidad) AS cant_salida
                            FROM facturas_detalle fd, facturacion f
                            WHERE fd.id_documento = f.id GROUP BY fd.id_producto) fa ON fa.id_producto = p.id
                WHERE p.nombre LIKE '%$producto%'
                ORDER BY p.id_grupo,p.nombre";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function cambiar_estado_facturacin($id, $estado) {
        $db = new MySQL();
        $sql = "UPDATE facturacion SET estado='$estado' WHERE id = '$id'";
        $db->consulta($sql);
        //actualizar detale
        $sql = "UPDATE facturas_detalle SET estado='$estado' WHERE id_documento = '$id'";
        $db->consulta($sql);
    }

    /*
     * VENDEDOR CLIENTE PARA OBTENER CUENTAS POR COBRAR
     * 
     */

    function get_lista_vendedor_cliente($id_vendedor) {
        $db = new MySQL();
        $sql = "
            SELECT 
                v.id, 
                c.codigo, 
                c.nrc, 
                c.nombre, 
                c.nombre_comercial, 
                CONCAT(c.nombre,' ',c.nombre_comercial) AS nombre_cliente_comercial,
                c.giro, 
                c.nit, 
                c.dir, 
                c.dir2, 
                c.tel, 
                c.dep, 
                c.mun, 
                c.venta_a_cta, 
                c.iva_retenido
            FROM
                vendedor_cliente v,
                clientes c
            WHERE 
                v.id_cliente = c.id AND
                v.id_vendedor = '$id_vendedor'
            ORDER BY c.nombre, c.nombre_comercial";
        /*
         * devuelve el arreglo
         */

        return $db->consulta($sql);
    }

    function set_vendedor_cliente($id_vendedor, $id_cliente) {
        $db = new MySQL();
        $sql = "
            INSERT INTO
                vendedor_cliente 
                (id_vendedor,
                id_cliente)
            VALUE 
                ('$id_vendedor',
                '$id_cliente')
            ";
        $db->consulta($sql);
    }

    function delete_vendedor_cliente($id) {
        $db = new MySQL();
        $sql = "
            DELETE FROM
                vendedor_cliente 
            WHERE id=$id";
        $db->consulta($sql);
    }

    function get_totales_documento($id_documento) {
        $db = new MySQL();
        $totales = $db->fetch_array($db->consulta("SELECT 
                sum(fd.ventas_gravadas) AS ventas_gravadas,
                sum(fd.ventas_gravadas)*.13 AS iva,
               (sum(fd.ventas_gravadas)+sum(fd.ventas_gravadas)*.13) AS subtotal,
			   IF(sum(fd.ventas_gravadas)>=100,sum(fd.ventas_gravadas)*c.iva_retenido,0) AS iva_retenido,
			   (sum(fd.ventas_gravadas)+sum(fd.ventas_gravadas)*.13)-IF(sum(fd.ventas_gravadas)>=100,sum(fd.ventas_gravadas)*c.iva_retenido,0) AS venta_total
            FROM facturas_detalle fd
				LEFT JOIN facturacion f ON fd.id_documento = f.id
				LEFT JOIN clientes c ON f.id_cliente = c.id
            WHERE fd.id_documento='$id_documento'
            GROUP BY id_documento
            "));
    }

    function get_lista_cobros($id_vendedor, $id_zona, $finicio, $ffin) {
        /*
         * Devuelve las cuentas por cobrar por cliente de un 
         * vededor determinado en un periodo de tiempo
         * solo factura que tienen saldo mayor de cero
         */
        if ($id_zona != "") {
            $cadenawere = "c.zona = '$id_zona' AND";
        } else {
            $cadenawere = "";
        }
        $db = new MySQL();
        $sql = ("SELECT DATE_FORMAT(f.fecha_documento,'%d/%m/%Y') fecha_documento,
                    CONCAT(t.tipo_documento,'-',f.n_documento) n_documento,
                    f.id_cliente,
                    UCASE(CONCAT(c.nombre,' ',c.nombre_comercial)) nombre_cliente,
                    IF(f.tipo_documento=1,f.ventas_gravadas - f.monto_cobrado,
                        IF(f.tipo_documento=2,f.venta_total - f.monto_cobrado,0)) saldo_actual                    
                  FROM vendedor_cliente vc, facturacion f, clientes c,
                    tipo_documento t
                  WHERE
                    vc.id_vendedor='$id_vendedor' AND
                    vc.id_cliente = f.id_cliente AND
                    vc.id_cliente = c.id AND
                    f.tipo_documento = t.id AND
                    IF(f.tipo_documento=1,f.ventas_gravadas - f.monto_cobrado,
                        IF(f.tipo_documento=2,f.venta_total - f.monto_cobrado,0)) > 0 AND
                    $cadenawere
                    f.condicion_pago = 2 AND
                    DATEDIFF('$ffin',f.fecha_documento) >= 30 
                  ORDER BY nombre, f.fecha_documento, n_documento
            ");
        return $db->consulta($sql);
    }

    function get_total_cxc($id_documento) {
        $db = new MySQL();
        $sql = "SELECT
            sum(r.monto) AS monto_cobrado
            FROM recibos_cxc r
            WHERE r.id_documento='$id_documento'
            GROUP BY id_documento
            ";
        return $db->fetch_array($db->consulta($sql));
    }

    function update_total_cobrado($id_documento) {
        $db = new MySQL();

        $r = $this->get_total_cxc($id_documento);

        $sql = "UPDATE facturacion SET
            monto_cobrado = '$r[monto_cobrado]'
            WHERE id='$id_documento'
            ";
        $db->consulta($sql);
    }

    function set_recibo_cxc($id_documento, $id_vendedor, $n_recibo, $fecha_recibo, $tipo_pago, $n_cheque, $banco, $monto) {
        $system_date = date("Y-m-d");
        $db = new MySQL();
        $sql = "
            INSERT INTO
                recibos_cxc 
                (id_documento,
                id_vendedor,
                n_recibo,
                fecha_recibo,
                tipo_pago,
                n_cheque,
                banco,
                monto,
                date_add)
            VALUE 
                ('$id_documento',
                '$id_vendedor',
                '$n_recibo',
                '$fecha_recibo',
                '$tipo_pago',
                '$n_cheque',
                '$banco',
                '$monto',
                '$system_date')";
        $db->consulta($sql);


        $this->update_total_cobrado($id_documento);
    }

    function update_recibo_cxc($id_documento, $id_recibo, $id_vendedor, $n_recibo, $fecha_recibo, $tipo_pago, $n_cheque, $banco, $monto) {
        $system_date = date("Y-m-d");
        $db = new MySQL();
        $sql = "
            UPDATE recibos_cxc SET
                id_vendedor='$id_vendedor',
                n_recibo='$n_recibo',
                fecha_recibo='$fecha_recibo',
                tipo_pago='$tipo_pago',
                n_cheque='$n_cheque',
                banco='$banco',
                monto='$monto',
                date_update='$system_date'
            WHERE id='$id_recibo'";
        $db->consulta($sql);

        $this->update_total_cobrado($id_documento);
    }

    function get_recibo_cobro($id_documento) {
        /*
         * Devuelve los recibos cobrados
         */

        $db = new MySQL();
        $sql = ("
            SELECT
                r.id,
                r.n_recibo,
                DATE_FORMAT(r.fecha_recibo,'%d/%m/%Y') fecha_recibo,
                t.tipo_pago,
                monto,
                id_vendedor
            FROM recibos_cxc r, tipo_pago t 
            WHERE 
            r.tipo_pago = t.id AND
            r.id_documento='$id_documento'
            ORDER BY r.fecha_recibo    
                ");
        return $db->consulta($sql);
    }

    function get_recibo_cobro_id($id_recibo) {
        $db = new MySQL();
        $sql = "
            SELECT 
                r.id_documento,
                r.id_vendedor,
                r.n_recibo,
                DATE_FORMAT(r.fecha_recibo,'%d/%m/%Y') fecha_recibo,
                r.tipo_pago,
                r.n_cheque,
                r.banco,
                r.monto
            FROM
                recibos_cxc r
            WHERE id='$id_recibo'";
        return $db->fetch_array($db->consulta($sql));
    }

    function delete_recibo_cobro($id_documento, $id_recibo) {
        $db = new MySQL();
        $sql = "
            DELETE FROM recibos_cxc
            WHERE id='$id_recibo'";
        $db->consulta($sql);

        $this->update_total_cobrado($id_documento);
    }

    function get_informe_cxc($finicio, $ffin, $id_zona = "") {
        /*
         * Devuelve las cuentas por cobrar por cliente de un 
         * vededor determinado en un periodo de tiempo
         * solo factura que tienen saldo mayor de cero
         */
        if ($id_zona != "") {
            $cadenawere = " c.zona = '$id_zona' AND";
        } else {
            $cadenawere = "";
        }
        $db = new MySQL();
        $sql = ("SELECT DATE_FORMAT(f.fecha_documento,'%d/%m/%Y') fecha_documento,
                    CONCAT(t.tipo_documento,'-',f.n_documento) n_documento,
                    c.codigo,
                    UCASE(CONCAT(c.nombre,' ',c.nombre_comercial)) nombre_cliente,
                    IF(f.tipo_documento=1, f.ventas_gravadas,
                      IF(f.tipo_documento=2, f.venta_total,0)) venta_total,
                    f.iva_retenido,
                    IF(f.tipo_documento=1, f.ventas_gravadas*0.01,
                      IF(f.tipo_documento=2, f.venta_total*0.01,0)) '1%',
                    GROUP_CONCAT(DATE_FORMAT(rcxc.fecha_recibo,'%d/%m/%Y') SEPARATOR ', ') fechas,
                    GROUP_CONCAT(rcxc.n_recibo SEPARATOR ', ') recibos,
                    SUM(rcxc.monto) total
                  FROM facturacion f, tipo_documento t, clientes c,
                    recibos_cxc rcxc
                  WHERE
                    f.tipo_documento = t.id AND
                    f.id_cliente = c.id AND
                    f.id = rcxc.id_documento AND
                    f.condicion_pago = 2 AND
                    $cadenawere
                    IF(f.tipo_documento=1,f.ventas_gravadas - f.monto_cobrado,
                      IF(f.tipo_documento=2,f.venta_total - f.monto_cobrado,0)) > 0 AND
                    f.fecha_documento >= '$finicio' AND f.fecha_documento <= '$ffin'
                  GROUP BY f.id
                  ORDER BY f.fecha_documento, rcxc.fecha_recibo");
        return $db->consulta($sql);
    }

    function login($usuario_login, $usuario_password) {
        $db = new MySQL();
        $sql = "
                SELECT t01.*,t02.nombre as laboratorio, t02.iva FROM 
                    ctl_usuario t01
                    LEFT JOIN laboratorio t02 ON t02.id = t01.id_laboratorio
                WHERE
                    usuario_login = '$usuario_login' AND
                    usuario_password = '$usuario_password'
                ";
        return $db->fetch_array($db->consulta($sql));
    }

    function lab_resultado_cargar_elementos($id_detallesolicitud, $id_pruebaslab, $id_sexo, $id_grupoedad) {
        $db = new MySQL();
            $sql = "
                SELECT 
                    t03.id as id_detallesolicitud,
                    t03.id_pruebaslab,
                    t01.id as id_elemento,
                    t01.nombre,
                    t04.resultado,
                    if(t04.resultado is null, CONCAT(t01.min,' - ', t01.max), t04.intervalo) as intervalo,
                    if(t04.resultado is null,t01.unidades, t04.unidades) as unidades
                FROM ctl_elemento as t01
          		left join ctl_pruebaslab as t02 on t02.id = t01.id_pruebaslab
                left join lab_detallesolicitud as t03 on t03.id_pruebaslab = t02.id
                left join lab_resultado as t04 on t04.id_elemento = t01.id
                WHERE (t01.id_sexo = 0 OR t01.id_sexo = $id_sexo )
                        AND (t01.id_grupoedad = 0 OR t01.id_grupoedad = $id_grupoedad )
                        AND t03.id = $id_detallesolicitud AND t03.id_pruebaslab = $id_pruebaslab                
                ";
//        }
        return $db->consulta($sql);
    }

    function lab_resultado_guardar($id_detallesolicitud, $id_elemento, $resultado, $intervalo, $unidades, $userID) {
        $db = new MySQL();
        $sql = "
                INSERT INTO 
                    lab_resultado(
                        id_detallesolicitud, 
                        fecha_resultado, 
                        id_elemento, 
                        resultado, 
                        intervalo, 
                        unidades,
                        date_add, 
                        user_add
                        ) 
                    VALUES 
                        (
                        '$id_detallesolicitud',
                        NOW(),
                        '$id_elemento',
                        '$resultado',
                        '$intervalo',
                        '$unidades',
                        NOW(),
                        $userID
                        )
                        ON DUPLICATE KEY UPDATE 
                            resultado='$resultado',
                            intervalo='$intervalo',
                            unidades='$unidades',
                            date_mod=NOW(),
                            user_mod=$userID
                ";
        return $db->consulta($sql);
    }

    function lab_resultado_cambiarestado($id_detallesolicitud) {
        $db = new MySQL();
        $sql = "
                UPDATE lab_detallesolicitud SET
                    id_estadosolicitud = 3
                WHERE
                    id = '$id_detallesolicitud'
            ";
        $db->consulta($sql);
        return true;
    }

}
