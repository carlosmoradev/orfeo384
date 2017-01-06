function loginTrue()
{
	document.formulario.submit();
}

function validar_formulario(){

	var formulario = document.getElementById('consultaweb');
	var error = 0;
	var mensaje = "Tiene los siguientes errores:\n\n";	
	if(formulario.numeroRadicado.value.length < 12){
		error = 1;
		mensaje += "-Número de radicado inválido\n";
	}

	if(formulario.codigoverificacion.value.length !=5){
		error = 1;
		mensaje += "-Código de verificación inválido\n";
	}
	
	if(formulario.captcha.value.length == 0 || !validar_captcha()){
		error = 1;
		mensaje += "-Imágen de verificación inválida\n";
	}
	if(error > 0){
		alert(mensaje);
		return false;
	}else{
		return true;
	}	
}

function validar_formulario_pqrsp(){
	
	var formulario = document.getElementById('consultaPQRSP');
	var error = 0;
	var mensaje = "Tiene los siguientes errores:\n\n";	
	if(formulario.ID.value.length > 5){
		error = 1;
		mensaje += "-Numero de ID inválido\n";
	}

	if(formulario.numeroDocumento.value.length != 0 && formulario.numeroDocumento.value.length < 5){
		error = 1;
		mensaje += "-Número de documento inválido\n";
	}
	
	if(formulario.numeroDocumento.value.length == 0 && formulario.ID.value.length == 0){
		error = 1;
		mensaje += "-Debe ingresar por lo menos un ID, o un número de documento.\n";
	}
	
	if(formulario.campo_captcha.value.length == 0 || !validar_captcha_prqsp()){
		error = 1;
		mensaje += "-Imágen de verificación inválida\n";
	}
	if(error > 0){
		alert(mensaje);
		return false;
	}else{
		return true;
	}	
	
}

function validar_captcha() {
	var url = "captcha.php";
	var valido = false;
	var pars = "captcha="
			+ document.getElementById('consultaweb').captcha.value;
	var ajax = new Ajax.Request(url, {
		//Cuando es sincrono _NO_ se ejecutan los callbacks
		asynchronous : false,
		parameters : pars,
		method : "post",		
		//onComplete : procesaRespuesta		
	});
	function procesaRespuesta(resp) {
		var text = resp.responseText;
		//alert(text);
		if (text == "true") {
			valido = true;
		} else {
			valido = false;
		}

	}
	procesaRespuesta(ajax.transport);
	return valido;
}

function validar_captcha_prqsp() {
	var url = "captcha.php";
	var valido = false;
	var pars = "captcha="
			+ document.getElementById('consultaPQRSP').campo_captcha.value;
	var ajax = new Ajax.Request(url, {
		//Cuando es sincrono _NO_ se ejecutan los callbacks
		asynchronous : false,
		parameters : pars,
		method : "post",		
		//onComplete : procesaRespuesta		
	});
	function procesaRespuesta(resp) {
		var text = resp.responseText;
		//alert(text);
		if (text == "true") {
			valido = true;
		} else {
			valido = false;
		}

	}
	procesaRespuesta(ajax.transport);
	return valido;
}


//Recargar captcha por JS
//@author  Sebastian Ortiz V.

function recargar_captcha() {
	var url = "captcha.php";
	var src = "";
	var pars = "recargar=si";
	var ajax = new Ajax.Request(url, {
		//Cuando es sincrono _NO_ se ejecutan los callbacks
		asynchronous : false,
		parameters : pars,
		method : "post",		
		//onComplete : procesaRespuesta		
	});
	function procesaRespuesta(resp) {
		var text = resp.responseText;	
		src = text;

	}
	procesaRespuesta(ajax.transport);
	return src;
}

function reloadImg(id) {
	   var obj = document.getElementById(id);
	   obj.src = recargar_captcha();
	   return false;
	}




var letters = ' ABCDEFGHIJKLMNÑOPQRSTUVWXYZabcdefghijklmnñopqrstuvwxyzáéíóúü\u0008'
var numbers = '1234567890\u0008'
var signs = ',.:;@-\''
var mathsigns = '+-=()*/'
var custom = '<>#$%&?'

function alpha(e, allow) {
	var k;
	k = document.all ? parseInt(e.keyCode) : parseInt(e.which);
	return (allow.indexOf(String.fromCharCode(k)) != -1);
}

function consultaPQRSSP(){

		if(document.getElementById('consultaweb').disabled == false){
			disableElementById('consultaweb');
			document.getElementById('consultaweb').style.visibility = "hidden";
		}
		enableElementById('consultaPQRSP');
}

function consultaWeb(){
	if(document.getElementById('consultaPQRSP').disabled == false){
		disableElementById('consultaPQRSP');
		document.getElementById('consultaPQRSP').style.visibility = "hidden";
	}
	enableElementById('consultaweb');
}

function disableElementById(idElement){
	document.getElementById(idElement).disabled = true;
	toggleVisibility(idElement);
}

function enableElementById(idElement){
	document.getElementById(idElement).disabled = false;
	toggleVisibility(idElement);
}

function toggleVisibility(controlId)
{
	var control = document.getElementById(controlId);
	if(control.style.visibility == "visible" || control.style.visibility == ""){
	control.style.visibility = "hidden";
	//control.style.float ="left";
	}
	else{
	control.style.visibility = "visible";
	//control.style.float ="right";
	}
}
