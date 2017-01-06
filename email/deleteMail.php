<?php
session_start();
?>
<html>
<head>
<title>WebMail OrfeoGpl.org</title>
<link rel="stylesheet" href="../estilos/orfeo.css" />
</head>
<body>
<center><h1></h1></center>
<hr />
<?php
include("connectPop3.php");

echo " Borrado de Mensaje($eMailMid) <br>".$pop3->DeleteMessage($eMailMid);
$pop3->close();
?>
