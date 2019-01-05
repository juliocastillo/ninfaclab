<?php
/**
 * Description of facFactura
 *
 * @author Julio Castillo
 */
class FacFacturaRepository {
        
    /*
     * CARGAR SOLICITUD A TRAVES DE ID
     */
    function getFactura_id ($id_factura) {
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
                    t03.nombre as municipio,
                    t04.nombre as departamento,
                    t02.nrc,
                    t02.giro,
                    id_condicionpago,
                    t05.nombre as condicionpago,
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
                LEFT JOIN ctl_condicionpago t05 ON t05.id = t01.id_condicionpago
                WHERE t01.id = $id_factura";
        return $db->fetch_array($db->consulta($sql));
    }
    
    function getFacturaDetalle_id($id_factura) {
        $db = new MySQL();
        $sql = "SELECT 
                1 as cantidad,
                t01.descripcion as descripcion, 
                t01.precio
            FROM fac_detallefactura t01
            WHERE t01.id_factura = $id_factura";
        return $db->consulta($sql);
    }
    
}
