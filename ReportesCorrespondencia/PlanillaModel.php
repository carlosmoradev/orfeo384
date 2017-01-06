<?php
if(empty($ruta_raiz)) 	$ruta_raiz="..";
require_once($ruta_raiz."/include/db/Connection/Connection.php");
class PlanillaModel{
 private $queryEntrega='SELECT r.RADI_NUME_RADI as "IDT_NUMERO RADICADO"
			  ,r.RADI_PATH as "HID_RADI_PATH"
 			  ,:FECHA as "DAT_FECHA RADICADO"
                          ,r.RADI_NUME_RADI as "HID_RADI_NUME_RADI"
			  ,:FECHDOC as "FECHA DOCUMETNO"
 			  ,r.RADI_NUME_RADI as "HID_RADI_NUME_RADI"
 			  ,UPPER(r.RA_ASUN)  as "ASUNTO"
 			  ,r.RADI_NUME_HOJA as "FOLIOS"
 			  ,substr(dr.sgd_dir_nomremdes,0,50) as "NOMBRE REMITENTE"
 			  ,d.DEPE_NOMB as "DEPENDENCIA DESTINO"
			  ,\'\' as "POSTFIRMA"
		  ,\'\' as "FIRMA"
						  FROM 
							 radicado r
								left  join SGD_TPR_TPDCUMENTO tp on r.tdoc_codi=tp.sgd_tpr_codigo
								inner join sgd_dir_drecciones dr on dr.radi_nume_radi=r.radi_nume_radi
								inner join dependencia d on d.depe_codi=r.radi_depe_radi
								inner join hist_eventos h on r.radi_nume_radi=h.radi_nume_radi and h.sgd_ttr_codigo=2
						  WHERE 
						r.radi_fech_radi BETWEEN :FECH_INI and :FECH_FIN 
						and dr.sgd_dir_tipo = 1';
 //Se mantiene la consulta original en caso de ser necesario dejarla como en su estado normal
 /*private $queryEntrega='SELECT r.RADI_NUME_RADI as "IDT_Numero Radicado"
				 			  ,r.RADI_PATH as "HID_RADI_PATH"
				 			  ,:FECHA as "DAT_Fecha Radicado"
							  ,:FECHDOC as "Fecha Documento"
				 			  ,r.RADI_NUME_RADI as "HID_RADI_NUME_RADI"
				 			  ,UPPER(r.RA_ASUN)  as "Asunto"
				 			  ,tp.SGD_TPR_DESCRIP as "Tipo Documento" 
				 			  ,d.depe_codi as "COD DEPENDENCIA DESTINO"
				 			  ,d.DEPE_NOMB as "DEPENDENCIA DESTINO"
				 			  ,r.RADI_NUME_HOJA as "FOLIOS"
				 			  ,dr.sgd_dir_direccion as "DIRECCION"
				 			  ,dr.sgd_dir_nombre as "NOMBRE ORIGEN"
				 			  ,dr.sgd_dir_nomremdes as "NOMBRE REMITENTE"
							  ,\'\' as "POSTFIRMA"
							  ,\'\' as "FIRMA"
						  FROM 
							 radicado r
								left  join SGD_TPR_TPDCUMENTO tp on r.tdoc_codi=tp.sgd_tpr_codigo
								inner join sgd_dir_drecciones dr on dr.radi_nume_radi=r.radi_nume_radi
								inner join dependencia d on d.depe_codi=r.radi_depe_radi
								inner join hist_eventos h on r.radi_nume_radi=h.radi_nume_radi and h.sgd_ttr_codigo=2
						  WHERE 
						r.radi_fech_radi BETWEEN :FECH_INI and :FECH_FIN 
						and dr.sgd_dir_tipo = 1 ';*/
		//echo "$queryEntrega <hr>";

		public function getTipoRadicado(){
			$salida=array();
			
			return $salida();
			
		}				
						
		public  function radicadosEntrega($arrayDatos){
			global $ruta_raiz;
			$db=Connection::getCurrentInstance();
			$fechaIni =$arrayDatos['fecha_busq']." ".$arrayDatos['hora_ini'].":".$arrayDatos['minutos_ini'];
			$fechaFin = $arrayDatos['fecha_fin']." ".$arrayDatos['hora_fin'].":".$arrayDatos['minutos_fin'];
			$remplazos =array($db->conn->SQLDate("Y-m-d H:i A","r.RADI_FECH_RADI"),
			$db->conn->DBTimeStamp($fechaIni),$db->conn->DBTimeStamp($fechaFin),$db->conn->SQLDate("Y-m-d","r.radi_fech_ofic"));
			$busqueda=array(':FECHA',':FECH_INI',':FECH_FIN',':FECHDOC');
			$query= str_replace($busqueda,$remplazos,$this->queryEntrega);
			$whereDendencia = !empty($arrayDatos['dependencia_bus'])? ' and r.radi_depe_radi ='.$arrayDatos['dependencia_bus']:"";
			//$whereTipoRadicado=!empty($arrayDatos['tipo_radicado'])? ' and r.radi_nume_radi like \'%'.$arrayDatos['tipo_radicado'].'\'':"";
                        $whereTipoRadicado=!empty($arrayDatos['tipo_radicado'])? ' and r.sgd_trad_codigo ='.$arrayDatos['tipo_radicado']:"";
			$whereOrigen=!empty($arrayDatos['dependencia_org'])?' and h.depe_codi='.$arrayDatos['dependencia_org']:"";
			$orderNo= $arrayDatos['orderNo'];
			if(strlen($orderNo)==0){
					$orderNo="2";
					$order = "1";
					//$order = 3;
			}else{
					$order = $orderNo +1;
			}
			$order = "9,2";
			$query=$query.$whereDendencia.$whereTipoRadicado.$whereOrigen.' order by '.$order .' ' .$arrayDatos['$orderTipo'];
			if(empty($arrayDatos['exportar'])){
						$datosEstado="";
						foreach($arrayDatos as $clave=>$valor)
							$datosEstado.=$clave."=".$valor."&";
						ob_start();
						$pager = new ADODB_Pager($db,$query,'adodb', true,$orderNo,$orderTipo);
						$pager->checkAll = false;
						$pager->checkTitulo = true;
						$pager->toRefLinks = $_SERVER['PHP_SELF']."?".$datosEstado;
						$pager->toRefVars = $encabezado;
						$pager->descCarpetasGen=$descCarpetasGen;
						$pager->descCarpetasPer=$descCarpetasPer;
						if($_GET["adodb_next_page"]) $pager->curr_page = $_GET["adodb_next_page"];
                                                $pager->Render($rows_per_page=2000,$linkPagina,$checkbox=chkAnulados);
						$resultado = ob_get_contents();
                                                ob_end_clean();
						return $resultado;
				}else{
						return $query;
				}
			}
			
}

?>
