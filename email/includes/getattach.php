<?php
// getattach.php

$filename=rawurldecode($_POST['id']);
$buffer=rawurldecode($_POST['attachment']);
header("Content-Disposition: attachment; filename=\"$filename\"");
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");
header("Content-Description: File Transfer");            
header("Content-Length: " . strlen($buffer));
flush(); // this doesn't really matter.

echo $buffer;
flush(); // this is essential for large downloads
