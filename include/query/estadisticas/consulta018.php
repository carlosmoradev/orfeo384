<?php
/** RADICADOS DE ENTRADA RECIBIDOSç
  * 
  * @autor JAIRO H LOSADA - SSPD
  * @version ORFEO 3.1
  * 
  */
$coltp3Esp = '"'.$tip3Nombre[3][2].'"'; 
if(!$orno) $orno= 1;
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
$whereTipoRadicado  = str_replace("A."," ",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("a."," ",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("r."," ",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("R."," ",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("t."," ",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("b."," ",$whereTipoRadicado);
$whereTipoRadicado  = str_replace("B."," ",$whereTipoRadicado);

if($_GET["tipoDocumentos"]) $tipoDocumentos=$_GET["tipoDocumentos"];

$whereTipoDocumento = "";
if(!empty($tipoDocumentos) and $tipoDocumentos!='9999' and $tipoDocumentos!='9998' and $tipoDocumentos!='9997')
	{
		$whereTipoDocumento.=" AND SGD_TPR_CODIGO in ( ". $tipoDocumentos . ")";
	}elseif ($tipoDocumentos=="9997")	
	{
		$whereTipoDocumento.=" AND SGD_TPR_CODIGO = 0 ";
	}

//*$condiRep Esta condicion permite que se genere la estadistica con solo una respuesta por radicado
switch($db->driver)
{ 
  case 'postgres':
  case 'oracle':
  case 'oci8':
  case 'oci805':
  case 'ocipo':
    { if ( $dependencia_busq != 99999)
      { $condicionE = " AND DEPE_CODI_DEST=$dependencia_busq AND DEPE_USUA =$dependencia_busq "; }
    if ( $dependencia_busqOri != 99999)
      { $condicionEDes = " AND DEPE_ORIGEN=$dependencia_busqOri "; }     

	$queryE = "
	  SELECT MIN(USUARIO) USUARIO , count(DISTINCT RADICADO) RADICADOS , 
		COUNT (DISTINCT CASE WHEN radi_nume_salida IS NOT NULL THEN radi_nume_salida ELSE res_asociado END ) TRAMITADOS,
		MIN(USUA_CODI) HID_COD_USUARIO , MIN(depe_usua) HID_DEPE_USUA 
	  FROM tmp_tramite 
	  WHERE 
	      TO_CHAR(hist_fech,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'  
              $condicionE
              $condicionEDes
              $whereTipoRadicado";
      
      $queryE .= " GROUP BY USUA_LOGIN  ORDER BY $orno $ascdesc ";
      
      /*
       * CONSULTA PARA VER DETALLES 
      */
     if ($condiRep == "SI" )
      {	
   
       $queryEDetalle = 
        "SELECT 
         radicado
          , MAX(peticionario) peticionario
	  , MAX(municipio)    municipio
          , MAX(asunto)       asunto
	  , MAX(TO_CHAR(radi_fech_radi, 'DD/MM/YYYY')) fecha_radicacion
          , MAX(depe_actu_nomb) depe_actu_nomb
          , MAX(usuario)        usuario
          , MAX(depe_usua)      depe_usua
          , MAX(depe_asign_nomb) depe_asign_nomb
          , MAX(radi_path)       HID_RADI_PATH
          , MAX(par_serv_secue)  par_serv_secue
          , MAX(par_serv_nombre) par_serv_nombre
          , MAX(sgd_cau_codigo)  sgd_cau_codigo
          , MAX(sgd_cau_descrip) sgd_cau_descrip
          , MAX(sgd_dcau_codigo) sgd_dcau_codigo
	  , MAX(sgd_dcau_descrip) sgd_dcau_descrip
          , MAX(sgd_ddca_codigo)  sgd_ddca_codigo
          , MAX(sgd_ddca_descrip) sgd_ddca_descrip
	  , MAX(sgd_tpr_codigo)   sgd_tpr_codigo
	  , MAX(sgd_tpr_descrip)  sgd_tpr_descrip
          , MAX(sgd_tpr_termino)  sgd_tpr_termino
          , MAX(TO_CHAR(fech_vcmto, 'DD/MM/YYYY')) fecha_vencimiento
          , MAX(TO_CHAR(dias_vencimiento, 'DD')) dias_vencimiento
          , MAX(radi_nume_salida) radi_nume_salida
          , MAX(radi_path_resp)    HID_RADI_PATH_RESP
	  , MAX(depe_respuesta)   depe_respuesta
	  , MAX(depe_resp_nomb)   depe_resp_nomb
          , MAX(TO_CHAR(anex_radi_fech, 'DD/MM/YYYY'))   anex_radi_fech
          , MAX(TO_CHAR(anex_fech_envio, 'DD/MM/YYYY'))  anex_fech_envio
          , MAX(TO_CHAR(dias_proyecto, 'DD'))    dias_proyecto
          , MAX(TO_CHAR(dias_envio, 'DD'))       dias_envio
          , MAX(TO_CHAR(dias_tramite,'DD'))     dias_tramite
          , MAX(res_asociado)     res_asociado
          , MAX(radi_path_asoc)   HID_RADI_PATH_ASOC
          , MAX(depe_asociado)    depe_asociado
          , MAX(depe_asoc_nomb)   depe_asoc_nomb
          , MAX(TO_CHAR(fecha_asociado, 'DD/MM/YYYY'))   fecha_asociado
          , MAX(TO_CHAR(fech_envio_as, 'DD/MM/YYYY'))    fech_envio_as
          , MAX(TO_CHAR(dias_proyecto_as, 'DD')) dias_proyecto_as
          , MAX(TO_CHAR(dias_envio_as, 'DD'))    dias_envio_as
          , MAX(tramite_as)       tramite_as
          , MAX(sgd_tma_descrip)  sgd_tma_descrip
          , MAX(mrec_desc)        mrec_desc
          , MAX(TO_CHAR(hist_fech, 'DD/MM/YYYY'))        hist_fech
          , MAX(TO_CHAR(anex_fech_anex, 'DD/MM/YYYY'))   anex_fech_anex
        FROM  tmp_tramite
        WHERE 
          TO_CHAR(hist_fech,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
        $condicionE
        $whereTipoRadicado
        $whereTipoDocumento
        $condicionEDes
        ";
        

         $agrupamiento = "GROUP BY radicado ";


     }else 
     {
       $queryEDetalle = 
        "SELECT 
         radicado
          , peticionario
	  , municipio
          , asunto
	  , TO_CHAR(radi_fech_radi, 'DD/MM/YYYY') fecha_radicacion
          , depe_actu_nomb
          , usuario
          , depe_usua
          , depe_asign_nomb 
          , radi_path HID_RADI_PATH
          , par_serv_secue
          , par_serv_nombre
          , sgd_cau_codigo
          , sgd_cau_descrip
          , sgd_dcau_codigo
	  , sgd_dcau_descrip
          , sgd_ddca_codigo
          , sgd_ddca_descrip
	  , sgd_tpr_codigo 
	  , sgd_tpr_descrip  
          , sgd_tpr_termino 
          , TO_CHAR(fech_vcmto, 'DD/MM/YYYY') fecha_vencimiento
          , TO_CHAR(dias_vencimiento, 'DD') dias_vencimiento
          , radi_nume_salida
          , radi_path_resp   HID_RADI_PATH_RESP
	  , depe_respuesta
	  , depe_resp_nomb
          , TO_CHAR(anex_radi_fech, 'DD/MM/YYYY')   anex_radi_fech
          , TO_CHAR(anex_fech_envio, 'DD/MM/YYYY')  anex_fech_envio
          , TO_CHAR(dias_proyecto, 'DD') dias_proyecto  
          , TO_CHAR(dias_envio , 'DD') dias_envio
          , TO_CHAR(dias_tramite, 'DD') as dias_tramite
          , res_asociado
          , radi_path_asoc     HID_RADI_PATH_ASOC
          , depe_asociado
          , depe_asoc_nomb
          , TO_CHAR(fecha_asociado, 'DD/MM/YYYY') fecha_asociado
          , TO_CHAR(fech_envio_as, 'DD/MM/YYYY')  fech_envio_as
          , TO_CHAR(dias_proyecto_as , 'DD') dias_proyecto_as
          , TO_CHAR(dias_envio_as , 'DD') dias_envio_as
          , TO_CHAR(tramite_as, 'DD') as tramite_as
          , sgd_tma_descrip
          , mrec_desc
          , TO_CHAR(hist_fech , 'DD/MM/YYYY') hist_fech
        FROM  tmp_tramite
        WHERE 
          TO_CHAR(hist_fech,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin' 
        $condicionE
        $whereTipoRadicado
        $whereTipoDocumento
        $condicionEDes
        ";
       //$agrupamiento = "GROUP BY radicado ";
      }

    if($codEsp) $queryEDetalle .= " AND EESP_CODI = $codEsp ";
    $condicionUS = " AND USUA_CODI=$codUs
                     AND depe_usua = $depeUs "; 
    $orderE = " ORDER BY $orno $ascdesc";

    /** CONSULTA PARA VER TODOS LOS DETALLES 
    */ 
    $queryETodosDetalle = $queryEDetalle . $agrupamiento.$orderE;
    $queryEDetalle .= $condicionUS . $agrupamiento. $orderE; 
    //echo $queryETodosDetalle;
    }break;
}
if(isset($_GET['genDetalle'])&& $_GET['denDetalle']=1)

$titulos=array("#","1#RADICADO","2#PETICIONARIO","3#MUNICIPIO","4#ASUNTO","5#FECHA RADICACION","6#POBLACION","7#MEDIO RECEPCION",
               "8#DEPENDENCIA ACTUAL","9#DEPENDENCIA ASIGNADA","10#FECHA ASIGNACION","11#TEMA","12#SUB TEMA","13#DETALLE",
               "14#DESAGRAGADO","15#TIPO DOCUMENTAL","16#TERMINO(DIAS HABILES)","17#FECHA VENCIMIENTO","18#DIAS MORA",
               "19#RESPUESTA", "21#DEPENDENCIA RESPUESTA","21#FECHA CREACION","22#FECHA ENVIO","23#DIAS TRAMITE",
               "24#RESPUESTA ASOCIADA","25#DEPENDENCIA ASOCIADO","26#FECHA ASOCIACION","27#FECHA ENVIO ASOC","28#DIAS TRAMITE ASOC");


else    
  $titulos=array("#","1#Usuario","2#Radicados","3#Respuestas");

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
      $datosEnvioDetalle="tipoEstadistica=".$_GET['tipoEstadistica']."&amp;genDetalle=1&amp;usua_doc=".urlencode($fila['HID_USUA_DOC'])."&amp;dependencia_busq=".$_GET['dependencia_busq']."&amp;dependencia_busqOri=".$_GET['dependencia_busqOri']."&amp;fecha_ini=".$_GET['fecha_ini']."&amp;fecha_fin=".$_GET['fecha_fin']."&amp;tipoRadicado=".$_GET['tipoRadicado']."&amp;tipoDocumentos=".$GLOBALS['tipoDocumentos']."&amp;codUs=".$fila['HID_COD_USUARIO']."&amp;depeUs=".$fila['HID_DEPE_USUA'];
      $datosEnvioDetalle=(isset($_GET['usActivos']))?$datosEnvioDetalle."&codExp=$codExp&amp;usActivos=".$_GET['usActivos']:$datosEnvioDetalle;
      $datosEnvioDetalle=(isset($_GET['conSinRep']))?$datosEnvioDetalle."&condiRep=SI":$datosEnvioDetalle."&condiRep=NO";
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
      global $ruta_raiz,$encabezado,$krd,$db,$radi_anterior;
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
            $salida="<center class=\"leidos\">".$fila['PETICIONARIO']."</center>";
            break;
          case 3:
            $salida="<center class=\"leidos\">".$fila['MUNICIPIO']."</center>";
            break;
          case 4:
              $salida="<center class=\"leidos\">".$fila['ASUNTO']."</center>";
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
		$salida="<center class=\"leidos\">".$fila['SGD_TMA_DESCRIP']."</center>";  
		break;
	  case 7:
		$salida="<center class=\"leidos\">".$fila['MREC_DESC']."</center>";    
		break;
          case 8:
		$salida="<center class=\"leidos\">".$fila['DEPE_ACTU_NOMB']."</center>";    
		break;
          case 9:
		$salida="<center class=\"leidos\">".$fila['DEPE_ASIGN_NOMB']."</center>";    
		break;
          case 10:
		$salida="<center class=\"leidos\">".$fila['HIST_FECH']."</center>";    
		break;
          case 11:
		$salida="<center class=\"leidos\">".$fila['PAR_SERV_NOMBRE']."</center>";    
		break;
          case 12:
		$salida="<center class=\"leidos\">".$fila['SGD_CAU_DESCRIP']."</center>";    
		break;
          case 13:
		$salida="<center class=\"leidos\">".$fila['SGD_DCAU_DESCRIP']."</center>";    
		break;
          case 14:
		$salida="<center class=\"leidos\">".$fila['SGD_DDCA_DESCRIP']."</center>";    
		break;
          case 15:
		$salida="<center class=\"leidos\">".$fila['SGD_TPR_DESCRIP']."</center>";    
		break;
          case 16:
		$salida="<center class=\"leidos\">".$fila['SGD_TPR_TERMINO']."</center>";    
		break;
          case 17:
		$salida="<center class=\"leidos\">".$fila['FECHA_VENCIMIENTO']."</center>";    
		break;
          case 18:
		$salida="<center class=\"leidos\">".$fila['DIAS_VENCIMIENTO']."</center>";    
		break;
          case 19:
		if($fila['HID_RADI_PATH_RESP'])
                  {
                    $radi = $fila['RADI_NUME_SALIDA'];
                    $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                    $valImg = $resulVali['verImg'];
                    if($valImg == "SI")
                      $salida="<center><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radi','$ruta_raiz');\">".$fila['RADI_NUME_SALIDA']."</a></center>";
                    else
		       $salida="<center><a class=vinculos href=javascript:noPermiso()>".$fila['RADI_NUME_SALIDA']."</a></center>";
                    } else   
                      $salida="<center class=\"leidos\">".$fila['RADI_NUME_SALIDA']."</center>";   
		break;
          case 20:
		$salida="<center class=\"leidos\">".$fila['DEPE_RESP_NOMB']."</center>";    
		break;
          case 21:
		$salida="<center class=\"leidos\">".$fila['ANEX_RADI_FECH']."</center>";    
		break;
          case 22:
		$salida="<center class=\"leidos\">".$fila['ANEX_FECH_ENVIO']."</center>";    
		break;
          case 23:
		$salida="<center class=\"leidos\">".$fila['DIAS_TRAMITE']."</center>";    
		break;
          case 24:
		if($fila['HID_RADI_PATH_ASOC'])
                  {
                    $radi = $fila['RES_ASOCIADO'];
                    $resulVali = $verLinkArchivo->valPermisoRadi($radi);
                    $valImg = $resulVali['verImg'];
                    if($valImg == "SI")
                      $salida="<center><a class=\"vinculos\" href=\"#2\" onclick=\"funlinkArchivo('$radi','$ruta_raiz');\">".$fila['RES_ASOCIADO']."</a></center>";
                    else
		       $salida="<center><a class=vinculos href=javascript:noPermiso()>".$fila['RES_ASOCIADO']."</a></center>";
                    } else   
                      $salida="<center class=\"leidos\">".$fila['RES_ASOCIADO']."</center>";   		
		break;
          case 25:
		$salida="<center class=\"leidos\">".$fila['DEPE_ASOC_NOMB']."</center>";    
		break;
	  case 26:
		$salida="<center class=\"leidos\">".$fila['FECHA_ASOCIADO']."</center>";    
		break;
	  case 27:
		$salida="<center class=\"leidos\">".$fila['FECHA_ENVIO_AS']."</center>";    
		break;
 	  case 28:
		$salida="<center class=\"leidos\">".$fila['TRAMITE_AS']."</center>";    
		break;
      }
      return $salida;
    }
?>                                                         