<?php
session_start();
$ruta_raiz = ".."; 
if (!$_SESSION['dependencia'])
   header ("Location: $ruta_raiz/cerrar_session.php");

/* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	     */
/*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS     */
/*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com   */
/* ===========================                                                       */
/*                                                                                   */
/* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
/* bajo los terminos de la licencia GNU General Public publicada por                 */
/* la "Free Software Foundation"; Licencia version 3. 			             */
/*                                                                                   */
/* Copyleft  2008 por :	  	  	                                     */
/* SSPd "Superintendencia de Servicios Publicos Domiciliarios"                       */
/*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
/*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
/*   */
/*   EMPRESA IYU                                             */
/*   Hollman Ladino       hladino@gmail.com                Desarrollador             */
/*                                                                                   */
/* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
/*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
/*  Infometrika            info@infometrika.com    2009-05  Eliminacino Variables Globales */
/*************************************************************************************/

if($_GET["codserie"]) $codserie = $_GET["codserie"];
if($_GET["tsub"]) $tsub = $_GET["tsub"];
if($_GET["tdoc"]) $tdoc = $_GET["tdoc"];
if($_GET["insertar_registro"]) $insertar_registro = $_GET["insertar_registro"];
if($_GET["actualizar"]) $actualizar = $_GET["actualizar"];
if($_GET["borrar"]) $borrar = $_GET["borrar"];
if($_GET["linkarchivo"]) $linkarchivo = $_GET["linkarchivo"];
$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$seriesVistaTodos = $_SESSION["seriesVistaTodos"];
$nurad       = $_GET["nurad"];

	if($nurad)
	{
		$ent = substr($nurad,-1);
	}
	include_once("$ruta_raiz/include/db/ConnectionHandler.php");
	
	$db = new ConnectionHandler("$ruta_raiz");
	
	if (!defined('ADODB_FETCH_ASSOC')) define('ADODB_FETCH_ASSOC',2);
   	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
  	include_once "$ruta_raiz/include/tx/Historico.php";
    include_once ("$ruta_raiz/class_control/TipoDocumental.php");
    include_once "$ruta_raiz/include/tx/Expediente.php";
    $coddepe = $dependencia;
	$codusua = $codusuario;
    $isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from RADICADO WHERE RADI_NUME_RADI = '$nurad'";
    $rsDepR = $db->conn->Execute($isqlDepR);
	if ($rsDepR)
	{	// $coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
		// $codusua = $rsDepR->fields['RADI_USUA_ACTU'];
	}
	
  	$trd             = new TipoDocumental($db);
    $Historico       = new Historico($db);		  
    $trdExp          = new Expediente($db);
    $numExpediente   = $trdExp->consulta_exp("$nurad");
    $mrdCodigo       = $trdExp->consultaTipoExpediente("$numExpediente");
    $trdExpediente   = $trdExp->descSerie." / ".$trdExp->descSubSerie;
    $descPExpediente = $trdExp->descTipoExp;
    $descFldExp      = $trdExp->descFldExp;
    $codigoFldExp    = $trdExp->codigoFldExp;
    $expUsuaDoc      = $trdExp->expUsuaDoc;
	
    // PARTE DE CODIGO DONDE SE IMPLEMENTA EL CAMBIO DE ESTADO AUTOMATICO AL TIPIFICAR.
	include ("$ruta_raiz/include/tx/Flujo.php");
    // $texp no viene de ningun lado 
    if 	(!empty($texp)){
        $objFlujo = new Flujo($db, $texp,$usua_doc);
        $expEstadoActual = $objFlujo->actualNodoExpediente($numExpediente);
        $arrayAristas =$objFlujo->aristasSiguiente($expEstadoActual);
        $aristaSRD = $objFlujo->aristaSRD;
        $aristaSBRD = $objFlujo->aristaSBRD;
        $aristaTDoc = $objFlujo->aristaTDoc;
        $aristaTRad = $objFlujo->aristaTRad;
        $arrayNodos = $objFlujo->nodosSig;
        $aristaAutomatica = $objFlujo->aristaAutomatico;
        $aristaTDoc = $objFlujo->aristaTDoc;
        if($arrayNodos)
        {
          $i = 0;
          foreach ($arrayNodos as $value)
          {
            $nodo = $value;
            $arAutomatica =  $aristaAutomatica[$i];
            $aristaActual = $arrayAristas[$i];
            $arSRD =  $aristaSRD[$i];
            $arSBRD = $aristaSBRD[$i];
            $arTDoc = $aristaTDoc[$i];
            $arTRad = $aristaTRad[$i];
            $nombreNodo = $objFlujo->getNombreNodo($nodo,$texp);
        if($arAutomatica==1 and $arSRD==$codserie and $arSBRD==$tsub and $arTDoc==$tdoc and $arTRad==$ent)
        {
            if($insertar_registro)
            {
                $objFlujo->cambioNodoExpediente($numExpediente,$nurad,$nodo,$aristaActual,1,"Cambio de Estado Automatico.");
                $codiTRDS = $codiTRD;
                $i++;
                $TRD = $codiTRD;
                $observa = "*TRD*".$codserie."/".$codiSBRD." (Creacion de Expediente.)";
                $radicados= $nurad;
                $tipoTx = 51;


                $rs=$db->query($sql);
                $mensaje = "SE REALIZO CAMBIO DE ESTADO AUTOMATICAMENTE AL EXPEDIENTE No. < $numExpediente > <BR>
                    EL NUEVO ESTADO DEL EXPEDIENTE ES  <<< $nombreNodo >>>";
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
              </table><TABLE><TR><TD></TD></TR></TABLE>";
      }
      $i++;
      }
    }
  }
  /*
  * Adicion nuevo Registro
  */
    if ($insertar_registro && $tdoc !=0 && $tsub !=0 && $codserie !=0 ){
    
    include_once("../include/query/busqueda/busquedaPiloto1.php");

    $sql = "SELECT $radi_nume_radi AS RADI_NUME_RADI 
				FROM SGD_RDF_RETDOCF r 
				WHERE RADI_NUME_RADI = '$nurad'";
if($seriesVistaTodos!=1){
        $sql.="  AND  DEPE_CODI =  '$coddepe'";
}
	$rs=$db->conn->query($sql);
	$radiNumero = $rs->fields["RADI_NUME_RADI"];

    if ($radiNumero !=''){

		$codserie = 0 ;
  		$tsub = 0  ;
  		$tdoc = 0;
		$mensaje_err = "<HR>
		   <center><B><FONT COLOR=RED>
		   	Ya existe una Clasificacion para esta dependencia <$coddepe> <BR>  VERIFIQUE LA INFORMACION E INTENTE DE NUEVO
		   	</FONT></B></center>
		   	<HR>";
		  }
		else
			{
  						
			$isqlTRD = "select SGD_MRD_CODIGO 
					from SGD_MRD_MATRIRD 
					where SGD_SRD_CODIGO = '$codserie'
				       and SGD_SBRD_CODIGO = '$tsub'
					   and SGD_TPR_CODIGO = '$tdoc'";
			if($seriesVistaTodos!=1){
							$sql.="  AND  DEPE_CODI =  '$coddepe'";
			}
			$rsTRD = $db->conn->Execute($isqlTRD);
			$i=0;
			while(!$rsTRD->EOF)
			{
	    		$codiTRDS[$i] = $rsTRD->fields['SGD_MRD_CODIGO'];
				$codiTRD = $rsTRD->fields['SGD_MRD_CODIGO'];
	    		$i++;
				$rsTRD->MoveNext();
			}    
			$radicados = $trd->insertarTRD($codiTRDS,$codiTRD,$nurad,$coddepe,$codusua,$tdoc);
		   	$TRD = $codiTRD;
			include "$ruta_raiz/radicacion/detalle_clasificacionTRD.php";
			//Modificado skina
			$sqlH = "SELECT $radi_nume_radi as RADI_NUME_RADI
					FROM SGD_RDF_RETDOCF r 
					WHERE r.RADI_NUME_RADI = '$nurad'
				    AND r.SGD_MRD_CODIGO =  '$codiTRD'";
			$rsH=$db->conn->query($sqlH);
			$i=0;
			while(!$rsH->EOF)
			{
	    		$codiRegH[$i] = $rsH->fields['RADI_NUME_RADI'];
	    		$i++;
				$rsH->MoveNext();
			}
            $observa = "*TRD*$codserie/$tsub (Asigancion tipo documental.)";
			$Historico->insertarHistorico($codiRegH, $dependencia, $codusuario, $dependencia, $codusuario, $observa, 32); 
		  	/*
		  	*Actualiza el campo tdoc_codi de la tabla Radicados
		  	*/
		 	$radiUp = $trd->actualizarTRD($codiRegH,$tdoc);
		
  			$codserie = 0 ;
  			$tsub = 0  ;
  			$tdoc = 0;
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
<form method="GET" action="<?=$encabezadol?>" name="TipoDocu"> 

<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<input type='hidden' name='nurad'               value='<?=$nurad?>'> 
<input type='hidden' name='ent'                 value='<?=$ent?>'> 
<input type='hidden' name='codiTRDModi'         value='<?=$codiTRDModi?>'> 
<input type='hidden' name='codiTREDli'          value='<?=$codiTREDli?>'> 
<input type='hidden' name='ind_ProcAnex'        value='<?=$ind_ProcAnex?>'> 


    <table border=0 width=70% align="center" class="borde_tab" cellspacing="0">
	  <tr align="center" class="titulos2">
	    <td height="15" class="titulos2">CUADRO DE CLASIFICACION DOCUMENTAL - Radicado No <?=$nurad?></td>
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
	$sqlFechaHoy2 = $db->conn->SQLDate('Y-m-d',$db->conn->sysTimeStamp);
	$check=1;
	$fechaf=date("dmy") . "_" . time("hms");
	$num_car = 4;
	$nomb_varc = "s.sgd_srd_codigo";
	$nomb_varde = "s.sgd_srd_descrip";
   	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php";
	$querySerie = "select distinct($sqlConcat) as detalle, s.sgd_srd_codigo 
	     from sgd_mrd_matrird m, sgd_srd_seriesrd s
			 where (cast(m.depe_codi as varchar(5)) = '$coddepe' or m.depe_codi_aplica like '%$coddepe%' or cast(m.depe_codi as varchar(5))='$depDireccion')
						 and s.sgd_srd_codigo = m.sgd_srd_codigo
			       and CAST(m.sgd_mrd_esta AS numeric(1) )       = 1 
             and ".$db->sysdate()." between s.sgd_srd_fechini and s.sgd_srd_fechfin";
	if($seriesVistaTodos!=1){
		 $querySerie .= " and m.depe_codi = $dependencia ";
	}
	
			//     and ".$sqlFechaHoy." between $sgd_srd_fechini and $sgd_srd_fechfin
	$querySerie .= "
			  ";
//        $db->conn->debug=true;
	$rsD=$db->conn->query($querySerie);
	$comentarioDev = "Muestra las Series Docuementales";
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsD->GetMenu2("codserie", $codserie, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
 ?>   
      </td>
     </tr>
   <tr>
     <td class="titulos5" >SUBSERIE</td>
	 <td class=listado5 >
	<?php
	$nomb_varc = "su.sgd_sbrd_codigo";
	$nomb_varde = "su.sgd_sbrd_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php"; 
   	$querySub = "select distinct ($sqlConcat) as detalle, su.sgd_sbrd_codigo 
	         from sgd_mrd_matrird m, sgd_sbrd_subserierd su
			 where
         (cast(m.depe_codi as varchar(5)) = '$coddepe' or m.depe_codi_aplica  like '%$coddepe%' or cast(m.depe_codi as varchar(5))='$depDireccion')
         and cast(m.sgd_mrd_esta as numeric(1))       = 1
			   and m.sgd_srd_codigo = $codserie
				 and su.sgd_srd_codigo = $codserie
			   and su.sgd_sbrd_codigo = m.sgd_sbrd_codigo
         and ".$db->sysdate()." between su.sgd_sbrd_fechini and su.sgd_sbrd_fechfin ";
	if($seriesVistaTodos!=1){
		// $querySub .= " and m.depe_codi = $dependencia ";
	}
 	$querySub .= " order by detalle
			  ";
	$rsSub=$db->conn->query($querySub);
	include "$ruta_raiz/include/tx/ComentarioTx.php";
	print $rsSub->GetMenu2("tsub", $tsub, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );

?> 
     </td>
     </tr>
   <tr>
     <td class="titulos5" >TIPO DE DOCUMENTO</td>
 	 <td class=listado5 >
<?php
	$nomb_varc = "t.sgd_tpr_codigo";
	$nomb_varde = "t.sgd_tpr_descrip";
	include "$ruta_raiz/include/query/trd/queryCodiDetalle.php"; 
	if($ent) $queryTrad = " and sgd_tpr_tp$ent >= 1";
	$queryTip = "select distinct ($sqlConcat) as detalle, t.sgd_tpr_codigo 
	        from sgd_mrd_matrird m, sgd_tpr_tpdcumento t, sgd_sbrd_subserierd sb
		 where cast(m.sgd_mrd_esta as numeric(1))       = 1
 		    and m.sgd_srd_codigo     = $codserie
		    and m.sgd_sbrd_codigo    = $tsub
 		    and t.sgd_tpr_codigo = m.sgd_tpr_codigo
 		    and sb.sgd_sbrd_codigo = m.sgd_sbrd_codigo
 		    and sb.sgd_srd_codigo = m.sgd_srd_codigo
                    and t.sgd_tpr_estado = 1
                    $queryTrad
 		    and ".$db->sysdate()." between sb.sgd_sbrd_fechini 
		    and sb.sgd_sbrd_fechfin ";
 	$queryTip .= " order by detalle";
        $rsTip=$db->conn->query($queryTip);
	$ruta_raiz = "..";
	include "$ruta_raiz/include/tx/ComentarioTx.php";
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
	 <center><input name="actualizar" type="button" class="botones_funcion" id="envia23" onClick="procModificar();"value=" Modificar "></center></TD>
        <td width="33%" class="listado2" height="25">
		 <center><input name="Cerrar" type="button" class="botones_funcion" id="envia22" onClick="opener.regresar();window.close();"value="Cerrar"></center></TD>
	   </tr>
	</table>
	<table width="70%" border="0" cellspacing="1" cellpadding="0" align="center" class="borde_tab">
	  <tr align="center">
	    <td>
		<?php
		include "lista_tiposAsignados.php";
		if ($ind_ProcAnex=="S")
			{
	      	echo " <br> <input type='button' value='Cerrar' class='botones_largo' onclick='opener.regresar();window.close();'> ";
			}	
		?>
	 	</td>
	   </tr>
	</table>
<script>
function borrarArchivo(anexo,linkarch){
	if (confirm('Esta seguro de borrar este Registro ?'))
	{
		nombreventana="ventanaBorrarR1";
		url="tipificar_documentos_transacciones.php?<?=session_name()."=".session_id()?>&borrar=1&nurad=<?=$nurad?>&codiTRDEli="+anexo+"&linkarchivo="+linkarch;
		window.open(url,nombreventana,'height=250,width=300');
	}
return;
}
function procModificar()
{
if (document.TipoDocu.tdoc.value != 0 &&  document.TipoDocu.codserie.value != 0 &&  document.TipoDocu.tsub.value != 0)
  {
  <?php
      $sql = "SELECT RADI_NUME_RADI
	             FROM SGD_RDF_RETDOCF 
		WHERE RADI_NUME_RADI = '$nurad'";
if($seriesVistaTodos!=1){
        $sql.="  AND  DEPE_CODI =  '$coddepe'";
}


		$rs=$db->conn->query($sql);
		$radiNumero = $rs->fields["RADI_NUME_RADI"];
		if ($radiNumero !='') {
			?>
			if (confirm('Esta Seguro de Modificar el Registro de su Dependencia ?'))
				{
					nombreventana="ventanaModiR1";
					url="tipificar_documentos_transacciones.php?<?=session_name()."=".session_id()?>&actualizar=1&usua=<?=$krd?>&codusua=<?=$codusua?>&tdoc=<?=$tdoc?>&tsub=<?=$tsub?>&codserie=<?=$codserie?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&nurad=<?=$nurad?>";
					window.open(url,nombreventana,'height=200,width=300');
				}
			<?php
	 		}else
			{
			?>
			alert("No existe Registro para Modificar ");
			<?php
			}
       ?>
     }
   else
   {
    alert("Campos obligatorios ");
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
