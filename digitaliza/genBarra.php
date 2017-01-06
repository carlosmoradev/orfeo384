<?
 $campo1 = $_GET["campo1"];
 $campo2 = $_GET["campo2"];
 $campo3 = $_GET["campo3"];
 
if($campo1 )
{
?>
<TABLE BORDER=0 WIDTH=90% >
 <TR>
  <TD align=center><?=$campo1?></TD>
 </TR>
 <TR>
  <?
     $campo1Bar39 = "*". trim($campo1) ."*";
  ?>
  <TD  align=center>
    <?=$campo1Bar39?>
  </TD>
 </TR>
<TR>
  <TD align=center>
<?
   $archivo = "../bodega/tmp/cundinamarca_".$campo1.".png";
   $archivo39 = "../bodega/tmp/cundinamarca39_".$campo1.".png";
   $archivo39jpg = "../bodega/tmp/cundinamarca39_".$campo1.".jpg";
   $ins = "qrencode -o $archivo '$campo1'";
   exec($ins, $outputt,$rett);
   $insBarcode = "barcode -o ".$archivo39." -b '$campo1' -e '39' -g 0x235x800";
   $convertB = "convert $archivo39 $archivo39jpg";
   exec($insBarcode, $outputt,$rett);
   exec($convertB, $outputt,$rett);
?>
  <img src=<?=$archivo?> width=200  ><br>
  <img src=<?=$archivo39jpg?> width=700 height=150 >
  </TD>
 </TR>
 
</TABLE>
<?
}
?>