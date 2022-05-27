<?php

class Registro {

	/**
	 * 
	 * @var Integer
	 */
	public $Id;
	/**
	 * 
	 * @var Usuario
	 */
	public $Usuario;
	/**
	 * 
	 * @var Aplicacion
	 */
	public $Aplicacion;
	/**
	 * 
	 * @var String
	 */
	public $Fecha;
	/**
	 * 
	 * @var Registro_Estado
	 */
	public $Estado;
	/**
	 * 
	 * @var Integer
	 */
	public $Validado;
	
	/**
	 * 
	 * @var String
	 */
	public $Error;
	
	/**
	 * 
	 * @var Integer
	 */
	public $Resolicitado = 0;
	
	/**
	 * 
	 * @var Integer
	 */
	public $IsUserNew = 1;
	
	/**
	 * 
	 * @var Integer
	 */
	public $Total_Rows;
}

?>