<?php 
if(!isset($ruta_raiz))
	$ruta_raiz="../";
	require_once  ($ruta_raiz."ReportesReasignado/PlanillaModel.php");
	require_once  ($ruta_raiz."include/exportar/GenReportFactory.php");
	require_once($ruta_raiz."include/class/OrganizationInfoSession.php");
	require_once ($ruta_raiz."pear/HTML/Template/IT.php");
class PlanillaControler{
		private $arrayPrincipal=array();
		private $arraySecundario=array();
		private $model;
		private $tpl;
		private $dependenciaTerritorial; 
		private $dependencia;
	public function PlanillaControler($arrayprincipal,$arraySecundario){
		global $ruta_raiz;
		$this->arrayPrincipal=$arrayprincipal;
		$this->arraySecundario=$arraySecundario;
		$this->tpl 	= new HTML_Template_IT($ruta_raiz."/ReportesReasignado/");
		$this->model=new PlanillaModel();
		
	}
	public function getDependenciaTerritorial(){
		return $this->dependenciaTerritorial;
	}
	public function setDependenciaTerritorial($dependenciaTerritorial){
		$this->dependenciaTerritorial=$dependenciaTerritorial;
	}
	
	public function getDependencia(){
		return $this->dependencia;
	}
	public function setDependencia($dependencia){
		$this->dependencia=$dependencia;
	}
	
	public function route(){
		//$arrayDatos=$this->arrayPrincipal+$this->arraySecundario;
		if(!empty($this->arraySecundario['exportar'])){
			$this->crearReporte($this->arraySecundario['exportar']);
		}else{
				$this->tpl->loadTemplatefile("vistaReporteCorrespondencia.tpl");
		 	if ($this->arraySecundario['generarPlanilla']){
				$this->cargarVista();
				$this->tpl->setVariable("RESULTADOS",$this->model->radicadosEntrega($this->arrayPrincipal+$this->arraySecundario));
			}else{
				$this->cargarVista();
				$this->tpl->setVariable("RESULTADOS","");
			}
			$this->tpl->show();
		}
	}
	public function cargarVista(){
		global $ruta_raiz;
		$arrayDatos=$this->arrayPrincipal+$this->arraySecundario;
		$organizacion=new OrganizationInfoSession();
		$fecha_busq=empty($arrayDatos['fecha_busq'])?date('Y-m-d'):$arrayDatos['fecha_busq'];
		$origen=empty($arrayDatos['dependencia_org'])?$_SESSION['dependencia']:$arrayDatos['dependencia_org'];
		$fechaBusqFin=empty($arrayDatos['fechaBusqFin'])?date('Y-m-d'):$arrayDatos['fechaBusqFin'];
		$this->tpl->setVariable("RUTA_RAIZ", $ruta_raiz);
		$this->tpl->setVariable("FECHA_BUS", $fecha_busq);
		$this->tpl->setVariable("FECHA_BUS_FIN", $fechaBusqFin);
		$this->tpl->setVariable("HORA_SELECT_INI", $this->construirHora("hora_ini",false,$arrayDatos['hora_ini']));
		$this->tpl->setVariable("HORA_SELECT_FIN", $this->construirHora("hora_fin",true,$arrayDatos['hora_fin']));
		$this->tpl->setVariable("MINUTOS_SELECT_INI", $this->construirMinutos("minutos_ini",false,$arrayDatos['minutos_ini']));
		$this->tpl->setVariable("MINUTOS_SELECT_FIN", $this->construirMinutos("minutos_fin",true,$arrayDatos['minutos_fin']));
		$this->tpl->setVariable("DEPENDENCIAS", $this->combo("dependencia_bus",$organizacion->findDependenciaByTerritorial(),
		$arrayDatos['dependencia_bus']));
		$this->tpl->setVariable("DEPENDENCIA", $this->combo("dependencia_org",$organizacion->findDependenciaByTerritorial(),
                $origen));	
		$this->tpl->setVariable("TIPO_RADICADO", $this->combo("tipo_radicado",$organizacion->listTipoRadicados(),$arrayDatos['tipo_radicado']));	
		if ($arrayDatos['generarPlanilla'])
			$this->tpl->setVariable("EXPORTAR_FILES",$this->exportFiles());
		else 
			$this->tpl->setVariable("EXPORTAR_FILES","");
	}
	public function crearReporte($tipoReporte){
		global $ruta_raiz,$_SESSION;
		ob_start();
		$reporte=GenReportFactory::factoryFileType($tipoReporte);
		$reporte->setTamano("LETTER");
		 require_once($ruta_raiz."config.php");
		$nombre=$entidad_largo." \n\n\n\n\n\n "." FECHA:".date('Y-m-d h:i:s')."                    PLANILA No:_______________";
		$reporte->setOptions(array('titulo'=>array("titulo"=>$nombre,"tamano"=>10,"options"=>array('justification'=>'center'))  
		,"pages"=>array(750,28,10,'','{PAGENUM}/{TOTALPAGENUM}',1),"table"=> array(
							'width'=>770,'fontSize'=>5,'cols'=>array("POSTFIRMA"=>array('width'=>50),"FIRMA"=>array('width'=>50),"Asunto"=>array('width'=>120)))));
		$reporte->setTitulos(array("IDT_Numero Radicado"=>"No Radicado","DAT_Fecha Radicado"=>"Fecha Rad","Fecha Documento"=>"Fecha Documento","Asunto"=>"Asunto"
		,"DIRECCION"=>"Dirección",
		"NOMBRE ORIGEN"=>"Nombre Origen","DIRECCION"=>"Dirección","NOMBRE REMITENTE"=>"Remitente","DEPENDENCIA DESTINO"=>"Dependencia Destino","POSTFIRMA"=>"Postfirma ","FIRMA"=>"Firma "));
		$arrayDatos=$this->arrayPrincipal+$this->arraySecundario;
		$reporte->genReportTable($reporte->consultarResultadosConsulta($this->model->radicadosEntrega($arrayDatos)));
	}
	
	private function construirMinutos($nombreCombo,$esFinal=false,$valor=null){
	   $salida;
	   if($valor==null && $esFinal==true) 
	   			$valor = date("i");
		else if($valor==null)
				$valor="01";	
	   
	   $salida="<select name='".$nombreCombo."' class='select'>";
	   $seleccionado="";
		   for($i=0;$i<=59;$i++){
		   	if($i < 10 )
						$i="0".$i;
		  	 $seleccionado=($valor==$i)?" selected ":"";
		   	 $salida.="<option value='".$i."' ".$seleccionado." >".$i."</option>";
			}
		$salida.="</select>";	
	   return $salida;
	}
	
	private function  construirHora($nombreCombo,$esFinal=false,$valor=null){
		$salida;
		if($valor==null && $esFinal==true) 
	   			$valor = date("H");
			else if($valor ==null)
				$valor="08";	
				
			$salida="<select name='".$nombreCombo."' class='select' >";
		   for($i=0;$i<=23;$i++){
		   			if($i < 10 )
						$i="0".$i;
		  	 $seleccionado=($valor==$i)?" selected ":"";
		   	 $salida.="<option value='".$i."' ".$seleccionado." >".$i."</option>";
			}
		$salida.="</select>";	
	   return $salida;
   }
   	private function combo($nombreCombo,$datos,$default=""){
		  $salida="<select name='".$nombreCombo."' class='select' >"
		  		  ."\n\t <option value='' >Todo</option>";
		  
		   foreach($datos as $valor){
		   	if($valor instanceof DependenciaInfo){
				$seleccionado=($default==$valor->getDepeCodi())?" selected ":"";
		   	 	$salida.="\n\t <option value='".$valor->getDepeCodi()."' ".$seleccionado." >".$valor->depeInfoNombre("XT")."</option>";
			 }else{
			 	$seleccionado=($default==$valor->getTipoRadCodigo())?" selected ":"";
		   	 	$salida.="\n\t <option value='".$valor->getTipoRadCodigo()."' ".$seleccionado." >".$valor->tipoInfoNombre()."</option>";
			 }
			 
			}
		$salida.="</select>";	
	   return $salida;
	}
	private  function exportFiles(){
			global $ruta_raiz;
		$formatos= GenReportFactory::getFormats();
		$salida;
		$pagina=$_SERVER['PHP_SELF'];
		foreach($this->arrayPrincipal+$this->arraySecundario as $clave=>$value){
				$datos.=$clave."=".urlencode($value)."&";
		}		
		foreach($formatos as $value){
			$salida.="<a href='".$pagina."?".$datos."exportar=".$value['tipo']."' target='_blank' ><img src='".$ruta_raiz."imagenes/".$value['extension'].".png'  title='exportar en ".$value['extension']."' alt='exportar en ".$value['extension']."' /></a>";
			}
		return $salida;	
	}
   
}
?>

