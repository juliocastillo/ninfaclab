<?php
class MySQL{
    private $conexion;
    private $total_consultas;
    public function MySQL(){
        include ('./config.php');
        if(!isset($this->conexion)){
            $this->conexion = (mysql_connect($host,$user,$password)) or die(mysql_error());
            mysql_select_db($database,$this->conexion) or die(mysql_error());
        }
    }
    public function consulta($consulta){
        $this->total_consultas++;
        $resultado = mysql_query($consulta,$this->conexion);
        if(!$resultado){
            echo 'MySQL Error: ' . mysql_error();
            exit;
        }
        return $resultado;
    }
    /*
     * tabla = nombre de la tabla
     * campo = nombre del campo
     * text  = string que se almacena
     * id    = llave primaria unica
     */
    public function setString($args){
        extract($args);
        if ($type == 'string')
            $resultado = mysql_query("UPDATE $tabla SET $campo = '$text' WHERE id=$key", $this->conexion);        
        if(!$resultado){
            echo 'MySQL Error: ' . mysql_error();
            exit;
        }
        return $resultado;
    }
    
    /*
     * tabla = nombre de la tabla
     * campo = nombre del campo
     * id    = llave primaria unica
     */
    public function getEntityId($tabla, $key) {
        $sql = "SELECT * FROM $tabla WHERE id=$key";
        $resultado = mysql_query($sql, $this->conexion);      
        if(!$resultado){
            echo 'MySQL Error: ' . mysql_error();
            exit;
        }
        return mysql_fetch_array($resultado);
    }
    
    public function fetch_array($consulta){
        return mysql_fetch_array($consulta);
    }
    public function num_rows($result){
        return mysql_num_rows($result);
    }

    public function result($query,$i,$campo){
        return mysql_result($query,$i,$campo);
    }

    public function getTotalConsultas(){
        return $this->total_consultas;
    }

    public function list_fields($db,$tabla){
        return mysql_list_fields($db,$tabla);
    }

    public function num_fields($list_atrib){
        return mysql_num_fields($list_atrib);
    }
    public function field_name($list_atrib, $i){
        return mysql_field_name($list_atrib, $i);
    }
    public function data_seek($consulta){
        return mysql_data_seek($consulta, 0);
    }
    public function ultimo_id_ingresado(){
        return mysql_insert_id($this->conexion);
    }
}
?>