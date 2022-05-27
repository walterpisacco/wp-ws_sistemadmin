<?php
require_once 'Destino.php';
/**
 * Clase de entidad Persona
 * 
 * @author walter
 * @version 1.0
 * @created 08-jul-2013 12:26:36 p.m.
 * @package Entidad
 */
class Personal
{

	/**
	 * @var Integer
	 */
	public $Legajo;
	/**
	 * @var String
	 */
	public $Nombre;
	/**
	 * @var String
	 */
	public $Apellido;
	/**
	 * @var Destino
	 */
	public $Destino;
	/**
	 * @var String
	 */
	public $Grado;
	/**
	 * 
	 * @var String
	 */
	public $Mail;
	/**
	 * 
	 * @var String
	 */
	public $Interno;
	/**
	 * 
	 * @var Aplicacion
	 */
	public $Aplicacion;
	/**
	 * @var String
	 */
	public $Documento;
	/**
	 * @var String
	 */
	public $CUIL;

}
?>