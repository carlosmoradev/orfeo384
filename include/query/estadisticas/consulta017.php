<?php
/** RADICADOS DE ENTRADA RECIBIDOSç
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


if($_GET["tipoDocumentos"]) $tipoDocumentos=$_GET["tipoDocumentos"];

$whereTipoDocumento = "";
if(!empty($tipoDocumentos) and $tipoDocumentos!='9999' and $tipoDocumentos!='9998' and $tipoDocumentos!='9997')
	{
		$whereTipoDocumento.=" AND t.SGD_TPR_CODIGO in ( ". $tipoDocumentos . ")";
	}elseif ($tipoDocumentos=="9997")	
	{
		$whereTipoDocumento.=" AND t.SGD_TPR_CODIGO = 0 ";
	}
$whereTipoRadicado  = str_replace("A.","r.",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("a.","r.",$whereTipoRadicado);
switch($db->driver)
{ 
  case 'postgres':
  case 'oracle':
  case 'oci8':
  case 'oci805':
  case 'ocipo':
    { if ( $dependencia_busq != 99999)
      { $condicionE = " AND h.DEPE_CODI_DEST=$dependencia_busq AND b.DEPE_CODI=$dependencia_busq "; }
      $queryE = "
          SELECT MIN(b.USUA_NOMB) USUARIO
          , count(r.RADI_NUME_RADI) RADICADOS
	  , count(a.anex_radi_nume) TRAMITADOS
          , MIN(b.USUA_CODI) HID_COD_USUARIO
          , MIN(b.depe_codi) HID_DEPE_USUA
        FROM RADICADO r LEFT JOIN (select distinct anex_radi_nume
       		from ANEXOS where anex_estado>=2) a ON r.RADI_NUME_RADI=a.ANEX_RADI_NUME
	, USUARIO b, HIST_EVENTOS h, SGD_TPR_TPDCUMENTO t
        WHERE 
          h.HIST_DOC_DEST=b.usua_doc
          AND r.tdoc_codi=t.sgd_tpr_codigo 
          $condicionE
          AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
          AND h.SGD_TTR_CODIGO in(2,9,12,16)
          AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
        $whereTipoRadicado 
        ";

$queryE = "
          SELECT MIN(b.USUA_NOMB) USUARIO
          , count(r.RADI_NUME_RADI) RADICADOS
          , MIN(b.USUA_CODI) HID_COD_USUARIO
          , MIN(b.depe_codi) HID_DEPE_USUA
        FROM RADICADO r , USUARIO b, HIST_EVENTOS h, SGD_TPR_TPDCUMENTO t
        WHERE
          h.HIST_DOC_DEST=b.usua_doc
          AND r.tdoc_codi=t.sgd_tpr_codigo
          $condicionE
          AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
          AND h.SGD_TTR_CODIGO in(2,9,12,16)
          AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
        $whereTipoRadicado
        ";

        
      if($codEsp) $queryE .= " AND r.EESP_CODI = $codEsp ";
      $queryE .= " GROUP BY b.USUA_LOGIN  ORDER BY $orno $ascdesc ";
      /** CONSULTA PARA VER DETALLES 
      */
      $queryEDetalle = "SELECT 
          r.RADI_NUME_RADI RADICADO
          , b.USUA_NOMB USUARIO
          , r.RA_ASUN ASUNTO
	  , r.radi_cuentai as REF 
          , TO_CHAR(r.RADI_FECH_RADI, 'DD/MM/YYYY HH24:MM:SS') FECHA_RADICACION
          , TO_CHAR(h.HIST_FECH, 'DD/MM/YYYY HH24:MM:SS') FECHA_DIGITALIZACION
          , r.RADI_PATH HID_RADI_PATH{$seguridad}
          , an.RADI_NUME_SALIDA
          , an.ANEX_RADI_FECH 
          , an.ANEX_FECH_ENVIO
          , t.SGD_TPR_TERMINO
          , t.SGD_TPR_DESCRIP
          , an.anex_radi_fech-r.RADI_FECH_RADI DIAS_TRAMITE
          , an.anex_fech_envio-r.RADI_FECH_RADI DIAS_TRAMITE_ENVIO
          , (".$db->sysdate()."-r.RADI_FECH_RADI)  DIAS_RAD
	  , d1.SGD_DIR_NOMREMDES DATO_1
	  , m1.muni_nomb MUNICIPIO_1
	  , (Select d.sgd_dir_nomremdes from sgd_dir_drecciones d where d.radi_nume_radi=r.radi_nume_radi AND d.SGD_DIR_TIPO=2) AS DATO_2
	  , an.ANEX_CREADOR
        FROM sgd_dir_drecciones d1,MUNICIPIO m1, USUARIO b, HIST_EVENTOS h, SGD_TPR_TPDCUMENTO t
          , RADICADO r left outer join anexos an 
          ON (R.RADI_NUME_RADI=an.ANEX_RADI_NUME ANd an.anex_estado>=2) 
        WHERE 
          r.tdoc_codi=t.sgd_tpr_codigo 
	  AND d1.radi_nume_radi=r.radi_nume_radi
	  AND d1.sgd_dir_tipo=1
	  AND d1.muni_codi=m1.muni_codi AND d1.dpto_codi=m1.dpto_codi
          AND h.HIST_DOC_DEST=b.usua_doc
          $condicionE
          AND h.RADI_NUME_RADI=r.RADI_NUME_RADI
          AND h.SGD_TTR_CODIGO in(2,9,12,16)

          AND TO_CHAR(r.radi_fech_radi,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
        $whereTipoRadicado $whereTipoDocumento";
    if($codEsp) $queryEDetalle .= " AND r.EESP_CODI = $codEsp ";
    $condicionUS = " AND b.USUA_CODI=$codUs
                     AND b.depe_codi = $depeUs "; 
    $orderE = " ORDER BY $orno $ascdesc";
    /** CONSULTA PARA VER TODOS LOS DETALLES 
    */ 
    $queryETodosDetalle = $queryEDetalle . $orderE;
    $queryEDetalle .= $condicionUS . $orderE; 
    }break;
}

if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)
  $titulos=array("#","1#RADICADO","2#USUARIO","3#ASUNTO","4#REF","5#FECHA RADICACION","6#FECHA DIGITALIZACION","7#RADICADO_SALIDA","8#FECHA_ANEX_SALIDA","9#FECHA ENVIO","10#TIPO DOCUMENTO","11#TERMINO","12#DIAS DE RESPUESTA","13#DIAS A ENVIO","14#DIAS DESDE RADICACION","15#DATO_1","16#MUNICIPIO_1","17#DATO_2","18#ANEX_CREADOR",);
else    
  $titulos=array("#","1#Usuario","2#Radicados","3#Tramitados");

function pintarEstadistica($fila,$indice,$numColumna)
{
  global $ruta_raiz,$_POST,$_GET,$krd;
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
      $datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;usua_doc=".urlencode($fila['HID_USUA_DOC'])."&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
      $datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&codExp=$codExp&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
      $salida="<a href=\"genEstadistica.php?{$datosEnvioDetalle}&codEsp=".$_GET["codEsp"]."&amp;krd={$krd}\"  target=\"detallesSec\" >".$fila['RADICADOS']."</a>";
      break;
    case 3:
      $salida=$fila['TRAMITADOS'];
      break;
    default: $salida=false;
  }
return $salida;
}
function pintarEstadisticaDetalle($fila,$indice,$numColumna){
      global $ruta_raiz,$encabezado,$krd,$db;
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
            if($fila['HID_RADI_PATH'])
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
              $salida="<center class=\"leidos\">".$fila['USUARIO']."</center>";
              break;
            case 3:
              $salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
              break;

            case 4:
              $salida="<center class=\"leidos\">".$fila['REF']."</center>";
              break;
            case 5:
		$radi = $fila['RADICADO'];
                $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                $valImg = $resulVali['verImg'];
		if($valImg == "SI")
		   $salida="<a class=\"vinculos\" href=\"{$ruta_raiz}verradicado.php?verrad=".$fila['RADICADO']."&amp;".session_name()."=".session_id()."&amp;krd=".$_GET['krd']."&amp;carpeta=8&amp;nomcarpeta=Busquedas&amp;tipo_carp=0 \" >".$fila['FECHA_RADICACION']."</a>";
		 else 
                    $salida="<a class=vinculos href=javascript:noPermiso()>".$fila['FECHA_RADICACION']."</a>";
            break;
          case 6:
            $salida="<center class=\"leidos\">".$fila['FECHA_DIGITALIZACION']."</center>";    
            break;
          case 7:
            $salida="<center class=\"leidos\">".$fila['RADI_NUME_SALIDA']."</center>";    
            break;
          case 8:
            $salida="<center class=\"leidos\">".$fila['ANEX_RADI_FECH']."</center>";    
            break;
          case 9:
            $salida="<center class=\"leidos\">".$fila['ANEX_FECH_ENVIO']."</center>";    
            break;
          case 10:
            $salida="<center class=\"leidos\">".$fila['SGD_TPR_DESCRIP']."</center>";    
            break;
          case 11:
            $salida="<center class=\"leidos\">".$fila['SGD_TPR_TERMINO']."</center>";
            break;
          case 12:
            $salida="<center class=\"leidos\">".$fila['DIAS_TRAMITE']."</center>";    
            break;
          case 13:
            $salida="<center class=\"leidos\">".$fila['DIAS_TRAMITE_ENVIO']."</center>";    
            break;
          case 14:
            $salida="<center class=\"leidos\">".$fila['DIAS_RAD']."</center>";    
            break;
          case 15:
            $salida="<center class=\"leidos\">".$fila['DATO_1']."</center>";
            break;
          case 16:
            $salida="<center class=\"leidos\">".$fila['MUNICIPIO_1']."</center>";
            break;
          case 17:
            $salida="<center class=\"leidos\">".$fila['DATO_2']."</center>";    
            break;
          case 18:
            $salida="<center class=\"leidos\">".$fila['ANEX_CREADOR']."</center>";    
            break;
      }
      return $salida;
    }
//echo $queryEDetalle;
?>
