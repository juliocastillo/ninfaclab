<?php
session_start();
$_SESSION['login'];
extract($_GET);
if (isset($salir)) {
    $_SESSION['login'] = false;
}

if ($_SESSION['login'] == true) {
    //archivos comunes *********************************
    include_once ('./common.html');
    include ("./conexion.php");
    echo '<div class="dividentifica" style="background-color: gray; color: white; font-size: 12px;">' . $_SESSION['username'] . '</div><span class="preload1"></span>';
    require_once('./menuprincipal.php');
    //**************************************************
} else {
    echo "<script>window.location = './login.php'</script>";
}
?>
<link href="public/AdminLTE/dist/css/AdminLTE.css" rel="stylesheet" type="text/css"/>
<link href="public/AdminLTE/bower_components/Ionicons/css/ionicons.min.css" rel="stylesheet" type="text/css"/>
<link href="public/AdminLTE/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css"/>
<!-- Main content -->
<section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3>150</h3>

                    <p>Solicitudes</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="labSolicitud.php" class="small-box-footer">Entrar <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3>53<sup style="font-size: 20px">%</sup></h3>

                    <p>Ventas</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="mntFacturaBuscar.php" class="small-box-footer">Entrar <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3>44</h3>

                    <p>Reportes</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">Entrar <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3>12</h3>

                    <p>Catalogos</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="catalogos.php" class="small-box-footer">Entrar <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
    </div>
    <!-- /.row -->
    <table width=100% height=100%> 
        <tr>
            <td style="text-align: center; vertical-align: middle;">
                <img src="public/images/lp.png"/>
            </td>
        </tr>
    </table>
</section>

