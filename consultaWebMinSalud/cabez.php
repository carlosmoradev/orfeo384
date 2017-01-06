
    <html>
        <head>
            
            <link rel="stylesheet" href="<?=$ruta_raiz."/estilos/orfeo38"?>/orfeo.css">
            
            <!--Se agregan localmente para no dañar el resto de pagians
            se arregla formato mediante el sisguiente css -->
            <style type="text/css">
            body {
                margin-bottom:0;
                margin-left:0;
                margin-right:0;
                margin-top:0;
                padding-bottom:0;
                padding-left:0;
                padding-right:0;
                padding-top:0; 
            }
            </style>
            
            
            
            <!-- xINICIO Script que crea la sesion y la cierra para el carro de compras-->
            <script language="javascript">                
                function returnKrdF_top(){
                    return '<?=$krd?>';
                };
    
                function nueva(){
                    open('plantillas.php?<?=session_name()."=".session_id()?>', 'Sizewindow', 'width=800,height=600,scrollbars=yes,toolbar=no') 
                } 

            </script>
            <script type="text/javascript" src="<?=$ruta_raiz?>/js/jquery-1.4.2.min.js"></script>            
            <!-- FIN    Script que crea la sesion y la cierra para el carro de compras-->
            
            
            <script language="JavaScript" type="text/JavaScript">

                function cerrar_session() {
		    if (confirm('Seguro de cerrar sesion ?')){
                        <?$fechah = date("Ymdhms"); ?>
                        document.form_cerrar.submit;
                        url="http://www.correlibre.org";	              
			window.location.href=url;
		    }
		}
            </script>
            <script language="JavaScript" type="text/JavaScript">                
                
                function MM_swapImgRestore(){
                    var i,x,a=document.MM_sr; for(i=0;a&&i
                    <a.length&&(x=a[i])&&x.oSrc;i++) x.src=x.oSrc;
                }
                
                function MM_preloadImages(){
                    var d=document; if(d.images){ if(!d.MM_p) d.MM_p=new Array();
                    var i,j=d.MM_p.length,a=MM_preloadImages.arguments; for(i=0; i
                    <a.length; i++)
                    if (a[i].indexOf("#")!=0){ d.MM_p[j]=new Image; d.MM_p[j++].src=a[i];}}
                }
                
                function MM_findObj(n, d){
                    var p,i,x;  if(!d) d=document; if((p=n.indexOf("?"))>0&&parent.frames.length) {
                    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
                    if(!(x=d[n])&&d.all) x=d.all[n]; for (i=0;!x&&i
                    <d.forms.length;i++) x=d.forms[i][n];
                    for(i=0;!x&&d.layers&&i
                    <d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
                    if(!x && d.getElementById) x=d.getElementById(n); return x;
                }
                
                function MM_swapImage(){
                    var i,j=0,x,a=MM_swapImage.arguments; document.MM_sr=new Array; for(i=0;i
                    <(a.length-2);i+=3)
                    if ((x=MM_findObj(a[i]))!=null){document.MM_sr[j++]=x; if(!x.oSrc) x.oSrc=x.src; x.src=a[i+2];}
                }
            </script>

        </head>
        <body topmargin="0" leftmargin="0" onLoad="MM_preloadImages('');MM_preloadImages('');MM_preloadImages('');MM_preloadImages('')">
           
                <table width="100%" border="0" cellpadding="0" cellspacing="0" class="eFrameTop" width="40px">
                    <tr>
                        <td valign="top" >
                            <img name="cabezote_r1_c1" src="../imagenes/logo.gif" width=100px height=40px  border="0" alt="" topmargin=0 top=0>
                        </td>
                        <td background="../imagenes/cabezote_r1_c2.gif">
<div style="position: absolute; top: 0px; left: 260px; heigth:36px; width=50px;"><img width="160" src="../logoEntidad.png"></div>                        
</td>
                        <td width="42" background="../imagenes/salir.gif" onclick="cerrar_session();">                           
                        </td>
                    </tr>
                    <form name=form_cerrar action=index_web.php?<?=session_name()."=".session_id()."&fechah=$fechah&krd=$krd"?> method=post>
</form>

        </body>
    </html>
