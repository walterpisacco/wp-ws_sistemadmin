<?php
require_once ('Personal.php');
require_once ('Fuente.php');
require_once ('Destino.php');

/**
 * Clase de entidad de Aplicacion
 * 
 * @author walter
 * @version 1.0
 * @created 08-jul-2013 12:39:42 p.m.
 * @package Entidad
 */
class Aplicacion
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
	public $Descripcion;
	/**
	 * 
	 * @var Personal
	 */
	public $Responsable;
	/**
	 * 
	 * @var Fuente
	 */
	public $Fuente;
	/**
	 * 
	 * @var String
	 */
	public $Ubicacion;
	/**
	 * 
	 * @var String
	 */
	public $Tipo;
	/**
	 * 
	 * @var String
	 */
	public $Publica;
	/**
	 * 
	 * @var Destino[]
	 */
	public $Destinos;
	/**
	 * @var Integer 
	 */
	public $Autogestionada;
	/**
	 * @var Integer 
	 */
	public $ValidaMail;
	/**
	 * @var Integer 
	 */
	public $Externa;
	/**
	 * @var Integer
	 */
	public $Default;
	/**
	 * @var Grupo
	 */
	public $Default_Perfil;
	/**
	 * @var Rol
	 */
	public $Default_Rol;
	/**
	 * @var String
	 */
	public $Error;
	/**
	 * 
	 * @var Integer
	 */
	public $Total_Rows;

}
?>