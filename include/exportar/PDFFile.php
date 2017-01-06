<?php 
if(!isset($ruta_raiz))
	$ruta_raiz="../../";
	
	
class PDFFile extends GenReportFactory{

	public function genReportTable($datos){
		global $ruta_raiz;
		$tamanoPapel=(!empty($this->tamano))?$this->tamano:'LETTER';
		$orientacion=(!empty($this->options['orientation']))?$this->options['orientation']:'landscape';
		$pdf = new Cezpdf($tamanoPapel,$orientacion);
		
		$this->font=(!isset($this->options['font']) || $this->options['font']=="")?$ruta_raiz."include/ezpdf/fonts/Helvetica.afm":$this->font;
		$pdf->selectFont($this->font);
		
		$superior=(!empty($this->options['superior']))?$this->options['superior']:2;
		$inferior=(!empty($this->options['inferior']))?$this->options['inferior']:2;
		$derecha=(!empty($this->options['derecha']))?$this->options['derecha']:2;
		$izquierda=(!empty($this->options['izquierda']))?$this->options['izquierda']:2;
		$countPages=false;
		$pdf->ezSetCmMargins($superior,$inferior,$derecha,$izquierda);
		if (!empty($this->options['pages'])){
		$pdf->ezStartPageNumbers($this->options['pages'][0],$this->options['pages'][1],$this->options['pages'][2],$this->options['pages'][3],
			$this->options['pages'][4],$this->options['pages'][5]);
			$countPages=true;
		}
		if(!empty($this->options['titulo']))
				$pdf->ezText($this->options['titulo']['titulo']."\n",$this->options['titulo']['tamano'],$this->options['titulo']['options']);	
		if(!empty($this->options['image']))
			$pdf->ezImage($this->options['image']);
		if(count($datos)>0){
		      	$pdf->ezTable($datos,$this->titulos,$this->headerTabla,$this->options['table']);
		}else{
			$pdf->ezText($this->mensajeNoDatos,20,array('justification'=>'center'));
		}
			if($countPages)
				$pdf->ezStopPageNumbers();
			return $pdf->ezStream();
	}
} 

?>
