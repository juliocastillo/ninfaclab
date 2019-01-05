<?php
class MySQL{
    private $conexion;
    private $total_consultas;
    public function MySQL(){
        include ('./config.php');
        if(!isset($this->conexion)){
            $this->conexion = (mysqli_connect($host,$user,$password, $database)) or die(mysqli_error($this->conexion));
            mysqli_select_db($this->conexion, $database) or die(mysqli_error($this->conexion));
        }
    }
    public function consulta($consulta){
        $this->total_consultas++;
        $resultado = mysqli_query($this->conexion, $consulta);
        if(!$resultado){
            echo 'MySQL Error: ' . mysqli_error($this->conexion);
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
            $resultado = mysqli_query($this->conexion, "UPDATE $tabla SET $campo = '$text' WHERE id=$key");        
        if(!$resultado){
            echo 'MySQL Error: ' . mysqli_error();
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
        $resultado = mysqli_query($this->conexion, $sql);      
        if(!$resultado){
            echo 'MySQL Error: ' . mysqli_error();
            exit;
        }
        return mysqli_fetch_array($resultado);
    }
    
    public function fetch_array($consulta){
        return mysqli_fetch_array($consulta);
    }
    public function num_rows($result){
        return mysqli_num_rows($result);
    }

    public function result($query,$i,$campo){
        return mysqli_result($query,$i,$campo);
    }

    public function getTotalConsultas(){
        return $this->total_consultas;
    }

    public function list_fields($db,$tabla){
        return mysqli_list_fields($db,$tabla);
    }

    public function num_fields($list_atrib){
        return mysqli_num_fields($list_atrib);
    }
    public function field_name($list_atrib, $i){
        return mysqli_field_name($list_atrib, $i);
    }
    public function data_seek($consulta){
        return mysqli_data_seek($consulta, 0);
    }
    public function ultimo_id_ingresado(){
        return mysqli_insert_id($this->conexion);
    }
}
?>