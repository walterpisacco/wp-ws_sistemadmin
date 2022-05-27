<?php
//require_once ('Dependencia.php');
require_once ('Aplicacion.php');

/**
 * Clase de Entidad de Rol
 * 
 * @author walter
 * @version 1.0
 * @created 08-jul-2013 12:31:16 p.m.
 * @package Entidad
 */
class Rol
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
	public $Contexto = 0;
	/**
	 * 
	 * @var Integer
	 */
	public $Total_Rows;
	/**
	 * 
	 * @var Aplicacion
	 */
	public $Aplicacion;
	/**
	 * @var Rol_Nodo[]
	 */
	public  $Nodos;
}
class Default_Rol extends Rol
{}


?>