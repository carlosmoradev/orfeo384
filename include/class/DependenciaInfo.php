<?php

class DependenciaInfo{
	protected $depeCodigo;
	protected $depeNomb;
	protected $depeSigla;
	protected $depeCodiTerritorial;
	protected $depeCodiPadre;
	protected $depeEstado=1;
	
	public function DependenciaInfo(){
	}
	
	public function getDepeCodi(){
		return $this->depeCodigo;
	}
	public function setDepeCodi($depeCodi){
		$this->depeCodigo=$depeCodi;
	}
	public function setDepeEstado($depeEstado){
		$this->depeEstado=$depeEstado;
	}
	public function isActiva(){
		return $this->depeEstado == 1;
	}
	
	public function getDepeNomb(){
		return $this->depeNomb;
	}
	public function setDepeNomb($depeNomb){
		$this->depeNomb=$depeNomb;
	}
	public function getDepeSigla(){
		return $this->depeSigla;
	}
	public function setDepeSigla($depeSigla){
		$this->depeSigla=$depeSigla;
	}
	public function getDepeCodiTierritorial(){
		return $this->depeCodiTerritorial;
	}
	public function setDepeCodiTerritorial($depeCodiTerritorial){
		$this->depeCodiTerritorial=$depeCodiTerritorial;
	}
	public function getDepeCodiPadre(){
		return $this->depeCodiPadre;
	}
	public function setDepeCodiPadre($depeCodiPadre){
		$this->depeCodiPadre=$depeCodiPadre;
	}
	public function depeInfoNombre($nullValue=""){
		$salida=null;
		$salida=($this->depeSigla!=null)?$this->depeSigla:$nullValue;
		$salida.=" - ".$this->depeNomb;
		return $salida;
	}

}


?>
