
<html>
<HEAD>
  <STYLE TYPE="text/css">
    #flotante { position: absolute; top:100; left: 550px; visibility: visible;}
  </STYLE>
</HEAD>
<BODY>
<table width="50%"><TR><TD>
<b><font size=3><a href='../radicacion/chequear.php?<?=session_name()?>=<?=session_id()?>&ent=2&eMailMid=<?=$eMailMid?>&eMailAmp=<?=$eMailAmp?>&eMailPid=<?=$eMailPid?>&fileeMailAtach=<?=$fname?>&tipoMedio=eMail' target='formulario'>Radicar Este Correo</a></font></b>
</td><td><b><font size=3><a href='forwardMail.php?<?=session_name()?>=<?=session_id()?>&ent=2&eMailMid=<?=$eMailMid?>&eMailAmp=<?=$eMailAmp?>&eMailPid=<?=$eMailPid?>&fileeMailAtach=<?=$fname?>&tipoMedio=eMail' >Reenviar</a></font></b>
</TD></TR></table>

</BODY>
</html>
