<?php
/**
 * Clase de entidad de Destino
 * 
 * @author walter
 * @version 1.0
 * @created 08-jul-2013 12:36:07 p.m.
 * @package Entidad
 */
class Destino
{

	/**
	 * @var Integer
	 */
	public $Id;
	/**
	 * @var String
	 */
	public $Nombre;
	/**
	 * 
	 * @var DestinoExtendido
	 */
	public $Padre;
	/**
	 * @var String
	 */
	public $Zona;	
}
class DestinoExtendido extends Destino
{}


?>