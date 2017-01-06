<?
$ruta_raiz = ".";
require_once("$ruta_raiz/include/db/ConnectionHandler.php");

if (!$db)
		$db = new ConnectionHandler($ruta_raiz);
$sqlTest="SELECT r.RADI_NUME_RADI, r.RADI_FECH_RADI, r.RA_ASUN, td.sgd_tpr_descrip, round(((r.RADI_FECH_RADI+(td.SGD_TPR_TERMINO * 7/5))-sysdate)) as diasr, r.RADI_NUME_HOJA, r.RADI_PATH, dir.SGD_DIR_DIRECCION, dir.SGD_DIR_MAIL, dir.SGD_DIR_NOMREMDES, dir.SGD_DIR_TELEFONO, dir.SGD_DIR_DIRECCION, dir.SGD_DIR_DOC, r.RADI_USU_ANTE, r.RADI_PAIS, dir.SGD_DIR_NOMBRE, dir.SGD_TRD_CODIGO, r.RADI_DEPE_ACTU, r.RADI_USUA_ACTU, r.CODI_NIVEL FROM sgd_dir_drecciones dir, radicado r, sgd_tpr_tpdcumento td WHERE dir.sgd_dir_tipo = 1 AND dir.RADI_NUME_RADI=r.RADI_NUME_RADI AND r.TDOC_CODI=td.SGD_TPR_CODIGO AND (r.radi_fech_radi>=TO_DATE('2005-01-01, 12:00:00 AM', 'RRRR-MM-DD, HH:MI:SS AM') and r.radi_fech_radi<=TO_DATE('2005-12-02, 11:59:59 PM', 'RRRR-MM-DD, HH:MI:SS AM') and ( (UPPER(dir.sgd_dir_nomremdes) LIKE '%CODENSA%' ) or (UPPER(dir.sgd_dir_nombre) LIKE '%CODENSA%' ) or (UPPER(r.ra_asun||r.radi_cuentai||dir.sgd_dir_telefono||dir.sgd_dir_direccion) LIKE '%CODENSA%' ))) order by r.radi_fech_radi ";
$rs=$db->query($sqlTest);
if ($rs){
	echo ("OK");
}else {
	echo ("No respuesta");
}
	


?>