<?php
session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}
//abrir conexion
include ("conexion.php");
?>

<html>
    <head>
        <link href="public/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" language="javascript">
            function onBackClick(id) {
                opener.window.location = 'labSolicitud.php?req=3&id_solicitud=' + id;
                window.close();
            }
        </script>        
    </head>
    <body>
        <form method="GET" action="">
            <table style="width: 100%">
                <tr>
                    <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                        <label for="nombres" class="control-label">Nombres:</label>
                        <input type="text" id="nombres" name="nombres" value="" autofocus>
                    </td>
                    <td style="border-bottom: 1px gray solid; border-top: 1px gray solid; background: gainsboro">
                        <label for="apellidos" class="control-label">Apellidos:</label>
                        <input type="text" id="apellidos" name="apellidos" value="">
                    </td>
                </tr>
            </table><br>
            <center>
                <input type="submit" value="Buscar paciente" class="btn btn-primary" onclick="getForm(document.frm.id_paciente.value)">
            </center>
        </form>


        <?php
        extract($GET);
        if ($nombres != "" || $apellidos != "") {
            $db = new MySQL();
            if ($nombres != "") {
                $where = " AND t02.nombres LIKE '%$nombres%'";
            }
            if ($apellidos != "") {
                $where = " AND t02.apellidos LIKE '%$apellidos%'";
            }
            $sqlcommand = "SELECT t01.id, date_format(t01.fecha_solicitud,'%d-%m-%Y %H:%i:%s') fecha_solicitud, t02.nombres, t02.apellidos
                     FROM lab_solicitud t01
                        LEFT JOIN mnt_paciente t02 ON t02.id = t01.id_paciente
                     WHERE t02.activo = 1 $where";
            $result = $db->consulta($sqlcommand);
            ?>
            <table style="width: 100%; border-collapse:collapse" border="1" id="tblresult">
                <?php
                if ($db->num_rows($result) == 0) {
                    echo "<tr><td>No no se encontro paciente</tr></td>";
                }
                echo "<tr><td>FECHA SOLICITUD</td><td>PACIENTE</td></td></tr>";
                while ($r = $db->fetch_array($result)) {
                    echo "<tr><td>" . $r['fecha_solicitud'] . "</td><td>" . $r['nombres'] . ' ' . $r['apellidos'] . "</td>" . "<td><a href='#' onclick='onBackClick(" . $r['id'] . ");'>Seleccione</a></td></tr>";
                }
            }
            ?>
        </table>

    </body>
</html>
