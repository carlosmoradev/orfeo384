<?
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="../estilos/orfeo.css">
<script src="../js/popcalendar.js"></script>
<script src="../js/mensajeria.js"></script>
 <div id="spiffycalendar" class="text"></div>
</head>
<body>
<form method=post action=recuperacionExtrema2.php>
	<input type=text name=usuaLogin  value='<?=$usuaLogin?>'>
	<input type=SUBMIT value='Listar'>
</form>
<?
error_reporting(0);
$ruta_raiz="..";
include_once("buscarFila.php");
$_SESSION["dependencia"] = "900";
$_SESSION["krd"] = "1";
error_reporting(7);
if($_GET["verradicado"]) $verradicado = $_GET["verradicado"];
if($_POST["usuaLogin"]) $usuaLogin = $_POST["usuaLogin"]; else $usuaLogin = $_GET["usuaLogin"];
if($_POST["codiCarp"]) $carpCodi = $_POST["carpCodi"]; else $carpCodi = $_GET["carpCodi"];

include_once "$ruta_raiz/include/db/ConnectionHandler.php";
$db = new ConnectionHandler("$ruta_raiz");
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
echo "<hr>".$usuaLogin."<hr>";

if($usuaLogin and !$carpCodi)
{
  $iSql = "Select r.carp_codi, count(1) numero, max(cp.NOMB_CARP)  NOMB_CARP, max(r.CARP_CODI) CARP_CODI
				from usuario u, carpeta_per cp, radicado r
				where 
					u.usua_login='". strtoupper($usuaLogin)."'
					and r.carp_codi=cp.codi_carp
				  and r.carp_per=1
					and u.depe_codi=cp.depe_codi
					and u.usua_codi=cp.usua_codi
					and u.depe_codi=r.radi_depe_actu 
					and u.usua_codi=r.radi_usua_actu
				group by r.carp_codi";
 $rs = $db->conn->query($iSql);
	echo "<hr>";
	while(!$rs->EOF){
		echo "<a href='recuperacionExtrema2.php?PHPSESSID=".session_id()."&usuaLogin=$usuaLogin
			&carpCodi=".$rs->fields["CARP_CODI"]."&Submit3=ModificarDocumentos&modificarRad=ModificarR&nurad=$verradicado&ent=$ent' target='oTraVeNtana'>";
		echo "".$rs->fields["NOMB_CARP"]. " (".$rs->fields["NUMERO"].") <br>";
		echo "</a>";
		
		$rs->MoveNext();
	}
	echo "<hr>";
}else{
  $iSql = "Select r.RADI_NUME_RADI as RADI_NUME_RADI
			from radicado r, carpeta_per cp, usuario u
			where 
				u.usua_login='". strtoupper($usuaLogin)."'
				and cp.codi_carp=$carpCodi
				and r.carp_codi=cp.codi_carp
				and r.carp_per=1
				and u.depe_codi=cp.depe_codi
				and u.usua_codi=cp.usua_codi
				and u.depe_codi=r.radi_depe_actu 
				and u.usua_codi=r.radi_usua_actu";
 $rs = $db->conn->query($iSql);
	echo "<hr>";
	$radicados = array();
	while(!$rs->EOF){
		$radicados[] = $rs->fields["RADI_NUME_RADI"];
		$verradicado = $rs->fields["RADI_NUME_RADI"];
		echo "Radicado :" .$verradicado ."<br>";
		
		$path = substr($verradicado,0,4 )."/".substr($verradicado,4,3 )."/docs/1".$verradicado."*" ;
		$comando = "ls -l /var/www/bodegaProd/$path sort"; 
		exec($comando,$a);
		$buscarAnexos = new buscarFila();
		$buscarAnexos->ini = "2009";
		$buscarAnexos->fin = "1";
?>

		
		<table class='e_tablas' width='100%'>
		<td  class=titulos2>Documento Anexo</td>
		<td  class=titulos2>Anexos</td>
		<td  class=titulos2>MOdifica Rad Entrada</td>
		<td  class=titulos2>Recupera Anexos</td>
		</tr>
		<?
		$buscarAnexos = new buscarFila();
		$buscarAnexos->ini = "2009";
		$buscarAnexos->fin = "1";
		$numConsecutivo = str_pad($num,6,"0", STR_PAD_LEFT);
		$numRadicado = $verradicado;
		$path = substr($numRadicado,0,4 )."/".substr($numRadicado,4,3)."/docs/1".$numRadicado."* ";
		$comando = "ls -l ../bodega/$path "; 
		//$comando = "ls -l ../bodega/* "; 
		$a="";
		exec($comando,$a);
?>
	<tr>
	<td  class=titulos2><?=$comando?></td>
	<td  class=titulos2>
	<? 
		foreach($a as $key=>$valor){
			echo "$valor <br>";
		}
	?><br></td>
	<td  class=titulos2><a href='../radicacion/NEW.php?<?="PHPSESSID=".session_id()."&usuaLogin=$usuaLogin
			&carpCodi=".$rs->fields["CARP_CODI"]."&Submit3=ModificarDocumentos&modificarRad=ModificarR&nurad=$verradicado&ent=2"?>' target='oTraVeNtana'> Modificar</a></td>
	<td  class=titulos2>
     <a href='recuperaAnexos.php?<?=session_name()?>=<?=session_id()?>&verradicado=<?=$verradicado?>' target='RAnexos'>Recuperar Anexos</a>
</td>
	</tr>
</table>

<?
		$rs->MoveNext();
	}
	echo "<hr>";
}

if (!$ruta_raiz) $ruta_raiz="..";
error_reporting(7);
?>
</body>
</html>