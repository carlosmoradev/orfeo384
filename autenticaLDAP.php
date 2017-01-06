<?php
function checkldapuser($username,$password,$ldap_server){
	
  //Cambiamos la versión del LDAP a la version 3 soportada por defecto por Microsoft
  // y soportar UTF8 en usuarios y contraseñas.	
  ldap_set_option($ldap, LDAP_OPT_PROTOCOL_VERSION, 3); 		

  if($connect=ldap_connect($ldap_server)){ 

   // enlace a la conexión
   /*
   if(($bind=ldap_bind($connect)) == false){
     	$mensajeError =  "Falla la conexi&oacute;n con el servidor LDAP";
     return $mensajeError;
   }


   // busca el usuario
   if (($res_id = ldap_search( $connect,
	"OU=usuarios,OU=correlibre,DC=correlibre,DC=gov,DC=co",
        "(cn=jrolon)")) == false) {
    	$mensajeError = "No encontrado el usuario en el árbol LDAP";
     return $mensajeError;
   }

   if (ldap_count_entries($connect, $res_id) != 1) {
     	$mensajeError =  "El usuario $username se encontr&oacute; mas de 1 vez";
     return $mensajeError;
   }

   if (( $entry_id = ldap_first_entry($connect, $res_id))== false) {
     	$mensajeError =  "No se obtuvieron resultados";
     return $mensajeError;
   }

   if (( $user_dn = ldap_get_dn($connect, $entry_id)) == false) {
     	$mensajeError = "No se puede obtener el dn del usuario";
     return $mensajeError;
   }
	error_reporting( 0 );
*/  
 /* Autentica el usuario */
   //$username=$_SESSION[""];
   //$password=$drd;
   $username = strtolower($username);
   $user_dn="cn=$username,OU=usuarios,OU=correlibre,DC=correlibre,DC=gov,DC=co";
   $user_dn = "$username@correlibre.gov.co";
   if (($link_id = ldap_bind($connect, $user_dn, $password)) == false) {

      $user_dn="cn=$username,OU=Directivos,OU=correlibre,DC=correlibre,DC=gov,DC=co";
      $user_dn = "$username@correlibre.org";      
      if (($link_id = ldap_bind($connect, $user_dn, $password)) == false) {
       error_reporting( 0 );
       $mensajeError = "USUARIO O CONTRASE&Ntilde;A INCORRECTOS - D";
       return $mensajeError;
      }
   }

   return '';
   ldap_close($connect);
  } else {                                 
   $mensajeError = "no hay conexión a '$ldap_server'";
   return $mensajeError;
  }

  ldap_close($connect);
  return(false);

}


/**

DC=correlibre,DC=gov,DC=co
-rama donde estan los usuarios del Directorio Activo                         OU=usuarios,OU=correlibre,DC=correlibre,DC=gov,DC=co
OU=Directivos,OU=correlibre,DC=correlibre,DC=gov,DC=co

*/
?>


