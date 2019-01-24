<?php

class Htmltipo_entrada {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, tipo_entrada as nombre
                        FROM tipo_entrada";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlproducto {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, CONCAT(nombre,' (',presentacion,')') AS nombre
                        FROM producto";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlproducto_existencia {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT
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
                WHERE (IF(pt.cant_entrada is not null, pt.cant_entrada,0) - IF(fa.cant_salida is not null,fa.cant_salida,0)) >0
                ORDER BY p.id_grupo,p.nombre";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlpresentacion {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, presentacion AS nombre
                        FROM producto_presentacion";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlTipocomprobante {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_tipocomprobante";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlCondicionpago {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre AS nombre
                        FROM ctl_condicionpago";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlEmpresa {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, CONCAT(nombre,' (',nombre_comercial,')') AS nombre
                        FROM ctl_empresa ORDER BY nombre,nombre_comercial";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlPaciente {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, CONCAT(nombres,' ',apellidos) AS nombre
                        FROM mnt_paciente ORDER BY nombres,apellidos";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

    function llenarlista_id($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, CONCAT(nombres,' ',apellidos) AS nombre
                        FROM mnt_paciente WHERE id = $sel";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlSexo {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT * FROM ctl_sexo";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlGrupoedad {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT * FROM ctl_grupoedad";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlvendedor {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre AS nombre
                        FROM vendedor";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlzonas {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, zona AS nombre
                        FROM zonas";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmltipo_pago {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, tipo_pago AS nombre
                        FROM tipo_pago";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlbancos {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, banco AS nombre
                        FROM bancos";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class Htmlestado {

    function llenarlista($sel) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, estado AS nombre
                        FROM estado";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlDepartamento {//DEPARTAMENTO

    function llenarlista($sel = 0, $depto = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id as id, departamento as nombre FROM departamento";
        if ($depto > 0)
            $sqlcommand = "SELECT departamentoId as id, UPPER(departamento) as nombre FROM departamento WHERE departamentoId='$depto'";

        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_decode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_decode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlMunicipio {//Municipio

    function llenarlista($sel = 0) {
        $db = new MySQL();
        /*
         * seleccionar deacuerdo a parametros enviados
         */

        $sqlcommand = "SELECT m.id, CONCAT(m.nombre, '(', d.nombre, ')') nombre FROM ctl_municipio m, ctl_departamento d WHERE
                        m.id_departamento = d.id
                 ";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlZona {//Municipio

    function llenarlista($sel = 0) {
        $db = new MySQL();
        /*
         * seleccionar deacuerdo a parametros enviados
         */

        $sqlcommand = "SELECT id as id, nombre FROM ctl_zona";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlTipoempresa {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_tipoempresa";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlCargo {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_cargo";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlArealab {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_arealab";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlFormatosalida {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_formatosalida";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlPruebaslab {
    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, CONCAT(codigo,' ', nombre) nombre
                        FROM ctl_pruebaslab";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

    function llenarlista_sinprecios($tipoempresa = 1, $sel=0) {
        $db = new MySQL();
        $sqlcommand = "
                        select prueba.id, CONCAT(prueba.codigo,' ', prueba.nombre) nombre, precioempresa.precio
                        from ctl_pruebaslab prueba
                                left join (select precio.id_tipoempresa, precio.precio, precio.id_pruebaslab 
                                                from mnt_precio_tipoempresa_pruebaslab precio 
                                                where precio.id_tipoempresa = $tipoempresa) as precioempresa 
                                on precioempresa.id_pruebaslab = prueba.id
                        where precioempresa.precio IS NULL
";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlMedico {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_medico";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlPerfil {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_perfil";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlProcedencia {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_procedencia";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlServicio {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_servicio";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlLugarentrega {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_lugarentrega";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}

class HtmlRol {

    function llenarlista($sel = 0) {
        $db = new MySQL();
        $sqlcommand = "SELECT id, nombre
                        FROM ctl_rol";
        $result = $db->consulta($sqlcommand);
        $html = "";
        while ($row = $db->fetch_array($result)) {
            /*
             * seleccionar el registro por default enviado
             */
            if ($row['id'] == $sel) {
                $html .= "<option value='" . $row['id'] . "' selected>" . utf8_encode($row['nombre']) . "</option>";
            } else {
                $html .= "<option value='" . $row['id'] . "'>" . utf8_encode($row['nombre']) . "</option>";
            }
        }
        return $html;
    }

}
