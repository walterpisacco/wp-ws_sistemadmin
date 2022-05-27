<?php
/**
 * 
 * Clase que hace de adaptador con la DLL de Conexion a BD
 * @author walter
 *
 */
class conectar {
	/**
	 * 
	 * Nombre de la Base de Datos
	 * @var String
	 */
	private $_db = 'SEGURIDAD';
	/**
	 * 
	 * Nombre del Servidor
	 * @var String
	 */
	private $_host = 'basedesa';
	
	/**
	 * 
	 * Constructor
	 * @param String $_db
	 * @param String $_host
	 */
	public function __construct($_db,$_host){
		$this->_db = (empty($_db))? NAMEDB: (string)$_db;
		$this->_host = (empty($_host))? HOSTDB:(string)$_host;
	}
	
	/**
	 * 
	 * Metodo que ejecuta una consulta SQL (DML)
	 * @param String $query
	 * @return Object
	 */
	public function ConsultaSelect($query){
		$obj = new COM("conweb.conn");
		$result=$obj->consultaSelect((string)$query,$this->_db,$this->_host); 
		return $result;
	}
} 
?>
