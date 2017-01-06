<?php
	$ruta_raiz = "../..";
	define('FPDF_FONTPATH',"$ruta_raiz/fpdf/font/");
	require($ruta_raiz . "/fpdf/fpdf.php");
	// Para Letra Arial 10 y 1500 en promedio por reglon es de 110
	
	class RadicadoPdf extends FPDF {
	//Page header
		var $camposRadicado;
		function Header() {
		}
		
		function asignarCampos($camposRadicado) {
		}
		
		//Page footer
		function Footer() {
			//Position at 1.5 cm from bottom
			$this->SetY(-15);
			//Arial italic 8
			$this->SetFont('Arial','I',8);
			//Page number
    			$this->Cell(0,10,'Pagina '.$this->PageNo().'/{nb}',0,0,'C');
		}
	}
	
	//Instanciation of inherited class
	$pdf = new RadicadoPdf();
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$pdf->SetFont('Arial','',10);
	
	$arregloPos["ciudad_largo"]["x"] = 0;
	$arregloPos["ciudad_largo"]["y"] = 45;
	$arregloPos["contestar"]["x"] = 150;
	$arregloPos["contestar"]["y"] = 0;
	$arregloPos["nombre_corto_entidad"]["x"] = 150;
	$arregloPos["nombre_corto_entidad"]["y"] = 10;
	$arregloPos["fecha_texto"]["x"] = 106;
	$arregloPos["fecha_texto"]["y"] = 0;
	$arregloPos["senores_texto"]["x"] = 0;
	$arregloPos["senores_texto"]["y"] = 50;
	$arregloPos["nombre_entidad"]["x"] = 0;
	$arregloPos["nombre_entidad"]["y"] = -30;
	$arregloPos["direccion_super"]["x"] = 0;
	$arregloPos["direccion_super"]["y"] = 38;
	$arregloPos["telefono_rem"]["x"] = 0;
	$arregloPos["telefono_rem"]["y"] = -15;
	$arregloPos["ciudad_texto"]["x"] = 0;
	$arregloPos["ciudad_texto"]["y"] = 7;
	$arregloPos["asunto_texto"]["x"] = 0;
	$arregloPos["asunto_texto"]["y"] = 20;
	$arregloPos["cordial_texto"]["x"] = 0;
	$arregloPos["cordial_texto"]["y"] = 10;
	$arregloPos["intro_texto"]["x"] = 0;
	$arregloPos["intro_texto"]["y"] = 10;
	$arregloPos["descripcion"]["x"] = 0;
	$arregloPos["descripcion"]["y"] = 4;
	$arregloPos["corfin_texto"]["x"] = 0;
	$arregloPos["corfin_texto"]["y"] = 30;
	$arregloPos["nombre_rem"]["x"] = 0;
	$arregloPos["nombre_rem"]["y"] = 10;
	$arregloPos["doc_texto"]["x"] = 0;
	$arregloPos["doc_texto"]["y"] = 10;
	$arregloPos["documento_id"]["x"] = 0;
	$arregloPos["documento_id"]["y"] = 10;
	$arregloPos["tel_texto"]["x"] = 0;
	$arregloPos["tel_texto"]["y"] = 10;
	$arregloPos["correo_texto"]["x"] = 0;
	$arregloPos["correo_texto"]["y"] = 0;
	$arregloPos["informacion"]["x"] = 0;
	$arregloPos["informacion"]["y"] = 15;
	$arregloPos["informacion2"]["x"] = 0;
	$arregloPos["informacion2"]["y"] = -4;
	$imagen["imagen_escudo"] = "escudoColombia.png";
	$imagen["codigo_barras"] = $file . ".png";	// variable que viene de radicarSugerencia en la parte de barcode
	$pdf->Image($imagen["codigo_barras"],106,70,87,23);
	$pdf->Image($imagen["imagen_escudo"],166,40,22,29);
	
	$camposRadicado["ciudad_largo"] = $ciudadLargo . ", " . $fechaRadicacionLarga;
	//$camposRadicado["fecha"] = date("d/m/Y");
	$espacio = "                                                                                      ";
	$camposRadicado["contestar"] = "$espacio Al contestar cite el numero de radicado de este documento";
	//$camposRadicado["contestar"]["alineacion"] = "L";
	$camposRadicado["nombre_corto_entidad"] = "                                                                     $nombreCorto Numero de Radicado $noRad ";
	//$camposRadicado["nombre_corto_entidad"]["alineacion"] = "R";
	/*$camposRadicado["numerorad_texto"] = "Numero de Radicado";
	$camposRadicado["numero_radicado"] = "20069050005112";*/
	$camposRadicado["fecha_texto"] = "                        Fecha: " . date("d/m/Y");
	//$camposRadicado["fecha_texto"]["alineacion"] = "R";
	$camposRadicado["senores_texto"] = "SENORES:";
	$camposRadicado["nombre_entidad"] = $nombre_us3;
	$camposRadicado["direccion_super"] = $direccionSuper;
	$camposRadicado["telefono_rem"] = $telefono;
	$camposRadicado["ciudad_texto"] = "CIUDAD";
	$camposRadicado["asunto_texto"] = "Asunto: " . $arregloTipo[$tipoQRS];
	//$camposRadicado["asunto"] = "Tipo Asunto";
	$camposRadicado["cordial_texto"] = "Cordial saludo,";
	$camposRadicado["intro_texto"] = "La presente es con el fin de informarles:";
	$camposRadicado["descripcion"] = $asunto;
	$camposRadicado["corfin_texto"] = "Cordialmente";
	$camposRadicado["nombre_rem"] = strtoupper($nombre_us1) . " " . strtoupper($apellidos);
	$camposRadicado["doc_texto"] = "Documento de identidad: $cc_documento_us1";
	$camposRadicado["direccion_texto"] = "Direccion: $direccion_us1";
	$camposRadicado["tel_texto"] = "Telefono: $telefono_us1";
	$camposRadicado["correo_texto"] = "Correo electronico: $mail_us1";
	$camposRadicado["informacion"] = "NOTA: Para realizar un seguimiento a su radicado por favor entre a nuestra pagina de internet:";
	$camposRadicado["informacion2"] = "       https://www.superservicios.gov.co/orfeoint/consultaWeb/index.php y digite el radicado $noRad";
	$nombreCampos = array_keys($camposRadicado);
	
	foreach($nombreCampos as $campo) {
		switch ($campo) {
			case "contestar":
				$pdf->SetFont('Arial','I',8);
				$pdf->Cell($arregloPos[$campo]["x"],
						$arregloPos[$campo]["y"],
						$camposRadicado[$campo],0,1,
						$camposRadicado[$campo]["alineacion"]);
				break;
			case "nombre_corto_entidad":
				$pdf->SetFont('Arial','',10);
				$pdf->Cell($arregloPos[$campo]["x"],
						$arregloPos[$campo]["y"],
						$camposRadicado[$campo],0,1/*,
						$camposRadicado[$campo]["alineacion"]*/);
				break;
			case "descripcion":
				$pdf->SetFont('Arial','',10);
				foreach($camposRadicado[$campo] as $renglon) {
					$pdf->Cell($arregloPos[$campo]["x"],
						$arregloPos[$campo]["y"],
						$renglon,0,1);
				}
				break;
			case "fecha_texto":
				$pdf->SetFont('Arial','',10);
				$pdf->Cell($arregloPos[$campo]["x"],
						$arregloPos[$campo]["y"],
						$camposRadicado[$campo],0,1,
						'R');
				break;
			case "informacion":
			case "informacion2":
				$pdf->SetFont('Arial','',8);
				$pdf->Cell($arregloPos[$campo]["x"],$arregloPos[$campo]["y"],$camposRadicado[$campo],0,1);
				break;
			default:
				$pdf->SetFont('Arial','',10);
				$pdf->Cell($arregloPos[$campo]["x"],$arregloPos[$campo]["y"],$camposRadicado[$campo],0,1);
		}
	}
	
	$generoPdf = false;
	$radicadoPdftmp = "tmp/" . $noRad . ".pdf";
	$pdf->Output($radicadoPdftmp);
	$generoPdf = true;
	chmod($radicadoPdftmp,0777);
	//$pdf->Output();
?>
