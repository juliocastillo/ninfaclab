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

    </head>
    <body class="PageBODY" onload="loadPrint();">
        <form action="" method="get" name="frm" style="background-color: white">
            <center>
                <div style="border: 1px seagreen solid;  border-radius: 1px; width: 800px; position: relative;">
                    <div id='frm_errorloc' class='frm_strings' style="background-color:orange; border: 0px; text-align: center"></div>
                    <table style="width:100%">
                        <tr>
                            <td colspan="2" align="center" style="border: 1px gainsboro outset; background: skyblue">
                                <font class="FormHeaderFONT">VENTAS</font>
                            </td>
                        </tr>
                        <tr >
                            <td class="" align="right">
                                <font class="ColumnFONT"><b>N&uacute;mero de factura</b></font>
                            </td><td colspan="3" align="left" class="">
                                <input type="text" name="n_documento" value="<?php if (isset($n_documento)) echo $n_documento; ?>" id="n_documento" size="10" maxlength="10" tabindex="6" />
                            </td>
                            </td>
                        </tr>
                        <tr >
                            <td class="" align="right">
                                <font class="ColumnFONT"><b>Fecha venta: </b>desde</font>
                            </td><td colspan="3" align="left" class="">
                                <input type="text" name="finicio" value="<?php if (isset($finicio)) echo $finicio; ?>" id="finicio" size="10" maxlength="10" tabindex="6" onBlur="formatofecha(this.id, this.value); date_system_valid(this.id)" onkeyup="mascara(this, '/', patron, true)"/></input>
                                <input type="button" value="..." id="finicio_btn" tabindex="7" ></input>
                                <!--<font class="ColumnFONT"><b>hasta</b></font>-->
                                Hasta <input type="text"  name="ffin" value="<?php if (isset($ffin)) echo $ffin; ?>" id="ffin" size="10" maxlength="10" tabindex="8" onBlur="formatofecha(this.id, this.value); date_system_valid(this.id)" onkeyup="mascara(this, '/', patron, true)" /></input>
                                <input type="button" value="..." id="ffin_btn" tabindex="9" ></input>
                            </td>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" align="center" class="">
                                <input type="submit" value="Buscar" tabindex="10" class="btn btn-success" />
                                <a href="mntFactura.php" class="btn btn-primary">Crear factura</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </center>
        </form>
    </body>
    <script>
        function loadPrint() {
            window.print();
            setTimeout(function () {
                window.close();
            }, 100);
        }
    </script>
</html>
