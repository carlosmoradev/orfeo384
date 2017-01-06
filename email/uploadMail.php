<?
include "../envios/class.phpmailer.php";
  $destinatario = $remitente;
//para el envío en formato HTML
  $mail = new PHPMailer();
  $cuerpo = "<br>$texto
                <br> ha dado respuesta a su solicitud No. " . $nurad . " mediante el oficio No." . $verradicado2 . ", la cual también puede ser consultada en el portal Web del DNP.</p>
                 <br><br><b><center>Si no puede visualizar bien el correo, o no llegaron bien los Adjuntos, puede Consultarlos en :
                 <a href='http://orfeo.correlibre.org/pqr/consulta.php?rad=$nurad'>http://orfeo.correlibre.org/pqr/consulta.php</a><br><br><br>".$respuesta."</b></center><BR>
                 ";
  $mail->Mailer = "smtp";
  $mail->From = $destinatario;
  echo "Destino : ".$msg->header[$eMailMid]['from'][0];
  $mail->FromName = $usuario_mail;
  $strServer="172.16.1.92:25";
  $mail->Host = $servidorSmtp;
  $mail->Mailer = "smtp";
  $mail->SMTPAuth = "true";
  $mail->Subject = $motivo;
  $mail->AltBody = "Para ver el mensaje, porfavor use un visor de E-mail compatibles!";
  $mail->Body = $cuerpo;
  $mail->AddAddress($msg->header[$eMailMid]['from'][0]);
  $mail->IsHTML(true);
  echo "<hr>";
  if(!$mail->Send())
  {
    echo "fallo el Envio de Correo respuesta $destinatario ->".$envioMail;
  }else{
  echo "Se envio el Correo a $destinatario ->".$envioMail;
}
?>

