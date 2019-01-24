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
include ("repository/repReporteRepository.php");
include ("tools.php");
datevalidsp();
$model = new repReporteRepository();
$db = new MySQL();
$cboEmpresa = new HtmlEmpresa();
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
            <form method="GET" action="" name="frm">

                <table style="width: 60%">
                    <tr>
                        <td colspan="4" align="center" style="border: 1px gainsboro outset; background: skyblue">
                            <font class="FormHeaderFONT">Reporte de Examenes realizados</font>
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
                        <td colspan="4" style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                            <label id="lblid_empresa">Buscar empresa: </label>
                            <select id="id_empresa" name="id_empresa"  style="width: 600px;">
                                <option value=""></option>
                                <?php print $cboEmpresa->llenarlista($id_empresa); ?>
                            </select>
                        </td>
                    </tr>

                </table>
                <center class="">
                    <input type="submit" name="action" value="Buscar" class="btn btn-primary">
                    <a href="#" onclick = "labSolicitudPdf()" class="btn btn-primary" />Imprimir</a>
                </center>
            </form>


            <?php
            extract($_GET);
            if (isset($action)) {
                $db = new MySQL();
                $result = $model->get_lista_solicitud($fechaini, $fechafin, $id_empresa);
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
                        <?php
                        while ($r = $db->fetch_array($result)) {
                            echo "<tr><td>" . $r['fecha_solicitud'] . "</td>" .
                            "<td>" . $r['paciente'] . "</td>" .
                            "<td>" . $r['estado'] . "</td>" .
                            "</tr>";
                        }
                    }
                }
                ?>
            </table>
        </div>
    </body>
    <script type="text/javascript">

        function labSolicitudPdf() {
            miVentana = window.open("repExamenesRealizadosPdf.php?fechaini=" + frm.fechaini.value + "&fechafin=" + frm.fechafin.value + "&id_empresa=" + frm.id_empresa.value, "Imprimir solicitud", "fullscreen='yes'");
        }
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
