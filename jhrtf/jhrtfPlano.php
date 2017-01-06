<?php
/*  CLASS jhrtf
 *  @autor JAIRO LOSADA - SIXTO - LILIANA GOMEZ
 *  @fecha 2003/10/16  2012/08/01
 *  @version 0.1
 *  Permite hacer combinaci�n de correspondencia desde php con filas rtf-
 *  @VERSION 0.2
 *  @fecha 2004/01/22
 *  Se a�ade combinaci�n masiva
 *  @fecha 2004/08/30
 *  Se a�aden las funci�nes:
 *  setTipoDocto(),verListado(),setDefinitivo(),mostrarError(),getNumColEnc(),validarEsp(),validarLugar(),validarTipo()
 *  validarRegistrosObligsCsv(),hayError(),cargarOblPlant(),cargarObligCsv(),cargarCampos(),validarArchs()
 *
 */
require_once("$ruta_raiz/include/pdf/class.ezpdf.inc");
require_once("$ruta_raiz/class_control/Dependencia.php");
require_once("$ruta_raiz/include/tx/Historico.php");
require_once("$ruta_raiz/class_control/Radicado.php");
require_once("$ruta_raiz/include/tx/Expediente.php");
require_once("$ruta_raiz/include/tx/Radicacion.php");


class jhrtf {

	var $archivo_insumo; // Ubicacion fisica del archivo que indica como habra de realizarce la combinacion
	var $alltext;        // ubicacion fisica del archivo a convertir
	var $encabezado;
	var $datos;
	var $ruta_raiz;
	var $definitivo;
	var $codigo_envio;
	//Contiene los campos obligatorios del archivo CSV
	var $camObligCsv;

	
	//Contiene los campos obligatorios del archivo CSV
        var $camObligPlano;
	//Contiene los posibles errores hallados en el encabezado
	var $errorEncab;
	var $errorComplCsv;
	//Contiene los posibles errores del campo tipo de registro del CSV
	var $arcPDF;
	//Contiene el path del archivo plantilla
	var $arcPlantilla;
	//Contiene el path del archivo CSV
	var $arcCSV;
	//Contiene el path del archivo Final
	var $arcFinal;
	//Contiene el path del archivo Temporal

	var $arcTmp;
	var $conexion;
	var $pdf;
	var $btt;       // Guarda la el objeto CLASS_CONTROL
	var $rad;       // Guarda la el objeto radicacion 
	var $handle; 	 // Almacena la conexion que permite efectuar algunas labores de masiva
	var $resulComb; // Almacena el resultado obtenido en la combinacion masiva
	var $objExp;

	var $radarray;



	/**
	 * Constructor que carga en la clase los parametros relevantes del proceso de combinaci�n de documentos
	 * @param	$archivo_insumo	string	es el path hacia el archivo que contiene los ratos de la combinaci�n
	 * @param	$ruta_raiz	string	es el path hacia la raiz del directorio de ORFEO
	 * @param	$arcPDF	string	es el path hacia el archivo PDF que habr� de mostrar el resultado de la combinaci�n
	 * @param	$db	ConnectionHandler	Manejador de la conexi�n con la base de datos
	 */
	function jhrtf($archivo_insumo,$ruta_raiz,$arcPDF,&$db){
		$this->arcCSV      = $archivo_insumo;
		$this->ruta_raiz   = $ruta_raiz;
		$this->arcPDF      = $arcPDF;
		$this->conexion    = $db;
		$this->setConexion($db);
	}


	/**
	 * Funcion que carga en la clase el manejador de conexion con la base de datos, en caso de ser necesario
	 * @param	$db	ConnectionHandler	Manejador de la conexi�n con la base de datos
	 */
	function setConexion($db){	
		$this->conexion = $db;	
	}


	/*
	 * Funcion encargada de gestionar en la base de datos la transaccion 
	 * que implica actualizar los datos reportados por la empresa de Correo en el archivo CSV
	 * @param	$dependencia	string	es la dependencia del usuario que realiza la combinacion
	 * @param	$codusuario	string	es el codigo del usuario que realiza la combinacion
	 * @param	$usua_doc	string	es numero del documento del usuario que realiza la combinacion
	 * @param	$usua_nomb	string	es el nombre del usuario que realiza la combinacion
	 * @param	$depe_codi_territorial	string	es el nombre de la territorial a la que 
	 * pertenece el usuario usuario que realiza la combinacion
	 */

	
	function actualizar_envio_corr(){

		//Var que contiene el arreglo de radicados
		$arrRadicados =  array();

		$year      = date("Y");
		$day       = date("d");
		$month     = date("m");

		$data     = array();
		$columna  = array();
		$contador = 0;
		require_once $this->ruta_raiz."/class_control/class_controlExcel.php";
		$this->btt = new CONTROL_ORFEO($this->conexion);
		$this->rad = new Radicacion($this->conexion);



		//Referencia el archivo a abrir
		$ruta = $this->ruta_raiz."/bodega/masiva/".$this->arcCSV;
		clearstatcache();
		$fp=fopen($ruta,'r');  //wb 2 r

		if ($fp){
			//Recorre el arrego de los datos
			for($ii=0; $ii < count ($this->datos) ; $ii++){   
				$i=0;
				// Aqui se accede a la clase class_control para actualizar expedientes.
				$ruta_raiz = $this->ruta_raiz;

				// Por cada etiqueta de los campos del encabezado del CSV efecta un reemplazo
				foreach($this->encabezado[0] as $campos_d){
					
					$dato_r = trim($this->datos[$ii][$i]);

					$texto_tmp = str_replace($campos_d,$dato_r,$texto_tmp);
					
					if($campos_d=="ORDEN") 		$orden_envio   =trim($dato_r);
                                        if($campos_d=="FECHA ADMISION") $fecha_envio   =$dato_r;
					if($campos_d=="ENVIO") 		$num_envio     =trim($dato_r);
					if($campos_d=="FECHA") 		$fecha_reporte =$dato_r;
					if($campos_d=="ESTADO") 	$estado_envio  =trim($dato_r);
					if($campos_d=="ADICIONAL 1") 	$radicado_envio=trim($dato_r);
					if($campos_d=="ADICIONAL 2") 	$copia_envio   =trim($dato_r);
				      
					$i++;
				}
                $numrad = $radicado_envio;
                if( $copia_envio =='')  $copia_envio =0;
                $numcop = $copia_envio;
                $conexion = & $this->conexion;
                /*
 sgd_renv_orden_envio character varying(25),
  sgd_renv_fech_envio date[],
  sgd_renv_num_envio character varying(25),
  sgd_renv_fecha_reporte date[],
  sgd_renv_estado_reporte character varying(25)[],
*/
                $queryUpdate = "update SGD_RENV_REGENVIO set SGD_RENV_ORDEN_ENVIO = '$orden_envio',  SGD_RENV_ESTADO_REPORTE = '$estado_envio', SGD_RENV_NUM_ENVIO = '$num_envio' where RADI_NUME_SAL = $numrad AND SGD_DIR_TIPO = $numcop";
                $rs=$this->conexion->query( $queryUpdate );
			if (!$rs)
			{	$this->conexion->conn->RollbackTrans();
			die ("<span class='etextomenu'>No se ha podido insertar la informaci&oacute;n de la secuencia '$nurad' con: $queryUpdate");
			}

                // En esta parte registra el envio en la tabla SGD_RENV_REGENVIO
		$contador = $ii + 1;

                }

                $arrRadicados[]=$nurad;
                fclose($fp);
		echo "</table>";
		echo "<span class='info'>Numero de registros $contador</span>";
	      }
		else exit("No se pudo abrir el archivo $this->archivo_insumo");
	    }
  

	function cargar_csv(){	
		$h = fopen($this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV ,"r") or $flag=2;

		if ($h){
			$contenidoCSV = file( $this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV );
			fclose($h);
		}

		foreach ( $contenidoCSV as $line_num => $line ) {
			if ( $line_num == 0 ) { //Esta línea contiene las variables a reemplazar
				$comaPos = stripos($line , ",");
				$puntocomaPos = stripos($line , ";");
				//				   		echo "<br>Separado por coma: " . $comaPos;
				//				   		echo "<br>Separado por punto y coma: " . $puntocomaPos;
				if($comaPos){
					$separador = ",";
				}elseif ($puntocomaPos) {
					$separador = ";";
				}else {
					die("Separador en archivo CSV inv&aacute;lido.");
				}
				break;
			}

		}

		$h = fopen($this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV ,"r") or $flag=2;

		if (!$h){	
			echo "<BR> No hay un archivo csv llamado *". $this->ruta_raiz . "/bodega/masiva/" . $this->arcCSV."*";
		}else{	
			$this->alltext_csv = "";
			$this->encabezado = array();
			$this->datos = array();
			$j=-1;
			while ( $line=fgetcsv ( $h, 10000, $separador ) )
				//	Comentariada por HLP. Para puebas de arhivo csv delimitado por ;
				//while ($line=fgetcsv ($h, 10000, ";"))
			{
				$j++;
				if ($j==0)
					$this->encabezado = array_merge ($this->encabezado,array($line));
				else
					$this->datos=  array_merge ($this->datos,array($line));
			} // while get

			//	echo ("El encabezado es " . $this->encabezado[0][0] .", ". $this->encabezado[0][1] .", ". $this->encabezado[0][2] .", ");
			//  echo ("Las lineas de datos son " . count ($this->datos));
			$c=0;
			while ($c < count ($this->datos)){	
				$c++;
			} 
		}	
	}


	
	/**
	 * Gestiona la validaci�n de las archivos que intervienen en el 
	 * proceso antes de invocar esta funci�n debe haberse invocado 	cargar_csv() y abrir();
	 */

    function validarArchsPlano($tipo) {	
        $this->cargarOblPlano($tipo);
        //	$this->camObligCsv;
        //*echo "<br>Entra a validacion de CSV";
        //Recorre los campos abligatorios buscando que cada uno de ellos se encuentre en el emcabezado del archivo CSV
            for($i=0; $i < count ($this->camObligPlano) ; $i++)
        {  	$sw=0;
        //    echo "<br>recorre campos obligatorios: $i";
            foreach($this->encabezado[0] as $campoEnc){
              //echo "<br>recorre encabezados: " . $this->encabezado[0] . ", campo: $campoEnc" ;

            if ($this->camObligPlano[$i] == $campoEnc){
                $sw=1;
            }
        }
        if ($sw==0){
            $this->errorEncab[]=$this->camObligPlano[$i];
        }
        }
       
        //$this->validarRegistrosObligsPlano();
    }
	/**
	 * Carga los campos obligatorios del tipo de archivo enviado como par�metro y lo hace en el arreglo referenciado en el arreglo definido como par�metro
	 * @param $tipo     	es el tipo de archivo de masiva
	 * @param $arreglo   es el arreglo donde han de quedar los capos abligatorios
	 */
    function cargarCampos($tipo,$arreglo){	
        $q  = "select * from sgd_cob_campobliga where sgd_tidm_codi = $tipo";
        $rs = $this->conexion->query($q);

        while  (!$rs->EOF){
            $arreglo[]=$rs->fields['SGD_COB_LABEL'];
            $rs->MoveNext();
        }
    }
        /**
	 * Carga los campos obligatorios del tipo de archivo enviado como par�metro y lo hace en el arreglo referenciado en el arreglo definido como par�metro
	 * @param $tipo     	es el tipo de archivo de masiva
	 * @param $arreglo   es el arreglo donde han de quedar los capos abligatorios
	 */
    function cargarCamposPlano($tipo){	
        $q  = "select * from sgd_cob_campobliga where sgd_tidm_codi = $tipo";
        $rs = $this->conexion->query($q);

        while  (!$rs->EOF){
            $arreglo[]=$rs->fields['SGD_COB_LABEL'];
            $rs->MoveNext();
        }
      return $arreglo;	
    }

		/**
		 * Carga los campos obligatorios del archivo plano Envio
		 */
		function cargarOblPlano($tipo)
		{	$this->camObligPlano = $this->cargarCamposPlano($tipo); 
                        //var_dump($this->camObligPlano);             
                }

		/**
		 * Pregunta si existe alg�ntipo de error, que puede ser de emcabezado, pantilla, lugar, ESP,de completitud del CSV, o del tipo de registro, antes de llamar esta funci�n se debi� validar mediante  validarArchs(). En caso de error retorna true, de lo contrario falso.
		 * @return	boolean
		 */
		function hayErrorPlano(){
      			if (count($this->errorEncab)>0||count($this->errorComplCsv)>0)
				return  true;
			else
				return false;
		}
	/**
	 * Busca si los campos obligatorios est�n completos en todos los registros del archivo CSV
	 * Si existe alg�n error lo registra en el arreglo errorComplCsv
	 */
	function validarRegistrosObligsPlano(){	//Recorre todos los registros del CSV
		for($i=0; $i < count ($this->datos) ; $i++)
		{	//Recorre todos campos obligatorios del CSV y los busca en cada registro
			for($j=0; $j < count ($this->camObligPlano) ; $j++){	
				$col= $this->getNumColEnc($this->camObligPlano[$j]);
				$dato = $this->datos[$i][$col];
				//Si no halla alg�n campo obligatorio lo pone en el arreglo de errores
				if (strlen($dato)==0){	
					$this->errorComplCsv[]="REG ".($i+1).": " .$this->camObligPlano[$j];
				}	
			}	
		}	
	}


	/**
	 * Retorna el n�mero de columna en que se encuentra el encabezado que le llegue como par�metro. Si no existe retorna -1
	 * @param $nombCol		es el nombre de la columna o encabezado
	 * @return   integer
	 */
	function getNumColEnc($nombCol){	
		$i=-1;
		$sw=0;
		//Recorre todo el encabezado
		foreach($this->encabezado[0] as $campoEnc){
			$i++;

			if ("*".$nombCol."*" == $campoEnc){
				$sw=1;
				break;
			}

		}
		if ($sw==1)
			return($i);
		else
			return -1;
	}
	/**
	 * Muestra los errores presentados en la validaci�n de los archivos
	 */
	function mostrarErrorPlano(){	
               	$auxErrrEnca = $this->errorEncab;
		$auxErrCmpCsv = $this->errorComplCsv;
		$ruta_raiz = "..";
                include "$ruta_raiz/radsalida/masiva/error_archivo.php";
	}



	/**
	 * Cambia el valor  del atributo que indica si se trata de ina combinaci�n definitiva
	 * @param $arg		nuevo valor de la variable, puede ser "si" o "no"
	 */
	function setDefinitivo($arg){
		$this->definitivo=$arg;
	}


	/**
	 * Cambia el valor del atributo que indica la caracter�stica de los documentos a combinar
	 * @param $tipo		nuevo valor de la variable
	 */
	function setTipoDocto($tipo){	
		$this->tipoDocto=$tipo;	
	}


	/**
	 * Carga los datos del archivo insumo para la generaci�n de masiva
	 */
	function cargarInsumo(){
		$fp=fopen("$this->ruta_raiz/bodega/masiva/$this->archivo_insumo",'r');
		$i=0;
		while (!feof($fp))
		{	$i++;
		$buffer = fgets($fp, 4096);
		if ($i==1)
		{   $this->arcPlantilla =  trim(substr($buffer,strpos($buffer,"=")+1,strlen($buffer)-strpos($buffer,"=")));
		}
		if ($i==2)
		{   $this->arcCSV =  trim(substr($buffer,strpos($buffer,"=")+1,strlen($buffer)-strpos($buffer,"=")));
		}
		if ($i==3)
		{	$this->arcFinal =  trim(substr($buffer,strpos($buffer,"=")+1,strlen($buffer)-strpos($buffer,"=")));
		}
		if ($i==4)
		{	$this->arcTmp =  trim(substr($buffer,strpos($buffer,"=")+1,strlen($buffer)-strpos($buffer,"=")));
		}
		}
		fclose ($fp);
	}

	/**
	 * Retorna el path del archivo insumo para masiva
	 */
	function getInsumo(){	
		return($this->archivo_insumo);	
	}

	/**
	 * Funcion que retorna la ruta del csv con los numeros de radicado
	 * para que el usuario realize la combinacion en word.
	 */
	function final_csv(){
		$path = $this->ruta_raiz."/bodega/masiva/";
		$ruta = $path.$this->arcCSV;
		$final= $path.'comp'.$this->arcCSV;
		$linea= null;
		$m    = 0;

		clearstatcache();
		$fp	  = fopen($ruta,'r');
		$fd   = fopen($final, "w"); 

		while ($linea= fgets($fp,1024)){
            $linea = str_replace(';',",",$linea);
            $linea = str_replace("\"",'',$linea);

			if(empty($m)){
				$linea = "*F_RAD_S*, *F_RAD_S*".$linea; 
			}else{
				$nmrad = $this->radarray[$m - 1];
				$hoy   = date('Y - m - d');
				$linea = "$nmrad,".$hoy.",".str_replace(';',",",$linea); 
			}
			fwrite ($fd, $linea); 
			$m++;
		}

		fclose($fd); //aqui estoy cerrando el archivo.txt
		return $final; //aqui muestro la ruta donde se creo y guardo el archivo.txt
	}

}

/**
 * Función que reemplaza caracteres con tíldes por sus contrapartes sin tílde, solo para mostrar por pantalla
 *
 * @param String $string
 * @return String
 */
function unhtmlspecialchars( $string ){
  $string = str_replace ( 'á', '&aacute;', $string );
  $string = str_replace ( 'é', '&eacute;', $string );
  $string = str_replace ( 'í', '&iacute;', $string );
  $string = str_replace ( 'ó', '&oacute;', $string );
  $string = str_replace ( 'ú', '&uacute;', $string );
  $string = str_replace ( 'Á', '&Aacute;', $string );
  $string = str_replace ( 'É', '&Eacute;', $string );
  $string = str_replace ( 'Í', '&Iacute;', $string );
  $string = str_replace ( 'Ó', '&Oacute;', $string );
  $string = str_replace ( 'Ú', '&Uacute;', $string );
  $string = str_replace ( 'ñ', '&ntilde;', $string );
  $string = str_replace ( 'Ñ', '&Ntilde;', $string );

  return $string;
}



?>
