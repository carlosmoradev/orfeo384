<?php
session_start();

    $ruta_raiz = "."; 
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$krd                = $_SESSION["krd"];
$dependencia        = $_SESSION["dependencia"];
$usua_doc           = $_SESSION["usua_doc"];
$codusuario         = $_SESSION["codusuario"];
$tip3Nombre         = $_SESSION["tip3Nombre"];
$tip3desc           = $_SESSION["tip3desc"];
$tip3img            = $_SESSION["tip3img"];
$tpNumRad           = $_SESSION["tpNumRad"];
$tpPerRad           = $_SESSION["tpPerRad"];
$tpDescRad          = $_SESSION["tpDescRad"];
$tip3Nombre         = $_SESSION["tip3Nombre"];
$tpDepeRad          = $_SESSION["tpDepeRad"];
$usuaPermExpediente = $_SESSION["usuaPermExpediente"];

$nomcarpeta=$_GET["nomcarpeta"];
$verradicado = $_GET['verrad'];

if (!$ent) $ent = substr($verradicado, -1 );
if(!$carpeta) $carpeta = $carpetaOld;
if(!$menu_ver_tmp) $menu_ver_tmp = $menu_ver_tmpOld;
if(!$menu_ver) $menu_ver = $menu_ver_Old;
if(!$menu_ver) $menu_ver=3;
if($menu_ver_tmp)	$menu_ver=$menu_ver_tmp;
if (!defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE', 1);
include_once "./include/db/ConnectionHandler.php";
if($verradicado)	$verrad= $verradicado;
if(!$ruta_raiz)	$ruta_raiz=".";
$numrad = $verrad;
//error_reporting(7);
$db = new ConnectionHandler(".");
$db->conn->SetFetchMode(3);
// verificacion si el radicado se encuentra en el usuario Actual
include "$ruta_raiz/tx/verifSession.php";
?>
<html>  <head>  <title>.: Modulo Movil :.</title></head>   
<link rel="stylesheet" href="movil/estilos/orfeomovil.css"></head>
<table border=0 width=100%  cellpadding="0" cellspacing="5" class="borde_tab">
 <tr>
  <td width=25% class="titulos2">RADICADO No:
<?
 if($krd)
	{
	$isql = "select * From usuario where USUA_LOGIN ='$krd' and USUA_SESION='". substr(session_id(),0,29)."' ";
	$rs = $db->query($isql);
	// Validacion de Usuario y password MD5
	if (($krd))
	{
echo"$verrad";
?>
	</td>
		<td class="titulos5" align="center" width="50%" height="20"  class="info"><?=$_SESSION['usua_nomb'] ?></td>
	<td class="titulos2">
      <a class="vinculos" width="25%" height="30"  href=<?="$ruta_raiz".'/cerrar_session.php'?>>Cerrar Orfeo Movil</a>
    </td>
 </tr>
</table>

<body>
<table border=0 align='center' cellpadding="0" cellspacing="0" width="100%" ><form action='verradicado_movil.php?<?=session_name()?>=<?=trim(session_id())?>&verrad=<?=$verrad?>&datoVer=<?=$datoVer?>&chk1=<?=$verrad."&carpeta=$carpeta&nomcarpeta=$nomcarpeta"?>' method='GET' name='form2'>
 <?
  echo "<input type='hidden' name='fechah' value='$fechah'>";
  print "<input type='hidden' name='verrad' value='".$verrad."'>";
include "ver_datosrad.php";
include "ver_datosgeo.php";
$tipo_documento .= "<input type=hidden name=menu_ver value='$menu_ver'>";
$hdatos = session_name()."=".session_id()."&leido=$leido&nomcarpeta=$nomcarpeta&tipo_carp=$tipo_carp&carpeta=$carpeta&verrad=$verrad&datoVer=$datoVer&fechah=fechah&menu_ver_tmp=";
?>
 
          <?
		  error_reporting(7);
		//	include "lista_general.php";

	  ?>

<table width="100%" border="0" cellpadding="0" cellspacing="5"bgcolor="#006699" >
     <tr bgcolor="#006699"> 
	 <td class="titulos4" align="center"colspan="6" >INFORMACION  RADICADO</td>
     </tr>
<tr> 
    <td align="center"   class="titulos2" >FECHA DE RADICADO</td>
    <td  align="right"   class="titulos3"><?=$radi_fech_radi ?></td>
</tr>
 <tr>
    <td   width="25%" align="center" height="25" class="titulos2" >ASUNTO</td>
    <td class='titulos3'  width="25%"><?=$ra_asun ?></td>
</tr>
<tr> 
	<td align="center"   height="25" class="titulos2"><?=$tip3Nombre[1][$ent]?></td>
	<td class='titulos3' width="25%" height="25"><?=$nombret_us1 ?>-- <?=$cc_documento_us1?></td>
</tr> 
<tr>	<td   width="25%" align="center" height="25" class="titulos2" >DIRECCI&Oacute;N CORRESPONDENCIA</td>
	<td class='titulos3' width="25%"><?=$direccion_us1 ?></td>
</tr>
<tr><td   width="25%" align="center" height="25" class="titulos2" >MUN/DPTO</td>
    <td class='titulos3' width="25%"><?=$dpto_nombre_us1."/".$muni_nombre_us1 ?></td>
</tr>
<tr>
	<td height="25"   align="center" class="titulos2"> <p>N&ordm; DE PAGINAS</p></td>
    <td class='titulos3' width="25%" height="25"> <?=$radi_nume_hoja ?></td>
</tr><tr>
    <td   width="25%" height="25" align="center" class="titulos2"> DESCRIPCION ANEXOS </td>
    <td class='titulos3'  width="25%" height="11"> <?=$radi_desc_anex ?></td>
</tr><tr>													<td align="left"   height="25" align="center" class="titulos2">DOCUMENTO<br>Anexo/Asociado</td>
	<td class='titulos3' width="25%" height="25">
	<?	
	if($radi_tipo_deri!=1 and $radi_nume_deri)
		{	echo $radi_nume_deri;
			echo "<br>(<a class='vinculos' href='$ruta_raiz/verradicado.php?verrad=$radi_nume_deri &session_name()=session_id()&krd=$krd' target='VERRAD$radi_nume_deri_".date("Ymdhi")."'>Ver Datos</a>)";
		}
		if($verradPermisos == "Full" or $datoVer=="985")
		{
	?>
		<input type=button name=mostrar_anexo value='...' class=botones_2 onClick="verVinculoDocto();">
	<?
		}
	?>
</tr><tr>	

	<td align="left" height="25" class="titulos2">IMAGEN</td>
	<td class='titulos3' colspan="1"><span class='vinculos'><?=$imagenv ?></span></td>
</tr><tr>
	<td align="left" height="25"  class="titulos2">Nivel de Seguridad</td>
	<td class='titulos3' colspan="3">
	<?
		if($nivelRad==1)
		{	echo "Privado";	}
		else 
		{	echo "P&uacute;blico";	}
	?>
	</td>
</tr><tr> 
	<td align="left" height="25" class="titulos2">TRD</td>
	<td class='titulos3' colspan="6">
	<?
		if(!$codserie) $codserie = "0";
		if(!$tsub) $tsub = "0";
		if(trim($val_tpdoc_grbTRD)=="///") $val_tpdoc_grbTRD = "";
	?>
		<?=$serie_nombre ?><font color=black>/</font><?=$subserie_nombre ?><font color=black>/</font><?=$tpdoc_nombreTRD ?>
        </tr>
    </td>
  </tr>
</table>
    <input type=hidden name=menu_ver value='<?=$menu_ver ?>'>
    <tr>
      <td height="17" width="94%" class="celdaGris"> <?
	}else {
	?>  </td>
  </tr>
</table>
	   <CENTER>
       <span class='titulosError'>SU SESION HA TERMINADO O HA SIDO INICIADA EN OTRO EQUIPO</span><BR>
       <span class='eerrores'>
       </CENTER></form>
           <?
		   }
			}else {echo "<center><b><span class='eerrores'>NO TIENE AUTORIZACION PARA INGRESAR</span><BR><span class='eerrores'><a href='login.php' target=_parent>Por Favor intente validarse de nuevo. Presione aca!</span></a>";}

?> </td>
    </tr>
   </form>
</table>
</body></html>
