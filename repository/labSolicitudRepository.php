<?php
/**
 * Description of LabSolicitud
 *
 * @author Julio Castillo
 */
class LabSolicitudRepository {
        
    /*
     * CARGAR SOLICITUD A TRAVES DE ID
     */
    function getSolicitud_id ($id_solicitud) {
        $db = new MySQL();
        $sql = "SELECT 
            t01.id as id_solicitud,
            date_format(t01.fecha_solicitud,'%d-%m-%Y') fecha_solicitud, 
            t01.id_paciente, 
            t01.id_medico,
            t03.nombre as medico,
            t01.id_lugarentrega,
            t01.id_servicio,
            concat(t02.nombres,' ', t02.apellidos) paciente,
            t02.edad,
            t02.id_sexo,
            t02.id_empresa,
            t04.nombre as empresa,
            t01.sumas,
            t01.descuento,
            t01.venta_total
                     FROM lab_solicitud t01
                        LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
                        LEFT JOIN ctl_medico t03 ON t03.id = t01.id_medico
                        LEFT JOIN ctl_empresa t04 ON t04.id = t01.id_empresa
                     WHERE t01.id=$id_solicitud";
        return $db->fetch_array($db->consulta($sql));
    }
    
    function getSolicitudDetalle($id_solicitud) {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id as id_detallesolicitud,
                    t02.id as id_pruebaslab,
                    t02.codigo,
                    t02.nombre as prueba,
                    t01.precio as precio_prueba
                FROM lab_detallesolicitud  t01
                LEFT JOIN ctl_pruebaslab t02 ON t02.id = t01.id_pruebaslab
                WHERE t01.id_estadosolicitud = 3 AND t01.id_solicitud = $id_solicitud
                ORDER BY t01.orden, t01.id";
        return $db->consulta($sql);
    }
    
    function getSolicitudDetalle_elemento($id_detallesolicitud) {
        $db = new MySQL();
        $sql = "
            SELECT 
            
t02.id as id_detallesolicitud,
                    t02.id_pruebaslab,
                    t03.id as id_elemento,
                    t03.nombre as nombre_elemento,
                    t01.resultado,
                    t03.estitulo,
                    t03.esantibiograma,
                    t03.escatalogo,
                    t01.intervalo,
                    t01.unidades,
                    t01.fueraderango 
                FROM lab_resultado as t01
                        join lab_detallesolicitud as t02 on t02.id = t01.id_detallesolicitud
                        left join ctl_elemento as t03 on t03.id = t01.id_elemento
                WHERE t01.id_detallesolicitud = $id_detallesolicitud
                ";
        return $db->consulta($sql);
    }
    
        function lab_resultado_cargar_microorganismo($id_detallesolicitud) {
        $db = new MySQL();
            $sql = "
                SELECT 
                    t01.id as id_detallesolicitud,
                    t01.id_microorganismo,
                    t02.microorganismo
                FROM lab_resultado_antibiograma as t01
                    LEFT JOIN ctl_microorganismo as t02 ON t02.id = t01.id_microorganismo
                WHERE t01.id_detallesolicitud = $id_detallesolicitud 
                GROUP BY t01.id_microorganismo
                ";
//        }
        return $db->fetch_array($db->consulta($sql));
    }

        function lab_resultado_cargar_antibiograma($id_detallesolicitud) {
        $db = new MySQL();
            $sql = "
                SELECT 
                    t01.id_detallesolicitud, 
                    t01.id_microorganismo, 
                    t01.id_antibiotico, 
                    t02.ANTIBIOTIC_ES,
                    t01.lectura, 
                    t01.categoria
                FROM lab_resultado_antibiograma as t01
                    LEFT JOIN ctl_antibiotico as t02 ON t02.id = t01.id_antibiotico
                WHERE t01.id_detallesolicitud = $id_detallesolicitud                
                ";
//        }
        return $db->consulta($sql);
    }

}
