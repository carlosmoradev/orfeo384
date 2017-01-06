<?php
if(empty($ruta_raiz))
	$ruta_raiz="../";
require_once($ruta_raiz."include/db/Connection/Connection.php");
class PlanillaModel{
 private $queryEntrega='SELECT DISTINCT h.RADI_NUME_RADI as "IDT_Numero Radicado"
				 			  ,r.RADI_PATH as "HID_RADI_PATH"
				 			  ,:FECHA as "DAT_Fecha Radicado"
                                                          ,r.RADI_NUME_RADI as "HID_RADI_NUME_RADI"
							  ,:FECHDOC as "Fecha Reasignado"
				 			  ,UPPER(r.RA_ASUN)  as "Asunto"
                                                          ,(select UPPER(de.depe_nomb) from dependencia de where r.radi_depe_actu=de.depe_codi) as "DEPENDENCIA ACTUAL" 
                                                          ,(select UPPER(U.usua_nomb) from usuario u where u.depe_codi=r.radi_depe_actu and u.usua_codi=r.radi_usua_actu) as "USUARIO ACTUAL"
							  ,\'\' as "POSTFIRMA"
							  ,\'\' as "FIRMA"
						  FROM 
							 radicado r
								left  join SGD_TPR_TPDCUMENTO tp on r.tdoc_codi=tp.sgd_tpr_codigo
								inner join sgd_dir_drecciones dr on dr.radi_nume_radi=r.radi_nume_radi
								inner join dependencia d on d.depe_codi=r.radi_depe_radi
								inner join hist_eventos h on r.radi_nume_radi=h.radi_nume_radi and h.sgd_ttr_codigo=9
						  WHERE 
						h.hist_fech BETWEEN :FECH_INI and :FECH_FIN' ;

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
				$db->conn->DBTimeStamp($fechaIni),$db->conn->DBTimeStamp($fechaFin),$db->conn->SQLDate("Y-m-d","h.hist_fech"));
				$busqueda=array(':FECHA',':FECH_INI',':FECH_FIN',':FECHDOC');
				$query= str_replace($busqueda,$remplazos,$this->queryEntrega);
				$whereDendencia = !empty($arrayDatos['dependencia_bus'])? ' and r.radi_depe_radi ='.$arrayDatos['dependencia_bus']:"";
				$whereTipoRadicado=!empty($arrayDatos['tipo_radicado'])? ' and r.radi_nume_radi like \'%'.$arrayDatos['tipo_radicado'].'\'':"";
				$whereOrigen=!empty($arrayDatos['dependencia_org'])?' and h.depe_codi='.$arrayDatos['dependencia_org']:"";
				$orderNo= $arrayDatos['orderNo'];
				if(strlen($orderNo)==0){
						$orderNo="2";
						$order = "1";
						$order = 3;

				}else{
						$order = $orderNo +1;
				}
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
                                                $pager->Render($rows_per_page=50,$linkPagina,$checkbox=chkAnulados);
						$resultado = ob_get_contents();
                                                ob_end_clean();
						return $resultado;
				}else{
						return $query;
				}
			}
			
}

?>
