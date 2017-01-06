<?php
	function formatearTextArea($textArea, $maxChars, $promedio = 5) {
		$promedio = (!empty($promedio)) ? $promedio : 5;
		$textoFiltrado = "";
		$palabrasMax = (int)$caracteresMax / $promedio;
		$textAreaArreglo = explode(" ",$textArea);
		foreach($textAreaArreglo as $palabra) {
			$textoFiltrado .= trim($palabra) . " ";
		}
		return $textoFiltrado;
	}
	
	function mesNo2Caracter($mes = 1) {
		$arregloMes[1] = "Enero";
		$arregloMes[2] = "Febrero";
		$arregloMes[3] = "Marzo";
		$arregloMes[4] = "Abril";
		$arregloMes[5] = "Mayo";
		$arregloMes[6] = "Junio";
		$arregloMes[7] = "Julio";
		$arregloMes[8] = "Agosto";
		$arregloMes[9] = "Septiembre";
		$arregloMes[10] = "Octubre";
		$arregloMes[11] = "Noviembre";
		$arregloMes[12] = "Diciembre";
		if ($mes > 0 && $mes < 13) {
			return $arregloMes[$mes];
		} else {
			return false;
		}
	}
	
	function text2pdf($texto, $maxCaracteres = 100) {
		$arregloFiltrado = array();
		$arregloTexto = array();
		$arregloTexto = explode(" ",$texto);
		$palabralon = 0;
		$palabralon2 = 0;
		$parte1 = "";
		foreach($arregloTexto as $palabra) {
			$palabralon = strlen($palabra);
			$palabralon2 = strlen($parte1);
			if($palabralon > $maxCaracteres) {
				$arregloFiltrado[] = substr($palabra,0,$maxCaracteres-1) . "-";
				$parte1 .= substr($palabra,$maxCaracteres,$palabralon) . " ";
			}
			if($palabralon2 < 100) {
				$parte1 .= $palabra . " ";
			} else {
				$arregloFiltrado[] = $parte1 . " ";
				$parte1 = "";
			}
		}
		//var_dump($arregloFiltrado);
		if (empty($arregloFiltrado[0])) {
			$arregloFiltrado[] = $parte1;
		}
		return $arregloFiltrado;
	}	
?>
