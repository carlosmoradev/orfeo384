<?
/**
  * Modificado Postgres Por Correlibre.org 2011-12
  * @autor Jairo Losada Correlibre.org
  *        Modificado correlibre
  * @licencia gpl v3
  *
  */

switch($db->driver)
{	case 'mssql':
	{	
		
		$radi_nume_radi = "cast(a.RADI_NUME_RADI as varchar(20))";
		$tmp_cad1 = "convert(varchar,".$db->conn->concat("'0'","'-'",$radi_nume_radi).")";
		$tmp_cad2 = "convert(varchar,".$db->conn->concat('CAST(c.info_codi as varchar(20))',"'-'",$radi_nume_radi).")";
		$redondeo = $db->conn->round($sqlOffset."-".$systemDate);
		$concatenar = "CAST(DEPE_CODI AS VARCHAR(10))";
$isql = 'select '.$radi_nume_radi.' "IMG_Numero Radicado",
			a.RADI_PATH  "HID_RADI_PATH",
			'.$sqlFecha.'  "DAT_Fecha Radicado",
			'.$radi_nume_radi.' "HID_RADI_NUME_RADI",
			c.info_desc "Asunto",
			b.sgd_tpr_descrip as "Tipo Documento",
			'.$redondeo.' as "Dias Restantes",
			'.chr(39).chr(39).'  AS "Informador",
			'.$tmp_cad1.' "CHK_checkValue",
			c.INFO_LEIDO as "HID_RADI_LEIDO"
 		from radicado a,
 			sgd_tpr_tpdcumento b,
 			informados c,
 			usuario d
		where a.radi_nume_radi=c.radi_nume_radi and a.tdoc_codi=b.sgd_tpr_codigo
			and a.radi_usua_actu=d.usua_codi and a.radi_depe_actu=d.depe_codi
			and c.depe_codi='.$dependencia.' and c.usua_codi='.$codusuario.' '.$where_filtro .'
			and c.info_codi is null
		UNION
		select '.$radi_nume_radi.' "IMG_Numero Radicado",
			a.RADI_PATH  "HID_RADI_PATH",
			'.$sqlFecha.'  "DAT_Fecha Radicado",
			'.$radi_nume_radi.' "HID_RADI_NUME_RADI",
			c.info_desc "Asunto",
			b.sgd_tpr_descrip as "Tipo Documento",
			'.$redondeo.' as "Dias Restantes",
			d2.usua_nomb  AS "Informador",
			'.$tmp_cad2.' "CHK_checkValue",
			c.INFO_LEIDO as "HID_RADI_LEIDO"
 		from radicado a,
 			sgd_tpr_tpdcumento b,
 			informados c,
 			usuario d, usuario d2
		where a.radi_nume_radi=c.radi_nume_radi and a.tdoc_codi=b.sgd_tpr_codigo
			and a.radi_usua_actu=d.usua_codi and a.radi_depe_actu=d.depe_codi
			and c.depe_codi='.$dependencia.' and c.usua_codi='.$codusuario.' '.$where_filtro .'
			and c.info_codi is not null and d2.usua_doc = c.info_codi
		order by '.$order.' '.$orderTipo;		
	}break;
	case 'oracle':
	case 'oci8':
	// Modificado SGD 21-Septiembre-2007
	// Modificado IDRD Noviembre 13
	case 'postgres':
	{ 
		$radi_nume_radi = "cast(a.RADI_NUME_RADI as varchar(20))";
		$tmp_cad1 = "cast( ".$db->conn->concat("'0'","'-'",$radi_nume_radi)." as varchar(20) )";
		$tmp_cad2 = "cast( ".$db->conn->concat("c.info_codi","'-'",$radi_nume_radi)." as varchar(50) )";
		$redondeo = round($sqlOffset."-".$systemDate);
		//$tmp_cad2 = "to_char(".$db->conn->concat('c.info_codi',"'-'",$radi_nume_radi).")";
		//$redondeo = $db->conn->round($sqlOffset."-".$systemDate);
		$concatenar = "CAST(DEPE_CODI AS VARCHAR(10))";
$isql = '
		select '.$radi_nume_radi.' 	AS "IMG_Numero Radicado",
			a.RADI_PATH 		AS "HID_RADI_PATH",
			'.$sqlFecha.'		AS "DAT_Fecha Radicado",
			'.$radi_nume_radi.' 	AS "HID_RADI_NUME_RADI",
			c.info_desc 		AS "Asunto",
			b.sgd_tpr_descrip 	AS "Tipo Documento",
			d2.usua_nomb  		AS "Informador",
			'.$tmp_cad2.' 		AS "CHK_checkValue",
			c.INFO_LEIDO 		AS "HID_RADI_LEIDO"
 		from radicado a,
 			sgd_tpr_tpdcumento b,
 			informados c,
 			usuario d, usuario d2
		where a.radi_nume_radi=c.radi_nume_radi and a.tdoc_codi=b.sgd_tpr_codigo
			and a.radi_usua_actu=d.usua_codi and a.radi_depe_actu=d.depe_codi
			and c.depe_codi='.$dependencia.' and c.usua_codi='.$codusuario.' '.$where_filtro .'
			and d2.usua_doc (+) = c.info_codi 
			and c.info_conjunto>=1
		order by '.$order.' '.$orderTipo;		
	}break;
}


$isql= ' SELECT '.$radi_nume_radi.' AS "IMG_Numero Radicado",
			a.RADI_PATH AS "HID_RADI_PATH",
			'.$sqlFecha.' AS "DAT_Fecha Radicado",
			'.$radi_nume_radi.' AS "HID_RADI_NUME_RADI",
			c.info_desc AS "Nota",
			a.ra_asun       AS "Asunto",
			b.sgd_tpr_descrip as "Tipo Documento",
			'.$redondeo.' as "Dias Restantes",
			d2.usua_nomb  AS "Informador",
			'.$tmp_cad2.' AS "CHK_checkValue",
			c.INFO_LEIDO as "HID_RADI_LEIDO"
 		from 
 			sgd_tpr_tpdcumento b
 			, informados c
 			LEFT JOIN  usuario d2  ON
 			d2.usua_doc = CAST( c.info_codi AS VARCHAR(20) )
 			, radicado a LEFT JOIN usuario d ON
 			(a.radi_depe_actu=d.depe_codi and a.radi_usua_actu=d.usua_codi)
		where a.radi_nume_radi=c.radi_nume_radi 
			and a.tdoc_codi=b.sgd_tpr_codigo
			and c.depe_codi='.$dependencia.' and c.usua_codi='.$codusuario.' '.$where_filtro .'
			and c.info_codi is not null 
			and c.info_conjunto >=1
			order by '.$order.' '.$orderTipo;		
		
?>
