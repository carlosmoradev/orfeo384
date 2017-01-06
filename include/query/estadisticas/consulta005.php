<?php
/** RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA
	* 
	* @autor JAIRO H LOSADA - SSPD
	* @version ORFEO 3.1
	* 
	*/
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';	
if(!$orno) $orno=2;
 /**
   * $db-driver Variable que trae el driver seleccionado en la conexion
   * @var string
   * @access public
   */
 /**
   * $fecha_ini Variable que trae la fecha de Inicio Seleccionada  viene en formato Y-m-d
   * @var string
   * @access public
   */
/**
   * $fecha_fin Variable que trae la fecha de Fin Seleccionada
   * @var string
   * @access public
   */
/**
   * $mrecCodi Variable que trae el medio de recepcion por el cual va a sacar el detalle de la Consulta.
   * @var string
   * @access public
   */
switch($db->driver)
{	
	case 'postgres':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND h.DEPE_CODI_DEST=$dependencia_busq AND b.DEPE_CODI=$dependencia_busq ";	}
			$queryE = "
	    		SELECT b.USUA_NOMB AS USUARIO
					, count($radi_nume_radi) AS RADICADOS
					, MIN(b.USUA_CODI) AS HID_COD_USUARIO
					, MIN(b.depe_codi) AS HID_DEPE_USUA
				FROM RADICADO r, USUARIO b, HIST_EVENTOS h
				WHERE 
					h.HIST_DOC_DEST=b.usua_doc
					$condicionE
					AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
					AND h.SGD_TTR_CODIGO=2
					AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  
					AND r.SGD_TRAD_CODIGO =  2
				$whereTipoRadicado 
				GROUP BY b.USUA_NOMB
				ORDER BY $orno $ascdesc";
 			/** CONSULTA PARA VER DETALLES 
	 		*/
			$queryEDetalle = "SELECT 
					$radi_nume_radi AS RADICADO
					, b.USUA_NOMB AS USUARIO_ACTUAL
					, r.RA_ASUN ASUNTO 
					, ".$db->conn->SQLDate('Y/m/d H:i:s','r.radi_fech_radi')." AS FECHA_RADICACION, 
					".$db->conn->SQLDate('Y/m/d H:i:s','h.HIST_FECH')." AS FECHA_DIGITALIZACION
					,r.RADI_PATH AS HID_RADI_PATH{$seguridad}
				FROM RADICADO r, USUARIO b, HIST_EVENTOS h
				WHERE 
					h.HIST_DOC_DEST=b.usua_doc
					$condicionE
					AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
					AND h.SGD_TTR_CODIGO=2
					AND r.SGD_TRAD_CODIGO=2
					AND ".$db->conn->SQLDate('Y/m/d','r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicado 
				";
                           $orderE = " ORDER BY $orno $ascdesc";
                          $condicionUS = " AND b.USUA_CODI=$codUs
                                         AND b.depe_codi = $depeUs "; 
                        /** CONSULTA PARA VER TODOS LOS DETALLES 
                         */ 
                       $queryETodosDetalle = $queryEDetalle . $orderE;
                       $queryEDetalle .= $condicionUS . $orderE; 

		 //return;
		}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND h.DEPE_CODI_DEST=$dependencia_busq AND b.DEPE_CODI=$dependencia_busq ";	}
			$queryE = "
	    		SELECT b.USUA_NOMB USUARIO
					, count(r.RADI_NUME_RADI) RADICADOS
					, MIN(b.USUA_CODI) HID_COD_USUARIO
					, MIN(b.depe_codi) HID_DEPE_USUA
				FROM RADICADO a, USUARIO b, HIST_EVENTOS h
				WHERE 
					h.HIST_DOC_DEST=b.usua_doc
					$condicionE
					AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
					AND h.SGD_TTR_CODIGO=2
					AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
					AND r.SGD_TRAD_CODIGO =  2
				$whereTipoRadicado 
				GROUP BY b.USUA_NOMB
				ORDER BY $orno $ascdesc";
 			/** CONSULTA PARA VER DETALLES 
	 		*/
			$queryEDetalle = "SELECT 
					r.RADI_NUME_RADI RADICADO
					, b.USUA_NOMB USUARIO_ACTUAL
					, r.RA_ASUN ASUNTO 
					, TO_CHAR(r.RADI_FECH_RADI, 'DD/MM/YYYY HH24:MM:SS') FECHA_RADICACION
					, TO_CHAR(h.HIST_FECH, 'DD/MM/YYYY HH24:MM:SS') FECHA_DIGITALIZACION
					,r.RADI_PATH HID_RADI_PATH{$seguridad}
				FROM RADICADO r, USUARIO b, HIST_EVENTOS h
				WHERE 
					h.HIST_DOC_DEST=b.usua_doc
					$condicionE
					AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
					AND h.SGD_TTR_CODIGO=2
					AND r.SGD_TRAD_CODIGO =  2
					AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicado 
				";
                          $orderE = " ORDER BY $orno $ascdesc";
                          $condicionUS = " AND b.USUA_CODI=$codUs
                                         AND b.depe_codi = $depeUs "; 
                        /** CONSULTA PARA VER TODOS LOS DETALLES 
                         */ 
                       $queryETodosDetalle = $queryEDetalle . $orderE;
                       $queryEDetalle .= $condicionUS . $orderE; 

		}break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
	$titulos=array("#","1#RADICADO","2#USUARIO DIGITALIZADOR","3#ASUNTO","4#FECHA RADICACION","5#FECHA DIGITALIZACION","6#MEDIO DE RECEPCION",);
else 		
	$titulos=array("#","1#Usuario","2#Radicados","3#HOJAS DIGITALIZADAS");

function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_POST,$_GET,$krd,$usua_doc;
	$salida="";
	switch ($numColumna)
	{
		case  0:
			$salida=$indice;
			break;
		case 1:	
			$salida=$fila['USUARIO'];
			break;
		case 2:
			$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;usua_doc=$usua_doc&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
			$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
			$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >".$fila['RADICADOS']."</a>";
			break;
		case 3:
			$salida=$fila['HOJAS_DIGITALIZADAS'];
			break;
		default: $salida=false;
	}
return $salida;
}

function pintarEstadisticaDetalle($fila,$indice,$numColumna){
	global $ruta_raiz,$encabezado,$krd,$db;
        include_once "$ruta_raiz/js/funtionImage.php";
        include_once "$ruta_raiz/tx/verLinkArchivo.php";
        $verLinkArchivo = new verLinkArchivo($db);
       	$numRadicado=$fila['RADICADO'];	

	switch ($numColumna){
	case 0:
		$salida=$indice;
		break;
	case 1:
		 if(!is_null($fila['HID_RADI_PATH']) && $fila['HID_RADI_PATH'] != '')
                          {
                           $radi = $fila['RADICADO'];
                           $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                           $valImg = $resulVali['verImg'];
                           if($valImg == "SI")
                            $salida="<center><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radi','$ruta_raiz');\">".$fila['RADICADO']."</a></center>";
                           else
		            $salida="<center><a class=vinculos href=javascript:noPermiso()>".$fila['RADICADO']."</a></center>";
                           } else   
                          $salida="<center class=\"leidos\">{$numRadicado}</center>";	
		break;
	case 2:
		$salida="<center class=\"leidos\">".$fila['USUARIO_ACTUAL']."</center>";
		break;
	case 3:
		$salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
		break;
	case 4:
		$radi = $fila['RADICADO'];
                $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                $valImg = $resulVali['verImg'];
		if($valImg == "SI")
		   $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICACION']."</a>";
		 else 
                    $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICACION']."</a>";
 		break;
	case 5:
		$salida="<center class=\"leidos\">".$fila['FECHA_DIGITALIZACION']."</center>";		
		break;
	}
	return $salida;
	}
?>
