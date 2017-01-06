<?php
$ruta_raiz = "../../..";
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler($ruta_raiz);
$id = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : 0;
//if ($deta_causal and $sector) {
	$isql = "SELECT u.USUA_NOMB, u.USUA_LOGIN, u.SGD_ROL_CODIGO FROM USUARIO u
        WHERE u.DEPE_CODI=$id AND u.SGD_ROL_CODIGO>=1
        ORDER BY u.SGD_ROL_CODIGO DESC";
	$rs = $db->conn->query($isql);
	if ($rs && !$rs->EOF) {
	?>
	<?
  $i=0;
  do {
    $usuaNomb =  utf8_encode($rs->fields[0]);
    $usuaLogin =  utf8_encode($rs->fields[1]);
    $rolCodigo =  $rs->fields[2];
    //$nombre_dcau =  utf8_encode($rs->fields[2]);
    $usuarios[$i] = $usuaNomb.'-'.$usuaLogin.'-'.$rolCodigo;
    $i++;
    $rs->MoveNext();
  }while(!$rs->EOF);
  //}
  }
 echo json_encode($usuarios);
?>