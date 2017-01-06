<?php
if(!isset($ruta_raiz))
	$ruta_raiz="../../";
 
 class XLSFile extends GenReportFactory{
         public function XLSFile(){
		 }
	 	/*public function genReportTable($datos){
		
		$xls = &new Spreadsheet_Excel_Writer();
		$archivo=(!empty($this->options['filename']))?$this->options['filename']:'reporte.xls';
		$maxancho=(!empty($this->options['maxcelda']))?$this->options['maxcelda']:40;	 
		$minancho=(!empty($this->options['mincelda']))?$this->options['mincelda']:10;
		$nombre=(!empty($this->options['nombre']))?$this->options['nombre']:'reporte';
		$num_hoja=0;
		$sheet= &$xls->addWorksheet("mensajes");
        if(count($datos)>0){
            	//impresion de los datos
        	//	$sheet=$this->crearHoja($nombre_hoja,$xls,$this->options['header']);
            	$i=0;
            	foreach($datos as $l => $j){
            		$r=0;
            		if($i<65000){	
						foreach($j as $k => $m){
								$valorCelda=$this->quitar_especiales($m);
								//$valor=(strlen($valorCelda) > $maxancho)?$maxancho:strlen($valorCelda);
								  //      $valor=($valor < $minancho)?$minancho:$valor;
								  //      $sheet->setColumn($k,$k,$valor);
								  $sheet->write($i+5,$r,$valorCelda);
								$r++;
							}		
            		}else{
							$num_hoja++;
							$i=-1;
                            $sheet= &$xls->addWorksheet("mensajes $num_hoja");
            	              	
            		}
					$i++;
            	}
            }else{
            	$sheet->write(4,3,$this->menasajeNoDatos);
            }
            $xls->send($archivo);
			$xls->close();
			
			return $xls;
		}*/
		
		public function genReportTable($datos){
				global $ruta_raiz;
				require_once($ruta_raiz."ReportesCorrespondencia/prueba.php");
		}
	 public function crearHoja($nombre,$file,$encabezado=""){
           $sheet = &$file->addWorksheet($nombre);   
		   $sheet->setMargins(0.50);
		
		if($this->orientation=="Lanscape")
	        	$sheet->setLandscape();
		else
			$sheet->setPortrait();
		 	$format = &$file->addFormat();
             $format->setAlign('center');
             $format->setBold();
             $format->setTextWrap();
             $format->setValign('vcenter');
             //$sheet->write(0,0,$titulo);
	     	
			//$sheet->setRow(0,null,$format);
	     	//$sheet->write(3,0,$parametros);
             //	$sheet->setRow(3,null,$format);
           
            /*for($i=0;$i<count($this->encabezado);$i++){
            		$sheet->setMerge(4,$i,4,$i);
            		$sheet->setRow(4,null,$format);
                        $sheet->write(4,$i,$this->encabezado[$i]);
            	}
            	$sheet->setRow(4,null,$format);*/
            return  $sheet;
            }

}

?>
