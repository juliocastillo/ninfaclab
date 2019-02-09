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
//include ("layout.php");
//activar listado de opciones y herramientas de trabajo
include ("llenarlistas.php");
include ("tools.php");

class Vista_form extends Model_form {

    public function __construct() {
        require_once ('./llenarlistas.php');
    }

    public function get_form($args) {

        $diccionario = array(
            'form' => array(
                'FileName' => $args['FileName'],
                'FormTitle' => $args['FormTitle'],
                'id' => $args['id'],
                'nombre' => $args['nombre'],
                'id_prueba' => $args['id_prueba'],
                'min' => $args['min'],
                'max' => $args['max'],
                'unidades' => $args['unidades'],
                'orden' => $args['orden'],
                'estitulo' => $args['estitulo'],
                'esantibiograma' => $args['esantibiograma'],
                'tblbody' => $args['tblbody']
            )
        );
        /*
         * cargar contenido de archivo
         * para hacer el parse
         */

        $tpl = file_get_contents($args['form']);

        foreach ($diccionario['form'] as $clave => $valor) {
            $tpl = $this->set_var($clave, $valor, $tpl);
        }

        $cbo = new HtmlSexo();
        $lista = $cbo->llenarlista($args['id_sexo']);
        $tpl = $this->set_var('id_sexo', $lista, $tpl);

        $cbo = new HtmlGrupoedad();
        $lista = $cbo->llenarlista($args['id_grupoedad']);
        $tpl = $this->set_var('id_grupoedad', $lista, $tpl);



        print $tpl; //despliega la vista renderizada
    }

    public function set_var($htmlfield, $var, $tpl) {
        /*
         * asignar contenido a las variables en el html
         * solo hacer un reemplazo ya que las variables son únicas.
         */
        return str_replace('{' . $htmlfield . '}', $var, $tpl);
    }

}

class Model_form {

    public function __construct() {
        /*
         * controlador de conexion
         */
        require_once('./conexion.php');
    }

    function get_field_id($id) {
        $db = new MySQL();
        $sql = "SELECT t01.id,
                    t01.nombre,
                    t01.min,
                    t01.max,
                    t01.unidades,
                    t01.orden,                    
                    t01.id_sexo,
                    t01.id_grupoedad,
                    t01.esantibiograma,
                    t01.estitulo
                FROM ctl_elemento           t01
                LEFT JOIN ctl_sexo          t02 ON t02.id = t01.id_sexo
                LEFT JOIN ctl_grupoedad     t03 ON t03.id = t01.id_grupoedad
                WHERE t01.id='$id'";
        return $db->fetch_array($db->consulta($sql));
    }

    function get_list_fields($id_prueba) {
        $db = new MySQL();
        $sql = "SELECT
                    t01.id,
                    t01.nombre,
                    t01.min,
                    t01.max,
                    t01.unidades,
                    t01.orden,                    
                    t01.estitulo,                    
                    t01.esantibiograma,                    
                    if(t02.nombre is not null, t02.nombre,'Todos') as sexo,
                    if(t03.nombre is not null, t03.nombre,'Todos') as grupoedad
                FROM ctl_elemento           t01
                LEFT JOIN ctl_sexo          t02 ON t02.id = t01.id_sexo
                LEFT JOIN ctl_grupoedad     t03 ON t03.id = t01.id_grupoedad
                WHERE id_pruebaslab = $id_prueba
                ORDER BY t01.orden";
        return $db->consulta($sql);
    }

    function set_form($id) {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        $estitulo = isset($_POST["estitulo"]) ? 1 : NULL;
        $esantibiograma = isset($_POST["esantibiograma"]) ? 1 : NULL;
        $sql = "UPDATE ctl_elemento SET 
                    nombre='$nombre',
                    min='$_POST[min]',
                    max='$_POST[max]',
                    unidades='$_POST[unidades]',
                    orden='$_POST[orden]',                    
                    estitulo='$estitulo',                    
                    id_sexo ='$_POST[id_sexo]',
                    esantibiograma ='$esantibiograma',
                    id_grupoedad ='$_POST[id_grupoedad]'
                WHERE id='$id'";
        $db->consulta($sql);
    }

    function insert_form($id_prueba) {
        $db = new MySQL();
        $nombre = strtoupper($_POST['nombre']);
        if (isset($_POST['estitulo'])) {
            $estitulo = 1;
        } else {
            $estitulo = NULL;
        }
        if (isset($_POST['esantibiograma'])) {
            $esantibiograma = 1;
        } else {
            $esantibiograma = NULL;
        }
        $sql = "INSERT INTO ctl_elemento(
                    nombre,
                    min,
                    max,
                    unidades,
                    orden,                    
                    estitulo,                    
                    id_sexo,
                    id_grupoedad,
                    id_pruebaslab,
                    esantibiograma,
                    activo
                    ) 
                VALUE (
                    '$nombre',
                    '$_POST[min]',
                    '$_POST[max]',
                    '$_POST[unidades]',
                    '$_POST[orden]',                    
                    '$estitulo',                    
                    '$_POST[id_sexo]',
                    '$_POST[id_grupoedad]',
                    '$_POST[id_prueba]',
                    '$esantibiograma',
                    1
                )";
        $db->consulta($sql);
    }

    function make_table($consulta, $id_prueba) {
        $db = new MySQL();
        $tblbody = '';
        while ($row = $db->fetch_array($consulta)) {
            $tblbody .= "<tr>" .
                    "<td><a href='adminElemento.php?req=3&id=" . $row['id'] . "&id_prueba=" . $id_prueba . "'>" . $row['nombre'] . "</td>" .
                    "<td>" . $row['sexo'] . "</td>" .
                    "<td>" . utf8_encode($row['grupoedad']) . "</td>" .
                    "<td>" . $row['min'] . "</td>" .
                    "<td>" . $row['max'] . "</td>" .
                    "<td>" . $row['unidades'] . "</td>" .
                    "<td>" . $row['orden'] . "</td>" .
                    "<td>" . $row['estitulo'] . "</td>" .
                    "<td><a onclick='addElementos(" . $row['id'] . ")' href='#'>" . "Eliminar" . "</td>" .
                    "</tr>";
        }

        return $tblbody;
    }

}

extract($_GET);
extract($_POST);
if (isset($_POST['estitulo'])) {
    $estitulo = 'checked';
} else {
    $estitulo = '';
}
if (isset($_POST['esantibiograma'])) {
    $esantibiograma = 'checked';
} else {
    $esantibiograma = '';
}

if (!isset($req)) {//ingresar nuevo registro desde cero
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $consulta = $model->get_list_fields($id_prueba);
    $tblbody = $model->make_table($consulta, $id_prueba);
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminElemento.html',
        'FileName' => 'adminElemento.php?req=2',
        'FormTitle' => 'Creacion/Edicion Elemento',
        'id' => '',
        'nombre' => '',
        'id_prueba' => $id_prueba,
        'id_sexo' => '',
        'id_grupoedad' => '',
        'min' => '',
        'max' => '',
        'unidades' => '',
        'orden' => '',
        'esantibiograma' => $esantibiograma,
        'estitulo' => $estitulo,
        'tblbody' => $tblbody
    );

    $vista->get_form($args);
} elseif ($req == 2) {//ingresar un nuevo registro
    $db = new MySQL();
    $model = new Model_form();
    $model->insert_form($id_prueba);
    print "<script>window.location = 'adminElemento.php?id_prueba=" . $id_prueba . "'</script>";
} elseif ($req == 3) {//mostrar para modificar registro
    $db = new MySQL();
    $vista = new Vista_form();
    $model = new Model_form();
    /*
     * declarar parametros para enviar a la vista
     */
    $rec = $model->get_field_id($id);
    // traer listado
    $consulta = $model->get_list_fields($id_prueba);
    $tblbody = $model->make_table($consulta, $id_prueba);
    if ($rec['estitulo'] == 1) {
        $estitulo = "checked";
    } else {
        $estitulo = "";
    }
    if ($rec['esantibiograma'] == 1) {
        $esantibiograma = "checked";
    } else {
        $esantibiograma = "";
    }
    $args = array(// parametro que se pasaran a la vista
        'form' => 'adminElemento.html',
        'FileName' => 'adminElemento.php?req=4&id_prueba=' . $id_prueba,
        'FormTitle' => 'Creacion/Edicion Pruebas lababoratorio',
        'id' => $rec['id'],
        'nombre' => $rec['nombre'],
        'id_prueba' => $id_prueba,
        'id_sexo' => $rec['id_sexo'],
        'id_grupoedad' => $rec['id_grupoedad'],
        'min' => $rec['min'],
        'max' => $rec['max'],
        'unidades' => $rec['unidades'],
        'orden' => $rec['orden'],
        'estitulo' => $estitulo,
        'esantibiograma' => $esantibiograma,
        'tblbody' => $tblbody
    );
    $vista->get_form($args);
} elseif ($req == 4) {//guardar lo modificado
    $db = new MySQL();
    $model = new Model_form();
    $model->set_form($id);
    print "<script>window.location = 'adminElemento.php?id_prueba=" . $id_prueba . "'</script>";
}
?>