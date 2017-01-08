<?php
/** OrfeoGPL 3.8.2 
 * Es una Version que mantiene Correlibre 
 * @licencia gnu/gpl v 3.0 
 *          Encontrara una copia de la misma en el directorio Instalacion o acceda a ella en : http://www.gnu.org/licenses/
 *
 * ---- NOTA ----
 * La pagina login.php posee un iframe al final que conecta con Correlibre.org
 * y envia informacion para poseer estadisticas de sitios en los cuales es instalado
 * si ud desea informacion Adicional puede enviar un correo a yoapoyo@orfeogpl.org
 * No se envian contraseñas de sus servicios.
 * 
 **
**/
//Nombre de la base de datos de ORFEO
$servicio = "dborfeo384";
//Usuario de conexion con permisos de modificacion y creacion de objetos en la Base de datos.
$usuario = "orfeo_user";
//Contraseña del usuario anterior
$contrasena= "0rf30**$$";
//Nombre o IP del servidor de BD. Opcional puerto, p.e. 120.0.0.1:1521
$servidor = "localhost:5432";
$db = $servicio;
//Tipo de Base de datos. Los valores validos son: postgres, oci8, mssql.
$driver = "postgres";
 //Variable que indica el ambiente de trabajo, sus valores pueden ser  desarrollo,prueba,orfeo
$ambiente = "orfeo";
//Servidor que procesa los documentos
$servProcDocs = "127.0.0.1:8000";
//Acronimo de la empresa
$entidad= "CORRELIBRE";
//Nombre de la EmpresaCD
$entidad_largo= 'FUNDACION PARA EL DESARROLLO DEL CONOCIMIENTO LIBRE';	//Variable usada generalmente para los t�tulos en informes.
//Telefono o PBX de la empresa.
$entidad_tel = 000000 ;
//Direccion de la Empresa.
$entidad_dir = "Calle ## No. ## - ## CIUDAD";
$entidad_depsal = 999;	//Guarda el codigo de la dependencia de salida por defecto al radicar dcto de salida
// 0 = Carpeta salida del radicador	>0 = Redirecciona a la dependencia especificada

// Ruta para las operaciones con archivos.
// Esta es la ubicacion de nuestra carpeta orfeo 
// ejm: linux    /var/www/miorfeo
//      windows  c:\apache2.0\hpdocs\miorfeo
$ABSOL_PATH              = "/var/www/html/orfeo384-base/";

/**
*	Se crea la variable $ADODB_PATH.
*	El Objetivo es que al independizar ADODB de ORFEO, este (ADODB) se pueda actualizar sin causar
*	traumatismos en el resto del codigo de ORFEO. En adelante se utilizara esta variable para hacer
*	referencia donde se encuentre ADODB
*/
$ADODB_PATH                    = $ABSOL_PATH."include/class/adodb";
$ADODB_CACHE_DIR               = "/tmp";
$MODULO_RADICACION_DOCS_ANEXOS = 1;

/**
 * Configuracion LDAP
 */
//Nombre o IP del servidor de autenticacion LDAP
$ldapServer     = '';

//Cadena de busqueda en el servidor.
$cadenaBusqLDAP = '';

//Campo seleccionado (de las variables LDAP) para realizar la autenticacion.
$campoBusqLDAP  = 'mail';

//Si esta variable va en 1 mostrara en informacion geneal el menu de 
//Rel. Procedimental, resolucion, sector, causal y detalle. en cero Omite este menu
$menuAdicional  = 0;

// Variables que se usan para la radicacion del correo electronio
// Sitio en el que encontramos la libreria pear instalada
$PEAR_PATH               = $ABSOL_PATH."pear/";

// Servidor de Acceso al correo Electronico
$servidor_mail           = "imap.admin.gov.co";

// Tipo de servidor de correo Usado
$protocolo_mail          = "imap"; // imap  | pop3

// Puerto del servidor de Mail.
$puerto_mail             = 143; //Segun servidor defecto 143 | 110

//Color de Fondo de OrfeoGPL
$colorFondo              = "8cacc1";

// Pais Empresa o Entidad
$pais                    = "Colombia";

// Correo Contacto o Administrador del Sistema
$administrador           = "sunombre@dominio.com";
// Directorio de estilos a Usar... Si no se establece una Ruta el sistema usara el que posee por Defecto en el directorio estilos.  orfeo.css para usarlo cree una carpeta con su personalizacion y luego copie el archivo orfeo.css y cambie sus colores.
$ESTILOS_PATH            = "orfeo38";

// Variable que se usa para enviar correos al radicar o reasignar
// Para configurar el correo electronico enviado se usa phpmailer que esta en include/ y se deben configurar
// Los archivos en /conf/ El servidor
// /conf/configPHPMailer.php Archivo con la configuracion de servidor y cuenta de correo.
// MailInformado.html, MailRadicado.hrml y MailReasignado.html (Archivos con el cuerpo de los correos)
$enviarMailMovimientos   = "1";
// Datos que se usan en el formulario web disponible a los usuarios
$depeRadicaFormularioWeb = 700;  // Es radicado en la Dependencia 900
$usuaRecibeWeb           = 1;  // Usuario que Recibe los Documentos Web
$secRadicaFormularioWeb  = 700;
// Esta variable si esta en 1 no discrimina seris por dependencia, todas las deps veran la msma
$seriesVistaTodos        = 1;
// Variable de acceso a ORfeo Local
$httpOrfeoLocal          = "http://orfeo.correlibre.gov.co/orfeo-3.8.2d/";
// Variable de acceso a ORfeo Remoto
$httpOrfeoRemoto         = " Por el momento OrfeoGPL no tiene Acceso Por Web Externa.";
// Variable de la Web Oficial de la Entidad.
$httpWebOficial          = "http://www.correlibre.org";

// Digitos de la Dependencia Minimo 1 maximo 5
$digitosDependencia      = 3;


ini_set('include_path', '.:'.$PEAR_PATH);

// Variable para Activar Tramite conjunto, esta variable cumple la misma funcion de informados pero con mas responsabilidad
$varTramiteConjunto     =0;


?>
