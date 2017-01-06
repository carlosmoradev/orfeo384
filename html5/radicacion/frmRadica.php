<?
session_start();
//define('ADODB_ASSOC_CASE', 0);
$ruta_raiz= "../..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$ent ="2";
$dependencia = 900;
$codusuario = 1;
$tpDepeRad[$ent] = 900;
$usua_doc= 79802120;
$med=1;
$radicadopadre="0";
$ane = "";
$_SESSION["doc"]= "79802120";
?>
<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>..::: Orfeo Radicacion en HTML5 ::::.....</title>
<link type="text/css" rel="stylesheet" href="../orfeoHtml5.css">
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css" type="text/css">
<script type='text/javascript' src="<?=$ruta_raiz?>/include/ajax/radicacion/buscarDirServer.php?client=all"></script>
<script type='text/javascript' src="<?=$ruta_raiz?>/include/ajax/radicacion/buscarDirServer.php?stub=buscarDir"></script>
<script type='text/javascript' src="<?=$ruta_raiz?>/include/ajax/radicacion/radicacionServer.php?client=all"></script>
<script type='text/javascript' src="<?=$ruta_raiz?>/include/ajax/radicacion/radicacionServer.php?stub=radicacionAjax"></script>
<script>
function radicar(){
var datosRad = new Array(20);
  datosRad['tipoRadicado'] = <?=$ent?>;
  datosRad['radiDepeRadi'] = <?=$dependencia?>;
  datosRad['radiDepeActu'] = document.getElementById('coddepe').value;
  datosRad['radiUsuaActu'] = document.getElementById('usuarioCodigoReasigna').value;
  datosRad['radiUsuaRadi'] = <?=$codusuario?>;
  datosRad['usuaDoc'] = <?=$usua_doc?>;
  datosRad['dependenciaSecuencia'] = <?=$tpDepeRad[$ent]?>;
  datosRad['asunto'] = document.getElementById('asunto').value;
  datosRad['cuentai'] = "'" + document.getElementById('cuentai').value + "'";
  datosRad['tipoRemitente'] = "0";
  datosRad['fechaOficio'] = document.getElementById('fechaDoc').value;
  datosRad['med'] = document.getElementById('med').value;
  if(document.getElementById('tdoc')){
     datosRad['tipoDocumento'] = document.getElementById('tdoc').value;
  }else{
    datosRad['tipoDocumento'] = '0';
  }
  datosRad['documentoUs3']="0";
  datosRad['radiPais'] = document.getElementById('idpais').value;
  <? if(!$radicadopadre) $radicadopadre='0'; ?>
  datosRad['radicadoPadre'] = '<?=$radicadopadre?>';
  datosRad['carpetaPer'] = '0';
  <? if(!$ent) $ent="0"; ?>
  <?
  if($ent==2){
   $carpetaCodi = '0';
  }else{
   $carpetaCodi = "'".$ent."'";
  }
  ?>
  datosRad['carpetaCodi'] = <?=$carpetaCodi?>;
  datosRad['radiPath'] = '';
  datosRad['tDidCodi'] = '0';
  datosRad['ane'] = document.getElementById('ane').value;
  remoteRad.newRadicadoAjax('noRadicado',datosRad['asunto'] 
	,datosRad['tipoRadicado'] 
	,datosRad['radiDepeRadi']
	,datosRad['radiDepeActu']
	,datosRad['dependenciaSecuencia']
	,datosRad['radiUsuaRadi']
	,datosRad['radiUsuaActu']
	,datosRad['usuaDoc']
	,datosRad['cuentai']
	,datosRad['documentoUs3']
	,datosRad['med']
	,datosRad['fechaOficio']
	,datosRad['radicadoPadre']
	,datosRad['radiPais']
	,datosRad['tipoDocumento']
	,datosRad['carpetaPer']
	,datosRad['carpetaCodi']
	,datosRad['tDidCodi']
	,datosRad['tipoRemitente']
	,datosRad['ane']
	,datosRad['radiPath']
   );
}
function radicar_doc(){
    radicar();
 }
 </script>

</head>
<body>
<div >
</div>
	<header class="body">
		<script type='text/javascript'>
		
		// Objeto de HTML_AJAX pear para Traer usuarios
			var remoteDir = new buscarDir({});
			var remoteRad = new radicacionAjax({});
		</script>
  </header>

    <section class="body">
      <form method="post">
			<table class=borde_tab width=700>
				<TR class=listado2>
					<TD><input type=text name="fechaDoc" id="fechaDoc" placeholder="Fecha del Documento"></TD>
					<TD><input type=text name="cuentai" id="cuentai" placeholder="Ref / Officio"></TD>
					</tr>
					<tr class=listado2>
					<td >Asunto</td>
					<TD colspan=2><textarea name=asunto id="asunto" type="texarea" placeholder="Asunto" rows="5" cols="100" ></textarea></TD>
					</tr>
	<tr class=listado2>
		
		<td >Descripcion Anexos</td>
		<td colspan="2">
		<input name="ane" id="ane" type="text" size="70" class="tex_area" placeholder="Escriba la Descripcion de Anexos" value="<?php echo htmlspecialchars(stripcslashes($ane));?>">
		</td>
  </tr>
						<tr class=listado2>
		<td >Dependencia</td>
		<TD>
						<?
							$queryWhere = " where depe_estado=1 ";
							$query = "select DEPE_NOMB,DEPE_CODI from dependencia $queryWhere order by depe_nomb";
							$ADODB_COUNTRECS = true;
							$rs=$db->conn->query($query);
							$numRegs = "!".$rs->RecordCount();
							$varQuery = $query;
							$comentarioDev = "Muestra las dependencias";
							$nombreUsuarioActual = "";
							$codUsuarioActual = "";
							if($ent!=2){
							$nombreUsuarioActual = $_SESSION["usua_nomb"];
							$codUsuarioActual = $_SESSION["codusuario"];
							}
							print $rs->GetMenu2("coddepe",$codUsuarioActual,"", false,false," id=coddepe class='select'");
						?>
						</td></tr>
						</tr>
						<tr class=listado2>
		<td >Medio</td>
						<td valign="top" >
						<?
							$query = "Select MREC_DESC, MREC_CODI from MEDIO_RECEPCION ";
							$rs=$db->conn->query($query);
								$varQuery = $query;
								if($rs)
								{
									print $rs->GetMenu2("med", $med, "", false,""," id=med  class='select' " );
								}
							?>
						</td>
<td valign="top">
	<input name="hoj" type=hidden value="<? echo $hoj; ?>">
	<?php
	 $query = "select SGD_TPR_DESCRIP
		 ,SGD_TPR_CODIGO 
		from SGD_TPR_TPDCUMENTO 
		WHERE SGD_TPR_TP$ent='1'
		 and SGD_TPR_RADICA='1' 
	ORDER BY SGD_TPR_DESCRIP ";
	$opcMenu = "0:-- Seleccione un tipo --";
	$fechaHoy = date("Y-m-d");
	$fechaHoy = $fechaHoy . "";
	$ADODB_COUNTRECS = true;
	$rs=$db->conn->query($query);
	if ($rs && !$rs->EOF )
	{	$numRegs = "!".$rs->RecordCount();
		$varQuery = $query;
		print $rs->GetMenu2("tdoc", $tdoc, "", false,""," class='ecajasfecha' " );
	}else
	{
		$tdoc = 0;
	}
	$ADODB_COUNTRECS = false;
?>
</font>
</td>
						</tr>
					<tr class=listado2>
					<TD align="center" colspan="2">
						<input type="button" Value="Radicar" id="GenerarRadicado"  onClick="radicar_doc();">
						
					</TD>
				</TR>
				<tr>
					<td  align="center" colspan="2">
						<input id="supervisor_us" name="supervisor_us" type="text" size="80" class="tex_area" placeholder="Usuario Destino" readonly>
						<input type=hidden id=usuarioCodigoReasigna name=usuarioCodigoReasigna  size=3>
						<input type=hidden id=idpais name=idpais value=170  size=3>
						<input type=hidden id=idcont name=idcont value=1  size=3>
						<input type=hidden id=idDepto name=idDepto value=11  size=3>
						<input type=hidden id=idmuni name=idMuni value=1  size=3>
					</td>
					</tr>
				<tr class=listado2><TD colspan="6" align="center">
					  <div id="noRadicado" style="border: 3px coral solid; width: 400px;" align="center"  >
              Prtobbbbbbbb
  					</div>
				</TD></tr>
			</table>
    </form>
    </section>

    <section class="body">
      <form method="post" action="index.php">
			<table class=borde_tab width=700 >
				<TR class=listado2>
					<TD>
					<input type=text name="Documento" id="Documento" placeholder="Documento" onChange="remoteDir.getBuscarCiuDoc('<?=$i?>',document.getElementById(<?="'"?>Documento<?="'"?>).value);" >
					</TD>
					<TD><input name="email" type="email" placeholder="Nombre / Apellido"></TD>
					<TD><input type="button" Value="Buscar">
				</TD>
				</TR>
				<tr class=listado2><TD colspan="6">
					<Div id=dirBusqueda  style="border-right: #000000 1px solid; border-top: #000000 1px solid; border-left: #000000 1px solid; border-bottom: #000000 1px solid; font-family: Arial, Tahoma; background-color: #e6e6fa; position: absolute;"></Div>
				</TD></tr>
			</table>
    </form>
    </section>
    <footer class="body">
    </footer>
</body>
</html>
