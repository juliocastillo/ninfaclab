<?php
session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login']!=true){
    echo "<script>window.location = './login.php'</script>";
} 
//abrir conexion
include ("conexion.php");

//entorno de trabajo
include ("layout.php");

//activar listado de opciones y herramientas de trabajo
include ("llenarlistas.php");
include ("tools.php");

// activar fechas en español
datevalidsp();

class Vista_form extends Model_form {

    public function __construct() {
        require_once ('llenarlistas.php'); 
    }

    public function get_form($args) {
        /*
         * inicio de plantilla
         */
        $db = new MySQL();
        ?>
        <div style="box-shadow: 10px 10px 5px grey; width: 40%;">
            <table id="tblresult" class="display" style="width: 100%;">
                <thead><th>Catalogo</th><th></th></thead>
        <?php
        while ($r = $db->fetch_array($args)){
            echo "<tr><td>".$r['nombre']."</td><td><a href=".$r['url_form']."> <img src='public/images/carpabiertat.gif' alt='abrir' height='20' width='20'></></td></tr>";
        }
        ?> 
            </table>
        </div>
        <?php
    }
}

class Model_form {
    public function __construct() {
        /*
         * controlador de conexion
         */
        require_once('conexion.php');
    }
    
    function get_list_fields() {
        $db = new MySQL();
        $sql = "SELECT 
                    *,
                    IF(activo='1', 'SI', 'NO') activo
                FROM ctl_catalogos 
                ORDER BY nombre";
        $result = $db->consulta($sql);
        return $result;
    }
}


$vista  = new Vista_form();
$model  = new Model_form();

// obtener registros de la base de datos
$args   = $model->get_list_fields();

//desplegar contenido
$vista->get_form($args)

?>

<script type="text/javascript">
    /*
    * inicializaDataTable(combobox, pageLength, lengthChange, searching)
    */
    inicializaDataTable('tblresult', 10, true, true);
</script>
