<?php
  include_once($ruta_raiz."include/PHPMailer_v5.1/class.phpmailer.php");
  $pattern="/([\s]*)([_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*([ ]+|)@([ ]+|)([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,}))([\s]*)/i";

  preg_match_all($pattern,$mailFrom, $salida);
  
  $destinatario=$salida[0];
  $destinatario=$destinatario[0];
  
  //para el envío en formato HTML
  $mail = new PHPMailer(true);
echo "<pre>";
  var_dump("../bodega".$archivoRadicado);
  $archivoRadicadoMail = "../bodega".$archivoRadicado;

echo  $cuerpo = "<br>$texto
                <br> Se ha recibido su correo y se ha radicado con el 
		$numeroRadicado, el cual tambien puede ser consultado en 
		el portal Web del DNP.</p>
                <br><br><b><center>Puede Consultarlos el estado en:
                <a href='http://www.correlibre.org'>

                 <hr>Documento Recibido<hr>
                 <table>
                 <tr><td>
                 $archivoRadicadoMail
                 </td></tr>
                 </table>";

  $mail->IsSMTP(); // telling the class to use SMTP
  $mail->SetFrom($admPHPMailer, $admPHPMailer);

  $mail->Host       = $hostPHPMailer;
  $mail->Port       = $portPHPMailer;

  $mail->SMTPDebug  = $debugPHPMailer;  // 1 = errors and messages // 2 = messages only 
  $mail->SMTPAuth   = "true";
  $mail->SMTPSecure = "tls";

  $mail->Username   = $userPHPMailer;   // SMTP account username
  $mail->Password   = $passwdPHPMailer; // SMTP account password

  $mail->Subject = "Se ha recibido su Correo (No. $numeroRadicado)";
  $mail->AltBody = "Para ver el mensaje, porfavor use un visor de E-mail compatibles!";

  $mail->AddAddress($destinatario, $destinatario);
  $mail->AddAddress("aurigadl@gmail.com");
  $mail->MsgHTML($cuerpo);

  //$mail->From = $usuaEmail;
  //$mail->FromName = $usuaEmail;

  echo "Destino : ".$destinatario;
  echo "<hr>";
  if(!$mail->Send()){
      echo "fallo el Envio de Correo respuesta $mailFrom ->".$destinatario;
  }else{
      echo "Se envio el Correo a $mailFrom ->".$destinatario;
  }
?>

