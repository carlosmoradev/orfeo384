<?php
	define('ADODB_ASSOC_CASE', 0);
	$ruta_raiz 	= "../..";
	$pearLib 	= $ruta_raiz . "/pear/";
	include_once($ruta_raiz . "/include/db/ConnectionHandler.php");
	require_once($pearLib . "HTML/Template/IT.php");
	$pathEstilos 	= "../css/";
	$estilo 	= "estilosQrs.css";
	$tituloPagina 	= "Formulario Qrs";
	$archivoExec	= "verificacion.php";
	$estilosRadicacion = $pathEstilos . $estilo;
	$paginaDeInicio = "http://www.superservicios.gov.co/superservicios1/energas/qrs_Aviso2.html";
	$paginaDeInicio = "http://www.superservicios.gov.co/superservicios1/QQRRSS2.htm";
	$paginaDeInicio = "http://www.google.com.co/";
	$ipServidor 	= "172.16.0.147:81";
	$archivoExec	= "http://$ipServidor/~jgonzal/br_3.6.0/radicacionWeb/radicacionQrs/verificacion.php";
	$db = new ConnectionHandler("$ruta_raiz");
	$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
	$tpl = new HTML_Template_IT($ruta_raiz . "/tpl");
	$comenzarRad = (!empty($HTTP_POST_VARS["radiQrs"])) ? $HTTP_POST_VARS["radiQrs"] : null;
	
	if(!empty($comenzarRad)) {
		// Capturando municipios del departamento para construir select 
		// si tiene un departamento asignado
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
		$tpl->loadTemplatefile("inicioRadicacion.tpl");
		$tpl->setVariable("TITULO_PAGINA",$tituloPagina);
		$tpl->setVariable("ARCHIVO_EXEC",$archivoExec);
		$tpl->setVariable("ESTILOS_RADICADO",$estilosRadicacion);
		$tpl->setVariable("ARCHIVO_EXEC",$archivoExec);
		$tpl->setVariable("MUNICIPIO_SELECT",$selectMuni);
		$tpl->setVariable("DEPARTAMENTO_SELECT",$selectDepto);
		$tpl->setVariable("ARREGLOJS",$arregloJs);
		$tpl->show();
		exit();
	} else {
		header("Location : $paginaDeInicio");
	}
?>
