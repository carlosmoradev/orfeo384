<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
//if ($deta_causal and $sector) {
	$isql = "SELECT u.USUA_NOMB, u.USUA_LOGIN, u.SGD_ROL_CODIGO, u.USUA_CODI
        FROM USUARIO u
        WHERE u.DEPE_CODI=$id 
        ORDER BY u.usua_NOMB DESC";
	$rs = $db->conn->query($isql);
	if ($rs && !$rs->EOF) {
  $i=0;
  do {
    $usuaNomb =  utf8_encode($rs->fields[0]);
    $usuaLogin =  utf8_encode($rs->fields[1]);
    $rolCodigo =  $rs->fields[2];
    $usCodigo =  $rs->fields[3];
    //$nombre_dcau =  utf8_encode($rs->fields[2]);
    $usuarios[$i] = $usuaNomb.'-'.$usuaLogin.'-'.$rolCodigo.'-'.$usCodigo;
    $i++;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
 echo json_encode($usuarios);
?>