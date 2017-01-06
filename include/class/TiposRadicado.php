<?php

class TiposRadicado{
	private $tipoRadCodigo;
	private $tipoDescripcion;
	private $tipoIcono;
	private $genSalida;
	public function DependenciaInfo(){
	}
	
	public function getTipoRadCodigo(){
		return $this->tipoRadCodigo;
	}
	public function setTipoRadCodigo($tipoRadCodigo){
		$this->tipoRadCodigo=$tipoRadCodigo;
	}
	public function isGenSalida(){
		return $this->depeEstado == 1;
	}
	public function setGenSalida($genSalida){
		$this->genSalida=$genSalida;
	}
	
	public function getTipoDescripcion(){
		return $this->tipoDescripcion;
	}
	public function setTipoDescripcion($tipoDescripcion){
		$this->tipoDescripcion=$tipoDescripcion;
	}
	public function getTipoIcono(){
		return $this->tipoIcono;
	}
	public function setTipoIcono($tipoIcono){
		$this->tipoIcono=$tipoIcono;
	}
	
	public function tipoInfoNombre(){
		$salida=null;
		$salida=$this->tipoRadCodigo;
		$salida.=" - ".$this->tipoDescripcion;
		return $salida;
	}

}


?>