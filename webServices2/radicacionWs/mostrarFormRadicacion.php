<?php
	if (!empty($departamento["depto_codi"])) {
		$municipio = (empty($municipio["codigo"])) ?
				null : $municipio["codigo"];
		$sqlMuni = "SELECT muni_nomb, muni_codi 
				FROM municipio 
				WHERE dpto_codi = '".
				$departamento["depto_codi"] ."'";
		$res = $db->conn->Execute($sqlMuni);
		$selectMuni = $res->GetMenu2('municipio[codigo]',	
					$municipio,
					false,
					false,
					0,
					"id=\"municipio\" class=\"select\"");
	} else {
		$selectMuni = "<select id=\"municipio\" name=\"municipio[codigo]\"  class=\"select\">\n
		<option value=\"1\" > Seleccione Municipio </option>\n
		</select>";
	}
	
	$sql = "SELECT dpto_nomb, dpto_codi FROM departamento ORDER BY dpto_nomb";
	$res = $db->conn->Execute($sql);
	
	// Si no tiene asignado ningun departamento entonces muestra 
	// en el select la palabra todos
	$departamento = (empty($departamento["depto_codi"])) ? 
				1 : $departamento["depto_codi"];
	
	$selectDepto = $res->GetMenu2('departamento[depto_codi]',
					$departamento, 
					false, 
					false, 
					0, 
					"onChange=\"javascript:cambiar_seccion(this);\" class=\"select\"");
	
	$sql = "SELECT dpto_codi, 
			muni_codi, 
			muni_nomb 
		FROM municipio ORDER BY dpto_codi";
	
	$res = $db->conn->query($sql);
	
	while (!$res->EOF) {
		$municipios[$cont]["codigoDepto"] = $res->fields["DPTO_CODI"];
		$municipios[$cont]["codigoMun"] = $res->fields["MUNI_CODI"];
		$municipios[$cont]["nombre"] = $res->fields["MUNI_NOMB"];
		$cont++;
		$res->MoveNext();
	}// Fin de la optencion de los municipios
			
	$mostrarComa = "";
	$cont = 0;
	$coma = ",";
		
	foreach ($municipios as $municipio) {
		if ($cont != 0) $mostrarComa = $coma . "\n";
		if ($cont > 0) $mostrarComa .= "\t\t\t\t\t";
		$arregloJs .= $mostrarComa . "new seccionE (\"" . $municipio["codigoMun"] . 
				"\",\"" . $municipio["nombre"] . 
				"\",\"" . $municipio["codigoDepto"] . "\")";
		$cont++;
	}
	$tpl->loadTemplatefile("verificacionRadicacion.tpl");
	$tpl->setVariable("TITULO_PAGINA",$tituloPagina);
	$tpl->setVariable("ESTILOS_RADICADO",$estilosRadicacion);
	$tpl->setVariable("VNOMBRES",$nombre_us1);
	$tpl->setVariable("VPAPELLIDO",$prim_apel_us1);
	$tpl->setVariable("VSAPELLIDO",$seg_apel_us1);
	$tpl->setVariable($arregloTipo[$tipoQRS],'checked="yes"');
	$tpl->setVariable("VCEDULA",$cc_documento_us1);
	$tpl->setVariable("VTELEFONO",$telefono_us1);
	$tpl->setVariable("VDIRECCION",$direccion_us1);
	$tpl->setVariable("VEMAIL",$mail_us1);
	$tpl->setVariable("DESCRIPCION",$descripcion);
	$tpl->setVariable("ARCHIVO_EXEC",$archivoExec);
	$tpl->setVariable("MUNICIPIO_SELECT",$selectMuni);
	$tpl->setVariable("DEPARTAMENTO_SELECT",$selectDepto);
	$tpl->setVariable("VRADICACION",$formRadInicio);
	$tpl->setVariable("CAPTCHA_IMG",$captchaImg);
	$tpl->setVariable("ARREGLOJS",$arregloJs);
	$tpl->show();
?>
