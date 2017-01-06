<?php
if(empty($ruta_raiz))
		$ruta_raiz="../../";

require_once($ruta_raiz."include/class/DependenciaInfo.php");		

class UsuarioInfo{
	protected  $userName;
	protected  $usuaNomb;
	protected  $usuaCodi;
	protected  $usuaProfile;
	protected  $activo;
	protected  $mail;
	protected  $depeInfo;
	protected  $nivelUsuario;
	protected  $usuaDoc;
	
	public function UsuarioInfo(){
		
	}
	public function getUsuaCodi(){
		return $this->usuaCodi;
	}
	public function setUsuaCodi($usuaCodi){
		$this->usuaCodi=$usuaCodi;	
	}
	public function getUserName(){
		return $this->userName;
	}
	public function setUserName($userName){
		$this->userName=$userName;	
	}
	public function getUsuaNomb(){
		return $this->usuaNomb;
	}
	public function setUsuaNomb($usuaNomb){
		$this->usuaNomb=$usuaNomb;	
	}
	public function getUsuaProfile(){
		return $this->usuaProfile;	
	}
	public function setUsuaProfile($usuaProfile){
		$this->usuaProfile=$usuaProfile;	
	}
	public function getUsuaMail(){
		return $this->mail;	
	}
	public function setUsuaMail($mail){

		$this->mail=$mail;	
	}
	public function getDependenciaInfo(){
		return $this->depeInfo;	
	}
	public function setDependenciaInfo($dependencia){
		$this->depeInfo=$dependencia;	
	}
	public function isActivo(){
		return 1==$this->activo;
	}
	public function setActivo($activo){
		$this->activo=$activo;
	}
	public function getNivelUsuario(){
		return $this->nivelUsuario;	
	}
	public function setNivelUsuario($nivelUsuario){
		$this->nivelUsuario=$nivelUsuario;	
	}
	public function getUsuaDoc(){
		return $this->usuaDoc;	
	}
	public function setUsuaDoc($usuaDoc){
		$this->usuaDoc=$usuaDoc;	
	}	
	
}

?>
