<?
/** CONSUTLA 002 
	* Estadiscas por medio de recepcion Entrada
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
{	case 'mssql':
	case 'postgres':
		{	if ( $dependencia_busq != 99999)
			{
			$condicionE = "	AND r.depe_codi={$_GET['dependencia_busq']} AND b.depe_codi = $dependencia_busq";	
			}else {
				$condicionE = "	AND r.radi_depe_radi=b.depe_codi";	
			}
			$queryE = "SELECT c.mrec_desc AS MEDIO_RECEPCION, COUNT(1) AS Radicados, max(c.MREC_CODI) AS HID_MREC_CODI
					FROM RADICADO r, MEDIO_RECEPCION c, USUARIO b
					WHERE 
						r.radi_usua_radi=b.usua_CODI 
						AND r.mrec_codi=c.mrec_codi
						$condicionE
						AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini' AND '$fecha_fin'
						$whereTipoRadicado
					GROUP BY c.mrec_desc
					ORDER BY $orno $ascdesc";	
 			/** CONSULTA PARA VER DETALLES 
	 		*/
			$queryEDetalle = "SELECT $radi_nume_radi 								AS RADICADO, 
						".$db->conn->SQLDate('Y/m/d h:i:s','r.radi_fech_radi')."  	AS FECHA_RADICADO
						,c.MREC_DESC 	AS MEDIO_RECEPCION
						,r.RA_ASUN 	AS ASUNTO
						,b.usua_nomb 	AS USUARIO
						,r.RADI_PATH 	AS HID_RADI_PATH{$seguridad}
					FROM RADICADO r, USUARIO b, MEDIO_RECEPCION c
					WHERE 
						r.radi_usua_radi=b.usua_CODI 
						AND r.mrec_codi=c.mrec_codi
						$condicionE
						AND ".$db->conn->SQLDate('Y/m/d', 'r.radi_fech_radi')." BETWEEN '$fecha_ini'  AND '$fecha_fin'
						$whereTipoRadicado";			

					$orderE = "	ORDER BY $orno $ascdesc";			

		 	/** CONSULTA PARA VER TODOS LOS DETALLES 
	 		*/ 
                        $condiMedio = " AND c.mrec_codi = $mrecCodi ";
			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .= $condiMedio . $orderE;
		}break;
	//case 'oracle':
	//case 'ocipo':
	case 'oci8':
	case 'oci805':
		{	if ( $dependencia_busq != 99999)
			{	$condicionE = "	AND r.depe_codi =$dependencia_busq AND b.depe_codi = $dependencia_busq";	}
			$queryE = "SELECT c.mrec_desc MEDIO_RECEPCION, COUNT(1) Radicados, max(c.MREC_CODI) HID_MREC_CODI
					FROM RADICADO r, MEDIO_RECEPCION c, USUARIO b
					WHERE 
						r.radi_usua_radi=b.usua_CODI 
						AND r.mrec_codi=c.mrec_codi
						$condicionE
						AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
						$whereTipoRadicado
						$whereUsuario
					GROUP BY c.mrec_desc
					ORDER BY $orno $ascdesc";	
 			/** CONSULTA PARA VER DETALLES 
	 		*/
  			$condicionDep = " AND b.depe_codi = {$_GET['dependencia_busq']} ";

			$queryEDetalle = "SELECT r.RADI_NUME_RADI RADICADO
						,TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd hh:mi:ss') FECHA_RADICADO
						,c.MREC_DESC MEDIO_RECEPCION
						,r.RA_ASUN ASUNTO
						,b.usua_nomb USUARIO
						,r.RADI_PATH HID_RADI_PATH{$seguridad}
					FROM RADICADO r, USUARIO b, MEDIO_RECEPCION c
					WHERE 
						r.radi_usua_radi=b.usua_CODI 
						AND r.mrec_codi=c.mrec_codi
						";
		 if($_GET['mrecCodi']) 	$queryEDetalle .= "	AND c.mrec_codi={$_GET['mrecCodi']}";
					$queryEDetalle .=	$condicionE ."
						AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
						$whereTipoRadicado";			

					$orderE = "	ORDER BY $orno $ascdesc";			

		 	/** CONSULTA PARA VER TODOS LOS DETALLES 
	 		*/ 

			$queryETodosDetalle = $queryEDetalle . $orderE;
			$queryEDetalle .=  $orderE;
		}break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
	$titulos=array("#","1#MEDIO DE RECEPCION","2#FECHA RADICADO","3#ASUNTO","4#ANEXOS","5#NO HOJAS","6#MEDIO DE RECEPCION","9#USUARIO");
else 		
	$titulos=array("#","1#MEDIO","2#RADICADOS");


function pintarEstadistica($fila,$indice,$numColumna)
{
	global $ruta_raiz,$_GET,$_GET,$krd,$usua_doc;
	$salida="";
	switch ($numColumna)
	{
		case  0:
			$salida=$indice;
			break;
		case 1:
			$salida=$fila['MEDIO_RECEPCION'];
			break;
		case 2:
			$datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;codus=".$_GET['codus']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumento=".$_GET['tipoDocumento']."&amp;mrecCodi=".$fila['HID_MREC_CODI']."&amp;mrecCodi=".$fila['HID_MREC_CODI'];
			$datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
			$salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&amp;krd={$krd}\"  target=\"detallesSec\" >".$fila['RADICADOS']."</a>";
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
                                                $radi = $fila['RADICADO'];

                                                $resulVali = $verLinkArchivo->valPermisoRadi($radi);

                                                $valImg = $resulVali['verImg'];
						if($valImg == "SI")
		   				   $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICADO']."</a>";
		   				else 
                                                  $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICADO']."</a>";
						break;

					case 3:
						$salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
						break;
					case 4:
						$salida="<center class=\"leidos\">".$fila['ANEXOS']."</center>";
						break;
					case 5:
						$salida="<center class=\"leidos\">".$fila['N_HOJAS']."</center>";			
						break;	
					case 6:
						$salida="<center class=\"leidos\">".$fila['MEDIO_RECEPCION']."</center>";			
						break;	
					case 7:
						$salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";			
						break;
			}
			return $salida;
		}
