<?
	/**
	  * CONSULTA RADICADOS IMPRESOS
          * 4 dic 2012
          * Liliana Gómez Velásquez
          *
	  */

        $where_impresion = ' and a.sgd_fech_impres is not null ';
	switch($db->driver)
	{
	case 'mssql':
		$isql = '' ;
		break;
	//
	default:
                 $sqlConcat = $db->conn->Concat("a.radi_nume_salida","'-'","a.sgd_dir_tipo");	
		$isql = 'select 
			a.anex_estado AS "CHU_ESTADO"
		        ,CAST(a.radi_nume_salida as varchar(20)) AS "IMG_RADICADO_SALIDA"
			,c.RADI_PATH AS "HID_RADI_PATH"						
		        ,substr(trim( CAST( a.sgd_dir_tipo AS VARCHAR(5) ) ),2,5) AS COPIA
			,CAST(a.anex_radi_nume as varchar(20)) AS RADICADO_PADRE
			,c.radi_fech_radi AS FECHA_RADICADO
			,dir.sgd_dir_nomremdes||'."'/'".'||dir.sgd_dir_nombre||'."'<br>'".'||dir.sgd_dir_direccion AS DESCRIPCION
			,a.sgd_fech_impres AS FECHA_IMPRESION
			,a.anex_creador AS GENERADO_POR
                        , '. $sqlConcat .  ' AS "CHK_RADI_NUME_SALIDA"
                        , a.sgd_dir_tipo     AS "HID_sgd_dir_tipo"
			,a.anex_nomb_archivo AS "HID_ANEX_NOMB_ARCHIVO" 
			,a.anex_tamano       AS "HID_ANEX_TAMANO"
			,a.ANEX_RADI_FECH    AS "HID_ANEX_RADI_FECH" 
			,' . "'WWW'" . '     AS "HID_WWW" 
			,' . "'9999'" . '    AS "HID_9999"     
			,a.anex_tipo         AS "HID_ANEX_TIPO" 
			,a.anex_radi_nume    AS "HID_ANEX_RADI_NUME" 
			,a.sgd_dir_tipo      AS "HID_SGD_DIR_TIPO"
			,a.sgd_deve_codigo   AS "HID_SGD_DEVE_CODIGO"
		from anexos a,usuario b, radicado c, sgd_dir_drecciones dir
		where a.ANEX_ESTADO>=' .$estado_sal. ' '.
				$dependencia_busq2 .'
				and a.ANEX_ESTADO <= ' . $estado_sal_max . '
				and a.radi_nume_salida=c.radi_nume_radi
				and a.radi_nume_salida=dir.radi_nume_radi
				and a.sgd_dir_tipo    =dir.sgd_dir_tipo
				and a.anex_creador=b.usua_login 
				and a.anex_borrado= ' . "'N'" . '
				and a.sgd_dir_tipo != 7
				and (a.sgd_deve_codigo >= 90 or a.sgd_deve_codigo =0 or a.sgd_deve_codigo is null)
				AND
				((c.SGD_EANU_CODIGO != 2
				AND c.SGD_EANU_CODIGO != 1) 
				or c.SGD_EANU_CODIGO IS NULL)
                                 ' . 
			        $condicion . '	
                                order by a.radi_nume_salida ';

         
	}
?>