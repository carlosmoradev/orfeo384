<?php
session_start();

foreach ($_GET  as $key => $val){ ${$key} = $val;}
foreach ($_POST as $key => $val){ ${$key} = $val;}

$ruta      = '/include';
while(!is_dir($ruta_raiz.$ruta)) $ruta_raiz .=  empty($ruta_raiz)? "../" : "..";


if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$verradicado     = $verrad;
if($verradicado) $verrad= $verradicado;

$numrad = $verrad;
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(3);
include "$ruta_raiz/ver_datosrad.php";
?>

<html>
<head>
<title>.: ORFEO - DATOS DEL RADICADO GENERADO :.</title>
</head>
<body bgcolor="#FFFFFF" topmargin="0">
<FORM
	ACTION='hojaResumenRad.php?krd=<?=$krd?>&verrad=<?=$verrad?>&ent=<?=$ent?>&<?=session_name()?>=<?=session_id()?>'
	METHOD=POST><?
	$fechah=date("dmy_h_m_s") . " ". time("h_m_s");
	$check=1;
	$numeroa=0;$numero=0;$numeros=0;$numerot=0;$numerop=0;$numeroh=0;

	$nurad = $verrad;
	include "$ruta_raiz/include/barcode/index.php";
	$inf1= '
    <TABLE BORDER="0" >
    <TR >
    <TD WIDTH=350 rowspan=3>
    <img src="../logoEntidad_blanco.png" WIDTH=250>
    .
    </TD>
    <TD WIDTH=450 >
    <img src="'.$file.".png\" WIDTH=250 HEIGHT=50 >.
    </TD>
    </TR><TR>
    <TD WIDTH=450>.</TD>
    <TD WIDTH=300 HEIGHT=50>
    <b>RADICADO No $verrad</b>
    </TD>
    </TR><TR>    
    <TD WIDTH=750 HEIGHT=80>
    Dependencia rad. $dependenciaOrigen
    </TD>
    </TR>
    <TR>
    <TD WIDTH=750>
    Fecha de Generacion ".date("Y-m-d h:i:s")." 
    </TD>
    </TR>
    </TABLE>
    ";
	include "$ruta_raiz/ver_datosgeo.php";
	if(!trim($nombret_us1)) $nombret_us1 = "-.- ";
	if(!trim($direccion_us1)) $direccion_us1 ="-.-";

	//El asunto puede ser de máximo 1000 caracteres que vamoa a imprimir en 8 lineas a de 110
	$ra_asun_split = str_split($ra_asun,110);
	for($linei=0;$linei<8;$linei++){
		if(!$ra_asun_split[$linei]){
			$ra_asun_split[$linei] = ".";
		}
	}

	$inf .="
    
    <TABLE BORDER=1>
    <TR WIDTH=750>
    <TD BGCOLOR='#CCCCCC' WIDTH=150>FECHA DE RAD</TD>
    <TD WIDTH=600>$radi_fech_radi</TD>
    </TR>
    <TR WIDTH=750>
    <TD BGCOLOR='#CCCCCC' WIDTH=150> 
    USUARIO </TD>
    <TD WIDTH=600> 
    $nombret_us1
    </td>
    </tr>
    <tr WIDTH=750>
    <td WIDTH=150 bgcolor='#CCCCCC'>DIRECCION</td>
    <td  WIDTH=600> 
    $direccion_us1
    ($dpto_nombre_us1 / $muni_nombre_us1)
    </td>
    </tr>
<tr>
<td WIDTH=750 bgcolor='#CCCCCC' align='center' colspan=1>ASUNTO</td>
</tr>
<TR WIDTH=750>
<TD WIDTH=750>
<tr WIDTH=750>
$ra_asun_split[0]
</tr>
<tr WIDTH=750>
$ra_asun_split[1]
</tr>
<tr WIDTH=750>
$ra_asun_split[2]
</tr>
<tr WIDTH=750>
$ra_asun_split[3]
</tr>
<tr WIDTH=750>
$ra_asun_split[4]
</tr>
<tr WIDTH=750>
$ra_asun_split[5]
</tr>
<tr WIDTH=750>
$ra_asun_split[6]
</tr>
<tr WIDTH=750>
$ra_asun_split[7]
</tr>
</TD>    
</TR>
<TR>
<TD BGCOLOR='#CCCCCC' WIDTH='750' colspan=2><CENTER><B>TEMA / SUBTEMA</B></CENTER>
</TD>
</TR>
<TR>
<TD WIDTH='750' colspan=2><CENTER>$causal_nombre / $dcausal_nombre</CENTER>
</TD>
</TR>
<TR>
	<TD WIDTH='400' HEIGHT='100' colspan=1><CENTER>-</CENTER>
	</TD>
	<TD WIDTH='350' HEIGHT='100' colspan=1><CENTER>-</CENTER>
	</TD>
</TR>
<TR>
	<TD WIDTH='400'  colspan=1><CENTER>Firma Usuario</CENTER>
	</TD>
	<TD WIDTH='350'  colspan=1><CENTER>Firma Funcionario $db->entidad</CENTER>
	</TD>
</TR>
</TABLE>";

    define(FPDF_FONTPATH,'../fpdf/font/');
    require("../fpdf/html_table.php");
    error_reporting(7);
    $espacio = "<table><tr><td>............................................................................................................................................................................................................................................................................................................................................................................</td></tr></table>";
    $pdf = new PDF("P","mm","LEGAL");
    $pdf->AddPage();
    $pdf->SetFont('Arial','',8);
    $pdf->WriteHTML($inf1 . $inf.$espacio.$inf1 . $inf);
    $arpdf_tmp = "../bodega/pdfs/planillas/envios/$krd"."_".$verrad."_".microtime(FALSE).".pdf";
    $pdf->Output($arpdf_tmp);

    echo "<br>";
    echo $inf1 . $inf;

    ?><br>
<table border=0 width=80%>
	<tr>
		<td><?
		echo "<center><a class=vinculos href='$arpdf_tmp?fechaf".date("dmYh").time("his")."'>Abrir Archivo Pdf</a></center></td><td>";
		if(!trim($radi_path) and !$subirImagen)
		{
			echo "<center><input type=submit name=subirImagen value='COLOCAR PDF COMO IMAGEN DEL RADICADO'></center>";
		}
		?></td>
	</tr>
</table>
		<?
		if($subirImagen and $verrad)
		{
			$depeDir = substr($verrad,4,$_SESSION['digitosDependencia']);
			//Truco para pasar de  0900 a 900
			//http://php.net/manual/en/language.types.type-juggling.php
			$depeDir += 0;
			$rutaNew = "/" . substr($verrad,0,4)."/".$depeDir."/$verrad".".pdf";
			//$rutaNew = "/". substr($verrad,0,4)."/".substr($verrad,4,3)."/".$nurad.".pdf";
			//ini_set("display_errors", 1);
			//shell_exec("cp $arpdf_tmp $ruta_raiz/bodega$rutaNew");
			//exec ("cp $arpdf_tmp $ruta_raiz/bodega$rutaNew",$output,$returnS);

			if(@copy($arpdf_tmp, $ruta_raiz."bodega".$rutaNew)){
				$sql = "UPDATE RADICADO SET RADI_PATH='$rutaNew' where radi_nume_radi=$verrad";
				if($db->conn->Execute($sql))
				{
					$radicadosSel[] = $verrad;
					$codTx = 42;	//Código de la transacción digitalización
					include "$ruta_raiz/include/tx/Historico.php";
					$hist = new Historico($db);
					$hist->insertarHistorico($radicadosSel,  $_SESSION['dependencia'] , $_SESSION['codusuario'], $_SESSION['dependencia'], $_SESSION['codusuario'], "Asociación imágen hoja resumen radicado.", $codTx);
				}else{
					echo "<hr>No actualizo la BD <hr>";
				}
			}
			else {
				echo "Error subiendo el archivo";
			}
		}
		?></FORM>
</body>
</html>
