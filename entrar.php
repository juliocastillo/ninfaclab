<?php
session_start();
// verificar datos ingresados de usuario y contraseña
extract($_POST);
if (isset($usuario_login) && isset($usuario_password)){
    include ("model.php");
    $model = new Model();
    require 'conexion.php'; // se requiere ya que aún no se ha iniciado el menu
    $user = $model->login($usuario_login,md5($usuario_password));
    if(isset($user['nombre'])){ //si los datos son correctos
//        $_SESSION['empresa'] = htmlentities($model->get_empresa());
        $_SESSION['login'] = TRUE;
        $_SESSION['userID'] = $user['id'];
        $_SESSION['username'] = $user['nombre'].' '.$user['apellido'];
        $_SESSION['rol'] = $user['id_rol'];
        $_SESSION['id_laboratorio'] = $user['id_laboratorio'];
        $_SESSION['laboratorio'] = $user['laboratorio'];
        $_SESSION['iva'] = $user['iva'];
        echo "<script>window.location = 'index.php'</script>"; // mostrar menu
    } else {
        echo "<script>window.location = 'login.php'</script>"; // solicitar nuevamente usuario y pass
    }
}
