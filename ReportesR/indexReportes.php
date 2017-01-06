<?php
session_start();
if(empty($ruta_raiz)) 	$ruta_raiz="..";
foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;

require_once($ruta_raiz."/ReportesR/PlanillaControler.php");
if(!isset($_GET['exportar'])){



	header("Content-type: text/html");	
}
$planilla = new PlanillaControler($_POST,$_GET);
$planilla->setDependencia($_SESSION['dependencia']);
$planilla->route();



?>
