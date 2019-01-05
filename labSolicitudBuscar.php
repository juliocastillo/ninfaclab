<?php
session_start();
$_SESSION['login'];
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
include ("./model.php");
datevalidsp();
$model = new Model();
$db = new MySQL();
extract($_GET);
?>

<html>
    <head>
        <link href="public/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <!--calendario-->
        <script src="js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <link href="css/select2.min.css" rel="stylesheet" type="text/css"/>
        <script src="js/select2.min.js" type="text/javascript"></script>
<!--        <script type="text/javascript" language="javascript">
            function onBackClick(id) {
                opener.window.location = 'labSolicitud.php?req=3&id_solicitud=' + id;
                window.close();
            }
        </script>        -->
    </head>
    <body>
        <div align="center">
            <form method="GET" action="">

                <table style="width: 60%">
                    <tr>
                        <td colspan="4" align="center" style="border: 1px gainsboro outset; background: skyblue">
                            <font class="FormHeaderFONT">Buscar solicitud</font>
                        </td>

                    </tr>
                    <tr>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <label for="fechaini" class="control-label">Fecha desde:</label>
                        </td>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <input type="text" id="fechaini" name="fechaini" value="<?php echo $fechaini; ?>" autofocus  onBlur="formatofecha(this.id, this.value); date_system_valid(this.id)"  onkeyup="mascara(this, '/', patron, true)">
                            <input type="button" value="..." id="fechaini_btn">
                        </td>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <label for="fechafin" class="control-label">Fecha hasta:</label>
                        </td>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <input type="text" id="fechafin" name="fechafin" value="<?php echo $fechafin; ?>"  onBlur="formatofecha(this.id, this.value); date_system_valid(this.id)"  onkeyup="mascara(this, '/', patron, true)">
                            <input type="button" value="..." id="fechafin_btn">
                        </td>
                    </tr>
                    <tr>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <label for="nombres" class="control-label">Nombres:</label>
                        </td>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <input type="text" id="nombres" name="nombres" value="<?php echo $nombres; ?>"  size="30px">
                        </td>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <label for="apellidos" class="control-label">Apellidos:</label>
                        </td>
                        <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <input type="text" id="apellidos" name="apellidos" value="<?php echo $apellidos; ?>" size="30px">
                        </td>
                    </tr>
                </table>
                <center class="">
                    <input type="submit" name="action" value="Buscar" class="btn btn-primary">
                </center>
            </form>


            <?php
            extract($GET);
            if ($action != "") {
                $db = new MySQL();
                if ($nombres != "") {
                    $where = " AND t02.nombres LIKE '%$nombres%'";
                }
                if ($apellidos != "") {
                    $where = " AND t02.apellidos LIKE '%$apellidos%'";
                }
                if ($fechaini != "" && $fechafin != "") {
                    $fechaini = datetosql($fechaini);
                    $fechafin = datetosql($fechafin);
                    $where = " AND t01.fecha_solicitud >= '$fechaini' AND t01.fecha_solicitud <= '$fechafin'";
                }
                if ($where) {
                    $sqlcommand = "SELECT t01.id, 
                        date_format(t01.fecha_solicitud,'%d-%m-%Y %H:%i:%s') fecha_solicitud, 
                        t02.nombres, 
                        t02.apellidos,
                        t03.nombre as estado,
                        t04.numero_comprobante as numero_factura,
                        t04.id as id_factura
                     FROM lab_solicitud t01
                        LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
                        LEFT JOIN ctl_estadosolicitud t03 ON t03.id = t01.id_estadosolicitud
                        LEFT JOIN fac_factura t04 ON t04.id_solicitud = t01.id
                     WHERE t02.activo = 1 $where";
                    $result = $db->consulta($sqlcommand);
                    ?>
                    <table style="width: 80%; border-collapse:collapse" border="1" id="tblresult">
                        <?php
                        if ($db->num_rows($result) == 0) {
                            echo "<tr><td>No no se encontro paciente</tr></td>";
                        } else {
                            ?>
                            <th width="200">FECHA SOLICITUD</th>
                            <th>PACIENTE</th>
                            <th>ESTADO</th>
                            <th>FACTURA</th>
                            <th></th>
                            <?php
                            while ($r = $db->fetch_array($result)) {
                                echo "<tr><td>" . $r['fecha_solicitud'] . "</td>" .
                                "<td>" . $r['nombres'] . ' ' . $r['apellidos'] . "</td>" .
                                "<td>" . $r['estado'] . "</td>" .
                                "<td><a href='facFactura.php?req=3&id_factura=" . $r['id_factura'] . "'>".$r['numero_factura']."</a></td>".
                                "<td><a href='labSolicitud.php?req=3&id_solicitud=" . $r['id'] . "'>Modificar solicitud</a></td></tr>";
                            }
                        }
                    }
                }
                ?>
            </table>
        </div>
    </body>
    <script type="text/javascript">
        //<![CDATA[
        Calendar.setup({
            inputField: "fechaini",
            trigger: "fechaini_btn",
            onSelect: function () {
                this.hide()
            },
            showTime: 12,
            weekNumbers: true,
            //dateFormat : "%Y-%m-%d %I:%M %p"
            dateFormat: "%d/%m/%Y",
            align: ""
        });

        //<![CDATA[
        Calendar.setup({
            inputField: "fechafin",
            trigger: "fechafin_btn",
            onSelect: function () {
                this.hide()
            },
            showTime: 12,
            weekNumbers: true,
            //dateFormat : "%Y-%m-%d %I:%M %p"
            dateFormat: "%d/%m/%Y",
            align: ""
        });
    </script>
</html>
