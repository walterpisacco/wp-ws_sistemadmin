<?php
/**
 * @author walter
 * @version 1.0
 * @created 17-dic-2014 01:01:47 p.m.
 * @package Entidad
 */
class Error1
{
	/**
	 * 
	 * Constructor
	 * @param Boolean $sucess
	 * @param String $text
	 */
	public function Error($sucess = TRUE,$text){
		$this->Sucess = (boolean) $sucess;
		$this->Text = (string) $text;
	}
	/**
	 * @var Boolean
	 */
	public $Sucess;
	/**
	 * @var String
	 */
	public $Text;
}
?>