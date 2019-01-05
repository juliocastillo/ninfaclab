<?php

session_start();
$_SESSION['login'];
extract($_GET);
if ($_SESSION['login'] != true) {
    echo "<script>window.location = './login.php'</script>";
}

//abrir conexion
include ("./conexion.php");
$db = new MySQL();


if ($bus == 'empresa') {
    $sqlcommand = "SELECT * FROM ctl_empresa WHERE id=$id";
    $result = $db->consulta($sqlcommand);

    while ($r = $db->fetch_array($result)) {
        if ($r['exento'] == 1) {
            $exento = true;
        } else {
            $exento = false;
        }
        if ($r['activo'] == 1) {
            $activo = true;
        } else {
            $activo = false;
        }
        $array = array(
            'obj' => array(
                'id' => $r['id'],
                'nombre' => $r['nombre'],
                'nombre_comercial' => $r['nombre_comercial'],
                'nrc' => $r['nrc'],
                'nit' => $r['nit'],
                'telefono' => $r['telefono'],
                'correo' => $r['correo'],
                'direccion' => $r['direccion'],
                'id_municipio' => $r['id_municipio'],
                'id_zona' => $r['id_zona'],
                'exento' => $exento,
                'id_tipoempresa' => $r['id_tipoempresa'],
                'id_tipocomprobante' => $r['id_tipocomprobante'],
                'activo' => $activo,
        ));
    }
}

if ($bus == 'paciente') {
    $sqlcommand = "SELECT 
                        t01.id,
                        t01.nombres,
                        t01.apellidos,
                        t01.registro,
                        t01.edad,
                        t01.id_sexo,
                        t01.direccion,
                        t01.telefono,
                        t01.id_empresa,
                        t02.id_tipoempresa,
                        t02.id_tipocomprobante,
                        t01.activo
                    FROM mnt_paciente   t01
                    LEFT JOIN ctl_empresa       t02 ON t01.id_empresa = t02.id
                    LEFT JOIN ctl_tipoempresa   t03 ON t02.id_tipoempresa = t03.id
                    WHERE t01.id=$id";
    $result = $db->consulta($sqlcommand);

    while ($r = $db->fetch_array($result)) {
        /* Convertir variables bolean */
        if ($r['activo'] == 1) {
            $activo = true;
        } else {
            $activo = false;
        }
        $array = array(
            'obj' => array(
                'id' => $r['id'],
                'nombres' => $r['nombres'],
                'apellidos' => $r['apellidos'],
                'registro' => $r['registro'],
                'edad' => $r['edad'],
                'id_sexo' => $r['id_sexo'],
                'direccion' => $r['direccion'],
                'telefono' => $r['telefono'],
                'id_empresa' => $r['id_empresa'],
                'id_tipoempresa' => $r['id_tipoempresa'],
                'id_tipocomprobante' => $r['id_tipocomprobante'],
                'activo' => $activo,
        ));
    }
}

if ($bus == 'lista_paciente') {
    $sqlcommand = "SELECT 
                        t01.id,
                        CONCAT(t01.nombres,' ',t01.apellidos) AS nombres
                    FROM mnt_paciente   t01
                    WHERE t01.nombres like '%$search%'";
    $result = $db->consulta($sqlcommand);

    $array = array(
            'obj' => array(
                'id' => '',
                'nombres' => ''
                
        ));
    while ($r = $db->fetch_array($result)) {
        /* Convertir variables bolean */
        
        array_push($array, array('id' => $r['id'], 'nombres' => $r['nombres'])); 
    }
}


if ($bus == 'precio') {
    $sqlcommand = "        
        SELECT precio FROM mnt_precio_tipoempresa_pruebaslab t01
        LEFT JOIN ctl_tipoempresa t02 ON t02.id = t01.id_tipoempresa
        LEFT JOIN ctl_pruebaslab t03 ON t03.id = t01.id_pruebaslab
        WHERE id_pruebaslab = $id_pruebaslab AND id_tipoempresa = $id_tipoempresa
    ";

    $result = $db->consulta($sqlcommand);

    if ($db->num_rows($result) > 0) {
        while ($r = $db->fetch_array($result)) {
            $array = array(
                'obj' => array(
                    'precio' => $r['precio'],
            ));
        }
    } else {
        $array = array(
            'obj' => array(
                'precio' => 0,
        ));
    }
}

if ($bus == 'eliminar_prueba_solicitud') {
    $sqlcommand = "
        DELETE FROM `lab_detallesolicitud` 
        WHERE id = $id_detallesolicitud AND id_estadosolicitud = 1
    ";

    $result = $db->consulta($sqlcommand);

    $array = array(
        'obj' => array(
            'message' => 'Prueba eliminada',
        )
    );
}

print json_encode($array);

