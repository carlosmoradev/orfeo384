<?php    
/** CONSUTLA 001 
  * Estadiscas por usuario
  * @autor JAIRO H LOSADA Correlibre.org
  * @version ORFEO 3.1
  * 
  * Arreglo por LIliana Gomez 2012
  */
$coltp3Esp = '"'.$tip3Nombre[3][2].'"';
if(!$orno) $orno=2;
$tmp_substr = $db->conn->substr;
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

$ln=$_SESSION["digitosDependencia"];

if($_GET["tipoDocumentos"]) $tipoDocumentos=$_GET["tipoDocumentos"];

$whereTipoDocumento = "";
if(!empty($tipoDocumentos) and $tipoDocumentos!='9999' and $tipoDocumentos!='9998' and $tipoDocumentos!='9997')
	{
		$whereTipoDocumento.=" AND t.SGD_TPR_CODIGO in ( ". $tipoDocumentos . ")";
	}elseif ($tipoDocumentos=="9997")	
	{
		$whereTipoDocumento.=" AND t.SGD_TPR_CODIGO = 0 ";
	}
if(!empty($depeUs)){
    $condicionDep = "AND b.depe_codi = $depeUs";
    $condicionE   = "AND b.USUA_CODI = $codUs $condicionDep ";
}


switch($db->driver)
{
	case 'mssql':
	case 'postgresql':	
	case 'postgres':	
	{	if($tipoDocumentos=='9999')
		{ $queryE = "SELECT b.USUA_NOMB as USUARIO, count(1) as RADICADOS, MIN(USUA_CODI) as HID_COD_USUARIO
		, MIN(b.depe_codi) as HID_DEPE_USUA 
		FROM RADICADO r 
		INNER JOIN USUARIO b ON r.radi_usua_radi=b.usua_CODI AND r.depe_codi=b.depe_codi
		WHERE ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin' 
		$whereDependencia $whereActivos $whereTipoRadicado 
					GROUP BY b.USUA_NOMB ORDER BY $orno $ascdesc";
		}
		else
		{	$queryE = "SELECT b.USUA_NOMB as USUARIO, t.SGD_TPR_DESCRIP as TIPO_DOCUMENTO, count(1) as RADICADOS,
						MIN(USUA_CODI) as HID_COD_USUARIO, MIN(SGD_TPR_CODIGO) as HID_TPR_CODIGO, MIN(b.depe_codi) as HID_DEPE_USUA
					FROM RADICADO r 
						INNER JOIN USUARIO b ON r.RADI_USUA_RADI = b.USUA_CODI AND r.depe_codi=b.depe_codi  
						LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.TDOC_CODI = t.SGD_TPR_CODIGO
					WHERE ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin' 
						$whereDependencia $whereActivos $whereTipoRadicado 
					GROUP BY b.USUA_NOMB,t.SGD_TPR_DESCRIP ORDER BY $orno $ascdesc";		
		}
                
 		/** CONSULTA PARA VER DETALLES 
         * Se incluye una nueva restriccion para que en el detalle unicamente 
         * muestre la direccion remitente/destinatario
         * Junio 14 2012
	 	*/
		$queryEDetalle = "SELECT DISTINCT $radi_nume_radi as RADICADO
			,r.RADI_FECH_RADI as FECHA_RADICADO
			,t.SGD_TPR_DESCRIP as TIPO_DE_DOCUMENTO
			,r.RA_ASUN as ASUNTO 
			,r.RADI_DESC_ANEX 
			,r.RADI_NUME_HOJA 
			,b.usua_nomb as Usuario
			,r.RADI_PATH as HID_RADI_PATH {$seguridad}
			, dir.SGD_DIR_NOMREMDES as REMITENTE
			,df.DEPE_NOMB as DEPE_NOMB
			,da.DEPE_NOMB as DEPE_NOMB_ACTUAL
			,r.RADI_USU_ANTE
			,ua.usua_nomb AS USUA_NOMB_ACTUAL
			FROM dependencia df,dependencia da,USUARIO ua, RADICADO r
			INNER JOIN USUARIO b ON r.radi_usua_radi=b.usua_CODI AND r.depe_codi=b.depe_codi
			LEFT OUTER JOIN SGD_TPR_TPDCUMENTO t ON r.tdoc_codi=t.SGD_TPR_CODIGO 
			LEFT OUTER JOIN SGD_DIR_DRECCIONES dir ON r.radi_nume_radi = dir.radi_nume_radi	
                        and dir.sgd_dir_tipo = '1'
			WHERE 
			r.radi_depe_actu=da.depe_codi AND
			r.radi_depe_actu=ua.depe_codi AND
			r.radi_usua_actu=ua.usua_codi AND
			r.RADI_DEPE_RADI=df.DEPE_CODI AND	
            ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'  
            $whereTipoRadicado $whereTipoDocumento ";
		$orderE = "	ORDER BY $orno $ascdesc";

		$queryETodosDetalle = $queryEDetalle . $whereDependencia . $orderE;
		$queryEDetalle .= $condicionE . $orderE;

                 
	}break;
	case 'oracle':
	case 'oci8':
	case 'oci805':
	case 'ocipo':
	{
		if($tipoDocumentos=='9999')
		{
			$queryE = 
			"SELECT b.USUA_NOMB USUARIO, 
				count(1) RADICADOS, 
				MIN(USUA_CODI) HID_COD_USUARIO, 
				MIN(depe_codi) HID_DEPE_USUA
			FROM RADICADO r, USUARIO b, sgd_dir_drecciones dir
			WHERE 
				r.radi_nume_radi=dir.radi_nume_radi and
				r.radi_usua_radi=b.usua_CODI 
				AND r.depe_codi=b.depe_codi
				$whereDependencia
				AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereActivos
			$whereTipoRadicado 
			GROUP BY b.USUA_NOMB
			ORDER BY $orno $ascdesc";
		}
		else
		{
			$queryE = "
		    SELECT b.USUA_NOMB USUARIO
				, t.SGD_TPR_DESCRIP TIPO_DOCUMENTO
				, count(1) RADICADOS
				, MIN(USUA_CODI) HID_COD_USUARIO
				, MIN(SGD_TPR_CODIGO) HID_TPR_CODIGO
				, MIN(depe_codi) HID_DEPE_USUA
			FROM RADICADO r, USUARIO b, SGD_TPR_TPDCUMENTO t
			WHERE 
				r.radi_usua_radi=b.usua_CODI 
				AND r.tdoc_codi=t.SGD_TPR_CODIGO (+)
				AND r.depe_codi=b.depe_codi
				$whereDependencia 
				AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
				$whereActivos
			$whereTipoRadicado 
			GROUP BY b.USUA_NOMB,t.SGD_TPR_DESCRIP
			ORDER BY $orno $ascdesc";
		}
 		/** CONSULTA PARA VER DETALLES 
	 	*/
		$queryEDetalle = "SELECT DISTINCT r.RADI_NUME_RADI RADICADO
			,r.RADI_FECH_RADI FECHA_RADICADO
			,t.SGD_TPR_DESCRIP 	TIPO_DE_DOCUMENTO
			,r.RA_ASUN ASUNTO
			,r.RADI_DESC_ANEX ANEXOS
			,r.RADI_NUME_HOJA N_HOJAS
			,b.usua_nomb USUARIO
			,r.RADI_PATH HID_RADI_PATH
			,dir.sgd_dir_nomremdes REMITENTE 
			FROM RADICADO r, 
				USUARIO b, 
				SGD_TPR_TPDCUMENTO t,
				sgd_dir_drecciones dir
		WHERE 
			r.radi_nume_radi = dir.radi_nume_radi 
			and r.radi_usua_radi=b.usua_CODI 
			AND r.tdoc_codi=t.SGD_TPR_CODIGO 
			AND r.depe_codi=b.depe_codi
			AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini' AND '$fecha_fin'
		$whereTipoRadicado $whereTipoDocumento";
		$orderE = "	ORDER BY $orno $ascdesc";			

		/** CONSULTA PARA VER TODOS LOS DETALLES 
	 	*/ 
		$queryETodosDetalle = $queryEDetalle . $condicionDep . $orderE;
		$queryEDetalle .= $condicionE . $orderE;
                
	}break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1){
	$titulos=array("#","1#RADICADO","2#FECHA RADICADO","3#TIPO DOCUMENTO","4#ASUNTO","5#NO HOJAS","6#USUARIO","7#REMITENTE","8#DEPENDENCIA_INICIAL","9#DEPENDENCIA_ACTUAL","10#USUARIO ACTUAL","11#USUARIO ANTERIOR");
}
else 		
	$titulos=array("#","1#Usuario","2#Radicados");
		
function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_POST,$_GET,$krd,$usua_doc,$tipoDocumentos;
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
	default: $salida=false;
	break;
}
	return $salida;
}

function pintarEstadisticaDetalle($fila,$indice,$numColumna)
{
	global $ruta_raiz,$encabezado,$krd, $db;
        include_once "$ruta_raiz/js/funtionImage.php";
        include_once "$ruta_raiz/tx/verLinkArchivo.php";
        $verLinkArchivo = new verLinkArchivo($db);
        $numRadicado=$fila['RADICADO'];	
	switch ($numColumna)
	{
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
		 $radi = $fila['RADICADO'];
                 $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                 $valImg = $resulVali['verImg'];
		 if($valImg == "SI")
		   $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICADO']."</a>";
		 else 
                   $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICADO']."</a>";
	        break;
	case 3:
		$salida="<center class=\"leidos\">".$fila['TIPO_DE_DOCUMENTO']."</center>";		
		break;
	case 4:
		$salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
		break;
	case 5:
		$salida="<center class=\"leidos\">".$fila['N_HOJAS']."</center>";			
		break;	
	case 6:
		$salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";			
		break;	
	case 7:
		$salida="<center class=\"leidos\">".$fila['REMITENTE']."</center>";			
		break;
	case 8:
		$salida="<center class=\"leidos\">".$fila['DEPE_NOMB']."</center>";			
		break;		
	case 9:
		$salida="<center class=\"leidos\">".$fila['DEPE_NOMB_ACTUAL']."</center>";			
		break;
	case 10:
		$salida="<center class=\"leidos\">".$fila['USUA_NOMB_ACTUAL']."</center>";			
		break;
	case 11:
		$salida="<center class=\"leidos\">".$fila['RADI_USU_ANTE']."</center>";			
		break;
	}
	return $salida;
}
?>
