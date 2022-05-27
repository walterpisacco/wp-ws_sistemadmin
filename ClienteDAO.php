<?php
require_once ('configini.php');
//require_once('conectar.php');
require_once 'conectar_sqlsrv.php';
require_once('cliente.php');
class ClienteDAO {	
	/**
	 * 
	 * Obtener el Menu del Usuario
	 * 
	 * @param Usuario
	 * 
	 * @return Componente[]
	 */
	public function getMenuUsuario(Usuario $usuario){
		try{
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);

			$iin = 0;
			$reg = $bd->ConsultaSelect('[PM_SEGURIDAD].dbo.spUsuMenu1 '.$usuario->Id.','.IDAPP);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oComponente = new Componente();
					$oComponente->Id = $reg->Fields(0)->value;
						$oPadre = new Componente();
						$oPadre->Id = $reg->Fields(1)->value; 
					$oComponente->Padre = $oPadre;
					$oComponente->Codigo = trim(utf8_encode($reg->Fields(2)->value));
					$oComponente->Texto = trim(utf8_encode($reg->Fields ( 3 )->value));
					$oComponente->Url = trim(utf8_encode($reg->Fields ( 4 )->value));
					$oComponente->Nivel = trim(utf8_encode($reg->Fields ( 5 )->value));
						$oClase = new Clase_Componente();
						$oClase->Id = 1;
						$oClase->Nombre = 'Menu';
							$oTipo = new Tipo_Componente();
							$oTipo->Id = 1;
							$oTipo->Nombre = 'W';
						$oClase->Tipo = $oTipo;
					$oComponente->Clase = $oClase;
					$rta[] = $oComponente;
					$reg->movenext ();
				}
			} else {
				$oComponente = new Componente ();
				$oComponente->Texto = 'No tiene Menu Asignado';
				$oComponente->Url = 'index.php?modulo=login&accion=logoff&ajax=true';
				$rta [] = $oComponente;
			}
		} catch ( Exception $e ) {
			$oComponente = new Componente();
			$oComponente->Error = $e->__toString();
			$rta [] = $oComponente;
		}
		return $rta;
	}
	/**
	 * 
	 * Obtener un listado de Menus acorde una Aplicacion dada
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Aplicacion $idapp
	 * @return Componente[]
	 */
	public function listarMenus($start = 1, $limit = 20, $sidx = 1, $sord = 'asc',Aplicacion $aplicacion){
		try{
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$iin = 0;
			$SQL.= 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY mn_id) as nRegistros,
					(SELECT COUNT(mn_id) FROM [MENU] AS TablaResultado WHERE mn_apli_id = '.$aplicacion->Id.')AS total_rows,
					mn_id,
					mn_Menu,
					mn_idpadre,
					mn_codigo,
					mn_pagina,
					mn_orden,
					mn_apli_id,
					mn_clase,
					mn_tipo,
					mn_hab 
					FROM [MENU] 
					WHERE mn_apli_id = '.$aplicacion->Id.')AS tblResult';
			
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= "ORDER BY $sidx $sord";
			
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oComponente = new Componente();
					$oComponente->Id = $reg->Fields(2)->value;
						$oPadre = new Componente();
						$oPadre->Id = $reg->Fields(4)->value; 
					$oComponente->Padre = $oPadre;
					$oComponente->Codigo = trim(utf8_encode($reg->Fields(5)->value));
					$oComponente->Texto = trim(utf8_encode($reg->Fields (3)->value));
					$oComponente->Url = trim(utf8_encode($reg->Fields (6)->value));
						$oClase = new Clase_Componente();
						$oClase->Id = $reg->Fields (9)->value;
						$oClase->Nombre = 'Menu';
							$oTipo = new Tipo_Componente();
							$oTipo->Id = $reg->Fields (10)->value;
							$oTipo->Nombre = 'W';
						$oClase->Tipo = $oTipo;
					$oComponente->Clase = $oClase;
					$oComponente->Habilitado = $reg->Fields (11)->value;
					$oComponente->Orden = $reg->Fields (7)->value;
					$oComponente->Total_Rows = $reg->Fields(1)->value;
					$rta[]=$oComponente;
					$reg->movenext ();
				}
			} else {
				$oComponente = new Componente();
				$oComponente->Error = 'true';
				$rta[] = $oComponente;
			}
		} catch ( Exception $e ) {
			$oComponente = new Componente();
			$oComponente->Error = $e->__toString();
			$rta[]=$oComponente;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo que devuelve un listado dependencias
	 * 
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * 
	 * @return Destino[]
	 */
	public function listarDestinos($start = 1, $limit = 20, $sidx = 1, $sord = 'asc') {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY A.org_id) as nRegistros,
					(SELECT COUNT(TablaResultado.org_id) FROM v_Organica AS TablaResultado)AS total_rows,
					A.org_id,A.org_padre_id,A.org_descripcion FROM v_Organica AS A 
					)AS tblResult ';
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= "ORDER BY $sidx $sord";
			
			$iin = 0;
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oDestino = new Destino();
					$oDestino->Id = $reg->Fields(2)->value;
					$oDestino->Nombre = trim(utf8_encode($reg->Fields(4)->value));
						$oPadre = new DestinoExtendido();
						$oPadre->Id = $reg->Fields(3)->value;
					$oDestino->Padre = $oPadre;
					$rta[] = $oDestino;
					$reg->movenext();
				}
			}else{
				$rta[] = null;
			}
		} catch (Exception $e) {
			$rta[] = $e->__toString(); 
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo que devuelve un listado de aplicaciones para un usuario determinado
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Usuario $usuario
	 * @param Filter[] Filters
	 * @return Aplicacion[]
	 */
	public function listarAplicaciones($start = 1, $limit = 20, $sidx = 1, $sord = 'asc',Usuario $usuario = null,$filters=null) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY A.Apli_Nombre) as nRegistros,
					(SELECT COUNT(TablaResultado.Apli_ID) FROM [PERS_APLICACIONES] AS TablaResultado';
					if(!is_null($usuario))$SQL.=' LEFT JOIN  PERS_APLICACIONES_ADMIN AS AD ON TablaResultado.Apli_ID = AD.Apli_ID';
					$SQL.=' WHERE GETDATE() = GETDATE()';
					if(!is_null($usuario))$SQL.= ' AND AD.idUsuario = '.$usuario->Id;
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
					$SQL.=')AS total_rows,
					A.[Apli_ID],A.[Apli_Nombre],A.[Apli_Desc],A.[Apli_Responsable],A.[Apli_Fuentes],A.[Apli_Ubicacion],
					A.[Apli_tipo],A.[Apli_publica],A.[Apli_AutogestionUsuarios],A.[Apli_ValidarMail],A.[Apli_Externa],A.[Apli_Default] FROM [PERS_APLICACIONES] AS A'; 
					if(!is_null($usuario))$SQL.=' LEFT JOIN  PERS_APLICACIONES_ADMIN AS AD ON A.Apli_ID = AD.Apli_ID';
					$SQL.=' WHERE GETDATE() = GETDATE()';
					if(!is_null($usuario))$SQL.= ' AND AD.idUsuario = '.$usuario->Id;
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
					$SQL.=')AS tblResult';
			
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= "ORDER BY $sidx $sord";
			
			$iin = 0;
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oAplicacion = new Aplicacion;
					$oAplicacion->Id = $reg->Fields(2)->value;
					$oAplicacion->Nombre = trim(utf8_encode($reg->Fields(3)->value));
					$oAplicacion->Descripcion = trim(utf8_encode($reg->Fields(4)->value));
						$oPersona = new Personal();
						$oPersona->Nombre = trim(utf8_encode($reg->Fields(5)->value));
					$oAplicacion->Responsable = $oPersona;
						$oFuente = new Fuente();
						$oFuente->Nombre = trim(utf8_encode($reg->Fields(6)->value));
					$oAplicacion->Fuente = $oFuente;
					$oAplicacion->Ubicacion = trim(utf8_encode($reg->Fields(7)->value));
					$oAplicacion->Tipo = $reg->Fields(8)->value;
					$oAplicacion->Destinos = $this->getDependenciasAplicacion($oAplicacion);
					$oAplicacion->Publica = $reg->Fields(9)->value;
					$oAplicacion->Autogestionada = $reg->Fields(10)->value; 
					$oAplicacion->ValidaMail = $reg->Fields(11)->value;
					$oAplicacion->Externa = $reg->Fields(12)->value;
					$oAplicacion->Default = (int) $reg->Fields(13)->value;
					$oAplicacion->Total_Rows = $reg->Fields(1)->value;
					$rta[] = $oAplicacion;
					$reg->movenext();
				}
			}else{
				$oAplicacion = new Aplicacion();
				$oAplicacion->Error='No se encuentran aplicaciones';
				$rta[] = $oAplicacion;
			}
		} catch (Exception $e) {
			$oAplicacion = new Aplicacion();
			$oAplicacion->Error=$e->__toString();
			$rta[] = $oAplicacion; 
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo que devuelve un listado de Tipos de Componentes
	 * 
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Filter[] $filters
	 * 
	 * @return Tipo_Componente[]
	 */
	public function listarTipoComponentes($start = 1, $limit = 20, $sidx = 1, $sord = 'asc', $filters) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL.= 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY [cm_id]) as nRegistros,
					(SELECT COUNT([cm_id]) FROM [TIPO_COMPONENTES] AS TablaResultado)AS total_rows,
					[cm_id],[cm_descripcion],[ico_id],[ico_nombre],[ico_path] FROM [TIPO_COMPONENTES]
					LEFT JOIN ICONO_COMPONENTES AS I ON ico_id = cm_icono
					WHERE GETDATE() = GETDATE()';
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}					
			$SQL.=')AS tblResult';
			
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			/** Filtros de Busqueda **/
            foreach ($filters as $search) {
             	$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));
            }
            			
			$SQL .= "ORDER BY $sidx $sord";
			
			$iin = 0;
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oComponente = new Tipo_Componente();
					$oComponente->Id = $reg->Fields(2)->value;
					$oComponente->Nombre = trim(utf8_encode($reg->Fields(3)->value));
						$oIcono = new Icono();
						$oIcono->Id = $reg->Fields(4)->value;
						$oIcono->Nombre = trim(utf8_encode($reg->Fields(5)->value));
						$oIcono->Path = trim(utf8_encode($reg->Fields(6)->value));
					$oComponente->Icono = $oIcono;
					$oComponente->Total_Rows = $reg->Fields(1)->value;
					$rta[] = $oComponente;
					$reg->movenext();
				}
			}else{
				$oComponente = new Tipo_Componente();
				$oComponente->Error = 'No se encuentran componentes';
				$rta[] = $oComponente;
			}
		} catch (Exception $e) {
			$oComponente = new Tipo_Componente();
			$oComponente->Error = $e->__toString();
			$rta[] = $oComponente;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de un Tipo de Componente
	 * 
	 * @param Tipo_Componente
	 * @return Existe
	 */
	public function saveComponente( Tipo_Componente $componente) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			if ($componente->Id == 0) {
				$SQL .= "INSERT INTO [TIPO_COMPONENTES]
          				 ([cm_descripcion],
          				  [cm_icono])
     					VALUES
           				('$componente->Nombre',
           				 ".$componente->Icono->Id.")";
				$bd->ConsultaSelect ( $SQL );
			} elseif (is_numeric ( $componente->Id ) && $componente->Id > 0) {
				$SQL .= "UPDATE [TIPO_COMPONENTES]
   						SET [cm_descripcion] = '$componente->Nombre',
   							[cm_icono] = ".$componente->Icono->Id."
 						WHERE [cm_id] = '$componente->Id'";
				$bd->ConsultaSelect ( $SQL );
			}
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = ($e->getCode() == -2147352567)? 'Ya existe el Componente':'Error!';
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para borrar Tipo Componente
	 * 
	 * @param Tipo_Componente $componente
	 * @return Existe
	 */
	public function deletComponente($componente){
		try {
			if (is_numeric ( $componente->Id ) && $componente->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL .= "DELETE FROM [TIPO_COMPONENTES]
 						WHERE [cm_id] = '$componente->Id'";
				$bd->ConsultaSelect($SQL);
				$rta = new Existe ();
				$rta->Existe = null;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			switch ($e->getCode()){
				Case '-2147352567':
					$msg = utf8_encode('Borre primero las asignaciones a aplicaciones.');
					break;
				default:
					$msg = $e->__toString();
					break;
			}
			$rta->Existe = $msg;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo que devuelve un listado de Roles 
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Filter[] $filters
	 * 
	 * @return Rol[]
	 */
	public function listarRoles($start = 1, $limit = 20, $sidx = 1, $sord = 'asc', $filters = null) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL.= 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY [Descripcion]) as nRegistros,
					(SELECT COUNT([id_roles]) FROM [ROLES] AS TablaResultado 
					WHERE GETDATE() = GETDATE() ';
					foreach ($filters as $search) {
						$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));
					}
					$SQL.= ')AS total_rows,
					[id_roles],
					[Descripcion],
					[rol_apli_id],
					[hab],
					[contexto]
					FROM [ROLES] 
					WHERE GETDATE() = GETDATE() ';
					foreach ($filters as $search) {
						$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));
					}
					$SQL.=')AS tblResult';
			
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit ";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= "ORDER BY $sidx $sord";
			
			$iin = 0;
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oRol = new Rol();
					$oRol->Id = $reg->Fields(2)->value;
					$oRol->Nombre = trim(utf8_encode($reg->Fields(3)->value));
						$oAplicacion = new  Aplicacion();
						$oAplicacion->Id = $reg->Fields(4)->value;
					$oRol->Aplicacion = $oAplicacion;
					$oRol->Habilitado = $reg->Fields(5)->value;
					$oRol->Contexto   = (int) $reg->Fields(6)->value;
					$oRol->Total_Rows = $reg->Fields(1)->value;
					$rta[] = $oRol;
					$reg->movenext();
				}
			}else{
				$oRol = new Rol();
				$oRol->Error ='No se encuentran roles';
				$rta[] = $oRol;
			}
		} catch (Exception $e) {
			$oRol = new Rol();
			$oRol = $e->__toString();
			$rta[] = $oRol;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Devuelve un listado de Dependencias en base a una Aplicacion
	 * @param Aplicacion $idapp
	 * @return Destino[]
	 */
	private function getDependenciasAplicacion(Aplicacion $aplicacion){
		try {
			$SQL = 'SELECT [id_Destino],
					   org_descripcion
					FROM [APL_DEST]
					INNER JOIN  [V_organica] ON [id_Destino] = org_id
					WHERE [apli_id] = '.$aplicacion->Id;
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$reg = $bd->ConsultaSelect ( $SQL );
			$rta = Array();
			if ($reg->Recordcount > 0) {
				while ( ! $reg->EOF ) {
					$oDestino = new Destino();
					$oDestino->Id = $reg->Fields(0)->value;
					$oDestino->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$rta[] = $oDestino;
					$reg->movenext ();
				}
			}else{
				$rta = null;
			}	
		} catch (Exception $e) {
			$rta = $e->__toString();
		}
		return $rta;
	}
	
	
	/**
	 * 
	 * Metodo que devuelve el detalle de la Aplicacion
	 * @param Aplicacion $aplicacion
	 * 
	 * @return Aplicacion
	 */
	public function getAplicacion( Aplicacion $aplicacion){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "SELECT TOP 1 [Apli_ID] as 'Id',
							[Apli_Nombre] as 'Aplicacion',
							[Apli_Desc] as 'Descripcion',
							[Apli_Responsable] as 'Responsable',
							[Apli_Fuentes] as 'Fuentes',
							[Apli_Ubicacion] as 'Ubicacion',
							[Apli_tipo] as 'Tipo',
							[Apli_AutogestionUsuarios] as 'Autogestion',
							[mgpo_id]  as 'Perfil',
							[id_roles] as 'Rol',
							[Apli_ValidarMail] as 'Validar_Mail',
      						[Apli_Externa] as 'Externa'
					 FROM [PERS_APLICACIONES] 
					 LEFT JOIN [DEFAULT_ROL_PERFIL] ON Apli_ID = def_apli_id
					 LEFT  JOIN [ROLES] ON def_rol_id = id_roles
					 LEFT  JOIN [MGRUPOS] ON def_perf_id = mgpo_id
					 WHERE [Apli_ID] = ".$aplicacion->Id;
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$rta = new Aplicacion();
					$rta->Id =$reg->Fields(0)->value;
					$rta->Nombre =trim(utf8_encode($reg->Fields(1)->value));
					$rta->Descripcion = trim(utf8_encode($reg->Fields(2)->value));
						$oPersona = new Personal();
						/** @todo para cambiar **/
						$oPersona->Nombre = $reg->Fields(3)->value;
					$rta->Responsable = $oPersona;
						$oFuente = new Fuente();
						$oFuente->Nombre = $reg->Fields(4)->value; 
					$rta->Fuente = $oFuente;
					$rta->Ubicacion = $reg->Fields(5)->value;
					$rta->Tipo = $reg->Fields(6)->value;
					$rta->Autogestionada = $reg->Fields(7)->value;
					$rta->ValidaMail = $reg->Fields(10)->value;
					$rta->Externa = $reg->Fields(11)->value;
						$oPerfil = new Grupo();
						$oPerfil->Id = $reg->Fields(8)->value;
					$rta->Default_Perfil = $oPerfil;
						$oRol = new Rol();
						$oRol->Id = $reg->Fields(9)->value;
					$rta->Default_Rol = $oRol;
					$reg->movenext();
				}
			}else{
				$rta = new Aplicacion();
				$rta->Error = 'No se encuentra la Aplicacion';
			}
		} catch (Exception $e) {
			$rta = new Aplicacion();
			$rta->Error = $e->__toString();
		}
		return $rta;
	}
	/**
	 * 
	 * Metodo para obtener los Grupos del Usuario
	 * @param Usuario $usuario
	 * @param Integer $idapp - Id de la Aplicacion
	 * @param Boolean $complete - Esta varible determina si el Grupo tambien sera completado con sus respectivos Compontentes
	 * @return Grupo[]
	 */
	private function getGrupos($usuario,$idapp,$complete = true){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT 	[mgpo_id],
							[mgpo_Grupo],
							[mgpo_hab],
							PERS_APLICACIONES.* 
					FROM [MGRUPOS] 
					INNER JOIN [MNG_PERS] ON [mgpo_id] = [mgu_mgpo_id]
					INNER JOIN PERS_APLICACIONES ON mgpo_apli_id = Apli_ID
					WHERE [mgpo_apli_id] ='.$idapp.' AND [idUsuario] = '.$usuario->Id;
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oGrupo = new Grupo();
					$oGrupo->Id = $reg->Fields(0)->value;
					$oGrupo->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$oGrupo->Habilitado = $reg->Fields(2)->value;
						$oAplicacion = new Aplicacion();
						$oAplicacion->Id = $reg->Fields(3)->value;
						$oAplicacion->Nombre = trim(utf8_encode($reg->Fields(4)->value));
						$oAplicacion->Descripcion = trim(utf8_encode($reg->Fields(5)->value));
							$oPersonal = new Personal();
							$oPersonal->Nombre = trim(utf8_encode($reg->Fields(6)->value));
						$oAplicacion->Responsable = $oPersonal;
							$oFuente = new Fuente();
							$oFuente->Nombre = trim(utf8_encode($reg->Fields(7)->value));
						$oAplicacion->Fuente = $oFuente;
						$oAplicacion->Ubicacion = trim(utf8_encode($reg->Fields(8)->value));
					$oGrupo->Aplicacion = $oAplicacion;
					if($complete)$oGrupo->Componentes = $this->getComponentesGrupo($oGrupo);
					$rta[] = $oGrupo;
					$reg->movenext();
				}
			}else{
				$oGrupo = new Grupo();
				$oGrupo->Error = 'true';
				$rta[] = $oGrupo;
			}
		} catch (Exception $e) {
				$oGrupo = new Grupo();
				$oGrupo->Error = $e->__toString();
				$rta[] = $oGrupo;
		}
		return $rta;

	}
	/**
	 * 
	 * Metodo para obtener los destinos del Rol
	 * @param Rol $rol
	 * 
	 * @return Rol_Nodo[]
	 */
	private function getDestinosRol($rol){
		try {
			$rta = Array();
			$nodos = $this->getNodosRol($rol);
			foreach($nodos as $nodo){
				if($nodo->Check == 1)$rta[] = $nodo;
			}
		} catch (Exception $e) {
			$rta[] = $e->__toString();
		}		
	return $rta;
	}
	
	/**
	 * 
	 * Metodo para obtener los Nodos de un Rol
	 * @param Rol $rol
	 * 
	 * @return Rol_Nodo[]
	 */
	public function getNodosRol($rol){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = '[dbo].[spArbolRol] '.(int)$rol->Id;
			$reg = $bd->ConsultaSelect ( $SQL );
			$rta = Array();
			if ($reg->Recordcount > 0) {
				while ( ! $reg->EOF ) {
					$oNodo = new Rol_Nodo();
					$oNodo->Id = $reg->Fields(1)->value;
						$oPadre = new Rol_NodoExtendido();
						//$oPadre = new Rol_Nodo();
						$oPadre->Id = $reg->Fields(2)->value;
					$oNodo->Padre = $oPadre;
					$oNodo->Check = $reg->Fields(6)->value;
						$oDestino = new Destino();
						$oDestino->Id = $reg->Fields(3)->value;
						$oDestino->Nombre = trim(utf8_encode($reg->Fields(0)->value));
					$oNodo->Destino = $oDestino;
					$oNodo->Nivel = $reg->Fields(4)->value;
					$rta[] = $oNodo;
					$reg->movenext ();
				}
			}else{
				$rta[] = null;
			}	
		} catch (Exception $e) {
			$rta[] = $e->__toString();
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para obtener los Roles del Usuario
	 * @param Usuario $usuario
	 * @param Integer $idapp - Id de la Aplicacion
	 * @param Boolean $complete - Esta varible determina si el Rol tambien sera completado con sus respectivos Nodos
	 * @return Rol[]
	 */
	private function getRoles($usuario,$idapp, $complete = true){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL= 'SELECT [id_roles],
						   [Descripcion],
						   [hab],
						   [rol_apli_id]
					FROM [ROLES]
					INNER JOIN [RGRU_PERS] ON [id_roles] = [rgru_rol_id]
					INNER JOIN PERS_APLICACIONES ON [rol_apli_id] = Apli_ID
					WHERE [rgru_idUsuario] = '.$usuario->Id.' AND [rol_apli_id] = '.$idapp;
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oRol = new Rol();
					$oRol->Id = $reg->Fields(0)->value;
					$oRol->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$oRol->Habilitado = trim(utf8_encode($reg->Fields(2)->value));
					if($complete) $oRol->Nodos = $this->getDestinosRol($oRol);
					$rta[] = $oRol;
					$reg->movenext();
				}
			}else{
				$oRol = new Rol();
				$oRol->Error ='No se encuentran roles';
				$rta[] = $oRol;
			}
		} catch (Exception $e) {
			$oRol = new Rol();
			$oRol->Error = $e->__toString();
			$rta[] = $oRol;
		}
		return $rta;
	}
	/**
	 * 
	 * Metodo para obtener los componentes del Grupo
	 * @param Grupo $grupo
	 * 
	 * @return Componente[]
	 */
	public function getComponentesGrupo($grupo){
		try{
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$iin = 0;
			$reg = $bd->ConsultaSelect('dbo.[spComponentesGrupo] '.$grupo->Aplicacion->Id.','.$grupo->Id);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oComponente = new Componente();
					$oComponente->Id = $reg->Fields(0)->value;
						$oPadre = new ComponenteExtendido();
						//$oPadre = new Componente();
						$oPadre->Id = $reg->Fields(1)->value; 
					$oComponente->Padre = $oPadre;
					$oComponente->Codigo = trim(utf8_encode($reg->Fields(2)->value));
					$oComponente->Texto = trim(utf8_encode($reg->Fields ( 3 )->value));
					$oComponente->Url = trim(utf8_encode($reg->Fields ( 4 )->value));
					$oComponente->Nivel = $reg->Fields ( 5 )->value;
						$oClase = new Clase_Componente();
						$oClase->Id = $reg->Fields ( 10 )->value;
						$oClase->Nombre = trim(utf8_encode($reg->Fields ( 9 )->value));  
							$oTipo = new Tipo_Componente();
							$oTipo->Id = $reg->Fields ( 8 )->value;
							$oTipo->Nombre = trim(utf8_encode($reg->Fields (7)->value));
							$icono = $this->getIconos($reg->Fields (13)->value);
							$oTipo->Icono = $icono[0]; 
						$oClase->Tipo = $oTipo;
					$oComponente->Clase = $oClase;
					$oComponente->Check = $reg->Fields ( 6 )->value;
					$oComponente->Habilitado = trim($reg->Fields(11)->value);
					$oComponente->Orden = $reg->Fields(12)->value;
					$oComponente->Icono = trim(utf8_encode($reg->Fields(14)->value));
					$rta[] = $oComponente;
					$reg->movenext ();
				}
			} else {
				$rta [] = null;
			}
		} catch ( Exception $e ) {
			$rta [] = $e->__toString();
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de una aplicacion
	 * 
	 * @param Aplicacion $app
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	public function saveAplicacion(Aplicacion $app, $usuario) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			if ($app->Id == 0) {
				$SQL .= "INSERT INTO [PERS_APLICACIONES]
           				([Apli_Nombre]
           				,[Apli_Desc]
           				,[Apli_Responsable]
           				,[Apli_Fuentes]
           				,[Apli_Ubicacion]
           				,[Apli_tipo]
           				,[Apli_publica]
           				,[Apli_AutogestionUsuarios]
           				,[Apli_ValidarMail]
      					,[Apli_Externa]
						,[Apli_Default])
     					VALUES
           				('".utf8_decode($app->Nombre)."'
           				,'".utf8_decode($app->Descripcion)."'
           				,'".utf8_decode($app->Responsable->Nombre)."'
           				,'".$app->Fuente->Nombre."'
           				,'$app->Ubicacion'
           				,'$app->Tipo'
           				,'$app->Publica'
           				,'$app->Autogestionada'
           				,'$app->ValidaMail'
           				,'$app->Externa'
						,'$app->Default')";
				$bd->ConsultaSelect ( $SQL );
				
				$SQLid = 'SELECT MAX([Apli_ID]) FROM [PERS_APLICACIONES]';
				
				$reg = $bd->ConsultaSelect ( $SQLid );
				if ($reg->Recordcount > 0) {
					while ( ! $reg->EOF ) {
						$app->Id = $reg->Fields (0)->value;
						$reg->movenext();
						$this->saveDestinosApp($app);
					}
					/** Lo habilitamos como Administrador por default de la aplicacion que dio de alta **/
					$SQL = 'INSERT INTO [PERS_APLICACIONES_ADMIN]
           						([idUsuario]
           						,[Apli_ID])
     						VALUES ('.$usuario->Id.','.$app->Id.')';
					$bd->ConsultaSelect($SQL);
				}
			} elseif (is_numeric ( $app->Id ) && $app->Id > 0) {
				$SQL = "UPDATE [PERS_APLICACIONES]
   						SET [Apli_Nombre] = '".utf8_decode($app->Nombre)."'
      						,[Apli_Desc] = '".utf8_decode($app->Descripcion)."'
      						,[Apli_Responsable] = '".utf8_decode($app->Responsable->Nombre)."'
      						,[Apli_Fuentes] = '".$app->Fuente->Nombre."'
      						,[Apli_Ubicacion] = '$app->Ubicacion'
      						,[Apli_tipo] = '$app->Tipo'
      						,[Apli_publica] = '$app->Publica'
      						,[Apli_AutogestionUsuarios] = '$app->Autogestionada'
      						,[Apli_ValidarMail] = '$app->ValidaMail'
      						,[Apli_Externa] = '$app->Externa'
      						,[Apli_Default] = '$app->Default'
 						WHERE [Apli_ID] = '$app->Id'";
				$bd->ConsultaSelect ( $SQL );
				/** Guardar Destinos **/
				$this->saveDestinosApp($app);
			}
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = ($e->getCode() == -2147352567)? 'Ya existe una Aplicacion con el mismo Nombre':'Error!';
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para insertar o update de Destinos de la Aplicacion
	 * 
	 * @param Aplicacion $app
	 */
	private function saveDestinosApp(Aplicacion $app){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$bd->ConsultaSelect ( "DELETE [APL_DEST] WHERE [apli_id] = '$app->Id'" );
			$SQLdep = 'INSERT INTO [APL_DEST]
           							([apli_id]
           							 ,[id_Destino])
     							  VALUES ';
			
			$count = count($app->Destinos);
			$i=1;
			foreach ($app->Destinos as $destino) {
           			$SQLdep.="($app->Id, $destino->Id)";
           			if($i < $count){
           				$SQLdep.=','; 
           				$i++;
           			}
			}
			$bd->ConsultaSelect ( $SQLdep );
		} catch (Exception $e) {
			/** No devuelve nada perdon **/
		}
		return;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de un Rol
	 * 
	 * @param Rol $rol
	 * 
	 * @return Existe
	 */
	public function saveRol(Rol $rol) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			if ($rol->Id == 0) {
				$SQL .= "INSERT INTO [ROLES]
          				 ([rol_apli_id]
           				 ,[Descripcion]
           				 ,[hab]
           				 ,[fechaAct]
           				 ,[fechacre]
						 ,[contexto])
     					VALUES
           				('".$rol->Aplicacion->Id."'
           				,'".utf8_decode($rol->Nombre)."'
           				,'$rol->Habilitado'
           				,GETDATE()
           				,GETDATE()
           				,".(int)$rol->Contexto.")";
				$bd->ConsultaSelect ( $SQL );
			} elseif (is_numeric ( $rol->Id ) && $rol->Id > 0) {
				$SQL .= "UPDATE [ROLES]
   						SET [rol_apli_id] = ".$rol->Aplicacion->Id."
      						,[Descripcion] = '".utf8_decode($rol->Nombre)."'
      						,[hab] = '$rol->Habilitado'
      						,[fechaAct] = GETDATE()
      						,[contexto] = ".(int)$rol->Contexto."
 						WHERE [id_roles] = '$rol->Id'";
				$bd->ConsultaSelect ( $SQL );
			}
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->__toString ();
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para borrar un Rol
	 * 
	 * @param Rol $rol
	 * 
	 * @return Existe
	 */
	public function deletRol(Rol $rol){
		try {
			if (is_numeric ( $rol->Id ) && $rol->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL .= "DELETE FROM [ROLES]
 						WHERE [id_roles] = '$rol->Id'";
				$bd->ConsultaSelect($SQL);
				$rta = new Existe ();
				$rta->Existe = null;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			switch ($e->getCode()){
				Case '-2147352567':
					$msg = utf8_encode('Borre primero los grupos y menus creados.');
					break;
				default:
					$msg = $e->__toString();
					break;
			}
			$rta->Existe = $msg;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para borrar Aplicacion
	 * 
	 * @param Aplicacion $app
	 * 
	 * @return Existe
	 */
	public function deletAplicacion(Aplicacion $app){
		try {
			if (is_numeric ( $app->Id ) && $app->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL .= "DELETE FROM [PERS_APLICACIONES] WHERE [Apli_ID] = '$app->Id'";
				$bd->ConsultaSelect($SQL);
				$rta = new Existe ();
				$rta->Existe = null;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			switch ($e->getCode()){
				Case '-2147352567':
					/** Para los otros conflictos de clave **/
					$msg='Borre primero los grupos y menus creados.';
					/** 
				 	 * Para que no tire conflicto primero hay que hacer que todos
				 	 * los usuarios dejen de "Administrar" esta aplicacion
				 	 **/
					if(strpos($e->getMessage(),'FK_PERS_APLICACIONES_ADMIN_PERS_APLICACIONES')!==false){
						try {
							$SQLAdmin .= "DELETE FROM [PERS_APLICACIONES_ADMIN] WHERE [Apli_ID] = '$app->Id'";
							$bd->ConsultaSelect($SQLAdmin);	
							$msg = null;
						} catch (Exception $e) {
							$msg = ' Error al borrar Adminnistradores.';
						}
						
					}
					break;
				default:
					$msg = $e->__toString();
					break;
			}
			$rta->Existe =  utf8_encode($msg);
		}
		return $rta;
	}
	/**
	 * 
	 * Metodo para obtener el listado de usuarios correspondientes a una Aplicacion
	 * 
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Filter[] $filters
	 * 
	 * @return Usuario[]
	 * 
	 * @todo Ver como se enganchan ahora
	 */
	public function listarUsuarios($start = 1, $limit = 20, $sidx = 1, $sord = 'asc ',$filters){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY U.idUsuario) as nRegistros,
					(SELECT COUNT(TablaResultado.idUsuario) FROM dbo.PERS_USUARIOS AS TablaResultado 
					LEFT JOIN V_AGRUPACION_MENUS AS M ON TablaResultado.idUsuario = M.idUsuario
					INNER JOIN dbo.PERS_APLICACIONES AS APP ON APP.Apli_ID = M.mgpo_apli_id
					LEFT JOIN dbo.USUARIOS_NOPM AS NU ON NU.idUsuario = TablaResultado.idUsuario
					LEFT JOIN dbo.[DEPENDENCIA_OPERATIVA] AS DOP ON DOP.IdUsuario = TablaResultado.idUsuario AND DOP.Aplicacion = APP.Apli_ID
					LEFT JOIN dbo.v_Organica AS ORG ON ORG.org_id = DOP.Dependencia
					WHERE GETDATE() = GETDATE()";
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
			$SQL.=")AS total_rows,
					U.idUsuario AS Id,
					U.Usu_Legajo, 
					U.usu_nom, 
					U.usu_ape,
					U.Usu_Mail, 
					U.Usu_TelInterno,
					APP.Apli_ID,
					APP.Apli_Nombre,
					USU_T.utp_id,
					USU_T.utp_nombre,
					NU.Usu_Nombre,
					NU.Usu_Apellido,
					U.usu_usuario,
					DOP.Dependencia as Dep_Operativa_Id,
					ORG.org_descripcion as Dep_Operativa_Nombre
					FROM dbo.PERS_USUARIOS AS U
					LEFT JOIN V_AGRUPACION_MENUS AS M ON U.idUsuario = M.idUsuario
					INNER JOIN dbo.PERS_APLICACIONES AS APP ON APP.Apli_ID = M.mgpo_apli_id
					INNER JOIN dbo.USU_TIPO AS USU_T ON USU_T.utp_id = U.usu_tipo_usuario
					LEFT JOIN dbo.USUARIOS_NOPM AS NU ON NU.idUsuario = U.idUsuario
					LEFT JOIN dbo.[DEPENDENCIA_OPERATIVA] AS DOP ON DOP.IdUsuario = U.idUsuario AND DOP.Aplicacion = APP.Apli_ID
					LEFT JOIN dbo.v_Organica AS ORG ON ORG.org_id = DOP.Dependencia
					WHERE GETDATE()=GETDATE()";
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
			$SQL.=") AS tblResult";
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= " ORDER BY $sidx $sord";
			
			$reg = $bd->ConsultaSelect($SQL);
			
			$rta = Array();
			if ($reg->Recordcount > 0){
				while (!$reg->EOF){
					$oUsuario= new Usuario();
					$oUsuario->Id = $reg->Fields(2)->value;
					$oUsuario->Legajo = trim($reg->Fields(3)->value);
					$oUsuario->Nombre = (is_null($reg->Fields(4)->value))? trim(utf8_encode($reg->Fields(12)->value)):trim(utf8_encode($reg->Fields(4)->value));
					$oUsuario->Apellido = (is_null($reg->Fields(5)->value))? trim(utf8_encode($reg->Fields(13)->value)):trim(utf8_encode($reg->Fields(5)->value));
					$oUsuario->Nickname = (string) trim(utf8_encode($reg->Fields(14)->value));
					$oUsuario->Mail = trim(utf8_encode($reg->Fields(6)->value));
					$oUsuario->Interno = trim(utf8_encode($reg->Fields(7)->value));
					$oUsuario->Roles = $this->getRoles($oUsuario,$reg->Fields(8)->value,false);
					$oUsuario->Grupos = $this->getGrupos($oUsuario,$reg->Fields(8)->value,false);
						$oApp = new Aplicacion();
						$oApp->Nombre = trim(utf8_encode($reg->Fields(9)));
					$oUsuario->Aplicacion = $oApp;
						$oTipo = new Tipo_Usuario();
						$oTipo->Id = (int) $reg->Fields(10)->value;
						$oTipo->Nombre = trim(utf8_encode($reg->Fields(11)->value));
					$oUsuario->Tipo = $oTipo;
						$oDestino = new Destino();
						$oDestino->Id = (int) $reg->Fields('Dep_Operativa_Id')->value;
						$oDestino->Nombre = trim(utf8_encode($reg->Fields('Dep_Operativa_Nombre')->value));
					$oUsuario->Dependencia_Operativa = $oDestino;
					$oUsuario->Total_Rows = $reg->Fields(1)->value;
					$rta[] = $oUsuario;
					$reg->movenext();
				}
			}else{
				$oUsuario= new Usuario();
				$oUsuario->Error = 'true';
				$rta[] = $oUsuario;
			}
		} catch (Exception $e) {
			$oUsuario= new Usuario();
			$oUsuario->Error = $e->__toString();
			//$oUsuario->Error = trim(utf8_encode($SQL));
			$rta[] = $oUsuario;
		}
		return $rta;
	}
	
	/**
	 * @todo ver como interactuan este metodo y el de mas arriba
	 * Metodo para obtener el listado de todos los usuarios
	 * 
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Filter[] $filters
	 * 
	 * @return Usuario[]
	 * @todo revisar esto
	 */
	public function listarUsuariosApps($start = 1, $limit = 20, $sidx = 1, $sord = 'asc',$filters){
		try {
		/*
		ini_set( 'memory_limit', '1024M' );
		set_time_limit(0);
		ini_set('max_execution_time', 1200);
		*/

		$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY U.IdUsuario) as nRegistros,
					(SELECT COUNT(TablaResultado.idUsuario) FROM dbo.PERS_USUARIOS AS TablaResultado
					INNER JOIN dbo.USU_TIPO AS USU_T ON USU_T.utp_id = TablaResultado.usu_tipo_usuario
					LEFT JOIN dbo.USUARIOS_NOPM AS NUDOS ON NUDOS.idUsuario = TablaResultado.idUsuario WHERE GETDATE() = GETDATE() AND usu_habilitado = 0';
		            foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
					$SQL.=')AS total_rows,
					U.idUsuario,
					U.Usu_Legajo Per_Legajo, 
					U.usu_nom Per_Nombre, 
					U.usu_ape Per_Apellido, 
					U.Usu_Mail, 
					U.Usu_TelInterno,
					NU.Usu_Nombre,
					NU.Usu_Apellido,
					U.usu_dni,
					U.usu_cuil,
					USU_T.utp_id,
					USU_T.utp_nombre,
					U.usu_usuario,
					U.usu_bloqueado,
					U.usu_habilitado,
					U.usu_bloqueo_session
					FROM dbo.PERS_USUARIOS AS U
					INNER JOIN dbo.USU_TIPO AS USU_T ON USU_T.utp_id = U.usu_tipo_usuario
					LEFT JOIN dbo.USUARIOS_NOPM AS NU ON NU.idUsuario = U.idUsuario
					WHERE GETDATE() = GETDATE() AND usu_habilitado = 0 ';
				    foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
       		$SQL.=') AS tblResult';
					if ($start != 0) {
						$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
					} else {
						$SQL .= " WHERE getdate()=getdate()";
					}
			$SQL .= " ORDER BY $sidx $sord";
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount > 0){
				while (!$reg->EOF){
					$oUsuario= new Usuario();
					$oUsuario->Id = $reg->Fields(2)->value;
					$oUsuario->Legajo = $reg->Fields(3)->value;
					$oUsuario->Nombre = (is_null($reg->Fields(4)->value))? trim(utf8_encode($reg->Fields(8)->value)):trim(utf8_encode($reg->Fields(4)->value));
					$oUsuario->Apellido = (is_null($reg->Fields(5)->value))? trim(utf8_encode($reg->Fields(9)->value)):trim(utf8_encode($reg->Fields(5)->value));
					$oUsuario->Nickname = (string) trim(utf8_encode($reg->Fields(14)->value));
					$oUsuario->Mail = trim(utf8_encode($reg->Fields(6)->value));
					$oUsuario->Interno = trim(utf8_encode($reg->Fields(7)->value));
						$oTipo = new Tipo_Usuario();
						$oTipo->Id = (int) $reg->Fields(12)->value;
						$oTipo->Nombre = trim(utf8_encode($reg->Fields(13)->value));
					$oUsuario->Tipo = $oTipo;
					$oUsuario->Total_Rows = $reg->Fields(1)->value;
					$oUsuario->Externo = (is_null($reg->Fields(5)->value))? 1:0;
					$oUsuario->Documento = $reg->Fields(10)->value;
					$oUsuario->CUIL = $reg->Fields(11)->value;
					$oUsuario->Bloqueado = (int) $reg->Fields(15)->value;
					$oUsuario->Habilitado = (int) $reg->Fields(16)->value;
					$oUsuario->BloqueoSession = (int) ($reg->Fields(17)->value < 3) ? 0 : 3;
					$rta[] = $oUsuario;
					$reg->movenext();
				}
			}else{
				$oUsuario= new Usuario();
				$oUsuario->Error = 'true';
				$rta[] = $oUsuario;
			}
		} catch (Exception $e) {
				$oUsuario= new Usuario();
				//$oUsuario->Error = $e->__toString();
				$oUsuario->Error = $SQL;
				$rta[] = $oUsuario;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para obtener un Usuario a partir de un legajo
	 * 
	 * @param String $legajo
	 * @param Integer $tipo - Default 1 (PM)
	 * 
	 * @return Usuario
	 */
	public function searchUsuario($legajo=NULL,$tipo=1){
		if($tipo == 3){
			return $this->searchUsuarioExterno($legajo);
		}else{
			return $this->searchUsuarioNoExterno($legajo,$tipo);			
		}
	}
	
	/**
	 * Buscar por un usuario que no sea del tipo externo
	 * 
	 * @param Integer $legajo
	 * @param Integer $tipo - Default 1 (PM)
	 * @return Usuario
	 */
	private function searchUsuarioNoExterno($legajo=NULL,$tipo=1){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL= 'SELECT VP.Per_Legajo as Per_Legajo,
									VP.Per_Nombre as Per_Nombre,
									VP.Per_Apellido as Per_Apellido,
									U.Usu_Mail as Usu_Mail,
									U.Usu_TelInterno as Usu_Interno,
									G.gr_desc as Per_Grado,
									D.Dep_IDGAP as Per_Destino_Id,
      								D.Dep_Desc as Per_Destino,
      								U.Usu_ForzarCambio as Usu_Forzar,
      								CAST(VP.Per_MAIL AS VARCHAR(200)) as Per_Mail,
      								U.idUsuario as Usu_Id,
      								VP.Per_Cuil as Per_Cuil,
      								VP.Per_Doc as Per_Doc,
      								VP.Per_Tipo as Per_Tipo,
					 				U.usu_usuario as Usu_usuario
      				FROM V_PERSONAL_UNION_PFA_PM AS VP
					LEFT JOIN  dbo.PERS_USUARIOS AS U ON CAST(VP.Per_Cuil AS VARCHAR) = CAST(U.usu_CUIL AS VARCHAR)
					LEFT JOIN [Tablas].[dbo].V_SIRHU_GRADOS G ON G.gr_id = VP.Per_Grado
  					LEFT JOIN [Tablas].[dbo].V_SIRHU_DEPENDENCIAS D ON D.Dep_IDSIRHU = VP.Per_Destino
					WHERE (VP.Per_Legajo = '.$legajo.' OR VP.Per_Cuil = CAST('.$legajo.' AS VARCHAR(11)) OR VP.Per_Doc = CAST('.$legajo.' AS VARCHAR(10))) 
					AND VP.Per_Tipo ='.$tipo;
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount > 0){
				while (!$reg->EOF){
					$rta= new Usuario();
					$rta->Id = $reg->Fields('Usu_Id')->value;
					$rta->Legajo = trim($reg->Fields('Per_Legajo')->value);
					$rta->Nombre =trim(utf8_encode($reg->Fields('Per_Nombre')->value));
					$rta->Apellido = trim(utf8_encode($reg->Fields('Per_Apellido')->value));
					$rta->Documento = trim(utf8_encode($reg->Fields('Per_Doc')->value));
					$rta->CUIL = trim(utf8_encode($reg->Fields('Per_Cuil')->value));
					/** Si no tiene mail en la tabla de usuarios usa el de SIRHU si tiene **/
					if(empty($reg->Fields('Usu_Mail')->value) || is_null($reg->Fields('Usu_Mail')->value)){
						$rta->Mail =  trim(utf8_encode($reg->Fields('Per_Mail')->value));
					}else{
						$rta->Mail =  trim(utf8_encode($reg->Fields('Usu_Mail')->value));
					}
					/** Hardcode de prueba **/
					//$rta->Mail = 'pabloalejandroruhl@gmail.com';
					$rta->Interno = trim(utf8_encode($reg->Fields('Usu_Interno')->value));
					$rta->Grado = trim(utf8_encode($reg->Fields('Per_Grado')->value));
						$destino = new Destino();
						$destino->Id = (int) $reg->Fields('Per_Destino_Id')->value;
						$destino->Nombre = trim(utf8_encode($reg->Fields('Per_Destino')->value));
					$rta->Destino = $destino;
					$rta->Forzar_Cambio = (int)$reg->Fields('Usu_Forzar')->value;
					$rta->Nickname = (string) $reg->Fields('Usu_usuario')->value;
					$reg->movenext();
				}
			}else{
				$rta = new Usuario();
				$rta->Error ='true';
			}
		} catch (Exception $e) {
			$rta= new Usuario();
			$rta->Error = 'true';
		}
		return $rta;
	}
	
	
	
	/**
	 * 
	 * Metodo para buscar un usuario ya sea Personal PM o Externo
	 * 
	 * @param String $param
	 * @return Usuario
	 */
	public function searchUsuarioIncluidoExterno($param){
		/** Primero vemos si es un usuario pm o no **/
		$usuario = $this->searchUsuario($param);
		if($usuario->Error != 'true'){
			return $usuario;
		}else{
			/** Si no es pm lo buscamos en los demas **/
			return $this->searchUsuarioExterno($param);	
		}
		
	}
	/**
	 * 
	 * Metodo para buscar solo usuarios externos
	 * @param String $cuil_dni
	 * @return Usuario
	 */
	private function searchUsuarioExterno($cuil_dni = null){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT	U.usu_dni as Usu_DNI, 
							U.usu_cuil as Usu_CUIL,
							NU.Usu_Nombre as Usu_Nombre, 
							NU.Usu_Apellido as Usu_Apellido,
							U.Usu_Mail as Usu_Mail, 
							U.Usu_TelInterno as Usu_Interno,
      						U.Usu_ForzarCambio as Usu_Forzar,
      						U.idUsuario as Usu_Id,
						    U.usu_usuario as Usu_usuario
					FROM  dbo.PERS_USUARIOS AS U
					INNER JOIN dbo.USUARIOS_NOPM AS NU ON NU.idUsuario = U.idUsuario
					WHERE U.usu_cuil = CAST('.$cuil_dni.' AS VARCHAR(11))  OR U.usu_dni = CAST('.$cuil_dni.' AS VARCHAR(11))';
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount > 0){
				while (!$reg->EOF){
					$rta= new Usuario();
					$rta->Id = $reg->Fields('Usu_Id')->value;
					$rta->Nombre =trim(utf8_encode($reg->Fields('Usu_Nombre')->value));
					$rta->Apellido = trim(utf8_encode($reg->Fields('Usu_Apellido')->value));
					$rta->Mail =  trim(utf8_encode($reg->Fields('Usu_Mail')->value));
					$rta->Interno = trim(utf8_encode($reg->Fields('Usu_Interno')->value));
						$destino = new Destino();
						$destino->Nombre = '';
					$rta->Destino = $destino;
					$rta->Forzar_Cambio = (int)$reg->Fields('Usu_Forzar')->value;
					$rta->Documento = $reg->Fields('Usu_DNI')->value;
					$rta->CUIL = $reg->Fields('Usu_CUIL')->value;
					$rta->Nickname = (string) trim(utf8_encode($reg->Fields('Usu_usuario')->value));
					$reg->movenext();
				}
			}else{
				$rta = new Usuario();
				$rta->Error ='true';
			}
		} catch (Exception $e) {
			$rta= new Usuario();
			$rta->Error = 'true';
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para listar grupos en base a una Aplicacion
	 * @param Integer $start
	 * @param Integer $limit
	 * @param Integer $sidx
	 * @param String $sord
	 * @param Filter[] $filters
	 * 
	 * @return Grupo[]
	 */
	public function listarGrupos($start = 1, $limit = 10, $sidx = 1, $sord = 'asc',$filters = null){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY [mgpo_id]) as nRegistros,
					(SELECT COUNT([mgpo_id]) FROM [MGRUPOS] AS TablaResultado 
					 WHERE GETDATE() = GETDATE() ';
					foreach ($filters as $search) {
						$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));
					}
					$SQL.=')AS total_rows,
					[mgpo_acc],
					[mgpo_hab],
					[mgpo_Grupo],
					[mgpo_apli_id],
					[mgpo_id],
					[mgpo_tipo]
					FROM [MGRUPOS] AS V 
					WHERE GETDATE() = GETDATE()';
					foreach ($filters as $search) {
						$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));
					}
					$SQL.=') AS tblResult';
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= " ORDER BY $sidx $sord";
			
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oGrupo = new Grupo();
					$oGrupo->Id = $reg->Fields(6)->value;
					/** @todo Ver esto **/
					$oGrupo->Nombre = stripcslashes(trim(utf8_encode($reg->Fields(4)->value)));
					$oGrupo->Habilitado = $reg->Fields(3)->value;
						$oAplicacion = new Aplicacion();
						$oAplicacion->Id = $reg->Fields(5)->value;
					$oGrupo->Aplicacion = $oAplicacion;
					$oGrupo->Tipo = (string) $reg->Fields(7)->value;
					$oGrupo->Total_Rows = $reg->Fields(1)->value;
					$rta[] = $oGrupo;
					$reg->movenext();
				}
			}else{
				$oGrupo = new Grupo();
				$oGrupo->Error = 'true';
				$rta[] = $oGrupo;
			}
		} catch (Exception $e) {
				$oGrupo = new Grupo();
				$oGrupo->Error = $e->__toString();
				$rta[] = $oGrupo;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de un Grupo
	 * 
	 * @param Grupo $grupo
	 * 
	 * @return Existe
	 */
	public function saveGrupo(Grupo $grupo) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			if ($grupo->Id == 0) {
				$SQL .= "INSERT INTO [MGRUPOS]
           				([mgpo_apli_id]
           				,[mgpo_Grupo]
           				,[mgpo_hab]
						,[mgpo_tipo])
     				VALUES
           				('".$grupo->Aplicacion->Id."'
           				,'".utf8_decode($grupo->Nombre)."'
           				,'$grupo->Habilitado'
						,'$grupo->Tipo')";
				$bd->ConsultaSelect ( $SQL );
			} elseif (is_numeric ( $grupo->Id ) && $grupo->Id > 0) {
				$SQL .= "UPDATE [MGRUPOS]
   							SET [mgpo_Grupo] = '".utf8_decode($grupo->Nombre)."'
      							,[mgpo_hab] = '$grupo->Habilitado'
      							,[mgpo_tipo] = '$grupo->Tipo'
 						WHERE [mgpo_id] = $grupo->Id ";
				$bd->ConsultaSelect ( $SQL );
			}
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = ($e->getCode() == '-2147352567') ? 'Nombre duplicado' : $e->getMessage();
		}
		return $rta;
	}
		
	/**
	 * 
	 * Metodo para borrar Grupo
	 * 
	 * @param Grupo $grupo
	 * 
	 * @return Existe
	 */
	public function deletGrupo(Grupo $grupo){
		try {
			if (is_numeric ( $grupo->Id ) && $grupo->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL .= "DELETE FROM [MGRUPOS]
 						WHERE [mgpo_id] = $grupo->Id";
				$bd->ConsultaSelect($SQL);
				$rta = new Existe ();
				$rta->Existe = null;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			switch ($e->getCode()){
				Case '-2147352567':
					$msg = utf8_encode('Borre primero los menús y usuarios asignados.');
					break;
				default:
					$msg = $e->__toString();
					break;
			}
			$rta->Existe = $msg;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de los Grupos a los que pertenece el Usuario
	 * 
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	public function saveGrupoUsuario(Usuario $usuario) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);			
			if(!empty($usuario->Grupos)){
				$SQLdelet = 'DELETE FROM [MNG_PERS]
							 WHERE [idUsuario] = '.$usuario->Id.' AND EXISTS (SELECT [mgpo_apli_id] FROM [MGRUPOS] WHERE  [mgpo_apli_id]= '.$usuario->Grupos[0]->Aplicacion->Id.' AND [mgpo_id] = [mgu_mgpo_id])';
								 
           		$bd->ConsultaSelect ( $SQLdelet );
           						
				$SQL = "INSERT INTO [MNG_PERS]
           				([mgu_mgpo_id]
           				,[mgu_legajo]
           				,[idUsuario])
     				VALUES ";
				$count = count($usuario->Grupos);
				$i=1;
				foreach ($usuario->Grupos as $grupo) {
           			$SQL.='('.$grupo->Id.'
           				,'.$usuario->Legajo.'
           				,'.$usuario->Id.')';
           			
           			if($i < $count){
           				$SQL.=','; 
           				$i++;
           			}
				}
				$bd->ConsultaSelect ($SQL);
			}
			
			if(!empty($usuario->Roles)){
				$SQLdelet = 'DELETE FROM [RGRU_PERS]
							 WHERE [rgru_idUsuario] = '.$usuario->Id.' AND EXISTS (SELECT [rol_apli_id] FROM [ROLES] WHERE [rol_apli_id] = '.$usuario->Roles[0]->Aplicacion->Id.' AND [id_roles] = [rgru_rol_id])';
								 
           		$bd->ConsultaSelect ( $SQLdelet );	
           					
				$SQL = "INSERT INTO [RGRU_PERS]
           					([rgru_legajo]
           					,[rgru_rol_id]
           					,[rgru_idUsuario])
     				    VALUES ";
				
				$count = count($usuario->Roles);
				$i=1;
				foreach ($usuario->Roles as $rol) {
           			$SQL.='('.$usuario->Legajo.'
           				,'.$rol->Id.'
           				,'.$usuario->Id.')';
           			if($i < $count){
           				$SQL.=','; 
           				$i++;
           			}
				}				
				$bd->ConsultaSelect ($SQL);
			}
			
			/** Crear Dependencia Operativa si corresponde **/
			if(!empty($usuario->Grupos) && !empty($usuario->Roles)){
				$this->saveDependenciaOperativa($usuario);
			}
			
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->__toString ();
			//$rta->Existe = $SQL;
		}
		return $rta;
	}
	
	/**
	 * Metodo para crear la dependencia operativa del Usuario
	 * 
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	private function saveDependenciaOperativa(Usuario $usuario){
		$rta = new Existe();
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'dbo.spAsignaDependenciaOperativa '.$usuario->Roles[0]->Aplicacion->Id.','.$usuario->Id.','.$usuario->Dependencia_Operativa->Id;
			$bd->ConsultaSelect ( $SQL );
		} catch (Exception $e) {
			$rta->Existe = $e->__toString ();
		}
		return $rta;
	}
	
	
	/**
	 * 
 	 * Metodo guardar el Tree de Menu Grupo
 	 * 
 	 * @param Grupo $grupo
 	 * 
 	 * @return Existe
	 */
	public function saveTreeMenuGrupo(Grupo $grupo) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQLdelet = 'DELETE FROM [MENU_GPO] WHERE [mngpo_mgpo_id] = ' . $grupo->Id;
			$bd->ConsultaSelect ( $SQLdelet );
			if(!empty($grupo->Componentes)){
				$SQL = 'INSERT INTO [MENU_GPO]
           				([mngpo_mn_id]
           				,[mngpo_mgpo_id]
           				,[gpo_hab])
     				VALUES ';
				$count = count($grupo->Componentes);
				$i=1;
				foreach ($grupo->Componentes as $componente) {
           				$SQL.="($componente->Id
           					,$grupo->Id
           					,'S')";
           			
           				if($i < $count){
           					$SQL.=','; 
           					$i++;
           				}
				}
				$bd->ConsultaSelect ( $SQL );
			}
			$rta = new Existe ();
			$rta->Existe = false;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = true;
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de un Usuario
	 * 
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	public function saveUsuario(Usuario $usuario) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			//$reg1 = $bd->ConsultaSelect("SELECT usu_CuiL from PERS_USUARIOS where idUsuario = ".$usuario->Id);
			$rta = new Existe ();
			$rta->Existe = null;
			if ($usuario->Id == 0) {			
				$SQL .= "INSERT INTO [PERS_USUARIOS]
           					([Usu_Legajo]
           					,[Usu_Password]
           					,[Usu_Mail]
           					,[Usu_TelInterno]
           					,[contrasena]
           					,[usu_logonWindows]
           					,[usu_fechaContrasena]
           					,[usu_dni]
           					,[idperson]
           					,[usu_CUIL]
           					,[usu_usuario]
           					,[usu_tipo_usuario]
           					,[usu_nom]
           					,[usu_ape])
     					VALUES
           					('$usuario->Legajo'
           					,NULL
           					,'". utf8_decode($usuario->Mail)."'
           					,'".utf8_decode($usuario->Interno)."'
           					,dbo.fnColocaClave('".$usuario->CUIL."')
           					,NULL
           					,GETDATE()
           					,'".$usuario->Documento."'
           					,(SELECT TOP 1 [id_person] FROM [PM_Seguridad].[dbo].[vwPersonalAll] where cuil = '".(string)$usuario->CUIL."')
           					,'".trim($usuario->CUIL)."'
           					,'".trim(strtolower(strtoupper((string)$usuario->Nickname)))."'
           					,'".(int) $usuario->Tipo->Id."'
           					,'".(string) trim(utf8_decode(strtoupper($usuario->Nombre)))."'
           					,'".(string) trim(utf8_decode(strtoupper($usuario->Apellido)))."')";
				$bd->ConsultaSelect ( $SQL );
				/** Necesitamos el id del registro **/
				$SQLSelect = "SELECT MAX(idUsuario) FROM [PERS_USUARIOS]";
				$reg = $bd->ConsultaSelect($SQLSelect);
				if ($reg->Recordcount >0){
					while (!$reg->EOF){
						$usuario->Id = $reg->Fields(0)->value;
						$reg->movenext ();
					}
				}
				if($usuario->Tipo->Id == 3) $this->saveUsuarioExterno($usuario);
				
				/** Agregar en Apps por Defaults **/
				if($usuario->Tipo->Id == 1) $this->addUsuarioDefaultsApps($usuario);
				
			} elseif (is_numeric ( $usuario->Id ) && $usuario->Id > 0) {
				$SQL = "UPDATE [PERS_USUARIOS]
   						SET [Usu_Mail] = '". utf8_decode($usuario->Mail)."'
      					   ,[Usu_TelInterno] = '".utf8_decode($usuario->Interno)."'
      					   ,[Usu_Legajo] = ".$usuario->Legajo."
      					   ,[usu_dni] = '".$usuario->Documento."'
      					   ,[Usu_Usuario] = '".trim(strtolower(strtoupper((string)$usuario->Nickname)))."'
      					   ,[usu_CUIL] = '".trim($usuario->CUIL)."'
      					   ,[usu_bloqueado] = ".(int) $usuario->Bloqueado."
      					   ,[usu_habilitado] = ".(int) $usuario->Habilitado."
      					   ,[usu_bloqueo_session] = ".(int) $usuario->BloqueoSession."
 						WHERE [idUsuario] = '$usuario->Id'";
				$bd->ConsultaSelect ( $SQL );
				if($usuario->Tipo->Id == 3) $this->saveUsuarioExterno($usuario);
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = ($e->getCode() == -2147352567)? 'Ya existe el Usuario':'Error!';
			//$rta->Existe = $e->getMessage();
		}
		return $rta;
	}
	
	/**
	 * Metodo para agregar al usuario a las aplicaciones que le son por default
	 * 
	 * @param Usuario $usuario
	 * @return Usuario
	 */
	private function addUsuarioDefaultsApps(Usuario $usuario){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "EXECUTE [spAddDefaultsApps] $usuario->Id,$usuario->Legajo";
			$reg = $bd->ConsultaSelect($SQL);
		} catch ( Exception $e ) {
			$usuario->Error = $e->getMessage();
		}
		return $usuario;
	}
		
	/**
	 * 
	 * Metodo para guardar o update de un Usuario Externo
	 * 
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	private function saveUsuarioExterno($usuario){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$this->deletUsuarioExterno($usuario);
			$SQLInsert = "INSERT INTO [USUARIOS_NOPM] 
						    ([idUsuario]
	        				,[Usu_Apellido]
           				  	,[Usu_Nombre])
     					  VALUES
           					(".$usuario->Id."
           					,'".(string) trim(utf8_decode(strtoupper($usuario->Apellido)))."'
           					,'".(string) trim(utf8_decode(strtoupper($usuario->Nombre)))."')";
			$bd->ConsultaSelect ( $SQLInsert );
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->getMessage();
		}
		return $rta;
	}
	/**
	 * 
	 * Metodo para guardar o update de un Usuario Externo
	 * 
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	private function deletUsuarioExterno($usuario){
		try {
			throw new Exception("No se pueden borrar usuarios hasta nuevo aviso", 1);			
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQLDelete = 'DELETE FROM [USUARIOS_NOPM] WHERE [idUsuario] = '.$usuario->Id;
			$bd->ConsultaSelect ( $SQLDelete );
			$rta = new Existe ();
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->getMessage();
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para borrar Usuario
	 * 
	 * @param Usuario $usuario
	 * 
	 * @return Existe
	 */
	public function deletUsuario(Usuario $usuario){
		try {
			throw new Exception("No se pueden borrar usuarios hasta nuevo aviso", 1);
			if (is_numeric ( $usuario->Id ) && $usuario->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL .= "DELETE FROM [PERS_USUARIOS]
 						WHERE [idUsuario] = '$usuario->Id'";
				$bd->ConsultaSelect($SQL);
				/** por si es externo **/
				$this->deletUsuarioExterno($usuario);
				$rta = new Existe ();
				$rta->Existe = null;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->getMessage();
		}
		return $rta;
	}
	
	/**
	 * 
 	 * Metodo guardar las Aplicaciones que Administra el Usuario
 	 * 
 	 * @param Usuario $usuario
 	 * 
 	 * @return Existe
	 */
	public function saveUsuarioAppsAdmin(Usuario $usuario) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQLdelet = 'DELETE FROM [PERS_APLICACIONES_ADMIN] WHERE [idUsuario] = ' . $usuario->Id;
			$bd->ConsultaSelect ( $SQLdelet );
				$SQL .= 'INSERT INTO [PERS_APLICACIONES_ADMIN]
           				([idUsuario]
           				,[Apli_ID])
     				VALUES ';
			$count = count($usuario->Administracion);
			$i=1;
			foreach ($usuario->Administracion as $aplicacion) {
           			$SQL.="($usuario->Id
           				,$aplicacion->Id)";
           			
           			if($i < $count){
           				$SQL.=','; 
           				$i++;
           			}
			}
			$bd->ConsultaSelect ( $SQL );
			$rta = new Existe ();
			$rta->Existe = false;
			//$rta->Existe = $SQL;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			//$rta->Existe = $SQL;
			$rta->Existe = true;
		}
		return $rta;
	}
	
	/**
	 * 
 	 * Metodo guardar el Tree del Rol
 	 * 
 	 * @param Rol $rol
 	 * 
 	 * @return Existe
	 */
	public function saveTreeRol($rol) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQLdelet = 'DELETE FROM [ROLE_DEST] WHERE [id_roles] = ' .$rol->Id;
			$bd->ConsultaSelect ( $SQLdelet );
			if(!empty($rol->Nodos)){
				$array_nodos = array_chunk($rol->Nodos,500);
				foreach($array_nodos as $items){
					$SQL = 'INSERT INTO [ROLE_DEST]
           				([id_roles]
      					,[id_destino])
     				VALUES ';
					$count = count($items);
					$i=1;
					foreach ($items as $nodo) {
	           			$SQL.= '('.$rol->Id.'
	           				  ,'.$nodo->Destino->Id.')';
	           			
	           			if($i < $count){
	           				$SQL.=','; 
	           				$i++;
	           			}
					}
					$bd->ConsultaSelect ( $SQL );	
				}
				
			}
			$rta = new Existe ();
			$rta->Existe = false;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			//$rta->Existe = $SQL;
			$rta->Existe = true;
		}
		return $rta;
	}
	
	
	/**
	 * 
	 * Metodo para borrar Grupo Usuario
	 * @param Usuario $usuario
	 * @param Aplicacion $aplicacion
	 * @return Existe
	 */
	public function deletGrupoUsuario(Usuario $usuario, Aplicacion $aplicacion){
		try {
			if (is_numeric ( $usuario->Id ) && $usuario->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				
				$SQLdelet = 'DELETE FROM [MNG_PERS] 
							 WHERE [idUsuario] = '.$usuario->Id.' 
							 AND [mgu_mgpo_id] IN (SELECT [mgpo_id] FROM [MGRUPOS] WHERE [mgpo_apli_id] = '.$aplicacion->Id.')';
				
				$SQLdeletroles = 'DELETE FROM [RGRU_PERS] 
								  WHERE [rgru_idUsuario] = '.$usuario->Id.' 
								  AND [rgru_rol_id] IN (SELECT [id_roles] FROM [ROLES] WHERE [rol_apli_id] = '.$aplicacion->Id.')';

           		$bd->ConsultaSelect ( $SQLdelet );
           		$bd->ConsultaSelect ( $SQLdeletroles );
				
				$rta = new Existe ();
				$rta->Existe = null;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->__toString();
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para guardar o update de un Componente
	 * 
	 * @param Componente $componente
	 * 
	 * @return Existe
	 */
	public function saveMenu(Componente $componente) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			if ($componente->Id == 0) {
				$SQL .= "INSERT INTO [MENU]
           					([mn_apli_id]
           					,[mn_idpadre]
           					,[mn_codigo]
           					,[mn_Menu]
           					,[mn_hab]
           					,[mn_pagina]
           					,[mn_orden]
           					,[mn_tipo]
           					,[mn_clase])
     					VALUES
           					('".$componente->Aplicacion->Id."'
           					,'".$componente->Padre->Id."'
           					,'".utf8_decode($componente->Codigo)."'
           					,'".utf8_decode($componente->Texto)."'
           					,'$componente->Habilitado'
           					,'".utf8_decode($componente->Url)."'
           					,'$componente->Orden'
           					,'".$componente->Clase->Tipo->Id."'
           					,'".$componente->Clase->Id."')";
				$bd->ConsultaSelect ( $SQL );
			} elseif (is_numeric ( $componente->Id ) && $componente->Id > 0) {
				$SQL .= "UPDATE [MENU]
   							 SET [mn_apli_id] = ".$componente->Aplicacion->Id."
      							,[mn_idpadre] = ".$componente->Padre->Id."
      							,[mn_codigo] = '".utf8_decode($componente->Codigo)."'
      							,[mn_Menu] = '".utf8_decode($componente->Texto)."'
     							,[mn_hab] = '$componente->Habilitado'
      							,[mn_pagina] = '".utf8_decode($componente->Url)."'
      							,[mn_orden] = $componente->Orden
      							,[mn_tipo] = '".$componente->Clase->Tipo->Id."'
      							,[mn_clase] = '".$componente->Clase->Id."'
 						WHERE [mn_id] = $componente->Id ";
				$bd->ConsultaSelect ( $SQL );
			}
			$rta = new Existe ();
			//$rta->Existe = $SQL;
			$rta->Existe = null;
		} catch ( Exception $e ) {
			$rta = new Existe ();
			$rta->Existe = $e->__toString ();
		}
		return $rta;
	}
	
	/**
	 * 
	 * Metodo para borrar un Componente
	 * 
	 * @param Componente $componente
	 * 
	 * @return Existe
	 */
	public function deletMenu(Componente $componente){
		try {
			if (is_numeric ( $componente->Id ) && $componente->Id > 0) {
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL .= "DELETE FROM [MENU]	WHERE [mn_id] = $componente->Id OR [mn_idpadre] = $componente->Id";
				$bd->ConsultaSelect($SQL);
				$rta = new Existe ();
				$rta->Existe = null;
				//$rta->Existe = $SQL;
			}else{
				$rta = new Existe ();
				$rta->Existe = 'Objeto Invalido';
				//$rta->Existe = $grupo_usuario;
			}
		} catch ( Exception $e ) {
			$rta = new Existe ();
			switch ($e->getCode()){
				Case '-2147352567':
					$msg = utf8_encode('Borre primero las asignaciones de este menu en el Grupo.');
					break;
				default:
					$msg = $e->__toString();
					break;
			}
			$rta->Existe = $msg;
			//$rta->Existe = $SQL;
		}
		return $rta;
	}
	/**
	 * 
	 * Devuelve un listado de iconos de componentes. Si no se pasa un Id de Icono devuelve
	 * todos los Iconos disponibles.
	 * 
	 * @access Tener en cuenta que la imagen del icono debe de estar en la carpeta img/iconos 
	 * 
	 * @param Integer $IdIcono - Default 0
	 * @return Icono[]
	 */
	public function getIconos($IdIcono = 0){
		try{
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL.= "SELECT [ico_id]
      					  ,[ico_nombre]
      					  ,[ico_path]
  					FROM [ICONO_COMPONENTES]";
  			if(is_numeric($IdIcono) && $IdIcono > 0) $SQL.= " WHERE [ico_id] = $IdIcono";
  			$SQL.= ' ORDER BY 2 ASC';
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oInc = New Icono();
					$oInc->Id = $reg->Fields(0)->value;
					$oInc->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$oInc->Path =   trim(utf8_encode($reg->Fields(2)->value));
					$iInc[] = $oInc;		
					$reg->movenext ();
				}
			} else {
				$oInc = New Icono();
				$oInc->Error = $SQL;
				$iInc [] = $oInc;
			}
		} catch ( Exception $e ) {
			$oInc = New Icono();
			$oInc->Error = utf8_encode($e->__toString());
			$iInc [] = $oInc;
		}
		return $iInc;
		
	}
	
	/**
	 * 
	 * Validar el Acceso del Usuario
	 * 
	 * @param string $usu - Legajo||CUIL||CUIT del Usuario
	 * @param String $clv - Password
	 * @param Integer $app [optional] - Id Aplicacion
	 * @param String $pc [optional] - Ip de la Pc desde la que se loguea
	 * 
	 * @todo Que vamos a hacer con los PFA
	 * 
	 * @return Usuario
	 */
	public function validarAcc($usu,$clv,$app = 0,$pc = 'localhost'){
		try {
			$dev = new Usuario();
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
//			if((int)$usu > 0 ){ // Usuario con LP o CUIL o CUIT
				$SQL = "[dbo].[spWvalacc1] 'I','".$usu."',";
				$SQL.= (is_numeric($app) && !is_null($app) && $app != 0) ? $app : IDAPP;
				$SQL.= ",0,'".$clv."','".$pc."','0','0'";
//			}else{
//				
//				$oError = $this->validarAccLDAP($usu, $clv); // Usuario con LDAP
//				if($oError->Sucess == true){
//					/** Tipo I||S||T **/
//					$SQL = "[dbo].[spWvalacc1] 'I','".$usu."',";
//					$SQL.= (is_numeric($app) && !is_null($app) && $app != 0) ? $app : IDAPP;
//					$SQL.= ",0,'".$clv."','".$pc."','0','1'";
//				}else{
//					throw new Exception((string)$oError->Text,1222);
//				}
//			}

			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount > 0){
				while(!$reg->EOF){
					if($reg->Fields('ok')->value == 'OK'){
						$dev->Error = 'Ok';
						$dev->Id = (int) $reg->Fields('idusuario')->value;
						$dev->Apellido = trim(utf8_encode($reg->Fields('per_apellido')->value));
						$dev->Nombre = trim(utf8_encode($reg->Fields('per_nombre')->value));
						$dev->Legajo = $reg->Fields('usu_legajo')->value;
						$dev->Mail = trim(utf8_encode($reg->Fields('usu_mail')->value));
						$dev->Documento = trim($reg->Fields('usu_dni')->value);
						$dev->CUIL = trim($reg->Fields('usu_cuil')->value);
						$dev->Forzar_Cambio = trim($reg->Fields('usu_forzarcambio')->value);
							$oDestino = new Destino();
							$oDestino->Id = (int) $reg->Fields('per_destino_id')->value;
							$oDestino->Nombre = trim(utf8_encode($reg->Fields('dep_desc')->value));
							$oDestino->Zona = trim(utf8_encode($reg->Fields('zona')->value));
						$dev->Destino = $oDestino;
						$dev->Roles = (is_numeric($app) && !is_null($app)) ? $this->getRoles($dev,$app) : $this->getRoles($dev,IDAPP);
						$dev->Grupos = (is_numeric($app) && !is_null($app))? $this->getGrupos($dev,$app) : $this->getGrupos($dev,IDAPP);
						$dev->Nickname = trim(utf8_encode($reg->Fields('usu_usuario')->value));
						$dev->Foto	= trim($reg->Fields('usu_foto'));
						$dev->DependenciasGrupos = $this->addDependenciasGrupos($dev->Destino->Id);
						$dev->Notificacion = ((int)$reg->Fields('okdesc')->value == 1) ? true : false;
					}else{
						$dev->Error = trim(utf8_encode($reg->Fields('okdesc')->value));
					}
					$reg->movenext();
				}
			}else{
				$dev->Error = ' El Usuario no Existe ';
			}
		} catch (Exception $e) {
			$dev->Error = ($e->getCode() == 1222) ? trim(utf8_encode($e->getMessage())) :' Error de Conexion ';
		}
		return $dev;
	}

	/**
	 * Darse por Notificado
	 *
	 * @param  Integer $idUsuario
	 * @return  Error
	 */
	public function setNotificacion($idUsuario){
		$oError = new Error();
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'EXECUTE [spDarsePorNotificado]'.(int) $idUsuario;
			$reg = $bd->ConsultaSelect($SQL);
			$oError->Sucess = true;
		} catch (Exception $e) {
			$oError->Sucess = false;
		}
		return $oError;
	}

	/**
	 * Funcion para obtener las Dependencias Agrupadas si tiene
	 * 
	 * @param  Integer $IdDestino
	 * 
	 * @return  Destino[]
	 */
	private function addDependenciasGrupos($IdDestino){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'EXECUTE [spDependenciasAgrupadas]'.(int) $IdDestino;
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount > 0){
				while(!$reg->EOF){
					$oDestino = new Destino();
					$oDestino->Id = (int) $reg->Fields('Id')->value;
					$oDestino->Nombre = trim(utf8_encode($reg->Fields('Descripcion')->value));
					$rta[] = $oDestino;
					$reg->movenext();
				}
			}else{
				$rta = array();
			}
		} catch (Exception $e) {
			$rta = array();
		}
		return $rta;
	}


	/**
	 * Metodo para setear una nueva Dependencia Operativa al Usuario
	 *
	 * @param Integer  $oUsu
	 * @param Integer  $idDependencia 
	 * @param Integer  $idApp
	 *
	 * @return  Existe
	 */
	public function setNuevaDependenciaOperativa($idUsuario,$idDependencia,$idApp){
		$rta = new Existe();
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = " EXECUTE dbo.spAsignaDependenciaOperativa $idApp,$idUsuario,$idDependencia";
			$reg = $bd->ConsultaSelect($SQL);
		} catch (Exception $e) {
			$rta->Existe = utf8_encode($e->getMessage());
		}
		return $rta;
	}
		
	/**
	 * Validar acceso mediante LDAP
	 * 
	 * @param string $usu - Legajo||CUIL||CUIT del Usuario
	 * @param String $clv - Password
	 * 
	 * @return Error
	 */
	private function validarAccLDAP($usu,$clv){
		$oError = new Error(false);
		try {
			$ldaprdn  = 'xx\\'.$usu;
			$ldappass = trim($clv);
			$ldapconn = ldap_connect("xx");
			if(!($ldapconn)) throw new Exception("No se puede conectar al Servidor LDAP.");
			
			$ldapbind = ldap_bind($ldapconn, $ldaprdn, $ldappass);
			if(!($ldapbind)){
				$oError->Text = trim(utf8_encode(ldap_err2str(ldap_errno($ldapconn))));
			}else{
				$oError->Sucess = true;
				$oError->Text = "Acceso Autorizado";
			}			
		} catch (Exception $e) {
			$oError->Text = trim(utf8_encode($e->getMessage()));
		}
		return $oError;
	}
	
	/**
	 * 
	 * Metodo para blanquear una password
	 * 
	 * @param Usuario $usuario - Usuario que se intena blanquear la clave
	 * @param Usuario $adminapp - Usuario logueado
	 * 
	 * @return Existe
	 */
	public function clearPassword(Usuario $usuario,$adminapp){
		$usuario = $this->restorePassword($usuario);
		$existe = new Existe();
		$existe->Existe = $usuario->Error;
		return $existe;
	}
	
	/**
     *
     * Metodo para restaurar el password de un Usuario
     * 
     * @param Usuario $usuario (Llenar el legajo y el mail)
     * 
     * @return Usuario
	 */
	public function restorePassword(Usuario $usuario){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = '[dbo].[spRestorePassword1] '.(int)$usuario->Id.','.(int)$usuario->Legajo;
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount > 0){
				while(!$reg->EOF){
					$usuario->Error = $reg->Fields(0)->value;
					$reg->movenext();
				}
			}
		} catch (Exception $e) {
			$usuario->Error = trim(utf8_encode($e->getMessage()));
		}
		return $usuario;
	}
	

	/**
	 *
	 * Metodo para reenviar el password de un Usuario
	 *
	 * @param String $usuario (Llenar el legajo)
	 * 
	 * @return Existe
	 * 
	 * @todo Verificar el store, modifique usuario->Id
	 */
	public function sendPassword($usuario){
		return $this->sendPasswordFromWS($usuario);
		//try {
		//	$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
		//	$SQL = "[dbo].[spSendPassword] 0,'".(string) $usuario."'";
		//	$reg = $bd->ConsultaSelect($SQL);
		//	$existe = new Existe();
		//	if ($reg->Recordcount > 0){
		//		while(!$reg->EOF){*/
					/** Modificado 16/06/2015 - Ahora el envio de mail se realize desde la base de datos **/
		//			$existe->Existe = trim(utf8_encode($reg->Fields(0)->value));
		//			$reg->movenext();
		//		}
		//	}
		//} catch (Exception $e) {
		//	$existe->Existe = trim(utf8_encode($e->getMessage()));
		//}
		//return $existe;
	}

	/**
	 *
	 * Metodo para reenviar el password de un Usuario pero desde aca
	 *
	 * @param String $usuario (Llenar el legajo)
	 * 
	 * @return Existe
	 */
	public function sendPasswordFromWS($usuario){
		try {

			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "[dbo].[spGETSendPassword] 0,'".(string) $usuario."'";
			$reg = $bd->ConsultaSelect($SQL);
			$existe = new Existe();
			if ($reg->Recordcount > 0){
				while(!$reg->EOF){
					$oUsuario = new Usuario();
					$oUsuario->Apellido = trim(utf8_encode($reg->Fields(0)->value));
					$oUsuario->Nombre   = trim(utf8_encode($reg->Fields(1)->value));
					$oUsuario->Mail     = $reg->Fields(3)->value;
					$password           = $reg->Fields(4)->value; 
					$existe->Existe     = $this->notifyRecuparacionPassword($oUsuario, $password)->Error;
					if(empty($existe->Existe)) $existe->Existe = 'Verifique en su correo: '.substr_replace($oUsuario->Mail, '****************',strrpos($oUsuario->Mail,'@',-1)+1).' la clave solicitada.';
					$reg->movenext();
				}
			}else{
				$existe->Existe = utf8_decode('Usted no posee correo electrónico declarado');
			}
		} catch (Exception $e) {
			$existe->Existe = trim(utf8_encode($e->getMessage()));
		}
		return $existe;
	}
	
	/**
	 * 
	 * Metodo para enviar un mail con la nueva clave generada para el usuario
	 * 
	 * @param Usuario $usuario
	 * @param String  $pass
	 */
	private function notifyNewPassword(Usuario $usuario,$pass){
		try {
// 				$config = array('auth' => 'login',
//                 'username' => USUARIOMAIL,
//                 'password' => PASSWORDMAIL);
// 			$transport = new Zend_Mail_Transport_Smtp(SMTP, $config);
			$transport = $this->getTransportEmail();
			$mail = new Zend_Mail();
			$template = $this->parseEmailTemplate($this->getEmailTemplate(2),$registro);
			$mail->setBodyHtml($template->Template);
			$mail->setBodyText('Password: '.$pass);
			$mail->setFrom(USUARIOMAIL, 'Autogestion de Usuarios');
			$mail->addTo($usuario->Mail, $usuario->Nombre);
			$mail->setSubject($template->Asunto);
			$mail->send($transport);
		} catch (Exception $e) {
		}
	}

	
    /**
     * Funcion para el cambio de password
     * 
     * @param String $old_cvl - Vieja password
     * @param String $new_cvl - Nueva password
     * @param Usuario $usuario - usuario logueado
     * 
     * @return Existe
     **/
     public function setNewPass($old_cvl, $new_cvl, $usuario)
     {
       	$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
       	$rta = new Existe ();
		$SQL = '[dbo].[spWactclv1] '.$usuario->Id . ',"'.$old_cvl.'","'.$new_cvl.'"';
		try {
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount >0){
				while(!$reg->EOF){
					$rta->Existe = $reg->Fields(0)->value;
					$reg->movenext();	
				}
			}
		} catch (Exception $e){
			$rta->Existe = trim(utf8_encode($e->getMessage()));
		}
		return $rta;
     }
     
     /**
      * 
      * Metodo para guardar una solicitud de registracion
      * @param Registro $registro
      * @return Registro
      */
     public function saveSolicitudRegistro(Registro $registro){
     	try {
			if (empty( $registro->Id ) || $registro->Id == 0) {
				if (empty($registro->Usuario->Interno)) {
					$registro->Usuario->Interno = '0000';
				} 
				$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
				$SQL = '[dbo].[spSolicitaPermisos] '.$registro->Usuario->Legajo.','.$registro->Aplicacion->Id.',"'.$registro->Usuario->Mail.'",'.$registro->Usuario->Interno.','.$registro->Resolicitado;
				$reg = $bd->ConsultaSelect($SQL);
				if ($reg->Recordcount >0){
					while(!$reg->EOF){
						$Hash = trim(utf8_encode($reg->Fields(0)->value));
						$registro->Error = trim(utf8_encode($reg->Fields(1)->value));
						$registro->Aplicacion = $this->getAplicacion($registro->Aplicacion);
							$oEstado = new Registro_Estado();
							$oEstado->Id = $reg->Fields(2)->value;
						$registro->Estado = $oEstado;
						$registro->Id = $reg->Fields(3)->value;
						/** Default Manda Hash **/
						if(!empty($Hash)) {
							$registro->Usuario = $this->searchUsuario($registro->Usuario->Legajo);
							$registro = $this->notifyHash($registro,$Hash);
						}
						$reg->movenext();	
					}
				}				
			}else{
				$registro->Error = 'Registro Incorrecto!';
			}
		} catch ( Exception $e ) {
			$registro->Error = trim(utf8_encode($e->getMessage()));
		}		
		return $registro;
     }
     
     /**
      * 
      * Metodo para obtener un listado de las solicitudes de registracion de nuevos usuarios
      * @param Integer $start
      * @param Integer $limit
      * @param Integer $sidx
      * @param String $sord
      * @param Aplicacion $Aplicacion
      * @param Filter[] $filters
      * 
      * @return Registro[]
      */
     public function listarRegistraciones($start = 1, $limit = 20, $sidx = 1, $sord = 'asc',Aplicacion $Aplicacion = null,$filters=null){
     	try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT * FROM (SELECT ROW_NUMBER() OVER (ORDER BY SPER_ID) as nRegistros,
					(SELECT COUNT(TablaResultado.SPER_ID) FROM [dbo].[SOLICITUD_PERMISOS] AS TablaResultado
						LEFT JOIN  [dbo].[SOLICITUD_ESTADOS] AS EDOS ON EDOS.[EST_ID] = TablaResultado.[SPER_ESTADO_SOLICITUD]
  						INNER JOIN  Tablas.dbo.V_SIRHU_PERSONAL AS VPDOS ON VPDOS.Per_Legajo = TablaResultado.SPER_LP
  						WHERE GETDATE() = GETDATE() AND SPER_APLI_ID ='.$Aplicacion->Id;
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
					$SQL.=")AS total_rows,
					[SPER_ID] as 'Id'
      				,[SPER_LP] as 'Legajo'
      				,VP.Per_Nombre as 'Nombre'
      				,VP.Per_Apellido as 'Apellido'
      				,[SPER_APLI_ID] as 'Aplicacion_Id'
      				,[SPER_CORREO] as 'Correo'
      				,[SPER_HASH] as 'Hash'
      				,CONVERT(CHAR(10),[SPER_FECHA_SOLICITUD],103) as 'Fecha_Solicitud'
      				,[EST_DESC] as 'Estado'
      				,[SPER_USUARIO_VALIDADO] as 'Validado'
      				,[SPER_LP_AUTORIZA] as 'Autorizado'
      				,[SPER_FECHA_RESPUESTA] as 'Respuesta'
      				,[EST_ID] AS 'Estado_Id'
  					FROM [PM_Seguridad].[dbo].[SOLICITUD_PERMISOS] AS R
  					LEFT JOIN  [dbo].[SOLICITUD_ESTADOS] AS E ON E.[EST_ID] = R.[SPER_ESTADO_SOLICITUD]
  					INNER JOIN  Tablas.dbo.V_SIRHU_PERSONAL AS VP ON VP.Per_Legajo = R.SPER_LP";
					$SQL.=' WHERE GETDATE() = GETDATE() AND [SPER_APLI_ID] = '.$Aplicacion->Id;
					/** Filtros de Busqueda **/
                	foreach ($filters as $search) {
                		$SQL .= ' '.$search->Concatenador.' '.$search->Campo.' '.$search->Operador.' '.trim(utf8_decode($search->Valor));	
                	}
					$SQL.=')AS tblResult';
			
			if ($start != 0) {
				$SQL .= " WHERE nRegistros BETWEEN $start AND $limit";
			} else {
				$SQL .= " WHERE getdate()=getdate()";
			}
			$SQL .= " ORDER BY $sidx $sord";
			$reg = $bd->ConsultaSelect($SQL);
			$rta = Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oRegistro = new Registro();
					$oRegistro->Id = $reg->Fields(2)->value;
						$oPersona = new Personal();
						$oPersona->Legajo = $reg->Fields(3)->value;
						$oPersona->Nombre = trim(utf8_encode($reg->Fields(4)->value));
						$oPersona->Apellido = trim(utf8_encode($reg->Fields(5)->value));
						$oPersona->Mail = trim(utf8_encode($reg->Fields(7)->value));
					$oRegistro->Usuario = $oPersona;
						$Aplicacion = $this->getAplicacion($Aplicacion);					
					$oRegistro->Aplicacion = $Aplicacion;
					$oRegistro->Fecha = $reg->Fields(9)->value;
						$oEstado = new Registro_Estado();
						$oEstado->Id = $reg->Fields(14)->value;
						$oEstado->Nombre = trim(utf8_encode($reg->Fields(10)->value));
					$oRegistro->Estado = $oEstado;
					$oRegistro->Validado = $reg->Fields(11)->value;
					$oRegistro->Total_Rows = $reg->Fields(1)->value;
					$rta[] = $oRegistro;
					$reg->movenext();
				}
			}else{
				$oRegistro = new Registro();
				$oRegistro->Error= true;
				$rta[] = $oRegistro;
			}
		} catch (Exception $e) {
			$oRegistro = new Registro();
			$oRegistro->Error= true;
			$rta[] = $oRegistro; 
		}
		return $rta;
     }
     
	/**
	 * 
	 * Metodo para guardar o update de los Perfiles y Roles de Default
	 * 
	 * @param Aplicacion $Aplicacion (Debe venir completado $Aplicacion->Default_Perfil, Default_Rol y Id)
	 * @return Aplicacion
	 */
	public function saveDefaultRolPerfil($Aplicacion) {
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQLDelete = 'DELETE FROM [dbo].[DEFAULT_ROL_PERFIL] WHERE [def_apli_id] = '.$Aplicacion->Id;
			$bd->ConsultaSelect ( $SQLDelete );
			$SQL .= 'INSERT INTO [dbo].[DEFAULT_ROL_PERFIL]
           				([def_apli_id]
           				,[def_rol_id]
           				,[def_perf_id])
     					VALUES
           				('.$Aplicacion->Id.'
           				,'.$Aplicacion->Default_Rol->Id.'
           				,'.$Aplicacion->Default_Perfil->Id.')';
			$bd->ConsultaSelect ( $SQL );
			$Aplicacion->Error = 'Se guardo con exito';
		} catch ( Exception $e ) {
			$Aplicacion->Error = trim(utf8_encode($e->__toString()));
		}
		return $Aplicacion;
	}
	
	/**
	 * 
	 * Metodo para enviar un mail con el Hash del usuario
	 * @param Registro $registro
	 * @param String $hash
	 * @return Registro
	 */
	private function notifyHash(Registro $registro,$hash){
		$transport = $this->getTransportEmail();
		$html = new Zend_View();
		$html->setScriptPath(PATHROOT . '/EmailTemplates/');
		$html->assign('toName',$registro->Usuario->Nombre.' '.$registro->Usuario->Apellido);
		$html->assign('urlEndPoint',HOSTAUTOGESTIONENDPOINT.$hash);
		$html->assign('idRegistro',$registro->Id);
		$parseTemplate = $html->render('AutogestionEnvioHash.phtml');
		$mail = new Zend_Mail();		
		$mail->setBodyHtml($parseTemplate);
		$mail->setFrom(USUARIOMAIL, 'Sistema de Autogestion de Usuarios');
		$mail->addTo($registro->Usuario->Mail, $registro->Usuario->Nombre);
		$mail->setSubject('Validación de Solicitud');
		try {
			$mail->send($transport);	
		} catch (Exception $e) {
		    $registro->Error = utf8_encode( $e->__toString());
		}
		return $registro;
	}
	/**
	 * 
	 * Funcion para parsear el template de email
	 * @param Email_Template $template
	 * @param Aplicacion $aplicacion
	 * @param Usuario $usuario
	 * @param String $hash
	 * @return Email_Template
	 */
		private function parseEmailTemplate (Email_Template $template,Registro $registro, $hash){
		$template->Objetos['$Usuario_Nombre$'] = $registro->Usuario->Nombre;
		$template->Objetos['$Usuario_Apellido$'] = $registro->Usuario->Apellido;
		$template->Objetos['$Usuario_Legajo$'] = $registro->Usuario->Legajo;
		$template->Objetos['$Usuario_Mail$'] = $registro->Usuario->Mail;
		$template->Objetos['$Url_Hash$'] = '<a href="'.HOSTAUTOGESTION.'/index.php?modulo=autogestion&accion=validate&ajax=false&hash='.$hash.'">'.HOSTAUTOGESTION.'/index.php?modulo=autogestion&accion=validate&ajax=false&hash='.$hash.'</a>';
		$template->Objetos['$Registro_Estado$'] = $registro->Estado->Nombre;
		$template->Objetos['$Aplicacion_Nombre$'] = $registro->Aplicacion->Nombre;
		$template->Objetos['$Registro_Id$'] = $registro->Id;
				
		if ($registro->IsUserNew == 0){
			$template->Objetos['$Aplicacion_Nombre$'] = $registro->Aplicacion->Nombre.' ('.$registro->Aplicacion->Ubicacion.')<br><br><b>Usuario:</b> su DNI. <br><br> Contraseña: '.substr($hash, 0, 4);
		}
		
		$template->Objetos['$Aplicacion_Descripcion$'] = $registro->Aplicacion->Descripcion;
		$template->Template = str_ireplace(array_keys($template->Objetos), array_values($template->Objetos),$template->Template);
		return $template;
	}
	
	/**
	 * 
	 * Metodo para validar un hash de un usuario
	 * @param String $hash
	 * @return Registro
	 */
	
	public function validateHash($hash){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "SELECT [SPER_ID]
      							,[SPER_LP]
      							,[SPER_APLI_ID]
      							,[SPER_CORREO]
      							,[SPER_HASH]
      							,[SPER_FECHA_SOLICITUD]
      							,[SPER_ESTADO_SOLICITUD]
      							,[SPER_USUARIO_VALIDADO]
      							,[SPER_LP_AUTORIZA]
      							,[SPER_FECHA_RESPUESTA]
  							FROM [dbo].[SOLICITUD_PERMISOS] 
  							WHERE [SPER_HASH] ='".trim(utf8_decode($hash))."'
  							 AND [SPER_USUARIO_VALIDADO] != 2
  							 AND [SPER_ESTADO_SOLICITUD] != 2";		
			$reg = $bd->ConsultaSelect($SQL);
			$oRegistro = new Registro();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oRegistro->Id = $reg->Fields(0)->value;
						$oUsuario = $this->searchUsuario($reg->Fields(1)->value);
					$oRegistro->Usuario = $oUsuario;
						$oAplicacion = new Aplicacion();
						$oAplicacion->Id = $reg->Fields(2)->value;
						$oAplicacion = $this->getAplicacion($oAplicacion);					
					$oRegistro->Aplicacion = $oAplicacion;
					$oRegistro->Fecha = $reg->Fields(4)->value;
					/** Asentamos que el hash coincidio y lo validamos**/
					//guardamos el valor anterior para saber si en el mail debe ir la nueva passrord
					    //$_SESSION['ValidadoAnt'] = $reg->Fields(7)->value;
						$oRegistro->IsUserNew = $reg->Fields(7)->value;
						$oRegistro->Validado = 2;
						$es_id = 1;
						if($oAplicacion->Autogestionada) $es_id = 3; 
							$oEstado = $this->getEstadosRegistro($es_id);
						$oRegistro->Estado = $oEstado;
						$this->updateEstadoSolicitudRegistracion($oRegistro);
					$reg->movenext();
				}
			}else{
				$oRegistro->Error=trim(utf8_encode('Ud. no tiene ninguna solicitud en curso.'));
			}
		} catch (Exception $e) {
			$oRegistro->Error= $e->__toString();
		}
		return $oRegistro;
	}
	
	/**
	 * 
	 * Metodo para cambiar el estado de la Solicitud
	 * 
	 * @param Registro $registro
	 * @return Registro
	 */
public function updateEstadoSolicitudRegistracion(Registro $registro){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
		
			$validado = ($registro->Estado->Id < 2)? $registro->Estado->Id:$registro->Validado;
			
			$SQL = "UPDATE [dbo].[SOLICITUD_PERMISOS]
						SET [SPER_ESTADO_SOLICITUD] = ".$registro->Estado->Id."
           					,[SPER_USUARIO_VALIDADO] = ".$validado."
           			  WHERE [SPER_ID] = ".$registro->Id;
			$bd->ConsultaSelect ( $SQL );
			if($registro->Estado->Id == 2 || $registro->Estado->Id == 3){
				/** Sacamos al usuario de la aplicacion **/
				$registro->Estado = $this->getEstadosRegistro($registro->Estado->Id);
				/** Mejorar esto no me gusta porque hay redundancia **/
				$registro->Aplicacion = $this->getAplicacion($registro->Aplicacion);
					$oUsuario = $this->searchUsuario($registro->Usuario->Legajo);
						$oPerfil = $registro->Aplicacion->Default_Perfil;
							$oPerfil->Aplicacion = $registro->Aplicacion;
						$oRol = $registro->Aplicacion->Default_Rol;
							$oRol->Aplicacion = $registro->Aplicacion;
							
					$oUsuario->Grupos = array($oPerfil);
					$oUsuario->Roles = array($oRol);
				if($registro->Estado->Id == 3){
					$this->saveGrupoUsuario($oUsuario);
					/** Guardar Usuarios Default **/
					$this->addUsuarioDefaultsApps($oUsuario);
				}else{
					$this->deletGrupoUsuario($oUsuario, $registro->Aplicacion);
				}
				$this->notifyAsignadoORechazado($registro);
			}elseif ($registro->Estado->Id == 0){
				$registro = $this->notifyHash($registro,$this->getHash($registro));
			}
		} catch ( Exception $e ) {
			$registro->Error = trim(utf8_encode($e->__toString()));
		}
		return $registro;
	} 
	
	/**
	 * 
	 * Metodo para recuperar el Hash de una solicitud
	 * 
	 * @param Registro $registro
	 * @return String
	 */
	private function getHash(Registro $registro){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "SELECT [SPER_HASH] FROM [dbo].[SOLICITUD_PERMISOS] 
  							WHERE [SPER_ID] = ".$registro->Id;
			$reg = $bd->ConsultaSelect ($SQL);
			$response = '';
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$response = trim(utf8_encode($reg->Fields(0)->value));
					$reg->movenext();
				}
			}
		} catch (Exception $e) {
		}
		return $response;
	}
	
	/**
	 * Devolver instancia de Transport de Zend
	 *
	 * @return Zend_Mail_Transport
	 */
	private function getTransportEmail(){
		$config = array('auth' => 'login',
				'username' => USUARIOMAIL,
				'password' => PASSWORDMAIL,
				'ssl' => 'ssl',
				'port'=>465);
			
		$transport = new Zend_Mail_Transport_Smtp(SMTP, $config);
			
		return $transport;
	}

	/**
	 *
	 * Metodo para enviar mail de asignado o rechazado
	 * 
	 * @param Usuario $registro
	 * @param String $password
	 * 
	 * @return Usuario
	 */
	private function notifyRecuparacionPassword(Usuario $usuario,$password){
		$transport = $this->getTransportEmail();
		$html = new Zend_View();
		$html->setScriptPath(PATHROOT . '/EmailTemplates/');
		$html->assign('toName',$usuario->Nombre.' '.$usuario->Apellido);
		$html->assign('password',$password);
		$parseTemplate = $html->render('AutogestionEnvioRecuperacionPassword.phtml');
		$mail = new Zend_Mail();
		$mail->setBodyHtml($parseTemplate);
		$mail->setFrom(USUARIOMAIL, 'Sistema de Autogestion de Usuarios');
		$mail->addTo($usuario->Mail, $usuario->Nombre);
		$mail->setSubject('Recuperación de Password');
		try {
			$mail->send($transport);
		} catch (Exception $e) {
			$usuario->Error = utf8_encode( $e->__toString());
		}
		return $usuario;
	}
		
	/**
	 * 
	 * Metodo para enviar mail de asignado o rechazado
	 * @param Registro $registro
	 * 
	 */
	private function notifyAsignadoORechazado(Registro $registro){
		$transport = $this->getTransportEmail();
		$html = new Zend_View();
		$html->setScriptPath(PATHROOT . '/EmailTemplates/');
		$html->assign('toName',$registro->Usuario->Nombre.' '.$registro->Usuario->Apellido);
		$html->assign('registroEstado',$registro->Estado->Nombre);
		$html->assign('registroAplicacion',$registro->Aplicacion->Nombre);
		$password = ($registro->IsUserNew == 0 && $registro->Estado->Id == 3) ? substr($this->getHash($registro), 0, 4) : NULL;//Envia Password si es nuevo
		$html->assign('password',$password);
		$usuario  = ($registro->IsUserNew == 0 && $registro->Estado->Id == 3) ? $registro->Usuario->Nickname : NULL;//Envia Password si es nuevo
		$html->assign('usuario',$usuario);
		$parseTemplate = $html->render('AutogestionEnvioConfirmacion.phtml');
		$mail = new Zend_Mail();		
		$mail->setBodyHtml($parseTemplate);
		$mail->setFrom(USUARIOMAIL, 'Sistema de Autogestion de Usuarios');
		$mail->addTo($registro->Usuario->Mail, $registro->Usuario->Nombre);
		$mail->setSubject('Estado de Solicitud');
		try {
			$mail->send($transport);	
		} catch (Exception $e) {
			$registro->Error = utf8_encode( $e->__toString());
		}
	}
	
	/**
	 * Metodo para obtener el Estado del registro
	 * @param Integer $id
	 * @return Registro_Estado
	 */
	private function getEstadosRegistro($id){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT [EST_ID],[EST_DESC] FROM [dbo].[SOLICITUD_ESTADOS] WHERE [EST_ID] = '.$id;
			$reg = $bd->ConsultaSelect ( $SQL );
			$estado = new Registro_Estado();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$estado->Id = $reg->Fields(0)->value;
					$estado->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$reg->movenext();
				}
			}
		} catch (Exception $e) {
		}
		return $estado;
	}
	/**
	 * 
	 * Metodo para obtener todos los templates de email disponibles
	 * 
	 * @return Email_Template[]
	 */
	public function listarEmailsTemplates(){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT [etpl_id],[etpl_nombre],[etpl_template],[etpl_asunto] FROM [dbo].[EMAIL_TEMPLATE]';
			$reg = $bd->ConsultaSelect ( $SQL );
			$rta =  Array();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oTemplate = new Email_Template();
					$oTemplate->Id = $reg->Fields(0)->value;
					$oTemplate->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$oTemplate->Template = trim($reg->Fields(2)->value);
					$oTemplate->Asunto = trim(utf8_encode($reg->Fields(3)->value));
					$rta[] = $oTemplate;
					$reg->movenext();
				}
			}
		} catch (Exception $e) {
			$oTemplate = new Email_Template();
			$oTemplate->Error = trim(utf8_encode($e->__toString()));
			$rta[] = $oTemplate;
		}
		return $rta;
	}
	/**
	 * 
	 * Metodo para obtener un Email Template
	 * @param Integer $id
	 * @return Email_Template
	 */
	public function getEmailTemplate($id){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = 'SELECT [etpl_id]
      					  ,[etpl_nombre]
      					  ,[etpl_template]
      					  ,[etpl_asunto]
  					FROM [dbo].[EMAIL_TEMPLATE]
  					WHERE [etpl_id] = '.(int)$id;
			$reg = $bd->ConsultaSelect ( $SQL );
			$oEmailTemplate = new Email_Template();
			if ($reg->Recordcount >0){
				while (!$reg->EOF){
					$oEmailTemplate->Id = $reg->Fields(0)->value;
					$oEmailTemplate->Nombre = trim(utf8_encode($reg->Fields(1)->value));
					$oEmailTemplate->Template = trim(base64_decode($reg->Fields(2)->value));
					$oEmailTemplate->Asunto = trim(utf8_encode($reg->Fields(3)->value));
					$reg->movenext();
				}
			}else{
				$oEmailTemplate->Error = 'No existe el template.';
			}
		} catch (Exception $e) {
			$oEmailTemplate->Error = trim(utf8_encode($e->__toString()));
		}
		return $oEmailTemplate;
	}
	/**
	 * 
	 * Metodo para guadar las modificaciones de un Email Template
	 * 
	 * @param Email_Template $template
	 * @return Email_Template
	 */
	public function saveEmailTemplate(Email_Template $template){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL = "UPDATE [dbo].[EMAIL_TEMPLATE] 
					SET [etpl_template] ='".$template->Template."',
						[etpl_asunto] = '".$template->Asunto."' 
					FROM [dbo].[EMAIL_TEMPLATE]
					WHERE [etpl_id] = ".$template->Id;
			$reg = $bd->ConsultaSelect ($SQL);
		} catch (Exception $e) {
			//$template->Error = trim(utf8_encode($e->__toString()));
			$template->Error = trim(utf8_encode($SQL));
		}
	  $template->Template = unserialize($template->Template);
	  return $template;
	}
	
	/**
	 *
	 * Metodo para comparar respuestas de seguridad de un Usuario a partir de un legajo
	 *
	 * @param Integer $legajo
	 * @param Preguntas $respuestas
	 * 
	 * @return Preguntas
	 */
	public function searchPreguntas($legajo, $respuestas){
		try {
			$bd = new conectarSQLSRV(NAMEDB, HOSTDB, USER, PASS);
			$SQL.= 'SELECT DISTINCT	(select count(id_perfam) from  [polmet].[dbo].[PerFam] P where id_parent=2 and  VP.Per_IDLegajo = P.id_person) as hijos,
									VP.Per_EstadoCivil,
									CONVERT(CHAR(10),VP.Per_FecNac,103) Fecha_Nac,
									VP.Per_CUIL,
									VP.Per_Legajo, 
									VP.Per_Nombre, 
									VP.Per_Apellido, 
									U.Usu_Mail, 
									U.Usu_TelInterno,
									G.gr_desc,
      								D.Dep_Desc,
      								U.Usu_ForzarCambio,
      								CAST(VP.Per_MAIL AS VARCHAR(200)),
      								U.idUsuario
					FROM Tablas.dbo.V_SIRHU_PERSONAL_FULL_V1 AS VP
					LEFT JOIN  dbo.PERS_USUARIOS AS U ON  VP.Per_Legajo = U.Usu_Legajo
					LEFT JOIN [Tablas].[dbo].V_SIRHU_GRADOS G ON G.gr_id = VP.Per_Grado
  					LEFT JOIN [Tablas].[dbo].V_SIRHU_DEPENDENCIAS D ON D.Dep_IDSIRHU = VP.Per_Destino
					WHERE VP.Per_Legajo = '.$legajo;
			$reg = $bd->ConsultaSelect($SQL);
			if ($reg->Recordcount > 0){
				while (!$reg->EOF){
					
					//si eligió estado civil 'otros' y en la base tiene Unión de Hecho, viudo, no cargado, conviviente valida la seleccion
					
					if(($reg->Fields(1)->value == 5) && ($reg->Fields(1)->value > 4)){
						$respuestas->EstCivil = $reg->Fields(1)->value;
					}
					//si eligió hijos '+3' y en la base tiene mas valida la seleccion
					if(($reg->Fields(0)->value == 4) && ($reg->Fields(0)->value > 3)){
						$respuestas->Hijos = $reg->Fields(0)->value;
					}
						
					$rta= new Preguntas();
					$rta->Hijos = trim($reg->Fields(0)->value);
					$rta->EstCivil =trim(utf8_encode($reg->Fields(1)->value));
					$rta->FechaNac = trim(utf8_encode($reg->Fields(2)->value));
					$rta->CUIL =  trim(utf8_encode($reg->Fields(3)->value));
					
					
					
					$Usu= new Personal();
					$Usu->Legajo = trim($reg->Fields(4)->value);
					$Usu->Nombre =trim(utf8_encode($reg->Fields(5)->value));
					$Usu->Apellido = trim(utf8_encode($reg->Fields(6)->value));
					// Si no tiene mail en la tabla de usuarios usa el de SIRHU si tiene
					if(empty($reg->Fields(7)->value) || is_null($reg->Fields(7)->value)){
					$Usu->Mail =  trim(utf8_encode($reg->Fields(2)->value));
					}else{
					$Usu->Mail =  trim(utf8_encode($reg->Fields(7)->value));
					}
					/** Hardcode de prueba **/
					$Usu->Mail = 'pabloalejandroruhl@gmail.com';
					//******************************************************************
					$Usu->Interno = trim(utf8_encode($reg->Fields(8)->value));
					$Usu->Grado = trim(utf8_encode($reg->Fields(9)->value));
					$destino = new Destino();
					$destino->Nombre = trim(utf8_encode($reg->Fields(10)->value));
					$Usu->Destino = $destino;
					$rta->Personal = $Usu;
					
					
					// verifica que las respuestas sean las correctas
					if(($reg->Fields(2)->value == $respuestas->FechaNac)&&(trim($reg->Fields(3)->value) == $respuestas->CUIL)){
						$respuestas->Error ='false';
						// si esta todo bien cargo los datos del usuario para mostrarlo en pantalla
						$respuestas = $rta;
						//***********************************
					}else{
						$respuestas->Error ='true';
					}
					
					
					$reg->movenext();
				}
			}else{
				$respuestas->Error ='true';
			}
		} catch (Exception $e) {
			$respuestas->Error = 'true';
		}
		return $respuestas;
	}

	

}
?>