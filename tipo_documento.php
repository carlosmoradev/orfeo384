<?php
session_start();

$ruta_raiz = ".";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

if (!defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE', 1);
include_once "./include/db/ConnectionHandler.php";

$db = new ConnectionHandler(".");
$db->conn->SetFetchMode(3);
?>
<html>
<head>
<link rel="stylesheet"
	href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
</head>

<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();">
<br/>
<form name=form_temas method="post" action="<?=$_SERVER['PHP_SELF']?>" >
<table width="70%" align="center" cellspacing="1" cellpadding="0"
	class="borde_tab">
	<input type=hidden name=ver_tema value="Si ver Causales">

	<input type=hidden name=carpeta value='<?=$carpeta?>'>

	<tr>

		<td class="titulos2">Tipo Población</td>

		<td width="323">
		<?php
		$ADODB_COUNTRECS = true;

		$isql = "SELECT
t.SGD_TMA_DESCRIP,
t.SGD_TMA_CODIGO 

FROM 
SGD_TMA_TEMAS t";

		$rs=$db->query($isql);
		$ADODB_COUNTRECS = false;

		if($rs)
		{
			?> <select name="tema" class="select">

			<?php
			do
			{	$codigo_tma = $rs->fields["SGD_TMA_CODIGO"];
			$nombre_tma = $rs->fields["SGD_TMA_DESCRIP"];
			if($codigo_tma==$tema)
			{	$datoss = " selected ";	}
			else
			{	$datoss = "  ";	}
			echo "<option value=$codigo_tma $datoss>$nombre_tma</option>";
			$rs->MoveNext();
			}while(!$rs->EOF);
			?>

		</select> 
		<input type="hidden" name=verrad value="<?=$verrad ?>">
		<input type=submit name=grabar_tema value='Grabar Cambio'
			class='botones'>

		<center><input name="Cerrar" type="button" class="botones_funcion"
			id="envia22" onClick="opener.regresar();window.close();"
			value="Cerrar"></center>
		</TD>
		<?php
		}
		else
		{
			echo "<p class='error'>No se han generado temas en el sistema</p>";
		}
		if($grabar_tema)
		{
			if(!$tema) $tema=0;
			$recordSet["SGD_TMA_CODIGO"] = $tema;
			$recordSet["RADI_NUME_RADI"] = $verrad;
			$actualizados = $db->conn->Replace("RADICADO", $recordSet,'RADI_NUME_RADI',false);
			if($actualizados==false)
			{
				echo "<span class=alarmas>No se ha podido Actualizar la población</span>";
			}
			else
			{
				echo "<span class=info>Población Actualizado</span>";
			}

		}
		echo "</td>";
		?>

	</tr>

</table>

</form>
</body>
</html>
