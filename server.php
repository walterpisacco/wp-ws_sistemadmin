<?php
require_once ('configini.php');
require_once('ClienteDAO.php');

// Si en la url está presente la entrada ?wsdl
if (strtolower ( $_SERVER ['QUERY_STRING'] ) == "wsdl") {
	require_once ('Zend/Soap/AutoDiscover.php');
	$wsdl = new Zend_Soap_AutoDiscover ('Zend_Soap_Wsdl_Strategy_ArrayOfTypeComplex');
	$wsdl->setClass ( 'ClienteDAO' );
	$wsdl->handle ();
} // Si no
else {
	require_once ('Zend/Soap/Server.php');
	$server = new Zend_Soap_Server (WSDL);
	$server->setClass ( 'ClienteDAO' );
	$server->setClassmap(Array('Aplicacion'=>'Aplicacion',
						   		 'Clase_Componente'=>'Clase_Componente',
								 'Icono'=>'Icono',
						   		 'Componente'=>'Componente',
								 'Destino'=>'Destino',
								 'Existe'=>'Existe',
								 'Fuente'=>'Fuente',
								 'Grupo'=>'Grupo',
								 'Personal'=>'Personal',
								 'Rol'=>'Rol',
								 'Tipo_Componente'=>'Tipo_Componente',
								 'Usuario' => 'Usuario',
								 'Filter' => 'Filter',
								 'Rol_Nodo' => 'Rol_Nodo',
								 'Registro' => 'Registro',
								 'Registro_Estado' => 'Registro_Estado',
								 'Email_Template' =>'Email_Template',
			                     'Preguntas' =>'Preguntas',
								 'ComponenteExtendido'=>'ComponenteExtendido',
								 'Rol_NodoExtendido'=>'Rol_NodoExtendido',
								 'DestinoExtendido'=>'DestinoExtendido',
								 'Default_Rol'=>'Default_Rol'
								));
	$server->setObject(new ClienteDAO());
	$server->handle ();
}