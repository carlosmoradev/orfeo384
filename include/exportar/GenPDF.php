<?php
session_start();
require_once("conexion.inc.php");
require_once("reporte.inc.php");
require_once("../ezpdf/class.ezpdf.php");
require_once("pdf/class.ezpdf.php");

class GenPDF extends GenFactoryExport{

	public function genPage(){
		$pdf = new Cezpdf($this->tamanoPapel,$this->orientartion);
		$this->font=($this->font="")?'pdf/fonts/Helvetica.afm':$this->font;
		$pdf->selectFont($this->font);
		$pdf->ezSetCmMargins(2,2,2,2);
		$headerTabla="Resultados de la consulta";
		$pdf->ezText($titulo."\n",20,array('justification'=>'center'));
		$pdf->addJpegFromFile('../img/escudo_armada.jpeg', puntos_cm(6), puntos_cm(15));
                //$pdf->ezImage("../img/escudo_armada.jpeg")
		if(count($datos)>0){
		        $pdf->ezTable($this->datos,$this->titulos,$this->headerTabla,array('fontSize'=>8,'width'=>700,'xPos'=>'center','shaded'=>0));
		}else{
			$pdf->ezText($this->mensajeNoDatos,20,array('justification'=>'center'));
		}
		$pdf->ezStream();

	}
}
?>
