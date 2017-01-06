<?php
/** CONSUTLA 004
	* Estadiscas de Numero de Radicados digitalizados y Hojas Digitalizadas.
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

$whereTipoRadicadoD = $whereTipoRadicado;
$whereTipoRadicado  = str_replace("r.","a.",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("R.","a.",$whereTipoRadicado);
switch($db->driver)
{	case 'mssql':
	case 'ocipo':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND h.DEPE_CODI=$dependencia_busq AND b.depe_codi = $dependencia_busq";	}
			$queryE = "
	    	SELECT b.USUA_NOMB USUARIO
				, count(1) RADICADOS
				, SUM(a.RADI_NUME_HOJA) HOJAS_DIGITALIZADAS	
				, MIN(b.USUA_CODI) HID_COD_USUARIO
				, MIN(b.DEPE_CODI) HID_DEPENDENCIA
			FROM RADICADO a, USUARIO b, HIST_EVENTOS h
			WHERE 
				h.USUA_CODI=b.usua_CODI 
				AND b.depe_codi = h.depe_codi
				$condicionE
				AND h.RADI_NUME_RADI=a.RADI_NUME_RADI
				AND h.SGD_TTR_CODIGO IN(22,42)
				AND TO_CHAR(a.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicado 
			GROUP BY b.USUA_NOMB
			ORDER BY $orno $ascdesc";
 			/** CONSULTA PARA VER DETALLES 
	 		*/

			$queryEDetalle = "SELECT 
				R.RADI_NUME_RADI RADICADO
				, b.USUA_NOMB USUARIO_DIGITALIZADOR
				, h.HIST_OBSE OBSERVACIONES
				, TO_CHAR(R.RADI_FECH_RADI, 'DD/MM/YYYY HH24:MI:SS') FECHA_RADICACION
				, TO_CHAR(h.HIST_FECH, 'DD/MM/YYYY HH24:MI:SS') FECHA_DIGITALIZACION
				, mr.mrec_desc MEDIO_RECEPCION
				,R.RADI_PATH HID_RADI_PATH{$seguridad}
				FROM RADICADO R, USUARIO b, HIST_EVENTOS h, MEDIO_RECEPCION mr
			WHERE 
				h.USUA_CODI=b.usua_CODI 
				AND b.depe_codi = h.depe_codi
				$condicionE
				AND h.RADI_NUME_RADI=R.RADI_NUME_RADI
				AND R.MREC_CODI=mr.MREC_CODI
				AND b.USUA_CODI=$codUs
                                AND b.USUA_CODI = $depe_usuario
				AND h.SGD_TTR_CODIGO IN(22,42)
				AND TO_CHAR(R.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicadoD 
			ORDER BY $orno $ascdesc";
		}break;
	case 'postgres':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND h.DEPE_CODI=$dependencia_busq AND b.depe_codi = $dependencia_busq";	}
			$queryE = "
	    	SELECT b.USUA_NOMB AS USUARIO
				, count(1) AS RADICADOS
				, SUM(a.RADI_NUME_FOLIO) AS HOJAS_DIGITALIZADAS	
				, MIN(b.USUA_CODI) AS HID_COD_USUARIO
                                , MIN(b.DEPE_CODI) AS HID_DEPENDENCIA
			FROM RADICADO a, USUARIO b, HIST_EVENTOS h
			WHERE 
				h.USUA_CODI=b.usua_CODI 
				AND b.depe_codi = h.depe_codi
				$condicionE
				AND h.RADI_NUME_RADI=a.RADI_NUME_RADI
				AND h.SGD_TTR_CODIGO IN(22,42)
				AND ".$db->conn->SQLDate('Y/m/d', 'a.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicado 
			GROUP BY b.USUA_NOMB
			ORDER BY $orno $ascdesc";
                  
 			/** CONSULTA PARA VER DETALLES 
	 		*/

			$queryEDetalle = "SELECT 
				$radi_nume_radi AS RADICADO
				, b.USUA_NOMB AS USUARIO_DIGITALIZADOR
				, h.HIST_OBSE AS OBSERVACIONES, ".
				$db->conn->SQLDate('Y/m/d H:i:s','r.radi_fech_radi')." AS FECHA_RADICACION, ".
				$db->conn->SQLDate('Y/m/d H:i:s','h.HIST_FECH')." AS FECHA_DIGITALIZACION
				, mr.mrec_desc AS MEDIO_RECEPCION
				, t.sgd_tpr_descrip AS TIPO_DE_DOCUMENTO
				,r.RADI_PATH AS HID_RADI_PATH{$seguridad}
				FROM RADICADO r, USUARIO b, HIST_EVENTOS h, sgd_tpr_tpdcumento t , MEDIO_RECEPCION mr
			WHERE 
				h.USUA_CODI=b.usua_CODI 
				AND b.depe_codi = h.depe_codi
				AND r.tdoc_codi = t.sgd_tpr_codigo
				$condicionE
				AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
				AND r.MREC_CODI=mr.MREC_CODI
				AND h.SGD_TTR_CODIGO IN(22,42)
				AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereTipoRadicadoD 
			        ";
                        $orderE = " ORDER BY $orno $ascdesc";
                        $condicionUS = " AND b.USUA_CODI=$codUs
                                         AND b.depe_codi = $depe_usuario "; 
                        /** CONSULTA PARA VER TODOS LOS DETALLES 
                         */ 
                       $queryETodosDetalle = $queryEDetalle . $orderE;
                       $queryEDetalle .= $condicionUS . $orderE; 
		}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
}

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1){
		$titulos=array("#","1#RADICADO","2#USUARIO DIGITALIZADOR","3#OBSERVACIONES","4#FECHA RADICACION","5#FECHA DIGITALIZACION","6#MEDIO DE RECEPCION", "7#TIPO DE DOCUMENTO");
}
	else{
		$titulos=array("#","1#Usuario","2#Radicados","3#HOJAS DIGITALIZADAS");
} 		

function pintarEstadistica($fila,$indice,$numColumna){
        	global $ruta_raiz,$_POST,$_GET,$krd,$usua_doc;
        	$salida="";
        	switch ($numColumna){
        		case  0:
        			$salida=$indice;
        			break;
        		case 1:	
        			$salida=$fila['USUARIO'];
        		break;
        		case 2:
        			$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;depe_usuario=".$fila['HID_DEPENDENCIA']."&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumento=".$_GET['tipoDocumento']."&amp;codUs=".$fila['HID_COD_USUARIO'];
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
	global $ruta_raiz,$db,$encabezado,$krd;
	//$verImg=($fila['SGD_SPUB_CODIGO']==1)?($fila['USUARIO']!=$_SESSION['usua_nomb']?false:true):($fila['USUA_NIVEL']>$_SESSION['nivelus']?false:true);
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
			$salida="<center class=\"leidos\">".$fila['USUARIO_DIGITALIZADOR']."</center>";
			break;
		case 3:
			$salida="<center class=\"leidos\">".$fila['OBSERVACIONES']."</center>";
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
		case 6:
			$salida="<center class=\"leidos\">".$fila['MEDIO_RECEPCION']."</center>";
			break;
		case 7:
			$salida="<center class=\"leidos\">".$fila['TIPO_DE_DOCUMENTO']."</center>";
			break;
	}
	return $salida;
}
?>
