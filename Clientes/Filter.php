<?php
/**
 * 
 * Clase de entidad de Filtro
 * @author walter
 * @package Entidad
 *
 */
class Filter {
	/**
	 * 
	 * @var String
	 */
	public $Campo;
	/**
	 * 
	 * @var String
	 */
	public $Operador;
	/**
	 * 
	 * @var String
	 */
	public $Valor;
	/**
	 * 
	 * @var String
	 */
	public $Concatenador = 'AND';
	
	public function __construct($campo,$operador,$valor,$concatenador = 'AND'){
		$this->Campo = (string) $campo;
		$this->Operador = (string) $operador;
		$this->Valor = (string) $valor;
		$this->Concatenador = (string) $concatenador;
	}
}

?>