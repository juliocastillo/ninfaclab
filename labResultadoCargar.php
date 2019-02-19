<?php
session_start();
$_SESSION['login'];
$_SESSION['numero_atb_mostrado'];
$numero_atb_mostrado = $_SESSION['numero_atb_mostrado'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}
//abrir conexion
include ("conexion.php");

//entorno de trabajo
echo '<link href="public/css/select2.min.css" rel="stylesheet" type="text/css"/>';
echo '<script src="public/js/select2.min.js" type="text/javascript"></script>';
//activar listado de opciones y herramientas de trabajo
include ("tools.php");
include ("model.php");
include ("llenarlistas.php");
$cboMicroorganismo = new HtmlMicroorganismo();
$cboAntibiotico = new HtmlAntibiotico();
$cboCategoria = new HtmlCategoria();
$cboPosibleresultado = new HtmlPosibleresultado();
datevalidsp();
$model = new Model();
$db = new MySQL();
?>
<html>
    <body>
        <form name="frm" id="frm">
            <?php
            $detallesolicitud = $model->lab_resultado_cargar_detallesolicitud($id_detallesolicitud);
            if ($detallesolicitud['id_estadosolicitud'] == 3 ) { //modificar la solicitud
                $result = $model->lab_resultado_cargar_elementos_modificar($id_detallesolicitud, $id_pruebaslab, $sexo, $grupoedad);
                
            } else { //nueva plantilla
                $result = $model->lab_resultado_cargar_elementos_nuevo_resultado($id_detallesolicitud, $id_pruebaslab, $sexo, $grupoedad);
            }
            
            $result_antibiograma = $model->lab_resultado_cargar_antibiograma($id_detallesolicitud);
            $microorganismo = $model->lab_resultado_cargar_microorganismo($id_detallesolicitud);
            if ($detallesolicitud['observacion'] == NULL && $detallesolicitud['id_pruebaslab'] == 49) {
                $observacion = "SE PRACTICO DIRECTO Y CONCENTRADO (FORMOL ETER)";
            } else {
                $observacion = $detallesolicitud['observacion'];
            }
            
            if ($db->num_rows($result) > 0) { // evaluar si el examen esta configurado
                echo "<table><tr><th></th><th></th><th></th><th></th><th></th><th>ELEMENTO</th><th>RESULTADO</th><th>INTERVALO</th><th>UNIDADES</th></tr>";
                $i = 0;
                $incluyeantibiograma = 0;
                while ($r = $db->fetch_array($result)) {
                    $i++;
                    echo "<tr>";
                    echo "<td><input name='id_detallesolicitud[]' id='id_detallesolicitud" . $i . "' type='hidden' value='" . $r['id_detallesolicitud'] . "'></td>";
                    echo "<td><input name='id_pruebaslab[]' id='id_pruebaslab" . $i . "' type='hidden' value='" . $r['id_pruebaslab'] . "'></td>";
                    echo "<td><input name='id_elemento[]' id='id_elemento' type='hidden' value='" . $r['id_elemento'] . "'></td>";
                    echo "<td><input name='min[]' id='min' type='hidden' value='" . $r['min'] . "'></td>";
                    echo "<td><input name='max[]' id='max' type='hidden' value='" . $r['max'] . "'></td>";
                    if ($r['esantibiograma'] != 1) {
                        echo "<td>" . $r['nombre'] . "</td>";
                    } else {
                        $incluyeantibiograma = 1;
                        $tituloantibiograma = $r['nombre'];
                        echo "<td></td>";
                    }
                    if ($r['estitulo'] != 1) {
                        if ($r['escatalogo'] != 1) {
                            echo "<td><input name='resultado[]' id='resultado" . $i . "' value='" . $r['resultado'] . "' style='width: 120px;' required></td>";
                        } else { //es un catalogo
                            echo "<td><select name='resultado[]' id='resultado'><option value='NO SE OBSERVAN'>NO SE OBSERVAN</option><option value='" . $r['resultado'] . "'>".$cboPosibleresultado->llenarlista($r['id_elemento'],$r['resultado'])."</value></td>";
                        }
                        echo "<td><input name='intervalo[]' id='intervalo" . $i . "' value='" . $r['intervalo'] . "'  style='width: 100px;'></td>";
                        echo "<td><input name='unidades[]' id='unidades" . $i . "' value='" . $r['unidades'] . "'  style='width: 100px;'></td>";
                    } else { //en caso que se titulo
                        echo "<td><input type='hidden' name='resultado[]' id='resultado" . $i . "' value='" . $r['resultado'] . "' style='width: 120px;' ></td>";
                        echo "<td><input type='hidden' name='intervalo[]' id='intervalo" . $i . "' value='" . $r['intervalo'] . "'  style='width: 100px;'></td>";
                        echo "<td><input type='hidden' name='unidades[]' id='unidades" . $i . "' value='" . $r['unidades'] . "'  style='width: 100px;'></td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
                // desplegar antibiograma si se ha incluido
                if ($incluyeantibiograma == 1) {
                    echo "MICROORGANISMO: ";
                    echo "<select name='id_microorganismo' id='id_microorganismo'><option value=''>...Seleccione...</option>" . $cboMicroorganismo->llenarlista($microorganismo['id_microorganismo']) . "</select>";
                    echo "<table><tr><th>" . $tituloantibiograma . "</th><td>ANTIBIOTICO</td><td>LECTURA</td><td>CATEGORIA</td></tr>";
                    $k = 0;
                    while ($r = $db->fetch_array($result_antibiograma)) {
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td><select name='id_antibiotico[]' id='id_antibiotico'><option value=''>...Seleccione...</option>" . $cboAntibiotico->llenarlista($r['id_antibiotico']) . "</select></td>";
                        echo "<td><input name='lectura[]' id='lectura' value='".$r['lectura']."'></td>";
                        echo "<td><select name='categoria[]' id='categoria'><option value=''>...Seleccione...</option>" . $cboCategoria->llenarlista($r['categoria']) . "</select></td>";
                        echo "</tr>";
                        $k++;
                    }
                    for ($i = 1; $i <= ($numero_atb_mostrado - $k); $i++) {
                        echo "<tr>";
                        echo "<td></td>";
                        echo "<td><select name='id_antibiotico[]' id='id_antibiotico'><option value=''>...Seleccione...</option>" . $cboAntibiotico->llenarlista() . "</select></td>";
                        echo "<td><input name='lectura[]' id='lectura' value=''></td>";
                        echo "<td><select name='categoria[]' id='categoria'><option value=''>...Seleccione...</option><option value='S'>SENSIBLE</option><option value='I'>INTERMEDIO</option><option value='R'>RESISTENTE</option></select></td>";
                        echo "</tr>";
                    }
                    echo "</table>";
                }
                echo "OBSERVACIONES: <br><textarea rows=2 cols=70 name='observacion' id='observacion'>". $observacion ."</textarea><br>";
                echo "<input type='button' value='Guardar' onclick='labResultadoGuardar();'>";
            } else {
                echo "Este examen aun no configurado .... " . "<a href='#' onclick='addElementos($id_pruebaslab)'> configurar ahora</a>";
            }
            ?>
        </form>
        <div id="message"></div>

    </body>
    <script>
        function labResultadoGuardar() { //enviar formulario
            var frm = $('#frm');
            $.ajax({
                type: "POST",
                url: 'labResultadoGuardar.php',
                data: frm.serialize(),
                async: true,
                success: function (result) {
                    $("#message").html(result);
                }});
//            }
        }
        
        $(document).ready(function () {
                $('#id_microorganismo').select2({
                    placeholder: 'Seleccionar microorganismo',
                    allowClear: true
                });
            });
    </script>
</html>