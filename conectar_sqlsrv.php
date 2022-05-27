<?php
require_once 'conectar.php';
class conectarSQLSRV extends conectar{
	
	protected $_connection_info = null;
	protected $_link = null;
	
	/**
	 * Constructor
	 * 
	 * @param unknown $_db
	 * @param unknown $_host
	 */
	public function __construct($_db,$_host,$_usuario,$_password){
		$this->_connection_info = array ('Database' => $_db, 'UID' => $_usuario, 'PWD' => $_password, "CharacterSet" => "UTF-8" );
		$this->_link = sqlsrv_connect ($_host, $this->_connection_info ) or die ( print_r ( sqlsrv_errors (), true ) );
	}
	
	/**
	 * (non-PHPdoc)
	 * @see conectar::ConsultaSelect()
	 * 
	 * @return tableRowSQLSRV
	 */
	public function ConsultaSelect($query){
		//Comprobamos si existe un conexion previamente abirta
		if (! isset ( $this->_link )) {
			throw new Exception( 'Fallo al intentar conectarse a la Base' );
		}
		
		if ($query == "" || empty($query) || is_null($query)) {
			throw new Exception("Debe especificar una sentencia sql");
			return 0;
		}
		
		//Lanzamos la consulta
		$consulta = sqlsrv_query ( $this->_link, $query, NULL,array('Scrollable'=>'static'));
		
	if ($consulta === false) {
			if (($errors = sqlsrv_errors ()) != null) {
				throw new Exception($errors[0][2]);
			}
		}
		return new tableRowSQLSRV($consulta);
	}
	
	/**
	 * Metodo para ejecutar un Store
	 * 
	 * @param String $query
	 * @param Array $params
	 * 
	 * @throws Exception
	 * 
	 * @return tableRowSQLSRV
	 */
	public function ConsultaStore($query,$params = NULL){
		//Comprobamos si existe un conexion previamente abirta
		if (! isset ( $this->_link )) {
			throw new Exception( 'Fallo al intentar conectarse a la Base' );
		}
		
		if ($query == "" || empty($query) || is_null($query)) {
			throw new Exception("Debe especificar una sentencia sql");
			return 0;
		}
		
		//Lanzamos la consulta
		$consulta = sqlsrv_query( $this->_link, $query, $params,array('Scrollable'=>'forward'));
		if ($consulta === false) {	
			if (($errors = sqlsrv_errors ()) != null) {
				throw new Exception($errors[0][2]);
			}
		}
		return new tableRowSQLSRV($consulta);
	}
	
	
}


class tableRowSQLSRV {
	
	public $Recordcount = 0;
	public $EOF = true;
	private $_index = 0;
	private $_source = array();
	private $_statment = null;
	
	/**
	 * 
	 * @param SQL Server Statement $statment
	 */
	public function __construct($statment){
		try {
			$this->_statment = $statment;
			//sqlsrv_fetch($statment);
			//var_dump(sqlsrv_get_field($statment,0));
			//exit();
			while($row = sqlsrv_fetch_array($this->_statment,SQLSRV_FETCH_BOTH)){
				$this->_source[] = $row;
			}
			$this->Recordcount = count($this->_source);
			if($this->Recordcount) $this->EOF = false;	
		} catch (Exception $e) {
			var_dump($e);
			exit();
		}
		
	}
	
	
	/**
	 * Devuelve el valor del campo
	 * @param String $indice (Puede ser el nombre del campo o su posición
	 */
	public function Fields($indice){
		if(!array_key_exists($indice,$this->_source[$this->_index])) throw new Exception("No existe la columna $indice");
		$row = new stdClass();
		$row->value = $this->_source[$this->_index][$indice];
		return $row;
	}
	
	/**
	 * Mover al Proximo indice
	 */
	public function movenext(){
		$this->_index++;
		if($this->_index >= $this->Recordcount) $this->EOF = true;
	}
}