<?php
require_once ('Personal.php');
require_once ('Rol.php');
require_once ('Grupo.php');
//require_once ('Session.php');
require_once ('Aplicacion.php');
require_once ('Tipo_Usuario.php');

/**
 * Clase de entidad Usuario
 * 
 * @author pablo
 * @version 1.0
 * @created 08-jul-2013 12:26:07 p.m.
 * @package Entidad
 */
class Usuario extends Personal
{
	/**
	 * @var Integer
	 */
	public $Id;
	/**
	 * @var Rol[]
	 */
	public $Roles;
	/**
	 * @var Grupo[]
	 */
	public $Grupos;
	/**
	 * @var Integer
	 */
	public $Habilitado;
	/**
	 * @var Aplicacion[]
	 */
	public $Administracion;
	/**
	 * @var Integer
	 */
	public $Forzar_Cambio;
	/**
	 * @var Session
	 */
	//public $Session;
	/**
	 * @var String
	 */
	public $Error;
	/**
	 * @var Integer
	 */
	public $Total_Rows;
	/**
	 * @var Integer
	 */
	public $Externo;
	/**
	 * @var Tipo_Usuario
	 */
	public $Tipo;
	/**
	 * @var String
	 */
	public $Nickname;
	/**
	 * @var Destino
	 */
	public $Dependencia_Operativa;
	/**
	 * @var Destino[]
	 */
	public $DependenciasGrupos;
	/**
	 * @var Boolean
	 */
	public $SwitchDependencia = false;
	/**
	 * @var String
	 */
	public $Foto;
	/**
	 * @var Boolean
	 */
	public $Notificacion;
	/**
	 * @var String
	 */
	public $Creado;
	/**
	 * @var String
	 */
	public $Creador;
	/**
	 * @var Integer
	 */
	public $Bloqueado;
	/**
	 * @var Integer
	 */
	public $BloqueoSession;
}
?>