<?
 $campo1 = $_POST["campo1"];
 $campo2 = $_POST["campo2"];
 $campo3 = $_POST["campo3"];
 $campo4 = $_POST["campo4"];
?>
<FORM ACTION=digitaliza.php method=POST />
C1 <INPUT TYPE=TEXT NAME=campo1 VALUE='<?=$campo1?>' /><br>
C2 <INPUT TYPE=TEXT NAME=campo2 VALUE='<?=$campo2?>' /><br>
C3 <INPUT TYPE=TEXT NAME=campo3 VALUE='<?=$campo3?>' /><br>
C4 <INPUT TYPE=TEXT NAME=campo4 VALUE='<?=$campo4?>' /><br>
<input type=submit value=Generar>
</FORM>
<?
if($campo1){
  ?>
    <script>
      window.open("genBarra.php?campo1=<?=$campo1?>&campo2=<?=$campo2?>&campo3=<?=$campo3?>", "Generacion de barras y QR Pb","toolbar=yes,location=no,resizable=no,height=250,width=450" );
    </script>
  <?
}
