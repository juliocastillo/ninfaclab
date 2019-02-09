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
?>
<html>
    <head>
        <!-- para graficos -->
        <script src="pivottable/plotly-basic-latest.min.js"></script>
        <!-- external libs from cdnjs -->
        <script type="text/javascript" src="pivottable/jquery.min.js"></script>
        <script type="text/javascript" src="pivottable/jquery-ui.min.js"></script>

        <!-- PivotTable.js libs from ../dist -->
        <link rel="stylesheet" type="text/css" href="pivottable/dist/pivot.css">
        <script type="text/javascript" src="pivottable/dist/pivot.js"></script>

        <!-- para graficos -->
        <script type="text/javascript" src="pivottable/dist/plotly_renderers.js"></script>

        <style>
            body {font-family: Verdana;}
        </style>

        <!-- optional: mobile support with jqueryui-touch-punch -->
        <script type="text/javascript" src="pivottable/jquery.ui.touch-punch.min.js"></script>

        <!-- for examples only! script to show code to user -->
        <script type="text/javascript" src="pivottable/show_code.js"></script>
    </head>
    <body>
        <script type="text/javascript">
            // This example loads the "Canadian Parliament 2012" dataset
            // and adds derived attributes: "Age Bin" and "Gender Imbalance".

            $(function () {
                var derivers = $.pivotUtilities.derivers;
                // activar los otros graficos ademas de la tabla basica
                var renderers = $.extend($.pivotUtilities.renderers,
                        $.pivotUtilities.plotly_renderers);
                $.getJSON("getjson.php", function (mps) {
                    $("#output").pivotUI(mps, {
                        // columnas distribuidas por default
                        rows: ["departamento"],
                        cols: ["sexo"],
                        // seleccionar grafico por default o tabla
                        rendererName: "Horizontal Stacked Bar Chart",
                        // ordenar columnas
                        rowOrder: "value_a_to_z", colOrder: "value_z_to_a",
                        // definir los cambios en la configuraciones inicial de datatable
                    });
                });
            });
        </script>
        <div id="output" style="margin: 30px;"></div>

    </body>
</html>