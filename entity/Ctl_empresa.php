<?php

/**
 * Description of CtlEmpresa
 *
 * @author julio castillo
 */
class Ctl_empresa {
    private $db;  //conexion
    private $empresa; // entidad
    
    private $id;  //primary key
    private $nombre;
    private $nombre_comercial;
    private $nrc;
    private $nit;
    private $telefono;
    private $correo;
    private $direccion;
    private $id_municipio;
    private $exento;
    private $id_zona;
    private $id_tipoempresa;
    private $id_tipocomprobante;
    private $activo;    
    
    
    public function __construct($id = null) {
         $this->db = new MySQL();
         $this->id = $id;
         if ($this->id) { //cargar el registro
             $this->empresa = $this->db->getEntityId(strtolower(get_class($this)),$this->id);
         } 
    }
    
    public function setNombre($nombre) {
        $this->nombre = $nombre;
        return $this;
    }
    
    public function setNombre_comercial($nombre_comercial) {
        $this->nombre_comercial = $nombre_comercial;
        return $this;
    }
    
    public function setRegistro($nrc) {
        $this->nrc = $nrc;
        return $this;
    }
    
    public function setNit($nit) {
        $this->nit = $nit;
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
    
    public function setDireccion($direccion) {
        $this->direccion = $direccion;
        return $this;
    }
    
    public function setId_municipio($id_municipio) {
        $this->id_municipio = $id_municipio;
        return $this;
    }
    
    public function setExento($excento) {
        $this->exento = $excento;
        return $this;
    }
    
    public function setId_zona($id_zona) {
        $this->id_zona = $id_zona;
        return $this;
    }
    
    public function setId_tipoempresa($id_tipoempresa) {
        $this->id_tipoempresa = $id_tipoempresa;
        return $this;
    }
    
    public function setId_tipocomprobante($id_tipocomprobante) {
        $this->id_tipocomprobante = $id_tipocomprobante;
        return $this;
    }
    
    public function setActivo($activo) {
        $this->activo = $activo;
        return $this;
    }
    
    
    /**********************GET************************/
    public function getNombre() {
        return $this->empresa['nombre'];
    }
    
    public function getNombre_comercial() {
        return $this->empresa['nombre_comercial'];
    }
    
    public function getRegistro() {
        return $this->empresa['nrc'];
    }
    
    public function getNit() {
        return $this->empresa['nit'];
    }
    
    public function getTelefono() {
        return $this->empresa['telefono'];
    }
    
    public function getCorreo() {
        return $this->empresa['correo'];
    }
    
    public function getDireccion() {
        return $this->empresa['direccion'];
    }
    
    public function getId_municipio() {
        return $this->empresa['id_municipio'];
    }
    
    public function getExento() {
        return $this->empresa['exento'];
    }
    
    
    public function getId_zona() {
        return $this->empresa['id_zona'];
    }
    
    public function getId_tipoempresa() {
        return $this->empresa['id_tipoempresa'];
    }
    
    public function getId_tipocomprobante() {
        return $this->empresa['id_tipocomprobante'];
    }
    
    public function getActivo() {
        return $this->empresa['activo'];
    }
    
    public function commit() {
        $tabla = strtolower(get_class($this));
        if ($this->id) {
            $sql = "UPDATE $tabla SET 
                        nombre              = '$this->nombre',
                        nombre_comercial    = '$this->nombre_comercial',
                        nrc            = '$this->nrc',
                        nit                 = '$this->nit',
                        telefono            = '$this->telefono',
                        correo              = '$this->correo',
                        direccion           = '$this->direccion',
                        id_municipio        = '$this->id_municipio',
                        exento              = '$this->exento',
                        id_zona             = '$this->id_zona',
                        id_tipoempresa      = '$this->id_tipoempresa',
                        id_tipocomprobante  = '$this->id_tipocomprobante',
                        activo              = '$this->activo'
                    WHERE id = $this->id
                ";
            $result = $this->db->consulta($sql);
        } else {
            $sql = "INSERT INTO  $tabla SET 
                    nombre              = '$this->nombre',
                    nombre_comercial    = '$this->nombre_comercial',
                    nrc            = '$this->nrc',
                    nit                 = '$this->nit',
                    telefono            = '$this->telefono',
                    correo              = '$this->correo',
                    direccion           = '$this->direccion',
                    id_municipio        = '$this->id_municipio',
                    exento              = '$this->exento',
                    id_zona             = '$this->id_zona',
                    id_tipoempresa      = '$this->id_tipoempresa',
                    id_tipocomprobante  = '$this->id_tipocomprobante',
                    activo              = '$this->activo'
                ";
            $result = $this->db->consulta($sql);
        }
        return $result;
    }    
}
