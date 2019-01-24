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
        <title></title>
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
                            <font class="ColumnFONT"><b>ID solicitud</b></font>
                            <input type="text" name="id_solicitud" id="id_solicitud" size="10" maxlength="10" tabindex="6" value="<?php echo $id_solicitud; ?>"/>
                        </td>
                        <td>
                            <input type="submit" value="Buscar solicitud" tabindex="10" class="btn btn-success" />
                            <a href="#" onclick = "labResultadoPdf(document.getElementById('id_solicitud').value)" class="btn btn-primary" />Imprimir</a>

                        </td>
                    </tr>
                </table>
            </div>
        </form>
        <?php
        if (isset($id_solicitud)) { //si hay fecha de inicio
            $solicitud = $model->get_lista_solicitud($id_solicitud);
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
                    $id_solicitud   = $row['id_solicitud'];
                    $id_sexo        = $row['id_sexo'];
                    $id_grupoedad   = $model->get_grupo_edad($row['edad']);
                    $pruebas        = $model->get_lista_detallesolicitud($id_solicitud);
                    while ($pr = $db->fetch_array($pruebas)) {
                        $j++;
                            ?>
                                <ul>
                                    <li id="child_node_<?php echo $j; ?>" onclick="labResultadoCargar(<?php echo $pr['id_detallesolicitud'] . ',' . $pr['id_pruebaslab'] . ',' . $id_sexo . ',' . $id_grupoedad['id_grupoedad']; ?>)">
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

            function labResultadoPdf(id_solicitud) {
                miVentana = window.open("labResultadoPdf.php?id_solicitud=" + id_solicitud, "Imprimir solicitud", "fullscreen='yes'");
            }

            function addElementos(id_prueba) {
                miVentana = window.open("adminElemento.php?id_prueba=" + id_prueba, "nombrePop-Up", "width=1024,height=600, top=25,left=25");
            }

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

            function labResultadoCargar(id_detalle, id_prueba, id_sexo, id_grupoedad) {
                $.ajax({
                    url: 'labResultadoCargar.php',
                    data: {id_detallesolicitud: id_detalle, id_pruebaslab: id_prueba, sexo: id_sexo, grupoedad: id_grupoedad},
                    async: true,
                    success: function (result) {
                        $("#result").html(result);
                    }});
            }
        </script>

</html>
