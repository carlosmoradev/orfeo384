<?
/** CONSULTA WEB A CIUDADANOS
  *@autor JAIRO LOSADA - SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIATIOS - COLOMBIA
  *@version 3.2
  *@fecha 21/10/2005
  *@licencia GPL
  */
$ruta_raiz = "../..";
$verradicado = $idRadicado;
$dependencia = 990;
$codusuario = 300;
$verrad = $idRadicado;
$ent = substr($idRadicado,-1);
error_reporting(7);
$iTpRad = 10;

/** Encriptacion de pagina para inactivar en una Hora
  */
  
$llave = date("YmdH") . "$verrad";
$password =md5($llave);
$fechah=date("YmdHis");
/**
  * Abrimos la coneccion a la base de OrfeoGPL
  */
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_NUM);	
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

include_once ("$ruta_raiz/include/db/ConnectionHandlerSUI.php");
$dbSUI = new ConnectionHandlerSUI("$ruta_raiz");
$dbSUI->conn->SetFetchMode(ADODB_FETCH_NUM);	
$dbSUI->conn->SetFetchMode(ADODB_FETCH_ASSOC);		
?>
<html>
<head>
<title>SSPD - SISTEMA DE GESTION DOCUMENTAL - CUIDADANOS</title>
<meta http-equiv="Content-Type" content="text/html;"><style type="text/css">
<!--
@import url("../web.css");
-->
</style><script language="JavaScript" type="text/JavaScript">
<!--
function MM_preloadImages() { //v3.0
  var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i<a.length; i++)
    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
}
function Start(URL, WIDTH, HEIGHT) {
windowprops = "top=0,left=0,location=no,status=no, menubar=no,scrollbars=yes, resizable=yes,width=1020,height=500";
preview = window.open(URL , "preview", windowprops);
}
//-->
</script>
</head>
<body bgcolor="#ffffff">
<form name=form_cerrar action=../index_web.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?> method=post>
</form>
<?
	include "../cabez.php";
?>
<table width="100%"  border="0" cellspacing="5" cellpadding="0" class="borde_tab">
  <tr>
    <td class="titulos2" ALIGN=CENTER >
			<FONT SIZE=2>CONSULTA TEMATICA DE RESOLUCIONES</FONT>
		</td>
	</tr>
</table>

<form name=form_cerrar action=./index.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?> method=post>
<table width="70%"  border="0" cellspacing="5" cellpadding="0" class="borde_tab">
  <tr>
	 <td class="titulos2">
			Año de Resolucion a Consultar
	 </td>
	 <td>
			<select name=anoTRad >
				<OPTION value="0" >Seleccione el A&ntilde;o</OPTION>
				<?
					$anoActual = date("Y");
					for($i=2005;$i<=$anoActual;$i++)
					{
						if($anoTRad==$i) 
								{  
										$selectV = "selected='selected'";
								}else{
										$selectV = "";
								}
										
				?>
				     <option value=<?=$i?> <?=$selectV?> ><?=$i?> </option>
				<?
					}
				?>
			</select>
   </td>
	</tr>
  <tr>
	 <td class="titulos2">
            Sector
          </td>
	 <td>
		<?
		$isql = "SELECT PAR_SERV_NOMBRE, PAR_SERV_SECUE FROM fldoc.PAR_SERV_SERVICIOS";
		
		if (count($recordSet)>0)	array_splice($recordSet, 0);
		if (count($recordWhere)>0)	array_splice($recordWhere, 0);
		$rs = $db->query($isql);
		echo $rs->GetMenu2('sector',$sector,'0:Seleccione El Sector.',false,1,' class="select" onChange="javascript:submit();"');
		?>
   </td>
	</tr>
  <tr>
	 <td class="titulos2">
            Empresa
          </td>
	 <td>
		<?
    $nombreEmpresa = $dbSUI->conn->substr ."(e.ARE_ESP_NOMBRE, 1, 80)";
		$isql = "select $nombreEmpresa Empresa,e.ARE_ESP_SECUE,sev.PAR_SERV_SECUE,sev.ARE_SEES_ESTADO
			
			from RUPS.ARE_ESP_EMPRESAS e,
					RUPS.ARE_SEES_SERESP sev
					
					where
					
					e.ARE_ESP_SECUE = sev.ARE_ESP_SECUE
					and sev.PAR_SERV_SECUE = '$sector'
					and sev.ARE_SEES_ESTADO = 'O'
					and e.ARE_ESP_SECUE < 99900
			order by e.ARE_ESP_NOMBRE";
    //$isql = "SELECT PAR_SERV_NOMBRE, PAR_SERV_SECUE FROM CON_SUM_SAS.PAR_SERV_SERVICIOS";
		
		if (count($recordSet)>0)	array_splice($recordSet, 0);
		if (count($recordWhere)>0)	array_splice($recordWhere, 0);
		$rsSUI = $dbSUI->query($isql);
		echo $rsSUI->GetMenu2('empresa',$empresa,'0:. . . Seleccione la Empresa . . .',false,1,' class="select" onChange="javascript:submit();"');
		?>
   </td>
	</tr>
  <tr>
	 <td class="titulos2">
           Causal
          </td>
	 <td>
		<?
		$isql = "SELECT SGD_CAU_DESCRIP, SGD_CAU_CODIGO FROM fldoc.SGD_CAU_CAUSAL ORDER BY SGD_CAU_CODIGO";
		$rs = $db->query($isql);
		echo $rs->GetMenu2('causal',$causal,'0:No aplica.',false,1,'onChange="javascript:submit();"  class="select"');
		?>
   </td>
	</tr>

  <tr>
	 <td class="titulos2">
     Detalle Causal
   </td>
	 <td>
<?
		if(!$causal) $causal = 0;
		$isql = "SELECT SGD_DCAU_DESCRIP, SGD_DCAU_CODIGO
		FROM fldoc.SGD_DCAU_CAUSAL 
		WHERE SGD_CAU_CODIGO = $causal";
		$rs = $db->query($isql);
		echo $rs->GetMenu2('deta_causal',$deta_causal,'0:No aplica.',false,1,'class="select"');
?>
   </td>
	</tr>
	<tr>
<TD class=titulos2>
    Topico de Asunto<TD><input type="text" name="txtValor" value=<?=$txtValor?>></TD>
  </TD>
</tr>
<tr>

  <TD>
    <center><input type="submit" value="Buscar" NAME="buscarVal"></center>
  </TD></tr>
</table>
</form>
<?
if($buscarVal!="Buscar") die ("<hr>Presione Buscar cuando Desee realizar la Busqueda.<hr>")
?>
<form name="form1" id="form1" action="./tx/formEnvio.php?<?=$encabezado?>" method="POST">

  <?

	/*  GENERACION LISTADO DE RADICADOS
	 *  Aqui utilizamos la clase adodb para generar el listado de los radicados
	 *  Esta clase cuenta con una adaptacion a las clases utiilzadas de orfeo.
	 *  el archivo original es adodb-pager.inc.php la modificada es adodb-paginacion.inc.php
	 */
	error_reporting(7);

	if(strlen($orderNo)==0)
	{
		$orderNo="2";
		$order = 3;
	}else
	{
		$order = $orderNo +1;
	}

	$sqlFecha = $db->conn->SQLDate("Y-m-d H:i A","b.RADI_FECH_RADI");
	//$sqlFecha = $db->conn->DBDate("b.RADI_FECH_RADI", "d-m-Y H:i A");
	//$sqlFecha = $db->conn->DBTimeStamp("b.RADI_FECH_RADI","" ,"Y-m-d H:i:s");
  //$db->SQLDate('Y-\QQ');
	$iSql = 'select
				to_char(b.RADI_NUME_RADI) as "IDT_Numero Radicado"
				,b.RADI_PATH as "HID_RADI_PATH"
				,'.$sqlFecha.' as "DAT_Fecha Radicado"
				,to_char(b.RADI_NUME_RADI) as "HID_RADI_NUME_RADI"
				,UPPER(b.RA_ASUN)  as "Asunto"'.
				',d.NOMBRE_DE_LA_EMPRESA 
				,c.SGD_TPR_DESCRIP as "Tipo Documento" 
		 from
		 fldoc.radicado b,
		 fldoc.SGD_TPR_TPDCUMENTO c,
		 fldoc.BODEGA_EMPRESAS d,
		 fldoc.SGD_DIR_DRECCIONES dir
	 where 
		b.tdoc_codi=c.sgd_tpr_codigo
	  and b.radi_nume_radi=dir.radi_nume_radi
		and dir.sgd_esp_codi=d.identificador_empresa
		and substr(b.radi_path,-3) = '."'tif'".'
		and b.radi_nume_radi like '."'$anoTRad%5'".'
		and d.identificador_empresa = '.$empresa;
  if ($txtValor) $iSql = $iSql . " and b.ra_asun like '%$txtValor%'";
	//	order by '.$order .' ' .$orderTipo;
	// and b.PAR_SERV_SECUE='.$sector.'
$rs=$db->conn->Execute($iSql);
  if ($rs->EOF and $busqRadicados)  {
		echo "<hr><center><b><span class='alarmas'>No se encuentra ningun radicado con el criterio de busqueda</span></center></b></hr>";
	}
	else{
		$pager = new ADODB_Pager($db,$iSql,'adodb', true,$orderNo,$orderTipo);
		$pager->checkAll = false;
		$pager->checkTitulo = true;
		$pager->toRefLinks = $linkPagina;
		$pager->toRefVars = $encabezado;
		$pager->descCarpetasGen=$descCarpetasGen;
		$pager->descCarpetasPer=$descCarpetasPer;
		$pager->Render($rows_per_page=150,$linkPagina,$checkbox=chkAnulados);
	}
	?>
	</form>

</body>
</html>
