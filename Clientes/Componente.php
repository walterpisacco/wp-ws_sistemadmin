<?php
require_once ('Clase_Componente.php');
require_once ('Aplicacion.php');

/**
 * Clase de entidad de Componente
 * 
 * @author walter
 * @version 1.0
 * @created 08-jul-2013 12:51:40 p.m.
 * @package Entidad
 */
class Componente
{

	/**
	 * @var Integer
	 */
	public $Id;
	/**
	 * @var ComponenteExtendido
	 */
	public $Padre;
	/**
	 * @var String
	 */
	public $Codigo;
	/**
	 * @var String
	 */
	public $Texto;
	/**
	 * @var String
	 */
	public $Url;
	/**
	 * @var Clase_Componente
	 */
	public $Clase;
	/**
	 * @var Integer
	 */
	public $Nivel;
	/**
	 * @var String
	 */
	public $Habilitado;
	/**
	 * @var Integer
	 */
	public $Orden;
	/**
	 * @var Object
	 */
	public $Aplicacion;
	/**
	 * @var String
	 */
	public $Error;
	/**
	 * @var Integer
	 */
	public $Check;
	/**
	 * @var Integer
	 */
	public $Total_Rows;
	/**
	 * @var String
	 */
	public $Icono;
}

class ComponenteExtendido extends Componente
{}

?>