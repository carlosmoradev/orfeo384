<?php
session_start();

$ruta_raiz = "..";
if (!$_SESSION['dependencia'])
header ("Location: $ruta_raiz/cerrar_session.php");

// Modificado 2010 aurigadl@gmail.com
// Modificado 2012 neoecos@gmail.com

/**
 * Paggina mod_causal.php que muestra el contenido de las Carpetas
 * Creado en la SSPD en el año 2003
 * Se añadio compatibilidad con variables globales en Off
 * @autor Jairo Losada 2012-01
 * @licencia GNU/GPL V 3
 */


foreach ($_GET as $key => $valor)   ${$key} = $valor;
foreach ($_POST as $key => $valor)   ${$key} = $valor;
define('ADODB_ASSOC_CASE', 2);
//$db->conn->debug=true;
$krd            = $_SESSION["krd"];
$dependencia    = $_SESSION["dependencia"];
$usua_doc       = $_SESSION["usua_doc"];
$codusuario     = $_SESSION["codusuario"];
include_once ("$ruta_raiz/include/db/ConnectionHandler.php");
$db = new ConnectionHandler("$ruta_raiz");
if(!$verrad and $_GET["verrad"]) $verrad = $_GET["verrad"];
define('ADODB_ASSOC_CASE', 2);
$db->conn->SetFetchMode(ADODB_FETCH_ASSOC);
?>
<html>
<head>
<link rel="stylesheet" href="<?=$ruta_raiz?>/estilos/orfeo.css">
<script language="javascript">
	vecSubseccionE = new Array (
<?php
// For para el javascript
//$db->conn->debug=true;
$rs = $db->conn->query("SELECT * FROM SGD_DCAU_CAUSAL");
$cont = 0;
while(!$rs->EOF)
{
	$coma = ($cont == 0) ? '': ',';
	echo $coma . 'new seccionE ("' .  $rs->fields["SGD_DCAU_CODIGO"] . '",' .
								'"'	. $rs->fields["SGD_DCAU_DESCRIP"] . '",' .
								'"' . $rs->fields["SGD_CAU_CODIGO"] . '")' . "\n";
	$cont++;
	$rs->MoveNext();
}
?>);
	vecSeccionE = new Array ();
	vecCategoriaE = new Array ();
	
	//Inicializo las variables isNav, isIE dependiendo del navegador
	var isNav, isIE

	if (parseInt(navigator.appVersion) >= 4)
	{
		if (navigator.appName == "Netscape" )
		{
			isNav = true;
		}
		else
		{
			isIE = true;
	}	}

	//Variable que va a tener el valor de la opcion seleccionada para hacer la busqueda.
	var idFinal=0 ;  

	//Estructuras para almacenar la informacion de las tablas de categorias, seccion y subseccion de la base de datos.
	function categoriaE (id, nombre)
	{
		this.id = id;
		this.nombre = nombre;
	}
	
	function seccionE (id, nombre, id_categoria)
	{
		this.id = id;
		this.nombre = nombre;
		this.id_categoria = id_categoria;
	}
	
	function subseccionE (id, nombre, id_seccion)
	{
		this.id = id;
		this.nombre = nombre;
		this.id_seccion = id_seccion;
	}
	
	// Funcion que segun la opcion de la categoria, arma el combo de la seccion con los datos que tienen como padre dicha categoria.
	function cambiar_seccion(elselect)
	{
		var j =1;
		limpiar_todo();
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		for (i=0;i<vecSubseccionE.length;i++) {
			if (vecSubseccionE[i].id_categoria==id) {
				document.form_causales.deta_causal.options[j] = new Option(vecSubseccionE[i].nombre,vecSubseccionE[i].id);
				j ++;
			}
		}
		if(j==1){
		   document.form_causales.causal_new.options[0] = new Option('No aplica.',0);
		   document.form_causales.deta_causal.options[0] = new Option('No aplica.',0);
		}
		idFinal = id;
		nombreFinal = nombre;
	}

	// Funcion que segun la opcion de la seccion, arma el combo de la subseccion con los datos que tienen como padre dicha seccion.
	function cambiar_subseccion(elselect) {
		limpiar_subseccion();
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		var j =1;
		for (i=0; i<vecSubseccionE.length;i++) {
			if (vecSubseccionE[i].id_seccion==id) {
				document.form_causales.deta_causal.options[j] = new Option(vecSubseccionE[i].nombre,vecSubseccionE[i].id);
				j++;
			}	
		}
		if(j==1){
			document.form_causales.deta_causal.options[0] = new Option('----',0);
		}
		idFinal = id;
		nombreFinal = nombre;
	}

	//Funciones que borran los datos de los combos y los deja con un solo valor 0.
	function limpiar_todo(){
		//document.form_causales.sector.options[0]= new Option('Escoja',0);
		document.form_causales.deta_causal.options[0]= new Option('----',0);
		//var tamsec = document.form_causales.sector.options.length;
		var tamsubsec = document.form_causales.deta_causal.options.length;
		for (j=1; j<tamsubsec ; j++) {
			document.form_causales.deta_causal.options[1] = null;
		}
	}

	function limpiar_subseccion(){
		document.form_causales.deta_causal.options[0]= new Option('---',0);
		var tamsubsec = document.form_causales.deta_causal.options.length;
		alert(document.form_causales.deta_causal.options[0]);
		for (j=1; j<tamsubsec ; j++) {
		  document.form_causales.deta_causal.options[1] = null;
		}
	}

	//Funcion que actualiza el idFinal
	function cambiar_idFinal(elselect){
		indice = elselect.selectedIndex;
		id = elselect.options[indice].value;
		nombre = elselect.options[indice].text;
		idFinal = id ;
		nombreFinal = nombre;
	}
	
	//Funcion que valida los campos y pasa a la pagina siguiente despues de hacer enter en el campo palabra
	function cambiar_pagina(){
		indice = document.form_causales.categoria.selectedIndex;     
		if (document.form_causales.categoria.options[indice].value == 0) {
			alert("Escoja una categoria");
			return (false);
		}  else if ( idFinal == 18 || idFinal == 16 ) {
			alert("Escoja una seccion");
			return (false);
		}  else if ( idFinal == 26 || idFinal == 27 || idFinal == 28 || idFinal == 29 ) {
			alert("Escoja una Subseccion");
			return (false);
		} else {
			document.form_causales.target = "";
			document.form_causales.action = "resultados_empleo.php";
			if (idFinal != "") {
				document.form_causales.id.value = idFinal;
				document.form_causales.nombre.value = nombreFinal;
			}	
			return (true); 
		}
	}

	//Funcion que valida los campos y pasa a la pagina siguiente despues de hacer click en el boton de buscar
	function cambiar_pagina_buscar(){
		//Obtengo la fecha que le interesa buscar al usuario
		//document.form_causales.historico.value = document.form_causales.fechas_historico.value;
		
		//Obtengo el indice de la fecha
		//indice_fecha = document.form_causales.fechas_historico.selectedIndex;
		
		//Obtengo el valor de la fecha completa
		//document.form_causales.fecha_completa.value = document.form_causales.fechas_historico.options[indice_fecha].text;
	
		indice = document.form_causales.categoria.selectedIndex;     
		if (document.form_causales.categoria.options[indice].value == 0) {
			alert("Escoja una categoria");
		} else if ( idFinal == 18 || idFinal == 16 ) {
			alert("Escoja una seccion");
		} else if ( idFinal == 26 || idFinal == 27 || idFinal == 28 || idFinal == 29 ) {
			alert("Escoja una Subseccion");
		} else {
			document.form_causales.target = "";
			document.form_causales.action = "resultados_empleo.php";
			if (idFinal != "") {
				document.form_causales.id.value = idFinal;
				document.form_causales.nombre.value = nombreFinal;
			}
			document.form_causales.submit();
		}
	}
	
	function verificacionCampos()
	{
		document.form_causales.grabar_causal.value = "yes";
		document.form_causales.submit();
	}
	
	function cerrar() {
		opener.regresar();
		window.close();
	}
</script>
</head>
<body>
<form name=form_causales method="post"
	action="<?=$ruta_raiz?>/causales/mod_causal.php?<?=session_name()?>=<?=trim(session_id())?>&krd=<?=$krd?>&verrad=<?=$verrad?>&verradicado=<?=$verrad?>&sectorCodigoAnt=<?=$sectorCodigoAnt?>&sectorNombreAnt=<?=$sectorNombreAnt?>">
<table border=0 width 100%  cellpadding="0" cellspacing="5"
	class="borde_tab">
	<tr>
	<td class="titulos2">Localidad</td>
	<td>
<?php
include_once($ruta_raiz."/sector/mod_sector.php");
?>
	</td>
</tr>
-->
	<tr>
		<td class="titulos2">Sector <?php
		if (!$ruta_raiz) $ruta_raiz="..";
		include_once($ruta_raiz."/include/tx/Historico.php");
		$objHistorico= new Historico($db);
		if  (count($recordSet)>0)
		array_splice($recordSet, 0);
		if  (count($recordWhere)>0)
		array_splice($recordWhere, 0);
		$fecha_hoy = Date("Y-m-d");
		$sqlFechaHoy=$db->conn->DBDate($fecha_hoy);
		$arrayRad = array();
		$arrayRad[]=$verrad;
		$actualizo = 0;
		$actualizoFlag = false;
		$insertoFlag = false;

		if (($grabar_causal == "yes") && (($causal_new != $causal_grb) || ($deta_causal != $deta_causal_grb)||($ddca_causal != $ddca_causal_grb)))
		{
			/** Intenta actualizar causal
			 *  Si esta no esta entonces simplemente le inserte
			 */
			if($causal_new==0)
			{
				$ddca_causal="0";
				$deta_causa ="0";
			}
			if($ddca_causal == null)
			{
				$ddca_causal="";
			}
			if($deta_causal == null)
			{
				$deta_causa ="";
			}
			if($causal_new !=null ) $recordSet["SGD_DCAU_CODIGO"] = "$causal_new";
			if($ddca_causal !=null ) $recordSet["SGD_DDCA_DDSGRGDO"] = "$ddca_causal";
			if($deta_causal !=null) $recordSet["SGD_DDCA_CODIGO"] = "$deta_causal";
			$recordWhere["RADI_NUME_RADI"] = $verrad;
			//$db->update("SGD_CAUX_CAUSALES", $recordSet,$recordWhere);
			$sqlSelect = "SELECT SGD_CAUX_CODIGO,COUNT(RADI_NUME_RADI) AS COUNT_RADI
					FROM SGD_CAUX_CAUSALES 
					WHERE RADI_NUME_RADI = $verrad
					GROUP BY SGD_CAUX_CODIGO";
//$rs =
//$db->conn->debug=true ;echo "2222222222222222";
			//select para saber habia registro por actualizar
			$rs = $db->conn->Execute($sqlSelect);

//$db->conn->debug=true;
			if (!$rs->EOF) $actualizo = $rs->fields["COUNT_RADI"];

			// Verifica banderas de actualizacion o de insercion para actulizar los nuevos datos
			if (isset($actualizo) && ($actualizo>0))
			{
				$sqlSelect = "SELECT caux.SGD_CAUX_CODIGO,
						cau.SGD_CAU_CODIGO,		
						dcau.SGD_DCAU_CODIGO,
						ddcau.SGD_DDCA_CODIGO,
						cau.SGD_CAU_DESCRIP,
						dcau.SGD_DCAU_DESCRIP,												
						ddcau.SGD_DDCA_DESCRIP
					FROM SGD_CAUX_CAUSALES caux,
						SGD_DCAU_CAUSAL dcau,
						SGD_CAU_CAUSAL cau,
						SGD_DDCA_DDSGRGDO ddcau
					WHERE caux.RADI_NUME_RADI = '$verrad' AND
			          dcau.SGD_DCAU_CODIGO = caux.SGD_DDCA_CODIGO AND
			          cau.SGD_CAU_CODIGO = caux.SGD_DCAU_CODIGO AND
			          ddcau.SGD_DDCA_CODIGO = caux.SGD_DDCA_DDSGRGDO";
				$rs = $db->query($sqlSelect);
				if (!$rs->EOF)
				{
					$causal_grb = $rs->fields["SGD_CAU_CODIGO"];
					$causal_nombre = $rs->fields["SGD_CAU_DESCRIP"];
					$deta_causal_grb = $rs->fields["SGD_DCAU_CODIGO"];
					$dcausal_nombre = $rs->fields["SGD_DCAU_DESCRIP"];
					$ddca_causal = $rs->fields["SGD_DDCA_CODIGO"];
					$ddca_causal_nombre = $rs->fields["SGD_DDCA_DESCRIP"];
					$ddca_causal_grb = $rs->fields["SGD_DDCA_CODIGO"];

				}

			$causal_nombre_grb = ($causal_nombre != '') ? $causal_nombre: 'Sin Clasificar' ;
			$dcausal_nombre_grb = ($dcausal_nombre != '') ? $dcausal_nombre : 'Sin Especificar' ;
			$ddca_causal_nombre = ($ddca_causal_nombre != '') ? $ddca_causal_nombre : 'No específicado' ;
			echo "<span class=info>Causal Actualizada</span>";
			$observa = "*Cambio Causal Eje / Tema / Críterio * ($causal_nombre_grb / $dcausal_nombre_grb / $ddca_causal_nombre)";
			$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
			$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 17);
			$actualizoFlag = true;

		} else if (!isset($actualizo) || ($actualizo==0) )
		{
				// Si no habia nada por actualizar inserta el registro
					
			// Si la causal no se encuentra la inserta en este procedimineto
			$flag = 0;

			$recordSet["RADI_NUME_RADI"] = $verrad;
			$recordSet["SGD_CAUX_FECHA"] = "now()";
			if($causal_new !=null ) $recordSet["SGD_CAU_CODIGO"] = "$causal_new";
//		if($ddca_causal !=null ) $recordSet["SGD_DDCA_DDSGRGDO"] = "$ddca_causal";
			if($deta_causal !=null) $recordSet["SGD_DCAU_CODIGO"] = "$deta_causal";
			$recordSet["SGD_DDCA_CODIGO"] = "1";	

				$rs = $db->insert("SGD_CAUX_CAUSALES", $recordSet);
				array_splice($recordSet, 0);
				if ($rs)
				{
					$sqlSelect = "SELECT caux.SGD_CAUX_CODIGO,
						cau.SGD_CAU_CODIGO,		
						dcau.SGD_DCAU_CODIGO,
						ddcau.SGD_DDCA_CODIGO,
						cau.SGD_CAU_DESCRIP,
						dcau.SGD_DCAU_DESCRIP,												
						ddcau.SGD_DDCA_DESCRIP
					FROM SGD_CAUX_CAUSALES caux,
						SGD_DCAU_CAUSAL dcau,
						SGD_CAU_CAUSAL cau,
						SGD_DDCA_DDSGRGDO ddcau
					WHERE caux.RADI_NUME_RADI = '$verrad' AND
			          dcau.SGD_DCAU_CODIGO = caux.SGD_DDCA_CODIGO AND
			          cau.SGD_CAU_CODIGO = caux.SGD_DCAU_CODIGO AND
			          ddcau.SGD_DDCA_CODIGO = caux.SGD_DDCA_DDSGRGDO";
					$rs = $db->query($sqlSelect);
					if (!$rs->EOF)
					{
						$causal_grb = $rs->fields["SGD_CAU_CODIGO"];
						$causal_nombre = $rs->fields["SGD_CAU_DESCRIP"];
						$deta_causal_grb = $rs->fields["SGD_DCAU_CODIGO"];
						$dcausal_nombre = $rs->fields["SGD_DCAU_DESCRIP"];
						$ddca_causal = $rs->fields["SGD_DDCA_CODIGO"];
						$ddca_causal_nombre = $rs->fields["SGD_DDCA_DESCRIP"];
						$ddca_causal_grb = $rs->fields["SGD_DDCA_CODIGO"];

					}
					array_splice($recordSet, 0);
					array_splice($recordWhere, 0);
					echo "<span class=info>Causal Agregada</span>";
		$causal_nombre_grb = ($causal_nombre != '') ? $causal_nombre: 'Sin Clasificar' ;
		$dcausal_nombre_grb = ($dcausal_nombre != '') ? $dcausal_nombre : 'Sin Especificar' ;
		$ddca_causal_nombre = ($ddca_causal_nombre != '') ? $ddca_causal_nombre : 'No específicado' ;
	$observa = "*Inserción Causal Eje / Tema / Críterio * ($causal_nombre_grb / $dcausal_nombre_grb / $ddca_causal_nombre)";
		$codusdp = str_pad($dependencia, 3, "0", STR_PAD_LEFT).str_pad($codusuario, 3, "0", STR_PAD_LEFT);
		$objHistorico->insertarHistorico($arrayRad,$dependencia ,$codusuario, $dependencia,$codusuario, $observa, 17);
		$insertoFlag = true;
	} // Fin de insercion de causales

	}



		}else if ($esverradicado==1){

		}

		?></td>
		<td width="70%"><?php
		error_reporting(7);
		// capturando causal cuando envie el radicado
		//		$isql = "SELECT caux.SGD_DCAU_CODIGO,
		//				dcau.SGD_CAU_CODIGO
		//		FROM SGD_CAUX_CAUSALES caux,
		//			SGD_DCAU_CAUSAL dcau
		//		WHERE caux.RADI_NUME_RADI = $verrad AND
		//			caux.SGD_DCAU_CODIGO = dcau.SGD_DCAU_CODIGO";
		//		$rsDetalleCau = $db->query($isql);
		//		if(!$rsDetalleCau->EOF)
		//		{
		//			if (empty($causal_new))
		//			{
		//				$deta_causal = $rsDetalleCau->fields["SGD_DCAU_CODIGO"];
		//				$causal_new = $rsDetalleCau->fields["SGD_CAU_CODIGO"];
		//			}
		//		}
	$causal  = $causal_new;
	if(!isset($causal_new)){
		$causal_new = $causal_grb;
	}
	if(!isset($deta_causal)){
		$deta_causal = $deta_causal_grb;
	}
	if(!isset($ddca_causal)){
		$ddca_causal = $ddca_causal_grb;
	}
	$isql = "SELECT SGD_CAU_DESCRIP, SGD_CAU_CODIGO FROM SGD_CAU_CAUSAL ORDER BY SGD_CAU_CODIGO";
	$rs = $db->conn->query($isql);
	echo $rs->GetMenu2('causal_new',$causal_new,false,false,1, 'onChange="submit();"  class="select"');
	?></td>
	<tr>
		<td class="titulos2">Tema</td>
		<td width="323"><?php
		$isql = "SELECT SGD_DCAU_DESCRIP, SGD_DCAU_CODIGO
	FROM SGD_DCAU_CAUSAL 
	WHERE SGD_CAU_CODIGO = $causal_new ORDER BY SGD_DCAU_CODIGO" ;
		$rs = $db->query($isql);
		if($causal_new ==  $causal_grb){
		echo $rs->GetMenu2('deta_causal',$deta_causal,false,false,1,'onChange="submit();"  class="select"');
		$causal_grb = $causal_new;
		}
		else{
		echo $rs->GetMenu2('deta_causal',NULL,false,false,1,'onChange="submit();"  class="select"');
			$isql = "SELECT SGD_DCAU_CODIGO
		FROM SGD_DCAU_CAUSAL 
		WHERE SGD_CAU_CODIGO = $causal_new ORDER BY SGD_DCAU_CODIGO LIMIT 1";
				$rs = $db->query($isql);
				$deta_causal = $rs->fields['SGD_DCAU_CODIGO'];
				$causal_grb = $causal_new;
			}
			?></td>
</tr><?php if (3>5)
{  ?>

	<tr>
		<td class="titulos2">Críterio de Ayuda</td>
		<td width="323" class='celdaGris' class='etextomenu'><?php
		if ($deta_causal != null)
		{
			$isql = "SELECT SGD_DDCA_DESCRIP, SGD_DDCA_CODIGO
				FROM SGD_DDCA_DDSGRGDO 
				WHERE SGD_DCAU_CODIGO = $deta_causal";
			$rs = $db->query($isql);
			if($deta_causal == $deta_causal_grb){
				echo $rs->GetMenu2('ddca_causal',$ddca_causal,false,false,1,'onChange="submit();"  class="select"');
				$deta_causal_grb = $deta_causal;

			} else {
				echo $rs->GetMenu2('ddca_causal',NULL,false,false,1,'onChange="submit();"  class="select"');
				$isql = "SELECT SGD_DDCA_CODIGO
					FROM SGD_DDCA_DDSGRGDO 
					WHERE SGD_DCAU_CODIGO = $deta_causal ORDER BY SGD_DDCA_CODIGO LIMIT 1" ;
				$rs = $db->query($isql);
				$ddca_causal = $rs->fields['SGD_DDCA_CODIGO'];
				$deta_causal_grb = $deta_causal;
			}
		}
	?></td>
		</tr>
		<?php } ?>
		<tr>
			<td colspan="2" align="center">
			<table>
				<tr>
					<td align="left"><input type="button" name="grabar" value='Guardar'
						class='botones' onclick="verificacionCampos();"></td>
					<td align="left"><input type="button" name="btnCerrar"
						value='Cerrar' class='botones' onclick="cerrar();"></td>
				</tr>
			</table>
			</td>
		</tr>
</table>
                <input type=hidden name=ver_causal value="Si ver Causales">
                <input type=hidden name="grabar_causal" id='grabar_causal' value="1">
                <input type=hidden name="verrad" value="<?=$verrad?>">
                <input type=hidden name="sectorNombreAnt" value="<?=$sectorNombreAnt?>">
                <input type=hidden name="sectorCodigoAnt" value="<?=$sectorCodigoAnt?>">
                <input type=hidden name="causal_grb" value="<?=$causal_grb?>">
                <input type=hidden name="causal_nombre" value="<?=$causal_nombre?>">
                <input type=hidden name="deta_causal_grb" value="<?=$deta_causal_grb?>">
                <input type=hidden name="dcausal_nombre" value="<?=$dcausal_nombre?>">
                <input type=hidden name="ddca_causal_nombre" value="<?=$ddca_causal_nombre?>">
	<?php
	$ruta_raiz = ".";
?></form>
</body>
</html>
