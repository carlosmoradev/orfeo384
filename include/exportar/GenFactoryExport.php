<?php

require_once("GenXLS.php");
require_once("GenPDF.php");
require_once("GenCVS.php");
include_once($ruta_raiz."include/db/ConnectionHandler.php");

class GenFactoryExport{
	private $nombreArchivo;
	private $titulos;
	private $orientacion;
	private $datos;
	private $tamanoPagina;
	private $fuente;
	private $nombreArchivo;
	private $encabezado;
	
	public function GenFactoryExport($db){
		set_time_limit(0);
	}
	public function setTitulos($titulos){
		$this->titulos=$titulos;
	}
	public function getTitulos(){
		return $this->titulos;	
	}
	public setNombreArchivo($nobre){
		$this->nombreArchivo=$nombre;
	}
	public getNombreArchivo(){
		return $nombreArchivo;	
	}
			
	public function exportResulset($db,$query){
		$db->conn->SetFetchMode(ADODB_FETCH_ASSOC); 
     		$rs= $db->query($query);
      		$datos=null;
      		if(!$this->titulos){
		for($i=0;$i<$rs->FieldCount();$i++){	
			$tit=$rs->FetchField($i);
			if(!eregi("[^HID]",$tit))
				$titulos[]=$tit;
			}
     		}
		$i=0;
		while(!$rs->EOF){
			foreach($titulos as  $tit)
			if(!eregi("[^HID]",$tit)			
				$this->datos[$i][]= $rs->fields[$tit];
			}
	 	}

	}
	
	public function exportArray($array){
		$this->datos=$array;	
	}

	
	
	public function createPage();		

}
?>
