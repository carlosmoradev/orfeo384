<?php
	print $encabezado1;
  	$krdOld=$krd;
 	session_start();
 	if(!$krd) $krd = $krdOld;
 	$ruta_raiz = "..";
    require_once("$ruta_raiz/_conf/constantes.php");
 	if(empty($_SESSION['dependencia'])) {
 		include (ORFEOPATH . "rec_session.php");
 	}

    $usua_doc = (!empty($_SESSION['usua_doc'])) ? $_SESSION['usua_doc']: null ;

    if (empty($usua_doc)) {
        echo "Error en Session del usuario";
        exit ();
    }
	if (!$nurad) $nurad= $rad;
	if($nurad){
		$ent = substr($nurad,-1);
	}

	include_once(ORFEOPATH . "include/db/ConnectionHandler.php");
    $db = new ConnectionHandler("$ruta_raiz");
	if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	include_once (ORFEOPATH . "include/tx/Historico.php");
    include_once (ORFEOPATH . "class_control/TipoDocumental.php");
    include_once (ORFEOPATH . "include/tx/Expediente.php");
    $coddepe = $dependencia;
	$codusua = $codusuario;
 	$isqlDepR = "SELECT RADI_DEPE_ACTU,
                        RADI_USUA_ACTU
                    from radicado
                    WHERE RADI_NUME_RADI = '$nurad'";

    $coditrdx =
				"SELECT
      				s.SGD_TPR_DESCRIP as TPRDESCRIP
				FROM
					RADICADO r,
     				SGD_TPR_TPDCUMENTO s
				WHERE
					r.TDOC_CODI = s.SGD_TPR_CODIGO AND
	 				r.RADI_NUME_RADI = '$nurad'";

   	$res_coditrdx 	= $db->conn->Execute($coditrdx);
   	$TDCactu 		= $res_coditrdx->fields['TPRDESCRIP'];

	$rsDepR 		= $db->conn->Execute($isqlDepR);
	
	if (!$rsDepR->EOF) {
        //$coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
		//$codusua = $rsDepR->fields['RADI_USUA_ACTU'];
	}

	$depex = $_SESSION["depe_nomb"]; $usuax = $_SESSION["usua_nomb"];

  	$trd = new TipoDocumental($db);
	$encabezadol  = "tipificar_documento.php?".session_name()."=".session_id();
    $encabezadol .= "&krd=$krd&nurad=$nurad&coddepe=$coddepe";
    $encabezadol .= "&codusuario=$codusua&codusua=$codusua";
    $encabezadol .= "&codusuario=$codusuario&depende=$depende";
    $encabezadol .= "&ent=$ent&tdoc=$tdoc&codiTRDModi=$codiTRDModi";
    $encabezadol .= "&codiTRDEli=$codiTRDEli&codserie=$codserie";
    $encabezadol .= "&tsub=$tsub&ind_ProcAnex=$ind_ProcAnex&texp=$texp";



	$trdExp         = new Expediente($db);
  	$numExpediente  = $trdExp->consulta_exp("$nurad");
	$mrdCodigo      = $trdExp->consultaTipoExpediente("$numExpediente");
	$trdExpediente  = $trdExp->descSerie." / ".$trdExp->descSubSerie;
	$descPExpediente = $trdExp->descTipoExp;
	$descFldExp     = $trdExp->descFldExp;
	$codigoFldExp   = $trdExp->codigoFldExp;
	$expUsuaDoc     = $trdExp->expUsuaDoc;

    // PARTE DE CODIGO DONDE SE IMPLEMENTA EL CAMBIO DE ESTADO AUTOMATICO AL TIPIFICAR.
	include (ORFEOPATH . "include/tx/Flujo.php");
	$objFlujo = new Flujo($db, $texp,$usua_doc);
	$expEstadoActual = $objFlujo->actualNodoExpediente($numExpediente);
	$arrayAristas = $objFlujo->aristasSiguiente($expEstadoActual);
	$aristaSRD  = $objFlujo->aristaSRD;
	$aristaSBRD = $objFlujo->aristaSBRD;
	$aristaTDoc = $objFlujo->aristaTDoc;
	$aristaTRad = $objFlujo->aristaTRad;
	$arrayNodos = $objFlujo->nodosSig;
	$aristaAutomatica = $objFlujo->aristaAutomatico;
	$aristaTDoc = $objFlujo->aristaTDoc;
	if($arrayNodos) {
	$i = 0;
	foreach ($arrayNodos as $value){
		$nodo = $value;
		$arAutomatica = $aristaAutomatica[$i];
		$aristaActual = $arrayAristas[$i];
		$arSRD =  $aristaSRD[$i];
		$arSBRD = $aristaSBRD[$i];
		$arTDoc = $aristaTDoc[$i];
		$arTRad = $aristaTRad[$i];
		$nombreNodo = $objFlujo->getNombreNodo($nodo,$texp);
		if($arAutomatica==1 and $arSRD==$codserie and $arSBRD==$tsub and $arTDoc==$tdoc and $arTRad==$ent) {
		if($insertar_registro) {
		$objFlujo->cambioNodoExpediente($numExpediente,
                                        $nurad,
                                        $nodo,
                                        $aristaActual,
                                        1,
                                        "Cambio de Estado Automatico.",
                                        $texp);
		$codiTRDS = $codiTRD;
		$i++;
		$TRD = $codiTRD;
		$observa = "*TRD*".$codserie."/".$codiSBRD." (Creacion de Expediente.)";
		include_once (ORFEOPATH . "include/tx/Historico.php");
		$radicados[] = $nurad;
		$tipoTx = 51;
		$Historico = new Historico($db);
				$rs=$db->conn->Execute($sql);
			   	$mensaje = "SE REALIZO CAMBIO DE ESTADO AUTOMATICAMENTE AL EXPEDIENTE No. < $numExpediente >
                            <BR> EL NUEVO ESTADO DEL EXPEDIENTE ES  <<< $nombreNodo >>>";
			}else
			{
				$mensaje = "SI ESCOGE ESTE TIPO DOCUMENTAL EL ESTADO DEL EXPEDIENTE  < $numExpediente >
			   			 CAMBIARA EL ESTADO AUTOMATICAMENTE A <BR> <<< $nombreNodo >>>";
			}

			echo "<table width=100% class=borde_tab>
					<tr><td align=center>
					<span class=titulosError align=center>
					$mensaje
					</span>
					</td></tr>
					</table><table><tr><td></td></tr></table>";

		}
		$i++;
	}
	}
?>
<html>
<head>
<title>Tipificar Documento</title>
<link href="../estilos/orfeo.css" rel="stylesheet" type="text/css">
<script>
function regresar(){
	document.TipoDocu.submit();
}
</script>
</head>
<body bgcolor="#FFFFFF">
<form method="post" action="<?=$encabezadol?>" name="TipoDocu">
<?php
  // Adicion nuevo Registro
  //if ($tdoc !=0 && $tsub !=0 && $codserie !=0 && $varInser == "Aceptar")
    if ($insertar_registro && $tdoc !=0 && $tsub !=0 && $codserie !=0 ) {
    include_once(ORFEOPATH . "include/query/busqueda/busquedaPiloto1.php");
	$sql = "SELECT $radi_nume_radi AS RADI_NUME_RADI
					FROM SGD_RDF_RETDOCF r
					WHERE RADI_NUME_RADI = '$nurad'
				    AND  DEPE_CODI =  '$dependencia'";
		$rs=$db->conn->Execute($sql);
		$radiNumero = $rs->fields["RADI_NUME_RADI"];
		if ($radiNumero !='') {
		   $codserie = 0 ;
  		   $tsub = 0  ;
  		   $tdoc = 0;
		   $mensaje_err = "<HR>
		   <center><B><font color='RED'>
		   	Ya existe una Clasificacion para esta dependencia <$coddepe>
                <BR>
                VERIFIQUE LA INFORMACION E INTENTE DE NUEVO
		   	</font></B></center>
		   	<HR>";
		  } else {
		  	
			$isqlTRD = "
					select 
						SGD_MRD_CODIGO
					from 
						SGD_MRD_MATRIRD
					where 
						DEPE_CODI 			= '$dependencia'
				 	    and SGD_SRD_CODIGO 	= '$codserie'
				        and SGD_SBRD_CODIGO = '$tsub'
					    and SGD_TPR_CODIGO 	= '$tdoc'";
			
			$rsTRD = $db->conn->Execute($isqlTRD);
			$i = 0;
			
			while(!$rsTRD->EOF) {
	    		$codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
				$codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
	    		$i++;
				$rsTRD->MoveNext();
			}
						
			$radicados = $trd->insertarTRD($codiTRDS,$codiTRD,$nurad,$coddepe, $codusua);
			
		    $TRD = $codiTRD;
			include (ORFEOPATH . "radicacion/detalle_clasificacionTRD.php");
			$sqlH = "SELECT $radi_nume_radi RADI_NUME_RADI
					FROM SGD_RDF_RETDOCF r
					WHERE r.RADI_NUME_RADI = '$nurad'
				    AND r.SGD_MRD_CODIGO =  '$codiTRD'";
			$rsH = $db->conn->Execute($sqlH);
			$i = 0;
			while(!$rsH->EOF) {
	    		$codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
	    		$i++;
				$rsH->MoveNext();
			}

  		  	$Historico = new Historico($db);
  		  	$observa   = "Datos Anteriores: Usuario: " . $usuax . " Dependencia: " . $depex . " Tipo Documental anterior: ". $TDCactu . "";
			$radiModi  = $Historico->insertarHistorico($codiRegH,
                                                        $dependencia,
                                                        $codusuario,
                                                        $dependencia,
                                                        $codusuario,
                                                        $observa,
                                                        32);
			
			
			//guardar el registro en el historico de tipo documental.
			//permite controlar cambios del td de un radicado
			
			$queryGrabar	= "INSERT INTO SGD_HMTD_HISMATDOC(											
                                            SGD_HMTD_FECHA,
                                            RADI_NUME_RADI,
                                            USUA_CODI,
                                            SGD_HMTD_OBSE,
                                            USUA_DOC,
                                            DEPE_CODI,
                                            SGD_MRD_CODIGO
                                            )";
			
	    	$queryGrabar 	.= " VALUES(
	    						".$db->conn->OffsetDate(0,$db->conn->sysTimeStamp).",
	    						$nurad,
	    						$codusua,
								'El usuario: $usuax Cambio el tipo de documento',
								$usua_doc,
								$dependencia,
								'$codiTRD')";
			
			$ejecutarQuerey	= $db->conn->Execute($queryGrabar);
			
	    	if(empty($ejecutarQuerey)){
	    		echo 'No se guardo el registro en historico documental';
	    	}
			
		  	// Actualiza el campo tdoc_codi de la tabla Radicados
		 	$radiUp = $trd->actualizarTRD($codiRegH,$tdoc);
  			$codserie = 0;
  			$tsub = 0;
  			$tdoc = 0;
		 }
  	}
?>
	<table border=0 width=70% align="center" class="borde_tab" cellspacing="0">
	  <tr align="center" class="titulos2">
	    <td height="15" class="titulos2">APLICACION DE LA TRD</td>
      </tr>
	 </table>
 	<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
      <tr >
	  <td class="titulos5" >SERIE</td>
	  <td class=listado5 >
<?php
    if(!$tdoc) $tdoc = 0;
    if(!$codserie) $codserie = 0;
	if(!$tsub) $tsub = 0;
	$fechah=date("dmy") . " ". time("h_m_s");
	$fecha_hoy = Date("Y-m-d");
	$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
	$check=1;
	$fechaf=date("dmy") . "_" . time("hms");
	$num_car = 4;
	$nomb_varc = "s.sgd_srd_codigo";
	$nomb_varde = "s.sgd_srd_descrip";
   	include (ORFEOPATH . "include/query/trd/queryCodiDetalle.php");
	$querySerie = "select distinct ($sqlConcat) as detalle,
                        s.sgd_srd_codigo
                 from sgd_mrd_matrird m, sgd_srd_seriesrd s
                 where m.depe_codi = '$dependencia'
                       and s.sgd_srd_codigo = m.sgd_srd_codigo and
                       m.sgd_mrd_esta = '1' and
                       $sqlFechaHoy between s.sgd_srd_fechini and s.sgd_srd_fechfin
                 order by detalle";
	$rsD=$db->conn->query($querySerie);
	$comentarioDev = "Muestra las Series Docuementales";
	include (ORFEOPATH . "include/tx/ComentarioTx.php");
	print $rsD->GetMenu2("codserie",
                            $codserie,
                            "0:-- Seleccione --",
                            false,
                            "",
                            "onChange='submit()' class='select'");
 ?>
      </td>
     </tr>
   <tr>
     <td class="titulos5" >SUBSERIE</td>
	 <td class=listado5 >
<?php
	$nomb_varc = "su.sgd_sbrd_codigo";
	$nomb_varde = "su.sgd_sbrd_descrip";
	include (ORFEOPATH . "include/query/trd/queryCodiDetalle.php");
   	$querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo
	         from sgd_mrd_matrird m, sgd_sbrd_subserierd su
			 where m.depe_codi = '$dependencia' and
                    m.sgd_srd_codigo = '$codserie' and
                    su.sgd_srd_codigo = '$codserie' and
                    su.sgd_sbrd_codigo = m.sgd_sbrd_codigo and
			        m.sgd_mrd_esta = '1' and
                    $sqlFechaHoy between su.sgd_sbrd_fechini and
                    su.sgd_sbrd_fechfin
			 order by detalle";
	$rsSub=$db->conn->Execute($querySub);
	include (ORFEOPATH . "include/tx/ComentarioTx.php");
	print $rsSub->GetMenu2("tsub",
                            $tsub,
                            "0:-- Seleccione --",
                            false,
                            "",
                            "onChange='submit()' class='select'");

?>
     </td>
     </tr>
   <tr>
     <td class="titulos5" >TIPO DE DOCUMENTO</td>
 	 <td class=listado5 >
<?php
	$nomb_varc = "t.sgd_tpr_codigo";
	$nomb_varde = "t.sgd_tpr_descrip";
	include (ORFEOPATH . "include/query/trd/queryCodiDetalle.php");
	$queryTip = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo
	         from sgd_mrd_matrird m, sgd_tpr_tpdcumento t
			 where m.depe_codi = '$dependencia'
			       and m.sgd_mrd_esta = '1'
 			       and m.sgd_srd_codigo = '$codserie'
			       and m.sgd_sbrd_codigo = '$tsub'
 			       and t.sgd_tpr_codigo = m.sgd_tpr_codigo
	  			   and t.sgd_tpr_tp$ent='1'
			 order by detalle";

	$rsTip=$db->conn->Execute($queryTip);
	$ruta_raiz = "..";
	include (ORFEOPATH . "include/tx/ComentarioTx.php");
	print $rsTip->GetMenu2("tdoc", $tdoc, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );		 
	?>
    </td>
    </tr>
   </table>
<br>
	<table border=0 width=70% align="center" class="borde_tab">
	  <tr align="center">
		<td width="33%" height="25" class="listado2" align="center">
         <center><input name="insertar_registro" type=submit class="botones_funcion" value=" Insertar "></center></TD>
		 <td width="33%" class="listado2" height="25">
		 <center><input name="actualizar" type="button" class="botones_funcion" id="envia23" onClick="procModificar();" value=" Modificar "></center></TD>
        <td width="33%" class="listado2" height="25">
		 <center><input name="Cerrar" type="button" class="botones_funcion" id="envia22" onClick="window.close();opener.regresar();"value="Cerrar"></center></TD>
	   </tr>
	</table>
	<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
	  <tr align="center">
	    <td>
<?php
		include_once (ORFEOPATH . "radicacion/lista_tiposAsignados.php");
		if ($ind_ProcAnex=="S") {
	      	echo " <br> <input type='button' value='Cerrar' class='botones_largo' onclick='opener.regresar();window.close();'> ";
}
		?>
	 	</td>
	   </tr>
	</table>
<script>
function borrarArchivo(anexo,linkarch){
	if (confirm('Esta seguro de borrar este Registro ?')) {
		nombreventana="ventanaBorrarR1";
		url="tipificar_documentos_transacciones.php?sessid=<?=session_id()?>&krd=<?=$krd?>&borrar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&nurad=<?=$nurad?>&depex=<?=$depex?>&usux=<?=$usux?>&codiTRDEli="+anexo+"&linkarchivo="+linkarch;
		window.open(url,nombreventana,'height=250,width=300');
	}
return;
}

//<!-- Funcion que modifica la trd existente-->
function procModificar(){
    if (document.TipoDocu.tdoc.value != 0 &&
    document.TipoDocu.codserie.value != 0 &&
    document.TipoDocu.tsub.value != 0) {
    <? $sql = "SELECT 
				COUNT(RADI_NUME_RADI) AS TOTAL
			FROM 
				SGD_RDF_RETDOCF
			WHERE 
				RADI_NUME_RADI = $nurad
				AND  DEPE_CODI = $coddepe";
			
	$rs 				= $db->conn->Execute($sql);
	$total 				= $rs->fields["TOTAL"];
	if(empty($total)){	
		    echo 'alert("No existe Registro para Modificar ");';
	}else{
	?>
        var agree = confirm('Esta Seguro de Modificar el Registro de su Dependencia ?');
        if (agree == true) {
            nombreventana = "ventanaModiR1";
            url = "tipificar_documentos_transacciones.php?sessid=<?=session_id()?>&krd=<?=$krd?>&modificar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&tdoc=<?=$tdoc?>&tsub=<?=$tsub?>&codserie=<?=$codserie?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&depex=<?=$depex?>&usuax=<?=$usuax?>&dependencia=<?=$dependencia?>&nurad=<?=$nurad?>";
            window.open(url, nombreventana, 'height=200,width=300');
        }
    <? }?>
    }else {
      alert("Campos obligatorios");
    }
    return;
}
</script>
</form>
</span>
<p>
<?=$mensaje_err?>
</p>
</span>
</body>
</html>
