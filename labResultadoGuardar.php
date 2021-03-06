<?php

session_start();
$_SESSION['login'];
$userID = $_SESSION['userID'];
extract($_POST);
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

for ($i = 0; $i < count($id_detallesolicitud); $i++) {
    if ($id_elemento[$i] != '') {
        if ((float)$resultado[$i] > (float)$min[$i] && (float)$resultado[$i] < (float)$max[$i]) { //valor normal
            $fueraderango = null;
        } else {
            $fueraderango = 1;
        }
        $model->lab_resultado_guardar(
                $id_detallesolicitud[$i], $id_elemento[$i], $resultado[$i], $intervalo[$i], $unidades[$i], $fueraderango, $userID
        );
    }
}

//guardar antibiograma
if (isset($id_antibiotico)) {
    for ($i = 0; $i < count($id_antibiotico); $i++) {
        if ($id_microorganismo != '' && $id_antibiotico[$i] != '' && $lectura[$i] != '' && $categoria[$i]) {
            $model->lab_resultado_antibiograma_guardar(
                    $id_detallesolicitud[0], $id_microorganismo, $id_antibiotico[$i], $lectura[$i], $categoria[$i], $userID
            );
        }
    }
}

if ($model->lab_resultado_cambiarestado($id_detallesolicitud[0], $observacion)) {
    echo 'Los datos se guardaron correctamente';
} else {
    echo 'ERROR NO SE HAN GARDADO LOS DATOS';
}
