<?php 
if(!isset($ruta_raiz))
	$ruta_raiz="../../";
		
class CSVFile extends GenReportFactory{

	public function genReportTable($data){
		global $ruta_raiz;
		$salida="";
		header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
		header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");
		header("Content-Type: application/octet-stream"); 
		header('Content-Disposition: attachment; filename="downloaded.csv"');
		 if(!empty($this->options['titulo']))
			$salida.=$this->options['titulo']['titulo']."\n";

		foreach($this->titulos as $clave =>$value)
			$salida.=$value.",";
		$salida.="\n";
			
		if(count($data)>0){
		foreach($data as $clave =>$value){
                       foreach( $this->titulos as $key =>$val)
				$salida.=$value[$val].",";
					$salida.="\n";
		}
		}else{
			$salida.=$this->mensajeNoDatos."\n";
		}
			echo $salida;
	}
} 

?>
