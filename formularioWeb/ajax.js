// JavaScript Document
function trae_municipio()
	{
	  document.getElementById('loader1').style.display="block";
      var url = "municipio.php";
      var pars = "depto="+document.quejas.depto.value;
  	  var ajax = new Ajax.Request( url, {
                                      parameters: pars,
                                      method:"get",
                                      onComplete: procesaRespuesta
                                         }
      );
	function procesaRespuesta( resp )
		{
        $("div-contenidos").innerHTML = resp.responseText;	
		document.getElementById('loader1').style.display="none";
		}
	}	
function trae_entidad()
	{
	  document.getElementById('loader2').style.display="block";
      var url = "entidad.php";
      var pars = "nit="+document.quejas.nit.value;
      var ajax = new Ajax.Request( url, {
                                      parameters: pars,
                                      method:"get",
                                      onComplete: procesaRespuesta
                                         }
      );
	function procesaRespuesta( resp )
		{
		$("div-contenidos2").style.display="block";
		$("div-contenidos2").innerHTML = resp.responseText;	
		$("loader2").style.display="none";
		}
	}
function trae_radicado()
	{
	  document.getElementById('loader3').style.display="block";
      var url = "radicado.php";
      var pars = "radicado="+document.quejas.radicado.value;
      var ajax = new Ajax.Request( url, {
                                      parameters: pars,
                                      method:"get",
                                      onComplete: procesaRespuesta
                                         }
      );
	function procesaRespuesta( resp )
		{
		$("div-contenidos3").style.display="block";
		$("div-contenidos3").innerHTML = resp.responseText;	
		$("loader3").style.display="none";
		}
	}
function valida_form()
{
mensaje='Se han encontrado los siguientes errores:\n\n';
error=0;
	if((document.quejas.nombre_remitente.value.length==0) || (document.quejas.nombre_remitente.value==""))
		{
			mensaje+='\n-Nombre del remitente invalido';
			error=1;
			
		}
	if((document.quejas.apellidos_remitente.value.length==0) || (document.quejas.apellidos_remitente.value==""))
		{
			mensaje+='\n-Apellidos del remitente invalido';
			error=1;
			
		}
	if((document.quejas.cedula.value.length < 8))
		{
			mensaje+='\n-Documento de identificacion del remitente invalido';
			error=1;
			
		}
		if((document.quejas.depto.value==0))
		{
			mensaje+='\n-Seleccione Departamento';
			error=1;
			
		}
		if((document.quejas.muni.value==0))
		{
			mensaje+='\n-Seleccione Municipio';
			error=1;
			
		}
		if((document.quejas.direccion_remitente.value.length==0))
		{
			mensaje+='\n-Direccion remitente invalida';
			error=1;
			
		}
		if((document.quejas.tipo.value==0))
		{
			mensaje+='\n-Seleccione tipo de solicitud';
			error=1;
		}

		if((document.quejas.asunto.value.length==0))
		{
			mensaje+='\n-Asunto Invalido';
			error=1;
		}
		if((document.quejas.desc.value.length==0))
		{
			mensaje+='\n-Descripcion Invalida';
			error=1;
		}
	
	if((document.quejas.valor_rad) && (document.quejas.valor_rad.value==1))
		{
			mensaje+='\n-Referencia de radicado invalida';
			error=1;
		}
	if(isEmailAddress(document.quejas.email)==false)
		{
			mensaje+='\n-Direccion de correo electronico invalida';
			error=1;

		}
if(error==1)
	{
		alert(mensaje);	
		return false;
	}
else
	return true;
}


function pasa_nit()
	{
		var i
    	for (i=0;i<document.busqueda.nit.length;i++){
       if (document.busqueda.nit[i].checked)
          break;
    }
    valor_nit = document.busqueda.nit[i].value;
	window.opener.document.quejas.nit.value=valor_nit;
	window.opener.trae_entidad();
	window.close();

}

//validacion caracteres

/*
<input type="text" onkeypress="return alpha(event,numbers)" />
<input type="text" onkeypress="return alpha(event,letters)" />
<input type="text" onkeypress="return alpha(event,numbers+letters+signs)" />
*/

var letters=' ABC�DEFGHIJKLMN�OPQRSTUVWXYZabc�defghijklmn�opqrstuvwxyz������������������������\u0008'
var numbers='1234567890\u0008'
var signs=',.:;@-\''
var mathsigns='+-=()*/'
var custom='<>#$%&?�'

function alpha(e,allow) {
var k;
k=document.all?parseInt(e.keyCode): parseInt(e.which);
return (allow.indexOf(String.fromCharCode(k))!=-1);
}

//validacion email
function isEmailAddress(theElement)
{
var s = theElement.value;
var filter=/^[A-Za-z][A-Za-z0-9_]*@[A-Za-z0-9_]+\.[A-Za-z0-9_.]+[A-za-z]$/;
if (s.length == 0 ) return true;
if (filter.test(s))
return true;
else
return false;
}
