<?php
session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}
//abrir conexion
include ("conexion.php");


//activar listado de opciones y herramientas de trabajo
include ("tools.php");
include ("model.php");
datevalidsp();
$model = new Model();
$db = new MySQL();
?>
<html>
    <body>
        <form name="frm" id="frm">
            <?php
            $result = $model->lab_resultado_cargar_elementos($id_detallesolicitud, $id_pruebaslab);
            if ($db->num_rows($result) > 0) {
                echo "<table><tr><th></th><th></th><th></th><th>Elemento</th><th>Resultado</th><th>Intervalo</th><th>Unidades</th></tr>";
                $i = 0;
                while ($r = $db->fetch_array($result)) {
                    $i++;
                    echo "<tr>";
                    echo "<td><input name='id_detallesolicitud[]' id='id_detallesolicitud" . $i . "' type='hidden' value='" . $r['id_detallesolicitud'] . "'></td>";
                    echo "<td><input name='id_pruebaslab[]' id='id_pruebaslab" . $i . "' type='hidden' value='" . $r['id_pruebaslab'] . "'></td>";
                    echo "<td><input name='id_elemento[]' id='id_elemento' type='hidden' value='" . $r['id_elemento'] . "'></td>";
                    echo "<td>" . $r['nombre'] . "</td>";
                    echo "<td><input name='resultado[]' id='resultado" . $i . "' value='" . $r['resultado'] . "'></td>";
                    echo "<td><input name='intervalo[]' id='intervalo" . $i . "' value='" . $r['intervalo'] . "'></td>";
                    echo "<td><input name='unidades[]' id='unidades" . $i . "' value='" . $r['unidades'] . "'></td>";
                    echo "</tr>";
                }
                echo "</table>";
                echo "<input type='button' value='Guardar' onclick='labResultadoGuardar();'>";
            } else {
                echo "Este examen aún no está configurado";
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
    </script>
</html>