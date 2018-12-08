<?php
session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}
//abrir conexion
include ("conexion.php");
?>

<html>
    <head>
        <link href="public/bootstrap/bootstrap.min.css" rel="stylesheet" type="text/css"/>
        <link href="public/datatable/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css"/>
        <script src="public/js/jquery-3.3.1.min.js" type="text/javascript"></script>
        <script src="public/datatable/js/jquery.dataTables.min.js" type="text/javascript"></script>
        <script src="public/js/funciones_generales.js" type="text/javascript"></script>
    </head>
    <body>
        <table style="border-collapse:collapse" id="tblresult" class="display compact">
            <thead>
                <tr>
                    <th width="30%">NOMBRE</th>
                    <th width="50px">SEXO</th>
                    <th width="125px">GRUPO_EDAD</th>
                    <th width="75px">MINIMO</th>
                    <th width="75px">MAXIMO</th>
                    <th width="75px">UNIDADES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $db = new MySQL();
                extract($GET);
                $sqlcommand = "SELECT t01.id, t01.nombre
                     FROM ctl_elemento t01
                     WHERE t01.id_pruebaslab =" . $id_prueba;
                $result = $db->consulta($sqlcommand);
                while ($r = $db->fetch_array($result)) {
                    echo "<tr>".
                            "<td>".
                                $r['nombre'] . 
                            "</td>".
                            "<td>".
                                $r['id_sexo'] . 
                            "</td>".
                            "<td>".
                                $r['id_grupoedad'] . 
                            "</td>".
                            "<td>".
                                $r['minimo'] . 
                            "</td>".
                            "<td>".
                                $r['maximo'] . 
                            "</td>".
                            "<td>".
                                $r['unidades'] . 
                            "</td>".
                        "</tr>";
                }
                ?>
            </tbody>
        </table>
    </body>
    <script language="Javascript">
        /*
         * inicializaDataTable(combobox, pageLength, lengthChange, searching)
         */
        inicializaDataTableEditor('tblresult', 10, false, false);        
           
       
    </script>
</html>
