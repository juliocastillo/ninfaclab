<?php

/**
 * Description of CtlEmpresa
 *
 * @author julio castillo
 */
class Mnt_paciente {

    private $db;  //conexion
    private $paciente; // entidad
    private $id;  //primary key
    private $nombres;
    private $apellidos;
    private $registro;
    private $edad;
    private $id_sexo;
    private $direccion;
    private $telefono;
    private $correo;
    private $id_empresa;
    private $activo;

    public function __construct($id = null) {
        $this->db = new MySQL();
        $this->id = $id;
        if ($this->id != "") { //cargar el registro
            $this->paciente = $this->db->getEntityId(strtolower(get_class($this)), $this->id);
        }
    }

    public function setNombres($nombres) {
        $this->nombres = $nombres;
        return $this;
    }

    public function setApellidos($apellidos) {
        $this->apellidos = $apellidos;
        return $this;
    }

    public function setRegistro($registro) {
        $this->registro = $registro;
        return $this;
    }

    public function setEdad($edad) {
        $this->edad = $edad;
        return $this;
    }

    public function setId_sexo($id_sexo) {
        $this->id_sexo = $id_sexo;
        return $this;
    }

    public function setDireccion($direccion) {
        $this->direccion = $direccion;
        return $this;
    }

    public function setTelefono($telefono) {
        $this->telefono = $telefono;
        return $this;
    }

    public function setCorreo($correo) {
        $this->correo = $correo;
        return $this;
    }

    public function setId_empresa($id_empresa) {
        $this->id_empresa = $id_empresa;
        return $this;
    }

    public function setActivo($activo) {
        $this->activo = $activo;
        return $this;
    }

    /*     * ********************GET*********************** */

    public function getNombres() {
        return $this->paciente['nombres'];
    }

    public function getApellidos() {
        return $this->paciente['apellidos'];
    }

    public function getRegistro() {
        return $this->paciente['registro'];
    }

    public function getEdad() {
        return $this->paciente['edad'];
    }

    public function getId_sexo() {
        return $this->paciente['id_sexo'];
    }

    public function getTelefono() {
        return $this->paciente['telefono'];
    }

    public function getCorreo() {
        return $this->paciente['correo'];
    }

    public function getDireccion() {
        return $this->paciente['direccion'];
    }

    public function getId_empresa() {
        return $this->paciente['id_empresa'];
    }

    public function getActivo() {
        return $this->paciente['activo'];
    }

    public function commit() {
        $tabla = strtolower(get_class($this));
        if ($this->id) {
            $sql = "UPDATE $tabla SET 
                        nombres         = '$this->nombres',
                        apellidos       = '$this->apellidos',
                        registro        = '$this->registro',
                        edad            = '$this->edad',
                        fecnac          = date_add(NOW(), INTERVAL -$this->edad YEAR),
                        id_sexo         = '$this->id_sexo',
                        direccion       = '$this->direccion',
                        telefono        = '$this->telefono',
                        correo          = '$this->correo',
                        id_empresa      = '$this->id_empresa',
                        activo          = '$this->activo'
                    WHERE id = $this->id
                ";
            $result = $this->db->consulta($sql);
        } else {
            $sql = "INSERT INTO  $tabla SET 
                        nombres         = '$this->nombres',
                        apellidos       = '$this->apellidos',
                        registro        = '$this->registro',
                        edad            = '$this->edad',
                        fecnac          = date_add(NOW(), INTERVAL -$this->edad YEAR),
                        id_sexo         = '$this->id_sexo',
                        direccion       = '$this->direccion',
                        telefono        = '$this->telefono',
                        correo          = '$this->correo',
                        id_empresa      = '$this->id_empresa',
                        activo          = '$this->activo'
                ";
            $result = $this->db->consulta($sql);
        }
        return $result;
    }

}
