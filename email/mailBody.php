<?php
session_start();

$num = $_GET["msgNo"];
//$num = 5;
$imap = imap_open("{poseidon.dnp.ad:995/pop3/ssl/novalidate-cert}", "jlosada", "Jhlc11724");
echo $num . "<hr>";
if( $imap ) {
   
     //Check no.of.msgs
     //$num = imap_num_msg($imap);

     //if there is a message in your inbox
     if( $num >0 ) {
          //read that mail recently arrived
          echo imap_fetchbody($imap, $num,1,FT_UID);
     }

     //close the stream
     imap_close($imap);
}

?>