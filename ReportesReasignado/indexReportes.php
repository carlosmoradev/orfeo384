<?php
if(empty($ruta_raiz))
	$ruta_raiz="../";

require_once($ruta_raiz."ReportesReasignado/PlanillaControler.php");
if(!isset($_GET['exportar'])){
session_start();
if(empty($_SESSION['dependencia'])) 
	require_once $ruta_raiz."rec_session.php";

	header("Content-type: text/html");	
}
$planilla = new PlanillaControler($_POST,$_GET);
$planilla->setDependencia($_SESSION['dependencia']);
$planilla->route();



?>
