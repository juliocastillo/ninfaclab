<?php
$array = array();

$elemento = array(
    'departamento' => '06 san salvador',
    'edad' => '10',
    'sexo' => '01 M'
);
array_push($array, $elemento);

$elemento = array(
    'departamento' => '03 sonsonate',
    'edad' => '10',
    'sexo' => '02 F'
);
array_push($array, $elemento);

$elemento = array(
    'departamento' => '03 sonsonate',
    'edad' => '15',
    'sexo' => '02 F'
);
array_push($array, $elemento);





echo json_encode($array);
?>