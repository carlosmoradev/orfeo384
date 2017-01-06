<?
session_start();
$ruta_raiz = "..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

// Modificado 2011 aurigadl@gmail.com

/**
* Paggina de modFlujo.php 
* Por Correlibre.org 2012/01
* Se añadio compatibilidad con variables globales en Off
* @autor Jairo Losada 2012-05
* @licencia GNU/GPL V 3
*/


foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;


define('ADODB_ASSOC_CASE', 2);
$verrad         = "";
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];

?>
<form name=form_flujo  method='post' action='verradicado.php?<?=session_name()?>=<?=trim(session_id())?>&krd=<?=$krd?>&verrad=<?=$verrad?>&<?="&mostrar_opc_envio=$mostrar_opc_envio&nomcarpeta=$nomcarpeta&carpeta=$carpeta&leido=$leido"?>'>
<table border=0 width 100% cellpadding="0" cellspacing="5" class="borde_tab">
<input type=hidden name=ver_flujo value="Si ver fLUJO">
<input type=hidden name=nomcarpeta value="<?=$nomcarpeta?>">
<tr> 
	<td class="titulos2">Flujo</td>
	<td width="323">
<?
$verradEntra=$verrad;
if (!$ruta_raiz) $ruta_raiz = "..";
include_once($ruta_raiz."/include/tx/Historico.php");
$objHistorico= new Historico($db);
$isql = "select * FROM SGD_FLD_FLUJODOC where sgd_tpr_codigo='$tdoc'";
	$rs=$db->query($isql);
	if($rs)
	{	if (!$grabar_flujo) $flujo = $flujo_grb;
?>
		<select name="flujo"  class="select">
	    <? 
	    do
	    {	$codigo_flujo = $rs->fields["SGD_FLD_CODIGO"];
	    	$nombre_flujo = $rs->fields["SGD_FLD_DESC"];
	    	if($codigo_flujo==$flujo)
	    	{	$datoss = " selected ";	}
			else
			{	$datoss = " ";	}
			echo "<option value=$codigo_flujo  $datoss>$codigo_flujo $nombre_flujo</option>"; 
			$rs->MoveNext();
		}while(!$rs->EOF);
		?>
		</select>
		<?
		if(!$modificar)
		{
		?>
			<input type=submit name=grabar_flujo value='Grabar Cambio' class='botones'> 
		<?
		}
	}
	if($grabar_flujo)
	{
      /**  INTENTA ACTUALIZAR LA CAUSAL 
	    *  Si esta no esta entonces simplemente le inserte
		*/
		if(!$ddca_causal)	$ddca_causal=0;
		if(!$deta_causal)	$data_causa =0;
		$recordSet2["SGD_FLD_CODIGO"] = $flujo;
		$recordWhere2["RADI_NUME_RADI"] = $verradEntra;
		$rs=$db->update("RADICADO", $recordSet2,$recordWhere2);
		array_splice($recordSet2, 0);
		if($rs)
		{
			echo "<br><span class=info>Estado  Actualizado</span>";
			$observa = "*Cambio Estado*  ";
			
			if ($flujo_nombre) $observa .= " ($flujo_nombre)";
			$arrayRad = array();
			$arrayRad[]=$verradEntra;
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
			$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 37);
		}
	 /* FIN ACUTALIZACION O INSERCION DE CAUSALES */ 
	}
		?>
	</td>
</tr>
</table>
</form>
