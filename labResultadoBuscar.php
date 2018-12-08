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
        <title>Ninfac</title>
        <!--creado por Julio Castillo, abril de 2013-->
        <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
        <link rel="stylesheet" href="" type="text/css">
        <link href="public/jstree/css/style.min.css" rel="stylesheet" type="text/css"/>
        <script src="public/jstree/js/jquery.min.js" type="text/javascript"></script>
        <script src="public/jstree/js/jstree.min.js" type="text/javascript"></script>
    </head>
    <body class="PageBODY">
        <form action="" method="get" name="frm" style="background-color: white">
            <div style="box-shadow: 5px 0px 5px grey; border: 1px seagreen solid;  border-radius: 1px; width: 75%; position: relative;">
                <div id='frm_errorloc' class='frm_strings' style="background-color:orange; border: 0px; text-align: center"></div>
                <table style="width:100%">
                    <tr>
                        <td colspan="2" align="center" style="border: 1px gainsboro outset; background: skyblue">
                            <font class="FormHeaderFONT">RESULTADOS DE LABORATORIO</font>
                        </td>
                    </tr>
                    <tr >
                        <td>
                            <font class="ColumnFONT"><b>N&uacute;mero de solicitud</b></font>
                            <input type="text" name="n_documento" value="" id="n_documento" size="10" maxlength="10" tabindex="6" />
                            <label>Fecha venta: desde</label>
                            <input type="text" name="finicio" value="" id="finicio" size="10" maxlength="10" tabindex="6" onBlur="formatofecha(this.id, this.value); date_system_valid(this.id)" onkeyup="mascara(this, '/', patron, true)"/></input>
                            <input type="button" value="..." id="finicio_btn" tabindex="7" ></input>
                            <label>Hasta</label> 
                            <input type="text"  name="ffin" value="" id="ffin" size="10" maxlength="10" tabindex="8" onBlur="formatofecha(this.id, this.value); date_system_valid(this.id)" onkeyup="mascara(this, '/', patron, true)" /></input>
                            <input type="button" value="..." id="ffin_btn" tabindex="9" ></input>
                        </td>
                        <td>
                            <input type="submit" value="Ejecutar la consulta" tabindex="10" class="btn btn-success" />

                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <?php
//        if (isset($cambio_estado) && $cambio_estado == 'I') {
//            $model->cambiar_estado_facturacin($id, 'I');
//        } elseif (isset($cambio_estado) && $cambio_estado == 'A') {
//            $model->cambiar_estado_facturacin($id, 'A');
//        }


        if ((isset($finicio) && isset($ffin)) || isset($n_documento)) { //si hay fecha de inicio
            if (isset($finicio))
                $finicio = datetosql($finicio);
            if (isset($ffin))
                $ffin = datetosql($ffin);
            $solicitud = $model->get_lista_solicitud($finicio, $ffin, $n_documento);
        } else {  // cargar las solicitudes recientes sin fecha de inicio
            if (isset($finicio))
                $finicio = date("Y-m-d");
            if (isset($ffin))
                $ffin = date("Y-m-d");
            $solicitud = $model->get_lista_solicitud($finicio, $ffin, 0);
        }
        ?>
        <div id="jstree"  style="box-shadow: 0px 5px 5px grey; overflow:auto; display: inline-block; border-style: solid; border-width: thin; width: 25%; height: 75%;">
            <!-- in this example the tree is populated from inline HTML -->
            <ul>
                <?php
                $i = $j = 0;
                while ($row = $db->fetch_array($solicitud)) {
                    $i++;
                    ?>
                    <li><?php
                        echo $row['nombre_paciente'];
                        $id_solicitud = $row['id_solicitud'];
                        $pruebas = $model->get_lista_detallesolicitud($id_solicitud);
                        while ($pr = $db->fetch_array($pruebas)) {
                            $j++;
                            ?>
                            <ul>
                                <li id="child_node_<?php echo $j; ?>" onclick="labResultadoCargar(<?php echo $pr['id_detallesolicitud'].','.$pr['id_pruebaslab']; ?>)">
                                    <?php echo $pr['pruebalab']; ?>
                                </li>
                            </ul>
                        <?php } ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div>
        <div id="result"  style="box-shadow: 5px 5px 5px grey; display: inline-block; position: absolute; border-style: solid; border-width: thin; width: 50%; height: 75%;">

        </div>
        <script language="Javascript">
            $(function () {
                // 6 create an instance when the DOM is ready
                $('#jstree').jstree();
                // 7 bind to events triggered on the tree
                $('#jstree').on("changed.jstree", function (e, data) {
                    console.log(data.selected);
                });
                // 8 interact with the tree - either way is OK
                $('button').on('click', function () {
                    $('#jstree').jstree(true).select_node('child_node_1');
                    $('#jstree').jstree('select_node', 'child_node_1');
                    $.jstree.reference('#jstree').select_node('child_node_1');
                });
            });

            function labResultadoCargar(id_detalle,id_prueba) {
                $.ajax({
                    url: 'labResultadoCargar.php', 
                    data: { id_detallesolicitud: id_detalle, id_pruebaslab: id_prueba },
                    async: true,
                    success: function (result) {
                        $("#result").html(result);
                    }});
            }

            Calendar.setup({
                inputField: "finicio",
                trigger: "finicio_btn",
                onSelect: function () {
                    this.hide()
                },
                showTime: 12,
                weekNumbers: true,
                //dateFormat : "%Y-%m-%d %I:%M %p"
                dateFormat: "%d/%m/%Y",
                align: ""
            });

            Calendar.setup({
                inputField: "ffin",
                trigger: "ffin_btn",
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
