<?php
require_once ('Componente.php');
require_once ('Aplicacion.php');

/**
 * Clase de entidad de Grupo
 * 
 * @author walter
 * @version 1.0
 * @created 08-jul-2013 03:24:24 p.m.
 * @package Entidad
 */
class Grupo
{
	/**
	 * 
	 * @var Integer
	 */
	public $Id;
	/**
	 * 
	 * @var String
	 */
	public $Nombre;
	/**
	 * 
	 * @var String
	 */
	public $Habilitado;
	/**
	 * 
	 * @var String
	 */
	public $Error;
	/**
	 * 
	 * @var Integer
	 */
	public $Total_Rows;
	/**
	 * 
	 * @var Componente[]
	 */
	public $Componentes;
	/**
	 * 
	 * @var Aplicacion
	 */
	public $Aplicacion;
	/**
	 *
	 * @var String
	 */
	public $Tipo = 'N';
	
}
?>