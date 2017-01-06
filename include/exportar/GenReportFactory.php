<?php
if(!isset($ruta_raiz))
	$ruta_raiz="../../";
require_once($ruta_raiz."include/db/Connection/Connection.php");
require_once($ruta_raiz."include/Spreadsheet/Excel/Writer.php");
require_once($ruta_raiz."include/ezpdf/class.ezpdf.php");
require_once($ruta_raiz."include/exportar/PDFFile.php");
require_once($ruta_raiz."include/exportar/XLSFile.php");
require_once($ruta_raiz."include/exportar/CSVFile.php"); 		

abstract class  GenReportFactory{
	protected $titulos;
	protected $header;
	protected $tamano='LEGAL';
	const PDF=1;
	const XLS=2;
	const CSV=3;
	protected $mensajeNoDatos;
	protected $options=array();
	
	public function GenReportFactory(){
		//parent::_construct();
		$this->mensajeNoDatos="No hay datos para el Reporte";
	}
	public function consultarResultadosConsulta($consulta,$camposOcultos="HID"){
	        if (!defined('ADODB_ASSOC_CASE'))
    			define('ADODB_ASSOC_CASE', 2); 
			$db = Connection::getCurrentInstance();
			$data=null;
			$db->conn->SetFetchMode(2); 
			
			if($rs=$db->query($consulta)){
				$fieldCount = $rs->FieldCount();
				$i=0;
				while(!$rs->EOF ){
						for($iE=0; $iE<=$fieldCount-1; $iE++) {	
							$fld = $rs->FetchField($iE);
							if(substr($fld->name,0,3)!=camposOcultos) {
								$data[$i][$fld->name]=$rs->Fields(strtoupper($fld->name));
							}
						}
					$i++;	
					$rs->MoveNext();
				}
			}
			unset($rs);
			return $data;
	}
	public function getTitulos(){
		return $this->titulos;
	}
	public function setTitulos($titulos){
		$this->titulos=$titulos;
	}
	public function getOptions(){
		return $this->options;
	}
	public function setOptions($options){
		$this->options=$options;
	}
	public function getTamano(){
		return $this->tamano;
	}
	public function setTamano($tamano){
		$this->tamano=$tamano;
	}
	public function getMensajeNoDatos(){
		return $this->mensajeNoDatos;
	}
	public function setMensajeNoDatos($mensajeNoDatos){
		$this->header=$mensajeNoDatos;
	}
	public function getHeader(){
		return $this->header;
	}
	public function setHeader($header){
		$this->header=$header;
	}
	public static  function factoryFileType($tipo){
		switch ($tipo){
			case   self::PDF:
				return new PDFFile();
				break;
			case  self::XLS:
				return new XLSFile();
				break;
			case self::CSV:
				return new CSVFile();
				break;
			default :
					return null;
					break;
		}
	}
	public static function getFormats(){
		return array(array("tipo"=>1,"extension"=>"pdf"));
	}
	public function quitar_especiales($cad,$reemplazo=" "){
		$u=preg_split("/[\s,]+/",$cad);
		$final=implode($reemplazo,$u);
		return $final;
	}
	public abstract function genReportTable($data);

}
?>
