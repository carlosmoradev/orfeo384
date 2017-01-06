<?php 

if(!isset($ruta_raiz))
	$rura_raiz = "../../";
	
require_once($ruta_raiz."include/db/Connection/Connection.php");
require_once($ruta_raiz."include/class/DependenciaInfo.php");
require_once($ruta_raiz."include/class/TiposRadicado.php");
require_once($ruta_raiz."include/class/UsuarioInfo.php");

class OrganizationInfoSession{

	public function findDependenciaByTerritorial($codiTerritorial=null){
			global $ruta_raiz;
			$dependencias=array();
			$db =Connection::getCurrentInstance();
			$territorial=($codiTerritorial!=null)?" and depe_codi_territorial=".$codiTerritorial:"";
				
			$consulta="SELECT depe_nomb,depe_codi,dep_sigla,depe_codi_padre,depe_codi_territorial  
								FROM dependencia  WHERE depe_estado= 1 ".$territorial." 
								order by DEP_SIGLA,DEPE_NOMB ";
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$dependencias[]=$this->fillDependencia($rs->fields);  
				$rs->MoveNext();
		}
		return $dependencias;
	}
	public function listDependencias($depe_estado=null){
			global $ruta_raiz;
			$dependencias=array();
			$db =Connection::getCurrentInstance();
			$consulta="SELECT depe_nomb,depe_codi,dep_sigla,depe_codi_padre,depe_codi_territorial  
								FROM dependencia WHERE depe_estado= 1";
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$dependencias[]=$this->fillDependencia($rs->fields); 
				$rs->MoveNext();
		}
		return $dependencias;
	}
	public function listAllDependencias(){
		global $ruta_raiz;
			$dependencias=array();
			$db =Connection::getCurrentInstance();
			$consulta="SELECT depe_nomb,depe_codi,dep_sigla,depe_codi_padre,depe_codi_territorial,depe_estado  
								FROM dependencia ";
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$dependencias[]=$this->fillDependencia($rs->fields);
				$rs->MoveNext();
		}
		return $dependencias;
	}
	public function findDependencia($codi,$nomb){
			global $ruta_raiz;
			$dependencias=array();
			$db =Connection::getCurrentInstance();
			$consulta="SELECT depe_nomb,depe_codi,dep_sigla,depe_codi_padre,depe_codi_territorial  
								FROM dependencia  where depe_codi_territorial=".$codiTerritorial;
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$dependencias[]=$this->fillDependencia($rs->fields);
				$rs->MoveNext();
		}
		return $dependencias;
	}
	public function loadDependencia($depeCodi){
		global $ruta_raiz;
			$dependencia=null;
			$db =Connection::getCurrentInstance();
			$consulta="SELECT depe_nomb,depe_codi,dep_sigla,depe_codi_padre,depe_codi_territorial  
								FROM dependencia  where depe_codi=".$depeCodi;
			$rs=$db->query($consulta);
		if ($rs!=false){
				$this->fillDependencia($rs->fields);
		}
		return $dependencia;
	}
	public function listTipoRadicados(){
			global $ruta_raiz;
		
		$query = "SELECT * FROM  sgd_trad_tiporad order by 1";
		$tiposRadicado=array();
		$db =Connection::getCurrentInstance();
		$db->conn->SetFetchMode(2); 
		$rs=$db->query($query);
		while (!$rs->EOF){
			$tiposRadicado[]=$this->fillTiposRadicado($rs->fields);
			$rs->MoveNext();
		}	
		return $tiposRadicado;
	}
 	public function listUsuariosDependencia($dependencia){
 		global $ruta_raiz;
 		$usuarios=array();
		$db =Connection::getCurrentInstance();
		if(is_array($dependencia)){
			$whereDep=" and d.depe_codi in (".implode(",",$dependencia).") ";
		}else{
			$whereDep=" and d.dependencia=".$dependencia;
		}
		
		$consulta="SELECT depe_nomb,d.depe_codi,dep_sigla,depe_codi_padre,
						depe_codi_territorial, usua_login,usua_codi,usua_doc,
						usua_email,usua_esta,usua_nomb,sgd_usua_profile,codi_nivel
							FROM dependencia d iner join usuario u on  u.depe_codi=d.depe_codi
							WHERE depe_estado= 1
							and usua_esta = '1'
							".$whereDep
							." order by d.depe_codi";
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$usuarios[]=$this->fillUsuario($rs->fields); 
				$rs->MoveNext();
		}
		return $usuarios;
 	}
 	public function loadUsuarioInfo($usuaLogin){
 		global $ruta_raiz;
		$usuario=null;
		
 		$db =Connection::getCurrentInstance();
		$db->conn->SetFetchMode(2);
		$whereUsuario="and  usua_login='".$usuaLogin."'";
		
		$consulta="SELECT depe_nomb,d.depe_codi,dep_sigla,depe_codi_padre,
						depe_codi_territorial, usua_login,usua_codi,usua_doc,
						usua_email,usua_esta,usua_nomb,sgd_usua_profile,codi_nivel	
							FROM dependencia d inner join usuario u on  u.depe_codi=d.depe_codi
							WHERE depe_estado= 1
							and usua_esta = '1'
							".$whereUsuario
							." order by d.depe_codi";
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$usuario=$this->fillUsuario($rs->fields);
		$rs->MoveNext();
		}
		return $usuario;
 	}
	public function loadUsuarioByCodiAndDepe($usuaCodi,$depeCodi){
 		global $ruta_raiz;
		$usuario=null;
 		$db =Connection::getCurrentInstance();
		$db->conn->SetFetchMode(2);
		$whereUsuario="and  usua_codi=".$usuaCodi." 
					  AND u.depe_codi=".$depeCodi;
		
		$consulta="SELECT depe_nomb,d.depe_codi,dep_sigla,depe_codi_padre,
						depe_codi_territorial, usua_login,usua_codi,usua_doc,
						usua_email,usua_esta,usua_nomb,sgd_usua_profile,u.codi_nivel
							FROM dependencia d inner join usuario u on  u.depe_codi=d.depe_codi
							WHERE d.depe_estado= 1
							and usua_esta = '1'
							".$whereUsuario
							." order by d.depe_codi";
			$rs=$db->query($consulta);
		while (!$rs->EOF){
				$usuario=$this->fillUsuario($rs->fields);
				$rs->MoveNext();
		}
		return $usuario;
 	}
	public function loadUsuarioFormRadicado($noRadicado){
		$db =Connection::getCurrentInstance();
		$db->conn->SetFetchMode(2);
		$consulta ="SELECT 
						depe_nomb,d.depe_codi,dep_sigla,depe_codi_padre,
						depe_codi_territorial, usua_login,usua_codi,usua_doc,
						usua_email,usua_esta,usua_nomb,sgd_usua_profile,u.codi_nivel
						FROM radicado r, dependencia d inner join usuario u on  u.depe_codi=d.depe_codi	
				WHERE
					r.RADI_USUA_ACTU =u.USUA_CODI
					r.RADI_DEPE_ACTU =u.DEPE_CODI
					AND r.RADI_NUME_RADI = ".$noRadicado;
				if($rs=$db->query($consulta))
						$usuario=$this->fillUsuario($rs->fields);
				return $usuario;	
	
	}
	public function loadUsuarioByRadicadoUsuaAnterior($noRadicado){
                       $db =Connection::getCurrentInstance();
			$db->conn->SetFetchMode(2);
			$consulta ="SELECT 
						depe_nomb,d.depe_codi,dep_sigla,depe_codi_padre,
						depe_codi_territorial, usua_login,usua_codi,usua_doc,
						usua_email,usua_esta,usua_nomb,sgd_usua_profile,u.codi_nivel
						FROM radicado r, dependencia d inner join usuario u on  u.depe_codi=d.depe_codi	
				WHERE
					r.RADI_USU_ANTE=u.USUA_LOGIN
					AND r.RADI_NUME_RADI = ".$noRadicado;
				if($rs= $db->query($consulta))
						$usuario=$this->fillUsuario($rs->fields);
				return $usuario;	
	}
	
	public function fillDependencia(&$array){
				$dependencia=new DependenciaInfo();
				$dependencia->setDepeCodi($array['DEPE_CODI']);
				$dependencia->setDepeNomb($array['DEPE_NOMB']);
				$dependencia->setDepeSigla($array['DEP_SIGLA']);
				$dependencia->setDepeCodiTerritorial($array['DEPE_CODI_TERRITORIAL']);
				$dependencia->setDepeCodiPadre($array['DEPE_CODI_PADRE']);
				return $dependencia;
	}
	public function fillUsuario(&$array){
				$usuario=new UsuarioInfo();
				$usuario->setUsuaCodi($array['USUA_CODI']);
				$usuario->setUsuaNomb($array['USUA_NOMB']);
				$usuario->setUserName($array['USUA_LOGIN']);
				$usuario->setUsuaMail($array['USUA_EMAIL']);
				$usuario->setUsuaProfile($array['SGD_USUA_PROFILE']);
				$usuario->setUsuaDoc($array['USUA_DOC']);
				$usuario->setActivo($array['USUA_ESTA']);
				$usuario->setNivelUsuario($array['CODI_NIVEL']);		
				$usuario->setDependenciaInfo($this->fillDependencia($array));
				return $usuario;
				
	}
	public function fillTiposRadicado(&$array){
		$tipoRadicado=new tiposRadicado();
			$tipoRadicado->setTipoRadCodigo($array['SGD_TRAD_CODIGO']);
			$tipoRadicado->setTipoDescripcion($array['SGD_TRAD_DESCR']);
			$tipoRadicado->setTipoIcono($array['SGD_TRAD_ICONO']);
			$tipoRadicado->setGenSalida($array['SGD_TRAD_GENRADSAL']);
			return  $tipoRadicado;
			
	}
}
?>
