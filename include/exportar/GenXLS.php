<?php

$ruta_raiz="../../"

require_once($ruta_raiz."include/Spreadsheet/Excel/Writer.php");
class GenXLS extends GenFactoryExport{

         public function GenXLS(){
	 	parent();
	 }
	 public cratePage(){
		 
		$xls =& new Spreadsheet_Excel_Writer();
             	$xls->send($archivo);
             	$maxancho=40;	 
		$minancho=10;
		for($i=0;$i < count($this->datos);$i+=65000){	
			$nombre=($i==0)?$this-nombre:$this->nombre."_".$i;
			$hoja=crearHoja($nombre,$this->encabezado,$xls);
			 $ereg="[<(\w|\"|'| |/|=|#|\?|\.|\(|\)|,|;)*>]";
                	
			foreach($datos as $l => $j){
                        	$r=0;
                        	foreach($j as $k => $m){
                                	if($r<65000){   
                                    		$valorCelda=preg_replace($ereg,"",$m);      
                                        	$valor=(strlen($valorCelda) > $maxancho)?$maxancho:strlen($valorCelda);
                                    		$valor=($valor < $minancho)?$minancho:$valor;
                                    		$sheet->setColumn($k,$k,$valor);
                                    		$sheet->write($i+5,$r,$valorCelda);
                                        	$r++;
                                	}else{
                                		$num_hoja++;
                              			$sheet=crearHoja($nombre_hoja." $num_hoja",$encabezados,$xls);^M
                                	}
                        	}
                        $i++;
		    }

		}
				

	 }

	 public function crearHoja($nombre,$encabezado,$file){
            
          	$sheet = &$file->addWorksheet($nombre);   
		$sheet->setMargins(0.50);
		if($this->orirntation=="Lanscape")
	        	$sheet->setLandscape();
		else
			$sheet->setProliant();
		
		$sheet->setMerge(0,0,2,count($encabezado)-1);
             	$format =& $file->addFormat();
             	$format->setAlign('center');
             	$format->setBold();
             	$format->setTextWrap();
             	$format->setValign('vcenter');
             	$sheet->write(0,0,$titulo);
	     	$sheet->setRow(0,null,&$format);
	     	$sheet->write(3,0,$parametros);
             	$sheet->setRow(3,null,&$format);
           
            for($i=0;$i<count($this->encabezado);$i++){
            		$sheet->setMerge(4,$i,4,$i);
            		$sheet->setRow(4,null,&$format);
                        $sheet->write(4,$i,$this->encabezado[$i]);
            	}
            	$sheet->setRow(4,null,&$format);
            return  $sheet;
            }

}


            $xls =& new Spreadsheet_Excel_Writer();
            $xls->send($archivo);
            $maxancho=40;
            $minancho=10;

            $num_hoja=0;
            if(count($datos)>0){
            	//impresion de los datos
                  $sheet=crearHoja($nombre_hoja,$encabezados,$xls);
            	$i=1;
            	$ereg="[<(\w|\"|'| |/|=|#|\?|\.|\(|\)|,|;)*>]";
            	foreach($datos as $l => $j){
            		$r=0;
            		foreach($j as $k => $m){
            			if($r<65000){	
                                    $valorCelda=preg_replace($ereg,"",$m);	
            				$valor=(strlen($valorCelda) > $maxancho)?$maxancho:strlen($valorCelda);
                                    $valor=($valor < $minancho)?$minancho:$valor;
                                    $sheet->setColumn($k,$k,$valor);
                                    $sheet->write($i+5,$r,$valorCelda);
            				$r++;
            			}
            			else{
            			$num_hoja++;
                              $sheet=crearHoja($nombre_hoja." $num_hoja",$encabezados,$xls);
            			}
                              	
            		}
            		$i++;
            	}
            }else{
            	$sheet->write(4,3,"No se encontraron datos");
            }
            $xls->close();
}else{
header("Location: ../index.php");
}
?>
