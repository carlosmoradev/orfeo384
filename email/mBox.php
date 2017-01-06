<?php
//$connection = "pop3s://$usuario_mail:$passwdEmail@poseidon.dnp.ad:995/$buzon_mail#$opciones_mail";
     $imap = imap_open("{poseidon.dnp.ad:995/pop3/ssl/novalidate-cert}INBOX", "jlosada", "Jhlc11726");
     $n_msgs = imap_num_msg($imap);
     error_reporting(7);
     echo "$n_msgs";
  /****** adding this line: ******/
     //$headdd = imap_headers($imap);
  /***************************/
     $s = microtime(true);
     for ($i=0; $i<$n_msgs; $i++) {
          $header = imap_header($imap, $i);
     }
     $e = microtime(true);
     echo ($e - $s);
     imap_close($imap);

?> 