<?php
session_start();

    $ruta_raiz   = "..";
    if (!$_SESSION['dependencia'])
        header ("Location: $ruta_raiz/cerrar_session.php");

foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;


$krd         = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
$usua_doc    = $_SESSION["usua_doc"];
$codusuario  = $_SESSION["codusuario"];
$tpNumRad    = $_SESSION["tpNumRad"];
$tpPerRad    = $_SESSION["tpPerRad"];
$tpDescRad   = $_SESSION["tpDescRad"];
$tip3Nombre  = $_SESSION["tip3Nombre"];
$tip3img     = $_SESSION["tip3img"];
$tpDepeRad   = $_SESSION["tpDepeRad"];

?>
<html>
<head>
<title>Buscar Radicado</title>
<link rel="stylesheet" href="../estilos/orfeo.css" type="text/css">
<script >

function solonumeros()
{
 jh =  document.getElementById('nurad').value;
 if(jh)
 {
		
		var1 =  parseInt(jh);
		if(var1 != jh)
		{
			alert("Atencion: El numero de Radicado debe ser de solo Numeros.");
			return false;
		}else{
			numCaracteres = document.getElementById('nurad').value.length;
                         <?php
                           $ln=$_SESSION["digitosDependencia"];
                           $lnr=11+$ln;
                        ?>
			if(numCaracteres>=6)
			{
				document.FrmBuscar.submit();
			}else
			{
				alert("Atencion: El numero de Caracteres del radicado es de <?php echo $lnr; ?>. (Digito :"+numCaracteres+")");
			}
			
		}
 }else{
 	document.FrmBuscar.submit();
 }
}
</script>

</head>

<body onLoad='document.getElementById("nurad").focus();'>
	<table border=0 width=100% class="borde_tab" cellspacing="5">
	<tr align="center" class="titulos5">
	<td height="15" class="titulos5">MODIFICACION DE RADICADOS </td>
</tr></Table>
<center></P>
  <form action='NEW.php?<?=session_name()."=".session_id()."&krd=$krd"?>&Submit3=ModificarDocumentos'  name="FrmBuscar" class=celdaGris method="POST">
    <table width="80%" class='borde_tab' cellspacing='5'>
  <tr class='titulos2'> 
        <td width="25%" height="49">Numero de Radicado</td>
    <td width="55%" class=listado2>
		<input type='text' name=nurad class=tex_area id=nurad>
		<input type=hidden name=modificarRad Value="ModificarR" id=modificarRad> 
		<input type=hidden name=Buscar Value="Buscar Radicado"> 
     <input type=button name=Buscar1 Value="Buscar Radicado" class=botones_largo onclick="solonumeros();"> 
	 </td>
  </tr>
</table>
</form>
</center>
</body>
</html>
