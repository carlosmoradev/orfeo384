<?php
//$connection = "pop3s://$usuario_mail:$passwdEmail@correlibre.org:995/$buzon_mail#$opciones_mail";
    $imap = imap_open("{poseidon.dnp.ad:995/pop3/ssl/novalidate-cert}INBOX", "jlosada", "Jhlc11726"); 
    $message_count = imap_num_msg($imap);
		echo "Numero de Mensajes -->".$message_count;

		echo "<h1>Headers in INBOX</h1>\n";
		$headers = imap_headers($imap);
		
		
		print_r($headers);
		if ($headers == false) {
				echo "Call failed<br />\n";
		} else {
				foreach ($headers as $val) {
						echo $val . "<br />\n";
				}
		}
		
		//imap_close($mbox);
		
    ?>
    <table border=1>
    <?
    for ($i = 1; $i <= $message_count; ++$i) { 
        $header = imap_header($imap, $i);
        ?>
         <tr>
          <td><?=$header->subject();?></td>
          <td>
        <?
        print_r($header);
        ?>
         </td></tr>
        <?
        $body = trim(substr(imap_body($imap, $i), 0, 100)); 
        $prettydate = date("jS F Y", $header->udate); 

        if (isset($header->from[0]->personal)) { 
            $personal = $header->from[0]->personal; 
        } else { 
            $personal = $header->from[0]->mailbox; 
        } 

        $email = "$personal <{$header->from[0]->mailbox}@{$header->from[0]->host}>"; 
        echo "On $prettydate, $email said \"$body\".\n"; 
    }
    ?>
    </table>
    <?
    imap_close($imap); 
?>
