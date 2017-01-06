<?
$paramsTRD=$phpsession."&krd=$krd&codiEst=$codiEst&dependencia=$dependencia&usua_nomb=$usua_nomb&"
				."depe_nomb=$depe_nomb&usua_doc=$usua_doc&codusuario=$codusuario";
?>
<form name = formaTRD action="uploadPlanos.php?<?=$paramsTRD?>" method="post">
<table width="70%" align="center" border="0" cellpadding="0" cellspacing="5" class="borde_tab">
	<tr align="center">
		<td height="35" colspan="2" class="titulos4">SELECCI&Oacute;N ESTRUCTURA</td>
	</tr>
	<tr align="center">
		<td width="36%" class="titulos2">EMPRESA</td>
		<td width="64%" height="35" class="listado2">
<?
//echo "<hr>$tipoRad";
$coddepe=$_SESSION['dependencia'];
if($codEmp!=0)
{	$queryTRD = "select distinct sgd_tidm_codi AS CODIESTR from sgd_cob_campobliga 
				where sgd_tidm_codi = '$codEmp'";
	$rsTRD=$db->conn->query($queryTRD);
	if($rsTRD)
   	{	$codiEst = $rsTRD->fields['CODIESTR'];	}
}

//$coddepe=$dependencia;
$num_car = 4;
$queryEst = "select distinct sgd_cob_desc AS EMPRESA, sgd_tidm_codi from sgd_cob_campobliga 
				where sgd_tidm_codi > 2 
			 order by sgd_cob_desc";
$rsD=$db->conn->query($queryEst);
$comentarioDev = "Despliega las Posibles Estructuras";
include "$ruta_raiz/include/tx/ComentarioTx.php";
print $rsD->GetMenu2("codEmp", $codEmp, "0:-- Seleccione --", false,"","onChange='submit()' class='select'" );
?>
	</td>
   </table>

<br/>
</form>