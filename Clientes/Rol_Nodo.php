<?php
require_once ('Destino.php');

/**
 * Clase de entidad de Rol Nodo
 * 
 * @author walter
 * @version 1.0
 * @created 12-jul-2013 11:05:26 a.m.
 * @package Entidad
 */
class Rol_Nodo
{
	/**
	 * 
	 * @var Integer
	 */
	public $Id;
	/**
	 * @var Rol_NodoExtendido
	 */
	public $Padre;
	/**
	 * 
	 * @var Integer
	 */
	public $Check;
	/**
	 * 
	 * @var Destino
	 */
	public $Destino;
	/**
	 * 
	 * @var Integer
	 */
	public $Nivel;
}

class Rol_NodoExtendido extends Rol_Nodo
{}

?>