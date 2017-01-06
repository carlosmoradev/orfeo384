<?
session_start();
/**
  * Modulo de Formularios Web para atencion a Ciudadanos.
  * @autor Carlos Barrero   carlosabc81@gmail.com SuperSolidaria
  * @fecha 2009/05
  * @Fundacion CorreLibre.org
  * @licencia GNU/GPL V2
  */

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

define('ADODB_ASSOC_CASE', 2);

if($_GET["orderNo"]) $orderNo=$_GET["orderNo"];

$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);

session_start();
$_SESSION['nombre_remitente']=$_GET['nombre_remitente'];
$_SESSION['apellidos_remitente']=$_GET['apellidos_remitente'];
$_SESSION['cedula']=$_GET['cedula'];
$_SESSION['depto']=$_GET['depto'];
$_SESSION['muni']=$_GET['muni'];
$_SESSION['direccion_remitente']=$_GET['direccion_remitente'];
$_SESSION['telefono_remitente']=$_GET['telefono_remitente'];
$_SESSION['email']=$_GET['email'];
$_SESSION['nit']=$_GET['nit']; 
if(!$_GET['nit']) $_SESSION['nit'] = "0";
$_SESSION['codigo_orfeo']="510";
$_SESSION['sigla']=$_GET['sigla'];
if(!$_GET['sigla']) $_SESSION['sigla'] = "0";
$_SESSION['usuario']=1;
if(!$_SESSION['dependencia']) $_SESSION['dependencia']=510;
$dependenciaRad = $_SESSION['dependencia'];
$_SESSION['tipo']=$_GET['tipo'];
$_SESSION['radicado']=$_GET['radicado'];
$_SESSION['asunto']=$_GET['asunto'];
$_SESSION['desc']=$_GET['desc'];
$_SESSION['documento_destino']=$_GET['documento_destino'];

$numero=substr('000000'.$db->conn->GenID('SECR_TP2_'.$_SESSION['secRadicaFormularioWeb']),-6);
$num_dir=$db->conn->GenID('SEC_DIR_DIRECCIONES');
$num_ciu=$db->conn->GenID('SEC_CIU_CIUDADANO');

// $depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
// $usuaRecibeWeb ; // Usuario que Recibe los Documentos Web
// $secRadicaFormularioWeb ;

$numeroRadicado = date('Y').$_SESSION['depeRadicaFormularioWeb'].$numero."2";


//inserta ciudadano
$ins_ciu="insert into sgd_ciu_ciudadano values(2,".$num_ciu.",'".strtoupper($_SESSION['nombre_remitente'])."','".strtoupper($_SESSION['direccion_remitente'])."','".strtoupper($_SESSION['apellidos_remitente'])."','','".$_SESSION['telefono_remitente']."','".$_SESSION['email']."',".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['cedula']."')";
$rs_ins_ciu=$db->conn->Execute($ins_ciu);


//inserta en sgd_dir_direcciones
$ins_dir="insert into sgd_dir_drecciones(sgd_dir_codigo,sgd_dir_tipo,sgd_oem_codigo,sgd_ciu_codigo,radi_nume_radi,sgd_esp_codi,muni_codi,dpto_codi,sgd_dir_direccion,sgd_dir_telefono,sgd_sec_codigo,sgd_dir_nombre,sgd_dir_nomremdes,sgd_trd_codigo,sgd_dir_doc)
values(".$num_dir.",1,0,".$num_ciu.",$numeroRadicado,0,".$_SESSION['muni'].",".$_SESSION['depto'].",'".$_SESSION['direccion_remitente']."','".$_SESSION['telefono_remitente']."',0,'".strtoupper($_SESSION['nombre_remitente'])." ".strtoupper($_SESSION['apellidos_remitente'])."','".strtoupper($_SESSION['sigla'])."',3,".$_SESSION['nit'].")";



//inserta en radicado
$ins_rad="insert into radicado (radi_nume_radi,radi_fech_radi,tdoc_codi,mrec_codi,eesp_codi,radi_fech_ofic,radi_nume_iden,radi_pais,muni_codi,carp_codi,dpto_codi,radi_nume_hoja,radi_desc_anex,";
if($_SESSION['radicado']!=NULL)
	$ins_rad.=" radi_nume_deri,";

if(!$_SESSION['telefono_remitente']) $_SESSION['telefono_remitente']=0;
$ins_rad.=" radi_path,radi_usua_actu,radi_depe_actu,ra_asun,radi_depe_radi,radi_usua_radi,codi_nivel,flag_nivel,carp_per,radi_leido,radi_tipo_deri,sgd_fld_codigo,sgd_apli_codi,sgd_ttr_codigo,sgd_spub_codigo) 
values ($numeroRadicado, to_date('".date('d')."/".date('m')."/".date('Y')." ".date('h').":".date('m').":".date('s')."','dd/mm/yyyy hh24:mi:ss'),".$_SESSION['tipo'].",4,".$_SESSION['codigo_orfeo'].",
to_date('".date('d')."/".date('m')."/".date('Y')."','dd/mm/yyyy')
,'".$_SESSION['cedula']."'
,'COLOMBIA'
,".$_SESSION['muni']."
,0,".$_SESSION['depto']."
,1
,'1 FOLIO', ";
if($_SESSION['radicado']!=NULL)
	$ins_rad.=$_SESSION['radicado'].", ";
 $depeRadicaFormularioWeb =  $_SESSION['depeRadicaFormularioWeb'];
 $anoRad = date("Y");

$rutaPdf ="/$anoRad/$depeRadicaFormularioWeb/$numeroRadicado".".pdf";
$ins_rad.="'/$anoRad/$depeRadicaFormularioWeb/$numeroRadicado".".pdf'
	,".$_SESSION['usuaRecibeWeb']."
	,".$_SESSION['depeRadicaFormularioWeb']."
	,'".strtoupper($_SESSION['asunto'])."'
	,".$_SESSION['dependencia'].",6,5,1,0,0,1,0,0,0,0)";

$rs_ins_rad=$db->conn->Execute($ins_rad);
$rs_ins_dir=$db->conn->Execute($ins_dir);
//Inserta historico
$ins_his="insert into hist_eventos (depe_codi,hist_fech,usua_codi,radi_nume_radi,hist_obse,usua_codi_dest,usua_doc,sgd_ttr_codigo,hist_doc_dest,depe_codi_dest) 
values($dependenciaRad,to_date('".date('d')."/".date('m')."/".date('Y')." ".date('h').":".date('m').":".date('s')."','dd/mm/yyyy hh24:mi:ss'),6,$numeroRadicado,'RADICACION PAGINA WEB',".$_SESSION['usuario'].",'22222222',2,'".$_SESSION['documento_destino']."',".$_SESSION['dependencia'].")";
$rs_ins_his=$db->conn->Execute($ins_his);

//num radicado completo
$_SESSION['radcom']=$numeroRadicado;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Entidad Usuaria de Orfeo - </title>
<link rel="stylesheet" href="css/structure.css" type="text/css" />
</head>

<body>
<p>&nbsp;</p>
<table width="80%" border="0" align="center" cellpadding="0" cellspacing="0" bgcolor="#FFFFFF">
  <tr>
    <td align="center"><br /><img src="../logoEntidadWeb.gif" width="532" height="128" /></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
  </tr>
  <tr>
<td>
Su solicitud ha sido registrada de forma exitosa con el radicado No. <font color = red><b><?=$numeroRadicado?></b></font>
</td>
  </tr>
  <tr>
    <td align="center">Pulse continuar para terminar la solicitud y visualizar el documento en formato PDF. Si desea almacenelo en su disco duro o imprimalo. La descarga puede tomar unos minutos. </td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
  <tr>
    <td align="center"><input type="button" name="Submit" value="Continuar" onclick="window.open('formulariopdf.php?rutaPdf=<?=$rutaPdf?> ')" />
    <input type="button" name="Submit2" value="Cerrar" onclick="window.close()" /></td>
  </tr>
  <tr>
    <td align="center">&nbsp;</td>
  </tr>
</table>
</body>
</html>
