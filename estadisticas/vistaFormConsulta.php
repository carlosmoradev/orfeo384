<?php
session_start();

    $ruta_raiz = "..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

$krd = $_SESSION["krd"];
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

$nomcarpeta                                   = $_GET["carpeta"];
$tipo_carpt                                   = $_GET["tipo_carpt"];
if($_GET["orderNo"]) $orderNo                 = $_GET["orderNo"];
if($_GET["orderTipo"]) $orderTipo             = $_GET["orderTipo"];
if($_GET["tipoEstadistica"]) $tipoEstadistica = $_GET["tipoEstadistica"];
else if(!$tipoEstadistica) $tipoEstadistica   = $_POST["tipoEstadistica"];

if($_GET["genDetalle"]) $genDetalle             = $_GET["genDetalle"];
if($_GET["dependencia_busq"]) $dependencia_busq = $_GET["dependencia_busq"];
if($_GET["fecha_ini"]) $fecha_ini               = $_GET["fecha_ini"];
if($_GET["fecha_fin"]) $fecha_fin               = $_GET["fecha_fin"];
if($_GET["codus"]) $codus                       = $_GET["codus"];
if($_GET["tipoRadicado"]) $tipoRadicado         = $_GET["tipoRadicado"];

if($_GET["codUs"]) $codUs                       = $_GET["codUs"];
if($_GET["fecSel"]) $fecSel                     = $_GET["fecSel"];
if($_GET["genDetalle"]) $genDetalle             = $_GET["genDetalle"];
if($_GET["generarOrfeo"]) $generarOrfeo         = $_GET["generarOrfeo"];
if($_GET["dependencia_busqOri"]) $dependencia_busqOri = $_GET["dependencia_busqOri"];

$dependencia           = $_SESSION["dependencia"];
$usua_doc              = $_SESSION["usua_doc"];
$codusuario            = $_SESSION["codusuario"];
$tip3Nombre            = $_SESSION["tip3Nombre"];
$tip3desc              = $_SESSION["tip3desc"];
$tip3img               = $_SESSION["tip3img"];
$usua_perm_estadistica = $_SESSION["usua_perm_estadistica"];


if(!$tipoEstadistica) $tipoEstadistica   = 1;
if(!$dependencia_busq) $dependencia_busq = $dependencia;
if(!$dependencia_busqOri) $dependencia_busqOri = $dependencia;


    /** DEFINICION DE VARIABLES ESTADISTICA
	*	var $tituloE String array  Almacena el titulo de la Estadistica Actual
	* var $subtituloE String array  Contiene el subtitulo de la estadistica
	* var $helpE String Almacena array Almacena la descripcion de la Estadistica.
	*/
	$tituloE[1] = "RADICACION - CONSULTA DE RADICADOS POR USUARIO";
	$tituloE[2] = "RADICACION - ESTADISTICAS POR MEDIO DE RECEPCION-ENVIO";
	$tituloE[3] = "RADICACION - ESTADISTICAS DE MEDIO ENVIO FINAL DE DOCUMENTOS";
	$tituloE[4] = "RADICACION - ESTADISTICAS DE DIGITALIZACION DE DOCUMENTOS";
	$tituloE[5] = "RADICADOS DE ENTRADA RECIBIDOS DEL AREA DE CORRESPONDENCIA";
	$tituloE[6] = "RADICADOS ACTUALES EN LA DEPENDENCIA";
	$tituloE[11] = "ESTADISTICA DE DIGITALIZACION";
	$tituloE[17] = "ESTADISTICA POR RADICADOS Y SUS RESPUESTAS";
	//$tituloE[18] = "SEGUIMIENTO TRAMITE";

	$subtituloE[1] = "ORFEO - Generada el: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[2] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[3] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[4] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[5] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[6] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[8] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";  
	$subtituloE[17] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";
	$subtituloE[18] = "ORFEO - Fecha: " . date("Y/m/d H:i:s"). "\n Parametros de Fecha: Entre $fecha_ini y $fecha_fin";

	$helpE[1] = "Este reporte genera la cantidad de radicados por usuario. Se puede discriminar por tipo de radicaci&oacute;n. " ;
	$helpE[2] = "Este reporte genera la cantidad de radicados de acuerdo al medio de recepci&oacute;n o envio realizado al momento de la radicaci&oacute;n. " ;
	$helpE[3] = "Este reporte genera la cantidad de radicados enviados a su destino final por el &aacute;rea.  " ;
	$helpE[4] = "Este reporte genera la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n." ;
	$helpE[5] = "Este reporte genera la cantidad de documentos de entrada radicados del &aacute;rea de correspondencia a una dependencia. " ;
	$helpE[6] = "Esta estadistica trae la cantidad de radicados \n generados por usuario, se puede discriminar por tipo de Radicacion. " ;
	$helpE[8] = "Este reporte genera la cantidad de radicados de entrada cuyo vencimiento esta dentro de las fechas seleccionadas. " ;
	$helpE[9] = "Este reporte muestra el proceso que han tenido los radicados tipo 2 que ingresaron durante las fechas seleccionadas. ";
	$helpE[10] = "Este reporte muestra cuantos radicados de entrada han sido asignados a cada dependencia. ";
	$helpE[11] = "Muestra la cantidad de radicados digitalizados por usuario y el total de hojas digitalizadas. Se puede seleccionar el tipo de radicaci&oacute;n y la fecha de digitalizaci&oacute;n." ;
	$helpE[12] = "Muestra los radicados que ten&iacute;an asignados un tipo documental(TRD) y han sido modificados";
	$helpE[13] = "Muestra todos los expedientes agrupados por dependencia que con el n&uacute;mero de radicados totales";
	$helpE[14] = "Muestra el total de radicados que tiene un usuario y 
				el detalle del radicado con respecto al Remitente(Detalle), 
				Predio(Detalle), ESP(Detalle) ";
	$helpE[17] = "Este reporte genera la cantidad de documentos que han llegado al area o usuarios sin importar su origen. " ;
	$helpE[18] = "Este reporte refleja el Tramite que se les ha dado a los Radicados HASTA EL DIA INMEDIATAMENTE ANTERIOR
                      Puede filtrarse por la fecha de REASIGNACION y la dependencia ORIGEN y DESTINO " ;
?>	  
<html>
<head>
<title>principal</title>
<link rel="stylesheet" href="../estilos/orfeo.css">
<?php 

 include_once "../js/funtionImage.php"; ?>

<link rel="stylesheet" type="text/css" href="../js/spiffyCal/spiffyCal_v2_1.css">
<script>
function adicionarOp (forma,combo,desc,val,posicion){
	o = new Array;
	o[0]=new Option(desc,val );
	eval(forma.elements[combo].options[posicion]=o[0]);
	//alert ("Adiciona " +val+"-"+desc );
	
}
function noPermiso(){

	alert ("No tiene permiso para acceder");

}
</script>
		 <script language="JavaScript" src="../js/spiffyCal/spiffyCal_v2_1.js"></script>
		  
		 <script language="javascript">
		 <!--
			<?//corrregido fecha inicial que se mostraba mal en enero rperilla
				$ano_ini = date("Y");
				$mes_ini = substr("00".(date("m")-1),-2);
				if ($mes_ini=="00") {$ano_ini=$ano_ini-1; $mes_ini="12";}
				$dia_ini = date("d");
				if(!$fecha_ini) $fecha_ini = "$ano_ini/$mes_ini/$dia_ini";
					$fecha_busq = date("Y/m/d") ;
				if(!$fecha_fin) $fecha_fin = $fecha_busq;
			?>
   var dateAvailable = new ctlSpiffyCalendarBox("dateAvailable", "formulario", "fecha_ini","btnDate1","<?=$fecha_ini?>",scBTNMODE_CUSTOMBLUE);
   var dateAvailable2 = new ctlSpiffyCalendarBox("dateAvailable2", "formulario", "fecha_fin","btnDate2","<?=$fecha_fin?>",scBTNMODE_CUSTOMBLUE);

//--></script>
</head>

<table><tr><TD></TD></tr></table>
<?php
$ruta_raiz = "..";
include_once "$ruta_raiz/include/db/ConnectionHandler.php";
include("$ruta_raiz/class_control/usuario.php");
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
$objUsuario = new Usuario($db);

?>
<body bgcolor="#ffffff" onLoad="comboUsuarioDependencia(document.formulario,document.formulario.elements['dependencia_busq'].value,'codus');" topmargin="0">
<div id="spiffycalendar" class="text"></div>
<form name="formulario"  method="GET" action='./vistaFormConsulta.php?<?=session_name()."=".trim(session_id())."&fechah=$fechah"?>'>

<table width="100%"  border="0" cellpadding="0" cellspacing="5" class="borde_tab">
  <tr>
    <td colspan="2" class="titulos4">POR RADICADOS  -  <A href='vistaFormProc.php?<?=session_name()."=".trim(session_id())."&krd=$krd&fechah=$fechah"?>' style="color: #FFFFCC">POR PROCESOS </A> </td>
  </tr>
  <tr>
    <td colspan="2" class="titulos3"><span class="cal-TextBox"><?=$helpE[$tipoEstadistica]?></span></td>
  </tr>
  <tr>
    <td width="30%" class="titulos2">Tipo de Consulta / Estadistica</td>
    <td class="listado2" align="left">
	   <select name=tipoEstadistica  class="select" onChange="formulario.submit();">
		<?php	
			foreach($tituloE as $key=>$value)
			{
		?>
	   <?php if($tipoEstadistica==$key) $selectE = " selected "; else $selectE = ""; ?>
			<option value=<?=$key?> <?=$selectE?>><?=$tituloE[$key]?></option>
		<?
		}
		?>
		</select>
	</td>
	</tr>
	<tr>
    <td width="30%" class="titulos2">Dependencia</td>
    <td class="listado2">
    <select name=dependencia_busq  class="select"  onChange="formulario.submit();">
    <?php

      $encabezado = "&genDetalle=$genDetalle&tipoEstadistica=$tipoEstadistica&codus=$codus&dependencia_busq=$dependencia_busq&dependencia_busqOri=$dependencia_busqOri&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&codUs=$codUs&fecSel=$fecSel&"; 
      if($usua_perm_estadistica>1)  {
	if($dependencia_busq==99999)  {
	  $datoss= " selected ";
	}
      ?>
	  <option value=99999  <?=$datoss?>>-- Todas las Dependencias --</option>
      <?php
      }

      $whereDepSelect=" DEPE_CODI = $dependencia ";
      if ($usua_perm_estadistica==1){
	      $whereDepSelect="( $whereDepSelect or depe_codi_padre = $dependencia )";	
      }
      if ($usua_perm_estadistica==2) {
	      $isqlus = "select a.DEPE_CODI,a.DEPE_NOMB,a.DEPE_CODI_PADRE from DEPENDENCIA a ORDER BY a.DEPE_NOMB";
      }else {
	      $isqlus = "select a.DEPE_CODI,a.DEPE_NOMB,a.DEPE_CODI_PADRE from DEPENDENCIA a 
						      where $whereDepSelect ";
      }

      $rs1=$db->query($isqlus);

      do{
	      $codigo = $rs1->fields["DEPE_CODI"]; 
	      $vecDeps[]=$codigo;
	      $depnombre = $rs1->fields["DEPE_NOMB"];
	      $datoss="";
	      if($dependencia_busq==$codigo){
		      $datoss= " selected ";
	      }
	      echo "<option value=$codigo  $datoss>$depnombre</option>";		
	      $rs1->MoveNext();
      }while(!$rs1->EOF);
	      ?>
     </select>
     </td>
</tr>
<?php
if ($tipoEstadistica==18)
   {
   ?>
    <tr>
     <td width="30%" class="titulos2">Dependencia ORIGEN</td>
	  <td class="listado2">
	      <select name=dependencia_busqOri  class="select"  onChange="formulario.submit();">
		<?php
		$encabezado = "&genDetalle=$genDetalle&tipoEstadistica=$tipoEstadistica&codus=$codus&dependencia_busq=$dependencia_busq&dependencia_busqOri=$dependencia_busqOri&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento&codUs=$codUs&fecSel=$fecSel&"; 
		if($usua_perm_estadistica>1)  {
		  if($dependencia_busqOri==99999)  {
		      $datossOri= " selected ";
		    }
		  ?>
			<option value=99999  <?=$datossOri?>>-- Todas las Dependencias --</option>
		  <?php
		  }
		$whereDepSelectOri=" DEPE_CODI = $dependencia ";
		if ($usua_perm_estadistica==1){
			$whereDepSelectOri="( $whereDepSelectOri or depe_codi_padre = $dependencia )";	
		}
		if ($usua_perm_estadistica==2) {
			$isqlusOri = "select a.DEPE_CODI,a.DEPE_NOMB,a.DEPE_CODI_PADRE from DEPENDENCIA a ORDER BY a.DEPE_NOMB";
		}else {
			$isqlusOri = "select a.DEPE_CODI,a.DEPE_NOMB,a.DEPE_CODI_PADRE from DEPENDENCIA a 
								where $whereDepSelectOri ";
		}
		$rsO=$db->query($isqlusOri);

		do{
			$codigoOri = $rsO->fields["DEPE_CODI"]; 
			$vecDeps[]=$codigoOri;
			$depnombreOri = $rsO->fields["DEPE_NOMB"];
			$datossOri="";
			if($dependencia_busqOri==$codigoOri){
				$datossOri = " selected ";
			}
			echo "<option value=$codigoOri  $datossOri>$depnombreOri</option>";		
		      $rsO->MoveNext();
		}while(!$rsO->EOF);
		  ?>
	    </select>
      </td>
      </tr>
      <?
  }
if($tipoEstadistica==1 or $tipoEstadistica==2 or $tipoEstadistica==3 or 
	$tipoEstadistica==4 or $tipoEstadistica==5 or $tipoEstadistica==6 or 
	$tipoEstadistica==7 or $tipoEstadistica==11 or $tipoEstadistica==12 or 
        $tipoEstadistica==17 or $tipoEstadistica==18)
{
?>
       <tr id="cUsuario">
	 <td width="30%" class="titulos2">Usuario
		<br />
	<?	$datoss = isset($usActivos) && ($usActivos) ?" checked ":"";	?>
	<input name="usActivos" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
	Incluir Usuarios Inactivos  
        <?
          if($tipoEstadistica==17 or $tipoEstadistica==18)
            {
        ?>
            
	      <?	$datoss = isset($conSinRep) && ($conSinRep) ?" checked ":"";	?>
	                <input name="conSinRep" type="checkbox" class="select" <?=$datoss?> onChange="formulario.submit();">
	                  Limitar a una respuesta  
        <?
           }
        ?>
        </td>
  	<td class="listado2">
	<select name="codus"  class="select"  onChange="formulario.submit();">
	<?php 	if(($tipoEstadistica==5) AND ($usua_perm_estadistica > 0)) {	?>
			<option value=0> -- NO AGRUPAR POR USUARIOS --</option>
	        <?php }
                if (($usua_perm_estadistica > 0) AND ($tipoEstadistica!=5)){	?>
			<option value=0> -- AGRUPAR POR TODOS LOS USUARIOS --</option>
	<?php }

    $whereDep = ($dependencia_busq != 99999) ? "  u.DEPE_CODI = " . $dependencia_busq : '';
                 
		$whereUsSelect = (!isset($_GET['usActivos']) )? " u.USUA_ESTA = '1' ":"";
		$whereUsSelect=($usua_perm_estadistica < 1)?
		(($whereUsSelect!="")?$whereUsSelect." AND u.USUA_LOGIN='$krd' ":" u.USUA_LOGIN='$krd' "):$whereUsSelect;	  if($dependencia_busq != 99999)  {
 			
			$whereUsSelect=($whereUsSelect=="")? $whereDep:$whereUsSelect." and  ".$whereDep;
			$isqlus = "select u.USUA_NOMB,u.USUA_CODI,u.USUA_ESTA from USUARIO u 
					   where  $whereUsSelect 
					   order by u.USUA_NOMB";
			//if($codusuario!=1) $isqlus .= " and a.usua_codi=$codusuario "; 
 			$rs1=$db->query($isqlus);
			while(!$rs1->EOF)  {
				$codigo = $rs1->fields["USUA_CODI"]; 
				$vecDeps[]=$codigo;
				$usNombre = $rs1->fields["USUA_NOMB"];
				$datoss=($codus==$codigo)?$datoss= " selected ":"";
				echo "<option value=$codigo  $datoss>$usNombre</option>";		
				$rs1->MoveNext();
			}
		}
		?>
		</select>
	&nbsp;</td>
  </tr>
  <?
  }
   $condiRep =  (!isset($_GET['conSinRep']) )? "NO":"SI";

  if($tipoEstadistica==1 or $tipoEstadistica==2 or $tipoEstadistica==3 or 
  		$tipoEstadistica==4 or $tipoEstadistica==6 or $tipoEstadistica==11 or 
  		$tipoEstadistica==12 or $tipoEstadistica==17  or $tipoEstadistica==18)
  {
  ?>
<tr>
	<td width="30%" height="40" class="titulos2">Tipo de Radicado </td>
	<td class="listado2">
	<?php
		$rs = $db->conn->Execute('select SGD_TRAD_DESCR, SGD_TRAD_CODIGO  from SGD_TRAD_TIPORAD order by 2');
		$nmenu = "tipoRadicado";
		$valor = "";
		$default_str=$tipoRadicado;
		$itemBlanco = " -- Agrupar por Todos los Tipos de Radicado -- ";
		print $rs->GetMenu2($nmenu, $default_str, $blank1stItem = "$valor:$itemBlanco",false,0,'class=select');
		?>&nbsp;</td>
</tr>
   <?
  }
  if($tipoEstadistica==1 or $tipoEstadistica==6 or $tipoEstadistica==10 or 
  	$tipoEstadistica==12 or $tipoEstadistica == 14 or $tipoEstadistica==17  or $tipoEstadistica==18 ) {
  ?>
  <tr>
    <td width="30%" height="40" class="titulos2">Agrupar por Tipo de Documento </td>
    <td class="listado2">
	<select name=tipoDocumento  class="select" >
        <?
 		$isqlTD = "SELECT SGD_TPR_DESCRIP, SGD_TPR_CODIGO 
					from SGD_TPR_TPDCUMENTO
					WHERE SGD_TPR_CODIGO<>0
				    order by  SGD_TPR_DESCRIP";
	    //if($codusuario!=1) $isqlus .= " and a.usua_codi=$codusuario "; 
		//echo "--->".$isqlus;
		$rs1=$db->query($isqlTD);
		$datoss = "";

		if($tipoDocumento!='9998'){
			$datoss= " selected ";
			$selecUs = " b.USUA_NOMB USUARIO, ";
			$groupUs = " b.USUA_NOMB, ";
		}

		$datoss = "";

		if($tipoDocumento=='9999'){
                        $datoss= " selected ";
                }
                ?>
                <option value='9999'  <?=$datoss?>>-- No Agrupar Por Tipo de Documento</option>
                <?   $datoss = "";


		if($tipoDocumento=='9997'){
			$datoss= " selected ";
		}
		if($tipoEstadistica==6)
		{
			if($tipoDocumento=='9998'){
                        	$datoss= " selected ";
                	}

		?>
			<option value='9998'  <?=$datoss?>>-- Agrupar Por Tipo de Documento</option>
		<?
		}
		?>

		<option value='9997'  <?=$datoss?>>-- Tipos Documentales No Definidos</option>
		<?
		do{
			$codigo = $rs1->fields["SGD_TPR_CODIGO"]; 
			$vecDeps[]=$codigo;
			$selNombre = $rs1->fields["SGD_TPR_DESCRIP"];
			$datoss="";
		if($tipoDocumento==$codigo){
				$datoss= " selected ";
			}
			echo "<option value=$codigo  $datoss>$selNombre</option>";		
			$rs1->MoveNext();
		}while(!$rs1->EOF);
		?>
		</select>
						
	  </td>
  </tr>
  <?
  }
  if($tipoEstadistica==1 or $tipoEstadistica==2 or $tipoEstadistica==3 or 
  		$tipoEstadistica==4 or $tipoEstadistica==5 or $tipoEstadistica==7 or 
  		$tipoEstadistica==8 or $tipoEstadistica==9 or $tipoEstadistica==10 or 
  		$tipoEstadistica==11 or $tipoEstadistica==12 or $tipoEstadistica==14 or 
                $tipoEstadistica==17  )
  {
  ?>  
  <tr>
    <td width="30%" class="titulos2">Desde fecha (aaaa/mm/dd) </td>
    <td class="listado2">
	<script language="javascript">
	dateAvailable.writeControl();
	dateAvailable.dateFormat="yyyy/MM/dd";
	</script>
	&nbsp;
  </td>
  </tr>
  <tr>
    <td width="30%" class="titulos2">Hasta  fecha (aaaa/mm/dd) </td>
    <td class="listado2">
	<script language="javascript">
	dateAvailable2.writeControl();
	dateAvailable2.dateFormat="yyyy/MM/dd";
	</script>&nbsp;</td>
  </tr>
    <?
  }
 if( $tipoEstadistica==18)
  {
  ?>  
  <tr>
    <td width="30%" class="titulos2">REASIGNADO Desde fecha (aaaa/mm/dd) </td>
    <td class="listado2">
	<script language="javascript">
	dateAvailable.writeControl();
	dateAvailable.dateFormat="yyyy/MM/dd";
	</script>
	&nbsp;
  </td>
  </tr>
  <tr>
    <td width="30%" class="titulos2">REASIGNADO Hasta  fecha (aaaa/mm/dd) </td>
    <td class="listado2">
	<script language="javascript">
	dateAvailable2.writeControl();
	dateAvailable2.dateFormat="yyyy/MM/dd";
	</script>&nbsp;</td>
  </tr>
    <?
  }
  ?>
  <tr>
    <td colspan="2" class="titulos2">
	<center>
	<input name="Submit" type="submit" class="botones_funcion" value="Limpiar"> 
	<input type="submit" class="botones_funcion" value="Generar" name="generarOrfeo">
	</center>
	</td>
  </tr>
</table>
</form>
<?php
$datosaenviar = "fechaf=$fechaf&tipoEstadistica=$tipoEstadistica&codus=$codus&dependencia_busq=$dependencia_busq&dependencia_busqOri=$dependencia_busqOri&ruta_raiz=$ruta_raiz&fecha_ini=$fecha_ini&fecha_fin=$fecha_fin&tipoRadicado=$tipoRadicado&tipoDocumento=$tipoDocumento";


//
if (isset($generarOrfeo) && $tipoEstadistica == 12) {
	global $orderby;
	$orderby = 'ORDER BY NOMBRE';
	$whereDep = ($dependencia_busq != 99999) ? "AND h.DEPE_CODI = " . $dependencia_busq : '';
	//modificado idrd para postgres	
	$isqlus = "SELECT u.USUA_NOMB NOMBRE, u.USUA_DOC, d.DEPE_CODI, 
					COUNT(r.RADI_NUME_RADI) as TOTAL_MODIFICADOS
					  FROM USUARIO u, RADICADO r, HIST_EVENTOS h, DEPENDENCIA d, SGD_TPR_TPDCUMENTO s
					  WHERE u.USUA_DOC = h.USUA_DOC
					    AND h.SGD_TTR_CODIGO = 32
					    AND h.HIST_OBSE LIKE '*Modificado TRD*%'
					    AND h.DEPE_CODI = d.DEPE_CODI
					    $whereDep
					    AND s.SGD_TPR_CODIGO = r.TDOC_CODI
					    AND r.RADI_NUME_RADI = h.RADI_NUME_RADI
					    AND TO_CHAR(r.RADI_FECH_RADI,'yyyy/mm/dd') BETWEEN '$fecha_ini'  AND '$fecha_fin'
					  GROUP BY u.USUA_NOMB, u.USUA_DOC, d.DEPE_CODI $orderby";
	
	$rs1 = $db->query($isqlus);
	while(!$rs1->EOF)  {
		$usuadoc[] = $rs1->fields["USUA_DOC"]; 
		$dependencias[] = $rs1->fields["DEPE_CODI"]; 
		$rs1->MoveNext();
	}
}

if($generarOrfeo)
{
  include "genEstadistica.php";
}
?>
