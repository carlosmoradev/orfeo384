<?php
session_start();
$ruta_raiz = "."; 
if (!$_SESSION['dependencia']) header ("Location: $ruta_raiz/cerrar_session.php");
//*************************************************************************************/
//* ORFEO GPL:Sistema de Gestion Documental		http://www.orfeogpl.org	             */
//*	Idea Original de la SUPERINTENDENCIA DE SERVICIOS PUBLICOS DOMICILIARIOS         */
//*				COLOMBIA TEL. (57) (1) 6913005  orfeogpl@gmail.com                   */
//* ===========================                                                       */
//*                                                                                   */
//* Este programa es software libre. usted puede redistribuirlo y/o modificarlo       */
//* bajo los terminos de la licencia GNU General Public publicada por                 */
//* la "Free Software Foundation"; Licencia version 2. 			                      */
//*                                                                                   */
//* Copyright (c) 2005 por :	  	  	                                              */
//* SSPS "Superintendencia de Servicios Publicos Domiciliarios"                       */
//*   Jairo Hernan Losada  jlosada@gmail.com                Desarrollador             */
//*   Sixto Angel Pinzón López --- angel.pinzon@gmail.com   Desarrollador             */
//* C.R.A.  "COMISION DE REGULACION DE AGUAS Y SANEAMIENTO AMBIENTAL"                 */
//*   Liliana Gomez        lgomezv@gmail.com                Desarrolladora            */
//*   Lucia Ojeda          lojedaster@gmail.com             Desarrolladora            */
//* D.N.P. "Departamento Nacional de Planeación"                                      */
//*   Hollman Ladino       hollmanlp@gmail.com                Desarrollador           */
//*                                                                                   */
//* Colocar desde esta lInea las Modificaciones Realizadas Luego de la Version 3.5    */
//*  Nombre Desarrollador   Correo     Fecha   Modificacion                           */
//*  Infometrika            info@infometrika.com  05/2009  Arreglo Variables Globales */
//*  Jairo Losada           jlosada@gmail.com     05/2009  Eliminacion Funciones-Procesos */
//*************************************************************************************/
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
// Modificado Infom�trika 22-Julio-2009
// Compatibilidad con register_globals = Off
if (!$ent) $ent = substr($verradicado, -1 );
if(!$menu_ver_tmp) $menu_ver_tmp = $menu_ver_tmpOld;
if(!$menu_ver) $menu_ver = $menu_ver_Old;
if(!$menu_ver) $menu_ver=3;
if($menu_ver_tmp)	$menu_ver=$menu_ver_tmp;
if (!defined('ADODB_ASSOC_CASE')) define('ADODB_ASSOC_CASE', 1);
include_once "./include/db/ConnectionHandler.php";
if($verradicado)	$verrad= $verradicado;
if(!$ruta_raiz)	$ruta_raiz=".";
$numrad = $verrad;
error_reporting(7);
$db = new ConnectionHandler(".");
$db->conn->SetFetchMode(3);

if($carpeta==8)
{	$info=8;
	$nombcarpeta = "Informados";
}

include_once "$ruta_raiz/class_control/Radicado.php";
$objRadicado = new Radicado($db);
$objRadicado->radicado_codigo($verradicado);
$path = $objRadicado->getRadi_path();
/*
  * @author Modificacion Liliana Gomez Velasquez
  * @since 10 noviembre 2009
  * @category imagenes
 */
include_once "$ruta_raiz/tx/verLinkArchivo.php";
$verLinkArchivo = new verLinkArchivo($db);

/** verificacion si el radicado se encuentra en el usuario Actual
*
*/

include "$ruta_raiz/tx/verifSession.php";

?>
<html><head><title>.: Modulo total :.</title>
<link rel="stylesheet" href="<?=$ruta_raiz."/estilos/".$_SESSION["ESTILOS_PATH"]?>/orfeo.css">
<!-- seleccionar todos los checkboxes-->
<?php include_once "$ruta_raiz/js/funtionImage.php"; ?>
<SCRIPT LANGUAGE="JavaScript">
function datosBasicos()
{
	window.location='radicacion/NEW.PHP?<?=session_name()."=".session_id()?>&<?="nurad=$verrad&fechah=$fechah&ent=$ent&Buscar=Buscar Radicado&carpeta=$carpeta&nomcarpeta=$nomcarpeta"; ?>';
}
function mostrar(nombreCapa)
{
	document.getElementById(nombreCapa).style.display="";
}
function ocultar(nombreCapa)
{
	if(document.getElementById(nombreCapa) != null)
	document.getElementById(nombreCapa).style.display="none";
}
var contadorVentanas=0
<?

if($dependencia==900 ) $verradPermisos = "Full";
if($carpeta==8 || $carpeta==66 )  $verradPermisos = "Full";

if($verradPermisos == "Full" or $datoVer=="985")
{	if($datoVer=="985")
	{
?>
function  window_onload()
{	<?	if($verradPermisos == "Full" or $datoVer=="985")
{
?>
window_onload2();
<?
}
?>
}
<?
}
?>
</script>
<?
include "pestanas.js";
?>
<script >
<?
}
else
{
?>
function changedepesel(xx)
{
}
<?
}
?>

function window_onload2()
{
<?
if ($menu_ver==3)
{
	echo "ocultar_mod(); ";
	if ($ver_causal) {echo "ver_causales();"; }
	if ($ver_sectores) {echo "ver_sector();"; }
	if ($ver_flujo) {echo "ver_flujo();"; }
	if ($ver_VinAnexo) {echo "verVinculoDocto();"; }
}
 ?>
}
function verNotificacion() {
   mostrar("mod_notificacion");
   ocultar("tb_general");
   ocultar("mod_causales");
   ocultar("mod_sector");
   ocultar("mod_flujo");
}
function ver_datos()
{
   mostrar("tb_general");
   ocultar("mod_causales");
   ocultar("mod_sector");
   ocultar("mod_flujo");
}
function ocultar_mod()
{
   ocultar("mod_causales");
   ocultar("mod_sector");
   ocultar("mod_flujo");
}

function ver_tipodocumental()
{
<?
	if($menu_ver_tmp!=2)
	{
?>
	   ocultar("tb_general");
	   ocultar("mod_causales");
	   ocultar("mod_flujo");
<?	} ?>
}
function ver_tipodocumento()
{
   ocultar("tb_general");
   ocultar("mod_causales");
   ocultar("mod_flujo");
}
function verDecision()
{
   ocultar("tb_general");
   ocultar("mod_causales");
   ocultar("mod_flujo");
}
function ver_tipodocuTRD(codserie,tsub)
 {
   <?php
 		//echo "ver_tipodocumental(); ";
 		$isqlDepR = "SELECT RADI_DEPE_ACTU,RADI_USUA_ACTU from radicado
 		            WHERE RADI_NUME_RADI = '$numrad'";
 		$rsDepR = $db->conn->Execute($isqlDepR);
 		$coddepe = $rsDepR->fields['RADI_DEPE_ACTU'];
 		$codusua = $rsDepR->fields['RADI_USUA_ACTU'];
 		$ind_ProcAnex="N";
   ?>
   window.open("./radicacion/tipificar_documento.php?<?=session_name()."=".session_id()?>&nurad=<?=$verrad?>&ind_ProcAnex=<?=$ind_ProcAnex?>&codusua=<?=$codusua?>&coddepe=<?=$coddepe?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>&tsub="+tsub+"&codserie="+codserie,"Tipificacion_Documento","height=500,width=750,scrollbars=yes");
 }

function verVinculoDocto()
{
<?php
		echo "ver_tipodocumental(); ";
  ?>
  window.open("./vinculacion/mod_vinculacion.php?verrad=<?=$verrad?>&codusuario=<?=$codusuario?>&dependencia=<?=$dependencia?>","Vinculacion_Documento","height=500,width=750,scrollbars=yes");
}


function verResolucion()
{
   ocultar("tb_general");
   ocultar("mod_causales");
   ocultar("mod_flujo");
   ocultar("mod_tipodocumento");
   mostrar("mod_resolucion");
   ocultar("mod_notificacion");
}

function ver_temas(){
   window.open("./tipo_documento.php?<?=session_name()."=".session_id()?>&verrad=<?=$verrad?>","Temas","height=200,width=400,scrollbars=yes");
}

function ver_flujo()
{
   ocultar("tb_general");
   ocultar("mod_tipodocumento");
   ocultar("mod_causales");
   ocultar("mod_sector");
   ocultar("mod_resolucion");
   ocultar("mod_notificacion");
}
function hidden_tipodocumento()
{
  <?
  if(!$ver_tipodoc)
  {
  ?>
      //ocultar_mod();
  <?
  }
  ?>
}
/** FUNCION DE JAVA SCRIPT DE LAS PESTA�S
  * Esta funcion es la que produce el efecto de pertanas de mover a,
  * Reasignar, Informar, Devolver, Vobo y Archivar
  */
</script>
<div id="spiffycalendar" class="text"></div>
<script language="JavaScript" src="js/spiffyCal/spiffyCal_v2_1.js"></script>
<link rel="stylesheet" type="text/css" href="js/spiffyCal/spiffyCal_v2_1.css">
<link rel="stylesheet" href="estilos/tabber.css" TYPE="text/css" MEDIA="screen">
</head>
<?php
// Modificado Supersolidaria
if( isset( $_GET['ordenarPor'] ) && $_GET['ordenarPor'] != "" )
{
 $body = "document.location.href='#t1';";
}
?>
<body bgcolor="#FFFFFF" topmargin="0" onLoad="window_onload();<? print $body; ?>">
<?
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$fechah=date("dmy_h_m_s") . " ". time("h_m_s");
	$check=1;
	$numeroa=0;$numero=0;$numeros=0;$numerot=0;$numerop=0;$numeroh=0;
  include "ver_datosrad.php";
    $_SESSION['dir_doc_us1'] = $cc_documento_us1;
    $_SESSION['dir_doc_us2'] = $cc_documento_us2;
      
	if($verradPermisos == "Full" or $datoVer=="985")
 		{

 		}else
 		{
 			$numRad = $verrad;
 			if($nivelRad==1) include "$ruta_raiz/seguridad/sinPermisoRadicado.php";
 			if($nivelRad==1) die("-");
 		}
 ?>
<table border=0 width=100%  cellpadding="0" cellspacing="0" class="borde_tab">
 <tr>
   <td class="titulos2"><A class=vinculos HREF='javascript:history.back();'>PAGINA ANTERIOR</A></td>
   <td width=85% class="titulos2">
<?
 if($krd)
	{
	$isql = "select * From usuario where USUA_LOGIN ='$krd' and USUA_SESION='". substr(session_id(),0,29)."' ";
	$rs = $db->query($isql);
	// Validacion de Usuario y COntrase� MD5
	//echo "** $krd *** $drde";
	//Modificado por idrd
	if (($krd))
	{
	//  $iusuario = " and us_usuario='$krd'";
	//  $isql = "select a.* from radicado a where radi_depe_actu=$dependencia  and radi_nume_radi=$verrad";
  ?>
	DATOS DEL RADICADO No
	<?
	if ($mostrar_opc_envio==0 and $carpeta!=8 and !$agendado)
	{
		$ent=substr($verrad, -1);
        echo "<a title='Click para modificar el Documento' href='./radicacion/NEW.php?nurad=$verrad&Buscar=BuscarDocModUS&".session_name()."=".session_id()."&Submit3=ModificarDocumentos&Buscar1=BuscarOrfeo78956jkgf' notborder >$verrad</a>";
	}else echo $verrad;

    /*
     *  Modificado: 15-Agosto-2006 Supersolidaria
     *  Muestra el numero del expediente al que pertenece el radicado.
	 */
	if( $numExpediente && $_GET['expIncluido'][0] == "" )
    { 
        echo "<span class=noleidos>&nbsp;&nbsp;&nbsp;PERTENECIENTE AL EXPEDIENTE No. ". ( $_SESSION['numExpedienteSelected'] != "" ?  $_SESSION['numExpedienteSelected'] : $numExpediente )."</span>";
	}
    else if( $_GET['expIncluido'][0] != "" )
	{
        echo "<span class=noleidos>&nbsp;&nbsp;&nbsp;PERTENECIENTE AL EXPEDIENTE No. ".$_GET['expIncluido'][0]."</span>";
        $_SESSION['numExpedienteSelected'] = $_GET['expIncluido'][0];
	}

	?>
	</td>
	<td class="titulos5">
      <a class="vinculos" href='./solicitar/Reservas.php?radicado=<?="$verrad"?>'>Solicitados</a>
    </td>
    <td class="titulos5">
      <a class="vinculos" href='./solicitar/Reservar.php?radicado=<?="$verrad&sAction=insert"?>'>Solicitar Fisico</a>
    </td>
  </tr>
</table>
<table width=100% class='t_bordeGris'>
<tr class='t_bordeGris' >
<td width='33%' height="6" >
	<table width='100%' border='0' cellspacing='0' cellpadding='0'>
	<tr class="celdaGris">
<?
$datosaenviar = "fechaf=$fechaf&mostrar_opc_envio=$mostrar_opc_envio&tipo_carp=$tipo_carp&carpeta=$carpeta&nomcarpeta=$nomcarpeta&datoVer=$datoVer&ascdesc=$ascdesc&orno=$orno";
?>
<td class="titulos2">LISTADO DE: </td>
</tr>
	<tr>
	<td class="info"><?=$nomcarpeta ?>
	</td>
	</tr>
</table>
</td>
<td width='33%' >
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="10%" class="titulos2">USUARIO:</td>
		</tr>
		<tr>
			<td width="90%" class="info"><?=$_SESSION['usua_nomb'] ?></td>
		</tr>
	</table>
</td>
<td width="33%">
	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td width="16%"  class="titulos2" >DEPENDENCIA:</td>
		</tr>
		<tr>
		<td width="84%" class="info" ><?=$_SESSION['depe_nomb'] ?></td>
	</tr>
</table>
</td>
</tr>
</table>
<form name="form1" id="form1" action="<?=$ruta_raiz?>/tx/formEnvio.php?<?=session_name()?>=<?=session_id()?>" method="GET">
<?php
if($verradPermisos=="Full" && !($carpeta==66 || $carpeta==8 ) )
{
 include "$ruta_raiz/tx/txOrfeo.php";
}
?>
<input type='hidden' name='<?=session_name()?>' value='<?=session_id()?>'> 
<input type=hidden name='checkValue[<?=$verrad?>]' value='CHKANULAR'>
<input type=hidden name=enviara value='9'>
</form>
<table border=0 align='center' cellpadding="0" cellspacing="0" width="100%" ><form action='verradicado.php?<?=session_name()?>=<?=trim(session_id())?>&verrad=<?=$verrad?>&datoVer=<?=$datoVer?>&chk1=<?=$verrad."&carpeta=$carpeta&nomcarpeta=$nomcarpeta"?>' method='GET' name='form2'>
 <?
echo "<input type='hidden' name='fechah' value='$fechah'>";
// Modificado Infom�trika 22-Julio-2009
// Compatibilidad con register_globals = Off.
print "<input type='hidden' name='verrad' value='".$verrad."'>";
if($flag==2)
	{
	echo "<CENTER>NO SE HA PODIDO REALIZAR LA CONSULTA<CENTER>";
	}
else
	{
	$row = array();
	$row1 = array();
	if($info)
	{
		$row["INFO_LEIDO"]=1;
		$row1["DEPE_CODI"] = $dependencia;
		$row1["USUA_CODI"] = $codusuario;
		$row1["RADI_NUME_RADI"] = $verrad;
		$rs = $db->update("informados", $row, $row1);
	}
	elseif (($leido!="no" or !$leido) and $datoVer!=985)
	{
		$row["RADI_LEIDO"]=1;
		$row1["radi_depe_actu"] = $dependencia;
		$row1["radi_usua_actu"] = $codusuario;
		$row1["radi_nume_radi"] = $verrad;
		$rs = $db->update("radicado", $row, $row1);
	}
}
include "ver_datosrad.php";
include "ver_datosgeo.php";
$tipo_documento .= "<input type=hidden name=menu_ver value='$menu_ver'>";
$hdatos = session_name()."=".session_id()."&leido=$leido&nomcarpeta=$nomcarpeta&tipo_carp=$tipo_carp&carpeta=$carpeta&verrad=$verrad&datoVer=$datoVer&fechah=fechah&menu_ver_tmp=";
?>
<tr>
	<td width="94%" class="listado1" colspan=16>
	<?
	$datos1 = "";$datos2 = "";$datos3 = "";$datos4 = "";$datos5 = "";
	if($menu_ver==5){$datos5 = "_R";}
	if($menu_ver==1){$datos1 = "_R";}
	if($menu_ver==2){$datos2 = "_R";}
	if($menu_ver==3){$datos3 = "_R";}
	if($menu_ver==4){$datos4 = "_R";}
?>
<table cellpadding="0" cellspacing="0"><TR><TD>
<div id="tab1" class="tabberlive" border="0" >
<ul class="tabbernav">
<?   if($menu_ver==1){ $datoss = "class=tabberactive";}else{$datoss ="";} ?>
<li <?=$datoss?> >
<a href='verradicado.php?<?=$hdatos ?>1'>
Informacion de Radicado
</a>
</li>
</ul>
</div>
	</TD><td>
	<div id="tab1" class="tabberlive" border="1">
<ul class="tabbernav">
<?   if($menu_ver==3){ $datoss = "class=tabberactive";}else{$datoss ="";} ?>
<li <?=$datoss?> >
<a href='verradicado.php?<?=$hdatos?>3'>
	Historico</a>
  </li></ul>
	</div>
</td>
<td>	
<div id="tab1" class="tabberlive" border="1">
<ul class="tabbernav">
<?   if($menu_ver==2){ $datoss = "class=tabberactive";}else{$datoss ="";} ?>
<li <?=$datoss?> >
	<a href='verradicado.php?<?=$hdatos ?>2' >
	Documentos</a>
</li>
</ul>
</div>
</td><td>		
	<div id="tab1" class="tabberlive" border="1">
<ul class="tabbernav">
<?   if($menu_ver==4){ $datoss = "class=tabberactive";}else{$datoss ="";} ?>
<li <?=$datoss?> >
<a href='verradicado.php?<?=$hdatos ?>4'>
	Expedientes
	</a>
</li>
</ul>
</div>
</td></tr>
</table>
	</td>
      <td rowspan="4" class="" width="3%">&nbsp;</td>
    </tr>
    <tr >
		<td  width="100%" >
		<table class=borde_tab width="100%" marginheight="0" marginwidth="0">
		<TR><TD>
		<?
		error_reporting(7);
		switch ($menu_ver) {	
		case 1:
			include "lista_general.php";
			break;
		case 2:
			include "./lista_anexos.php";
			break;
		case 3:
			include "ver_historico.php";
			break;
		case 4:
			include "./expediente/lista_expediente.php";
			break;
		case 5:
			include "plantilla.php";
			break;
								default:break;
					}
		?>
		</TD></TR>
		</table>
		</td>
    </tr>
    <input type=hidden name=menu_ver value='<?=$menu_ver ?>'>
    <tr>
      <td height="17" width="94%" class="celdaGris"> <?
	}else {
	?>  </td>
  </tr>
</table>
<form name='form1' action='enviar.php' method='GET'>
<input type=hidden name=depsel>
<input type=hidden name=depsel8>
<input type=hidden name=carpper>
<CENTER>
	<span class='titulosError'>SU SESION HA TERMINADO O HA SIDO INICIADA EN OTRO EQUIPO</span><BR>
	<span class='eerrores'>
	</CENTER></form>
		<?
	}
}else {echo "<center><b><span class='eerrores'>NO TIENE AUTORIZACION PARA INGRESAR</span><BR><span class='eerrores'><a href='login.php' target=_parent>Por Favor intente validarse de nuevo. Presione aca!</span></a>";}

?> </td>
</tr>
<tr>
	<td height="15" width="94%" class="listado2">&nbsp;</TD>
</tr>
</form>
</table>
</body></html>
