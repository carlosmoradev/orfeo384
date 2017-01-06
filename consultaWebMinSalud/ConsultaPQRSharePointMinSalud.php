<?php
/**
 * Modulo de consulta Web de PQR creadas en SharePoint
 * @autor Sebastian Ortiz
 * @fecha 2012/10
 *
 */

session_start();
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
header('Content-Type: text/html; charset=UTF-8');

define('ADODB_ASSOC_CASE', 1);

$ruta_raiz = "..";
$ADODB_COUNTRECS = false;
require_once("$ruta_raiz/include/db/ConnectionHandler.php");
include "../config.php";
$_SESSION["depeRadicaFormularioWeb"]=$depeRadicaFormularioWeb;  // Es radicado en la Dependencia 900
$db = new ConnectionHandler($ruta_raiz);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
include('./captcha/simple-php-captcha.php');

$isCaptchaOK = strcasecmp ($captcha ,$_SESSION['captcha_consulta']['code'] ) == 0?true:false;
if($isCaptchaOK){
	if(!empty($ID) && !empty($numeroDocumento)){
		//Envia los dos parametros
		$sql_pqr = "SELECT * FROM PQR WHERE ID=$ID AND IDENTIFICACION_CIUDADANO='$numeroDocumento'";

	}else if(!empty($ID) && empty($numeroDocumento)){
		//Envia solo el ID
		$sql_pqr = "SELECT * FROM PQR WHERE ID=$ID";

	}else if(empty($ID) && !empty($numeroDocumento)){
		//Envia solo el numeroDocumento
		$sql_pqr = "SELECT * FROM PQR WHERE IDENTIFICACION_CIUDADANO='$numeroDocumento' ORDER BY ID DESC";

	}else if(empty($ID) && empty($numeroDocumento)){
		//No envia ninguno de los dos
		$sql_pqr = "SELECT * FROM PQR WHERE ID=-1";
	}
	$respuestas = array();
	$rs_pqr = $db->conn->Execute($sql_pqr);

	if($rs_pqr->RecordCount() == 0){
		$respuestas[0]=array('No se encontró ningún registro.');
	}

	while(!$rs_pqr->EOF){
		
		$tmp = $rs_pqr->fields;
		$sql_pqrorfeo = "SELECT * FROM RADICADO WHERE RADI_DEPE_RADI = 4240 AND RADI_CUENTAI='".$tmp['ID']."'";
		$rs_pqrorfeo = $db->conn->Execute($sql_pqrorfeo);
		if($rs_pqrorfeo->EOF){
			array_push($tmp,"No");
			array_push($tmp,"");
			
		}else{
			array_push($tmp,$rs_pqrorfeo->fields['RADI_NUME_RADI']);
			array_push($tmp,$rs_pqrorfeo->fields['RADI_PATH']);
			array_push($tmp,$rs_pqrorfeo->fields['SGD_RAD_CODIGOVERIFICACION']);
		}
		
		$respuestas[] = $tmp;
		$rs_pqr->MoveNext();
	}
}else{
	//Captcha inválido
	//TODO
	echo "Captcha inválido";
}
?>
<html>
<head>
<title>Consulta web del estado del documento</title>
<!-- Meta Tags -->
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<!-- CSS -->
<link rel="stylesheet" href="css/structure2.css" type="text/css" />
<link rel="stylesheet" href="css/form.css" type="text/css" />

<!-- JavaScript -->
<script type="text/javascript" src="js/wufoo.js"></script>
<!-- prototype -->
<script type="text/javascript" src="js/prototype.js"></script>
<!--funciones-->
<script type="text/javascript" src="js/orfeo.js"></script>

<!--
<style type="text/css">
<!--
.style1 {
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: smaller;
	color: #FF0000;
}
-->
</style>
</head>

<body id="public">
<!--onload="disableElementById('consultaPQRSP');"-->

<div id="container">

<h1>&nbsp;</h1>



<div class="info">
<center><img src='../logoEntidad.png'></center>
<h4><?=$db->entidad_largo?></h4>
<p>
La repuesta a su solicitud es la siguiente:
</p>
</div>

<table class="pqrshare1" border="0px" cellspacing="1px" cellpadding="9px">
<?php
if($isCaptchaOK){
	for($i=0;$i<sizeof($respuestas);$i++){
		if($i==0 && sizeof($respuestas[$i])>1){
			echo "<tr>";
			echo "<td class='titulos4'>";
			echo "ID";
			echo "</td>";
			echo "<td class='titulos4'>";
			echo "Respuesta";
			echo "</td>";
			echo "<td class='titulos4'>";
			echo "Estado";
			echo "</td>";
			echo "<td class='titulos4'>";
			echo "Fecha Radicado";
			echo "</td>";
			echo "<td class='titulos4'>";
			echo "Fecha Máxima de Respuesta";
			echo "</td>";
			echo "</td>";
			echo "<td class='titulos4'>";
			echo "Archivos adjuntos";
			echo "</td>";
			echo "<td class='titulos4'>";
			echo "Nuevo radicado";
			echo "</td>";
			echo "</tr>";
		}else if ($i==0 && sizeof($respuestas[$i])==1){
			//No se encontraron resultados
			//TODO Agregar mensaje de no resultados
			echo "<tr><td>No se encontró ningún resultado que coincida con su búsqueda, por favor intente de nuevo.</td></tr>";
			break;
		}
        $class = ($i%2 == 0)? "class='listado2'" : "class='listado1'";
		echo "<tr $class>";
		echo "<td>";
		echo $respuestas[$i]['ID'];
		echo "</td>";
		echo "<td>";
		echo $respuestas[$i]['RESPUESTA'];
		echo "</td>";
		echo "<td>";
		echo $respuestas[$i]['ESTADO'];
		echo "</td>";
		echo "<td>";
		echo $respuestas[$i]['FECHA_RADICACION'];
		echo "</td>";
		echo "<td>";
		echo $respuestas[$i]['FECHA_MAX_RESPUESTA'];
		echo "</td>";
		echo "<td>";
		//Recorrer los ajduntos		
		$sql_adjuntos = "SELECT * from pqr_adjuntos where id=" . $respuestas[$i]['ID'];
		$rs_adjuntos = $db->conn->Execute($sql_adjuntos);
		
		if($rs_adjuntos->RecordCount() == 0){
			$adjuntos=array('No se encontró ningún registro.');
		}
		
		$str_adjuntos = "";

		while(!$rs_adjuntos->EOF){
			$adjuntos = $rs_adjuntos->fields;
			$rs_adjuntos->MoveNext();
		}
		for($j=1;$j<sizeof($adjuntos);$j++){
			if (!empty($adjuntos["ADJUNTO" . $j])){
				$str_adjuntos = $str_adjuntos . "<a href=\"https://orfeo.correlibre.org/orfeo/bodega/pqrs_sharepoint/" .(string)("" . $adjuntos["ADJUNTO" . $j] . "\" target=\"_blank\">".
				 $adjuntos["ADJUNTO" . $j] . "</a> &nbsp;") ;
			}
		}
		echo $str_adjuntos;
		echo "</td>";
		echo "<td>";
		//$radicado = $respuestas[$i][0]=="No"?"No":"<a href=\"https://orfeo.correlibre.org/orfeo/bodega/".$respuestas[$i][1] ."\" target=\"_blank\">".$respuestas[$i][0].+"</a>";
		$radicado = $respuestas[$i][0]=="No"?"No":"<a href=\"index_web.php?numeroRadicado=".$respuestas[$i][0]."&codigoverificacion=" .$respuestas[$i][2]. "&dontcare=oks&captcha=notengo\" target=\"_self\">".$respuestas[$i][0]."</a>";
		echo $radicado;
		echo "</td>";
		echo "</tr>";

	}
}else{
	echo "<tr><td>El texto de la imágen de verificación NO coincide con la imágen mostrada, por favor intente de nuevo.</td></tr>";
}

?>
<tr>
<td>
<input type="button" value="Regresar" onclick="window.history.back();">
</td>
</tr>

</table>
</body>
</html>
