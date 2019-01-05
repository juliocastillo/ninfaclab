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

    </head>
    <body class="PageBODY">
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
                                <a href="facFactura.php" class="btn btn-primary">Crear factura</a>
                            </td>
                        </tr>
                    </table>
                </div>
        </form>
        <?php
        if (isset($cambio_estado) && $cambio_estado == 'I') {
            $model->cambiar_estado_facturacin($id, 'I');
        } elseif (isset($cambio_estado) && $cambio_estado == 'A') {
            $model->cambiar_estado_facturacin($id, 'A');
        }


        if ((isset($finicio) && isset($ffin)) || isset($n_documento)) {
            if (isset($finicio))
                $finicio = datetosql($finicio);
            if (isset($ffin))
                $ffin = datetosql($ffin);
            $consulta = $model->get_lista_factura($finicio, $ffin, $n_documento);
        } else {  // cargar las solicitudes recientes
            if (isset($finicio))
                $finicio = date("Y-m-d");
            if (isset($ffin))
                $ffin = date("Y-m-d");
            $consulta = $model->get_lista_factura($finicio, $ffin, $n_documento);
        }
        ?>
        <br>
    <center>
        <div style="width: 1024px;"> 
            <table class="display" style="width: 100%;" id="tblresult">

                <thead>
                    <tr>
                        <th># Comprobante</th>
                        <th>Fecha solicitud</th>
                        <th>Paciente</th>
                        <th>Tipo</th>
                        <th></th>
                    </tr>
                </thead>
                <?php
                $i = 0;
                while ($row = $db->fetch_array($consulta)) {
                    $i++;
                    ?>
                    <tr>
                        <td><?php echo htmlentities($row['numero_comprobante']); ?></td>
                        <td><?php echo $row['fecha']; ?></td>
                        <td><?php echo htmlentities($row['nombre']); ?></td>
                        <td><?php echo htmlentities($row['tipo_comprobante']); ?></td>
                        <td>
                            <?php if ($row['id_factura'] == null) { ?>
                                <a href="facFactura.php?id_solicitud=<?php echo $row['id_solicitud']; ?>">Facturar</a>
                            <?php } else {
                                ?>
                                <a href="facFactura.php?req=3&id_factura=<?php echo $row['id_factura']; ?>">Ver Factura</a>
                            <?php } ?>
                        </td>
                        <?php
                    }
                    ?>

                </tr>
            </table>
        </div>
    </center>
</body>
<script language="Javascript">
    /*
     * inicializaDataTable(combobox, pageLength, lengthChange, searching)
     */
    inicializaDataTable('tblresult', 10, true, true);

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

    function popup(URL) {
        myWindow = window.open(URL, '" + "', 'scrollbars=yes, width=600, height=600, top = 50');
    }

    var frmvalidator = new Validator("frm");
    frmvalidator.EnableOnPageErrorDisplaySingleBox();
    frmvalidator.EnableMsgsTogether();

    frmvalidator.addValidation("finicio", "required", "Ingrese fecha desde");
    frmvalidator.addValidation("ffin", "required", "Ingrese fecha de fin");

</script>

</html>
