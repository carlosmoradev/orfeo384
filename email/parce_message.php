<?php
session_start();
/*
 * browse_mailbox.php
  * @(#) $Header: /home/mlemos/cvsroot/pop3/browse_mailbox.php,v 1.1 2008/01/09 07:36:25 mlemos Exp $
  */

?><html>
<head>
<title>WebMail OrfeoGpl.org</title>
<link rel="stylesheet" href="../estilos/orfeo.css" />
</head>
<body>
<center><h1></h1></center>
<hr />
<?php
    include("connectPop3.php");
    if(!$connect){
    
    }else{
      echo "Error en los datos de Acceso....";
      //include "login_email.php";
      die(" . . .");
    }
    $message_file='pop3://'.$user.':'.$password.'@'.$servidor_mail.'/'.$message.
        '?debug='.$debug.'&html_debug='.$html_debug.'&realm='.$realm.'&workstation='.$workstation.
        '&apop='.$apop.'&authentication_mechanism='.$authentication_mechanism;
    /*
     * Access Gmail POP account
     */
    /*
     $message_file='pop3://'.$user.':'.$password.'@pop.gmail.com:995/1?tls=1&debug=1&html_debug=1';
      */

    $mime=new mime_parser_class;

    /*
     * Set to 0 for not decoding the message bodies
     */
    $mime->decode_bodies = 1;

    $parameters=array(
        'File'=>$message_file,
        
        /* Read a message from a string instead of a file */
        /* 'Data'=>'My message data string',              */

        /* Save the message body parts to a directory     */
        /* 'SaveBody'=>'/tmp',                            */

        /* Do not retrieve or save message body parts     */
           'SkipBody'=>0,
    );
    $success=$mime->Decode($parameters, $decoded);


    if(!$success)
        echo '<h2>MIME message decoding error: '.HtmlSpecialChars($mime->error)."</h2>\n";
    else
    {
        echo '<h2>MIME message decoding successful</h2>'."\n";
        echo '<h2>Message structure</h2>'."\n";
        echo '<pre>';
        //var_dump($decoded[0]);
        echo '</pre>';
        if($mime->Analyze($decoded[0], $results))
        {
            echo '<h2>Message analysis</h2>'."\n";
            echo '<pre>';
            //var_dump($results);
            print_r($results["Headers"]);
            echo '</pre>';
        }
        else
            echo 'MIME message analyse error: '.$mime->error."\n";
    }
?>
<hr />
</body>
</html>