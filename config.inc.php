<?php

$GLOBALS["servidor"] = "localhost";
$GLOBALS["usuario"] = "root";
$GLOBALS["contrasena"] = "";
$GLOBALS["base_datos"] = "ecologico";
$GLOBALS["raiz_sitio"] = "http://localhost/ecologico/";


class Conexiondb{
	private $servidor;
	private $usuario;
	private $contrasena;
	private $db;
	public $conexion;

	public function __construct(){
		$this->servidor = $GLOBALS["servidor"];
		$this->usuario = $GLOBALS["usuario"];
		$this->contrasena = $GLOBALS["contrasena"];
		$this->db = $GLOBALS["base_datos"];
	}

	public function getConnection(){
		// Si estamos en modo testing, usar mock
		if (defined('TESTING_MODE') && TESTING_MODE) {
			return new MockMysqli();
		}
		
		// En producción, usar conexión real
		$this->conexion = new mysqli($this->servidor, $this->usuario, $this->contrasena, $this->db);
		
		if($this->conexion->connect_error){
			die("Conexion fallida: " . $this->conexion->connect_error);
		}
		return $this->conexion;
	}
}
