<?php
/**
 * 
 * Clase para editar los templates de los mails que se envian
 * @author walter
 *
 */
class Email_Template {

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
	 * @var String
	 */
	public $Asunto;
	/**
	 * 
	 * @var String
	 */
	public $Template;
	/**
	 * 
	 * @var Array
	 */
	public $Objetos = Array('$Usuario_Nombre$'=>'Nombre','$Usuario_Apellido$'=>'Apellido','$Usuario_Mail$'=>'Mail','$Usuario_Legajo$'=>'Legajo','$Url_Hash$'=>'Url-Hash','$Aplicacion_Nombre$'=>'Nombre','$Aplicacion_Descripcion$'=>'Descripcion','$Registro_Estado$'=>'Estado','$Registro_Id$'=>'Codigo');
	/**
	 * @var String
	 */
	public $Error;
}

?>