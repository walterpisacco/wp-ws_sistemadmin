<?php
/*
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
*/
set_include_path(get_include_path().PATH_SEPARATOR.'C:\Trabajos\Web;C:\Trabajos\Web\LibJs;C:\Trabajos\Web\LibJs\Zend\;C:\Trabajos\Web\Lib\SSRSReport\bin;C:\Trabajos\Web\Lib\SSRSReport');


define('HOSTDB','DESKTOP-M8OCNVV');
define('HOSTDBREPLICA','DESKTOP-M8OCNVV');
define("NAMEDB",'PM_SEGURIDAD');
define('USER','sa');
define('PASS','pintame');

 /** Constante del path físico **/
define('PATHROOT','C:\Trabajos\Web\229_wssistemadmin');
//define('PATHROOT','e:\sitioweb\wwwroot\wssistemadmin');

/** Clases Auxiliares **/
define('PATHCLASS',PATHROOT.'\class');

/** ID APP **/
define('IDAPP',34);

/** Definir el Nivel de Log de la Aplicacion 
 * 1.- Registra errores de Debug y Errores de PHP (Desarrollo)
 * 2.- No registra errores (Produccion);  
 */
define('DEBUG',2);

/** WSURI **/
define('WSURI','http://localhost/229_wssistemadmin/server.php');

/** WSDL **/
define('WSDL','http://localhost/229_wssistemadmin/server.php?wsdl');

/** HOST **/
define('HOSTAUTOGESTION','https://10.20.1.218/AutogestionUsuario/');
define('HOSTAUTOGESTIONENDPOINT',HOSTAUTOGESTION.'/index.php?modulo=autogestion&accion=validate&ajax=false&hash=');

/** SMTP **/
define('SMTP','mr.fibercorp.com.ar');

/** USUARIO MAIL **/
define('USUARIOMAIL','sistemas_resp_auto@policiadelaciudad.gob.ar');

/** PASSWORD MAIL **/
define('PASSWORDMAIL','Abc123');

require 'Zend/Loader/Autoloader.php';

$autoloader = Zend_Loader_Autoloader::getInstance();

?>