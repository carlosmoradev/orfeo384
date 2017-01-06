<html>
<head>
    <link rel="stylesheet" href="../estilos/orfeo38/orfeo.css">
</head>
<body>
<?
ini_set("display_errors",1);
define('ADODB_ASSOC_CASE', 1);
$ruta_raiz= "..";
$myServer = "172.16.1.123"; //This would be the ip address of your server running mssql
$myServer = "minps19\sql2005";
$myUser = "lgomezv"; //This would be the username that you have assigned on your server with access rights to the database
$myPass = "lgomezv"; //This would be the password for the user you have assigned on your server with access rights to the database
$myDB= "P_correspondencia"; //This would by the Database Name
include('../include/class/adodb/adodb.inc.php'); 
$db = & ADONewConnection("mssql");
$db->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->charSet="UTF-8";<
if($db->Connect($myServer, $myUser, $myPass, 'p_correspondencia')==false){echo "<br>No se conecto a Melba";} else echo("<br>Si se conecto a Melba<br>");
$usuario = "postgres";
$contrasena= "0rfe0gplMcorrelibre";
$servidor = "172.17.21.31:5432";
$base= "orfeoMelba"; //This would by the Database Name
include('../include/class/adodb/adodb.inc.php'); 
$dbOrfeo = & ADONewConnection("postgres");
$db->SetFetchMode(ADODB_FETCH_ASSOC);
//$db->charSet="UTF-8";
if($dbOrfeo->Connect($servidor, $usuario, $contrasena, $base)==false){echo "<br>No se conecto a Orfeo";} else echo("<br>Si se conecto a Orfeo");



//$db->debug = true;
$iSqlRadicados = "select 
radicado, ano,fecha,tipo_radicado,
anexos,folios,tipo_documento,asunto,
tipo_anexos,tipo_poblacion,
tipo_llegada,dirigido,
remitente_persona,remitente_funcionario,
depe_origen_salida,firma_salida,
cedula_persona,apellidos_remitente,
nombres_remitente,ciudad_remitente,
direccion_remitente,institucion_remitente,
cargo_remitente,nombre_institucion,
direccion_institucion,telefono_institucion,
ciudad_institucion,depe_func_remitente,
usua_radicador,depe_radicador,
fecha_mvto,depe_destino,
depe_origen,recibido,
descargado,trasladado,
asignado,func_actu
from
radicadotemp 
where ano = 2012
and radicado >= 20000
and radicado <  50000
order by radicado
";

$iSqlHistorico = "select 
  radicado,
  ano,
  operacion,
  estado,
  fecha,
  tipo_llegada,
  dirigido,
  institucion,
  direccion,
  telefono,
  ciudad,
  apellidos,
  nombres,
  cargo,
  cedula,
  dependencia_origen,
  dependencia_destino,
  referido_radicado,
  referido_ano,
  referido_radicado2,
  referido_ano2,
  referido_radicado3,
  referido_ano3,
  referido_radicado4,
  referido_ano4,
  referido_radicado5,
  referido_ano5,
  funcionario,
  firma,
  dependencia,
  tarifa,
  login,
  tipo_poblacion,
  comentario,
  usuario 
from
hist_eventostemp 
where ano = 2012
and radicado >= 25000
order by radicado
";

$iSqlUsuarios = "select 
        login,
	apellidos,
	nombres,
	cargo,
	dependencia,
        activo,
        firma
from
usuario_temp
order by dependencia
";

$iSqlTarifas = "select 
        codigo ,
        tipo_envio,
	ano,
	peso_minimo,
	peso_maximo,
	valor,
	activo,
        empresa_correo,
	nombre,
	descripcion,
	activo_empresa
from
tarifa_temp
order by tipo_envio,codigo
";
$iSqlReferidos = "select 
        radicado,
	ano,
	referido,
	referido_ano
from
anexostemp
order by ano,radicado
";
$iSqleNVIOS = "select 
        codigo,
  radicado,
  ano,
  tarifa,
  guia,
  persona,
  fecha,
  enviado,
  v_declarado,
  t_seguro,
  v_seguro,
  v_total,
  realizado_por,
  cedula_destinatario,
  apellidos_destinatario,
  nombres_destinatario,
  ciudad_destinatario,
  direccion_destinatario,
  institucion,
  cargo_destinatario,
  nombre_inst,
  dir_inst,
  tel_inst,
  ciu_inst
from
enviotemp
where ano = 2012
and radicado >= 1
order by ano,radicado
";

      $rs = $db->Execute($iSql);
?>
<table class=borde_tab>
<tr class=tilutos5>
<?
var_dump($rs->MetaType($fld->name));

foreach($rs->fields as $campo=>$valorCampo) {
    ?>
      <td><?=$campo?></td>  
    <?
  }
?>
</tr>
<?
$dbOrfeo->debug=true;
while(!$rs->EOF){
    echo "<tr class=titulos2>";
  foreach($rs->fields as $campo=>$valorCampo) {
   $campo=strtoupper($campo);
   $valorCampo=utf8_encode($valorCampo);
  ?>
    <td><?=$valorCampo?></td>  
  <?
     if(($valorCampo)){ $record[$campo] =$valorCampo; }
    else {$record[$campo] = 0; }
   
  }
   //$insertSQL = $dbOrfeo->Replace('anexostemp',$record,array('ano','radicado','referido_ano','referido'), $autoquote = true);
    $insertSQL = $dbOrfeo->Replace('enviotemp',$record,array('codigo','ano','radicado'), $autoquote = true);

 ?>
   <td class=listado2> <?=$insertSQL?></td>
    
   
   </tr>
   <?
 $rs->MoveNext();
}

?>
</table>
</body>
</html>