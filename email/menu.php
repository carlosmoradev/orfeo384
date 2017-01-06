<?
session_start();
$krd = $_SESSION["krd"];
$dependencia = $_SESSION["dependencia"];
import_request_variables("gp", "");
?>
<html>
<body>
<?
error_reporting(7);
$ruta_raiz = "..";
//if(!$dependencia or !$krd) include ("../rec_session.php");
 $encabezado = session_name()."=".session_id()."&krd=$krd&fechah=$fechah";
 include "connectIMAP.php";  
 if($msg->getMailboxes($host))
 {
			$listMailBoxes = $msg->getMailboxes($host);
		foreach($listMailBoxes as $name)
		{
			?>
			-<font size=1><a href='emailinbox.php?inboxEmail=<?=$name?>' target='formulario'>
			<?=$name?></a></font><br>
			<?
		}
 }else {
	?>
		<br>
		<font size=1><a href='login_email.php?inboxEmail=<?=$name?>' target='formulario'>
			Inbox</a></font><br>	
		<bR><br>
		<a href='menu.php?inboxEmail=<?=$name?>'>
			Recargar Carpetas</a></font><br>	
	<?
 }
?>

</body>
</html>
