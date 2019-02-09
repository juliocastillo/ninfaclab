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
//var_dump($_POST); exit();
for ($i = 0; $i < count($id_detallesolicitud); $i++) {
    if ($id_elemento[$i] != '' && $resultado[$i] != '') {
        $model->lab_resultado_guardar(
                $id_detallesolicitud[$i], $id_elemento[$i], $resultado[$i], $intervalo[$i], $unidades[$i], $userID
        );
    }
}
//guardar antibiograma
for ($i = 0; $i < count($id_antibiotico); $i++) {
    if ($id_microorganismo != '' && $id_antibiotico[$i] != '' && $lectura[$i] != '' && $categoria[$i]) {
        $model->lab_resultado_antibiograma_guardar(
                $id_detallesolicitud[0], $id_microorganismo, $id_antibiotico[$i], $lectura[$i], $categoria[$i], $userID
        );
    }
}
if ($model->lab_resultado_cambiarestado($id_detallesolicitud[0])) {
    echo 'Los datos se guardaron correctamente';
} else {
    echo 'ERROR NO SE HAN GARDADO LOS DATOS';
}
