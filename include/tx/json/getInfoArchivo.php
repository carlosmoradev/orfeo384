<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
//if ($deta_causal and $sector) {
	$isql = "select SGD_EIT_NOMBRE,SGD_EIT_CODIGO, SGD_EIT_COD_PADRE,CODI_MUNI, CODI_DPTO from SGD_EIT_ITEMS
            where SGD_EIT_COD_PADRE = '".id."'
						order by SGD_EIT_NOMBRE";
	$rs = $db->conn->query($isql);
	if ($rs && !$rs->EOF) {
  $i=0;
  do {
    $eitNombre =  utf8_encode($rs->fields[0]);
    $eitCodigo =  utf8_encode($rs->fields[1]);
    $eitCodigoPadre =  $rs->fields[2];
    $eitMuni =  $rs->fields[3];
		$eitDpto =  $rs->fields[4];
    //$nombre_dcau =  utf8_encode($rs->fields[2]);
    $usuarios[$i] = $usuaNomb.'-'.$usuaLogin.'-'.$rolCodigo.'-'.$usCodigo;
    $i++;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
 echo json_encode($usuarios);
?>