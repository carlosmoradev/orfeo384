<?php

/**
 * Planillas clase encarga de generar el archivo del para el sipos de 4/72 u otras planillas.
 * @author Hardy Deimont Niño  Velasquez
 * @name sipos
 * @version	1.0
 *
 */
class panillasClass {
	private $rutaArchivo; // ruta a donde guardar la planilla
	private $numPlanilla; // nuemro de planilla
	private $ruta_raiz; // ruta raiz de la aplicacion.
	private $entidad_largo_Planilla; // nombre entidad largo
	private $data; // array $data[$i]['PAIS'] cada fila se identifica con el
	               // nombre de encabezado.
	private $encabezado; // string con el nombre del encabzado separado por punto
	                     // y comas y en mayusculas PAIS,DIRECCION,RADICADO

	/**
	 *
	 * @return the $ruta_raiz
	 */
	public function getRuta_raiz() {
		return $this->ruta_raiz;
	}

	/**
	 *
	 * @return the $entidad_largo_Planilla
	 */
	public function getEntidad_largo_Planilla() {
		return $this->entidad_largo_Planilla;
	}

	/**
	 *
	 * @return the $data
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 *
	 * @return the $encabezado
	 */
	public function getEncabezado() {
		return $this->encabezado;
	}

	/**
	 *
	 * @param field_type $ruta_raiz
	 */
	public function setRuta_raiz($ruta_raiz) {
		$this->ruta_raiz = $ruta_raiz;
	}

	/**
	 *
	 * @param field_type $entidad_largo_Planilla
	 */
	public function setEntidad_largo_Planilla($entidad_largo_Planilla) {
		$this->entidad_largo_Planilla = $entidad_largo_Planilla;
	}

	/**
	 *
	 * @param field_type $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 *
	 * @param field_type $encabezado
	 */
	public function setEncabezado($encabezado) {
		$this->encabezado = $encabezado;
	}

	/**
	 *
	 * @return the $rutaArchivo
	 */
	public function getRutaArchivo() {
		return $this->rutaArchivo;
	}

	/**
	 *
	 * @return the $numPlanilla
	 */
	public function getNumPlanilla() {
		return $this->numPlanilla;
	}

	/**
	 *
	 * @param field_type $rutaArchivo
	 */
	public function setRutaArchivo($rutaArchivo) {
		$this->rutaArchivo = $rutaArchivo;
	}

	/**
	 *
	 * @param field_type $numPlanilla
	 */
	public function setNumPlanilla($numPlanilla) {
		$this->numPlanilla = $numPlanilla;
	}
	function __construct() {
	}

	/**
	 * Function generar sipos genera un cvs y txt
	 *
	 * @return string
	 */
	function generarSipos() {
		$numdata = count ( $this->data );
		$data1 = $this->data;
		$contenidoCvs1 .= $this->encabezado . "\n";
		//$contenidotxt1 .= $this->limpiarDato ( str_replace ( ';', '\t', $this->encabezado ) ) . "\r\n";
                $titulo = explode ( ';', $this->encabezado );
                // Se formatea el encabezado para el caso del archivo .txt
	        for($tii = 0; $tii < count ( $titulo ); $tii ++) {
				if ($tii == 0) {
					$contenidotxt1 .= trim ( $titulo [$tii] );
				} else {
					$contenidotxt1 .= "\t" . $this->limpiarDato ($titulo [$tii] );
				}
			}
                $contenidotxt1 .= "\r\n";

		for($i = 0; $i < $numdata; $i ++) {
			$contenidoCvstmp = '';
			$contenidotxttmp = '';
			for($ti = 0; $ti < count ( $titulo ); $ti ++) {
				if ($ti == 0) {
					$datos =  trim ($data1 [$i] [$titulo [$ti]]);
                                        $datos =  $this->eliminarCaracteres ($datos);
					$contenidoCvstmp .= trim ( $datos );
					//$contenidoCvstmp .= trim ( $data1 [$i] [$titulo [$ti]] );
					//$contenidotxttmp .= $this->limpiarDato ( $data1 [$i] [$titulo [$ti]] );
                                        $contenidotxttmp .= $this->limpiarDato ($datos );

				} else {
					$datos = trim ($data1 [$i] [$titulo [$ti]]);
                                        $datos =  $this->eliminarCaracteres ($datos);
					$contenidoCvstmp .= ";" . trim ( $datos);		
					//$contenidoCvstmp .= ";" . trim ( $data1 [$i] [$titulo [$ti]] );
					//$contenidotxttmp .= "\t" . $this->limpiarDato ( $data1 [$i] [$titulo [$ti]] );
                                        $contenidotxttmp .= "\t" . $this->limpiarDato ($datos);
				}
			}
			$contenidoCvs .= str_replace ( '\t', ' ', str_replace ( '\n', '', $contenidoCvstmp ) ) . "\n";
			$contenidotxt .= $contenidotxttmp . "\r\n";
		}
		//echo $contenidoCvs1 . $contenidoCvs . "<br>";
		//echo $contenidotxt1 . $contenidotxt . "<br>";
		// Archivo csv
		$nombre_archivo = $this->rutaArchivo . "/" . $this->numPlanilla . ".csv";
		fopen ( $nombre_archivo, 'wra+' );

		// Asegurarse primero de que el archivo existe y puede escribirse sobre
		// el.
		if (is_writable ( $nombre_archivo )) {

			// En nuestro ejemplo estamos abriendo $nombre_archivo en modo de
			// adicion.
			// El apuntador de archivo se encuentra al final del archivo, asi
			// que
			// alli es donde ira $contenido cuando llamemos fwrite().
			if (! $gestor = fopen ( $nombre_archivo, 'a' )) {
				echo "No se puede abrir el archivo ($nombre_archivo)";
				exit ();
			}

			// Escribir $contenido a nuestro arcivo abierto.
			if (fwrite ( $gestor, $contenidoCvs1 . $contenidoCvs ) === FALSE) {
				echo "No se puede escribir al archivo ($nombre_archivo)";
				exit ();
			}

			// echo "&Eacute;xito, se escribi&oacute; ($contenidoCvs) d al
			// archivo ($nombre_archivo)";

			fclose ( $gestor );
		} else {
			echo "No se puede escribir sobre el archivo $nombre_archivo";
		}
		// Archivo TXT
		$nombre_archivo2 = $this->rutaArchivo . "/" . $this->numPlanilla . ".txt";
		fopen ( $nombre_archivo2, 'wra+' );

		// Asegurarse primero de que el archivo existe y puede escribirse sobre
		// el.
		if (is_writable ( $nombre_archivo2 )) {

			// En nuestro ejemplo estamos abriendo $nombre_archivo en modo de
			// adicion.
			// El apuntador de archivo se encuentra al final del archivo, asi
			// que
			// alli es donde ira $contenido cuando llamemos fwrite().
			if (! $gestor = fopen ( $nombre_archivo2, 'a' )) {
				echo "No se puede abrir el archivo ($nombre_archivo2)";
				exit ();
			}

			// Escribir $contenido a nuestro arcivo abierto.
			$tmp = chr ( 255 ) . chr ( 254 ) . mb_convert_encoding ( $contenidotxt1 . $contenidotxt, 'UTF-16LE', 'UTF-8' );
			if (fwrite ( $gestor, $tmp ) === FALSE) {
				echo "No se puede escribir al archivo ($nombre_archivo2)";
				exit ();
			}

			// echo "&Eacute;xito, se escribi&oacute; ($contenidoCvs) d al
			// archivo ($nombre_archivo)";

			fclose ( $gestor );
		} else {
			echo "No se puede escribir sobre el archivo $nombre_archivo";
		}
		$resultado ['csv'] = $nombre_archivo;
		$resultado ['txt'] = $nombre_archivo2;
		return $resultado;
		;
	}

	/**
	 * Limpia datos
	 * @param unknown_type $dato
	 * @return mixed
	 */
	function limpiarDato($dato) {
              
		return str_replace ( '\t', ' ', str_replace ( '\n', '', trim ( $dato ) ) );
	}
        function eliminarCaracteres($datos) {
            $datos = str_replace(
		      array("\\", "¨", "º","~",
			    "#", "@", "|", "!", "\"",
			    "·", "$", "%", "&", "/",
			    "(", ")", "?", "'", "¡",
			    "¿", "[", "^", "`", "]",
			    "+", "}", "{", "¨", "´",
			    ">", "<", ";", ",", ":"),
			    '',$datos);
            return $datos;
	}

	}

