<?php
/*
 * @$Header: /var/cvsroot/pop3ml/Attic/viewmsg.php,v 1.1.2.19 2010/03/20 07:55:14 cvs Exp $
 */
/*  
    Copyright (C) 2009- Giuseppe Lucarelli <giu.lucarelli@gmail.com>

    This program is free software; you can redistribute it and/or modify
    it under the terms of version 2 of the GNU General Public License as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// error_reporting(E_ALL & ~E_NOTICE);

    require("config.php");
    if (isset($global_options['passwdfile']) && !$_SERVER['PHP_AUTH_USER']) {
        header("WWW-Authenticate: Basic realm=\"Pop3ml\"");
        header("HTTP/1.0 401 Unauthorized");
        exit;
    }
    require(CLASSES_DIR_PATH.DS.'class.viewmsg.php');
    require(CLASSES_DIR_PATH.DS.'class.pop3ml.php');

    $lang = array(
        'en-gb' => array('listname'=>'listname','search'=>'search','subject'=>'subject','sender'=>'sender','header'=>'header','entire message'=>'entire message','date'=>'date','send'=>'send','reset'=>'reset','messages'=>'messages','from'=>'from','body'=>'body','message'=>'message','attachment'=>'attachment','attachments'=>'attachments','view detail'=>'view detail','hide detail'=>'hide detail'),
        'it-it' => array('listname'=>'nome lista','search'=>'cerca','subject'=>'oggetto','sender'=>'mittente','header'=>'header','entire message'=>'intero messaggio','date'=>'data','send'=>'invia','reset'=>'azzera','messages'=>'lista messaggi','from'=>'da','body'=>'corpo','message'=>'messaggio','attachment'=>'allegato','attachments'=>'allegati','view detail'=>'visualizza dettaglio','hide detail'=>'nascondi dettaglio')
        );

    $MyPop3ml = new pop3ml;
    $MyPop3ml->now = strtotime('now');
    $MyPop3ml->dbconn = &$global_options['dbconn'];
    $MyPop3ml->mltable = $global_options['mltable'];
    $MyPop3ml->messages = $global_options['messages'];
    $MyPop3ml->subscribers = $global_options['subscribers'];
    $MyPop3ml->language = false;

    if(empty($_POST['id'])) $_POST['id'] = '';
    if(empty($_POST['mlname'])) $_POST['mlname'] = '';
    if(empty($_POST['search'])) $_POST['search'] = '';
    if(empty($_POST['searchtype'])) $_POST['searchtype'] = '';
    if(empty($_POST['attachments'])) $_POST['attachments'] = '';

    if(!$MyPop3ml->dbconn=@mysql_connect($global_options['databaseHost'], $global_options['databaseUsername'], $global_options['databasePassword'])) {
        die("\ndocument.getElementById('result').value+=unescape('".
            rawurlencode('database connection failed for ['.mysql_error()."]\n").
            "');\n</script>\n");
    }
    if(!@mysql_select_db($global_options['databaseName'],$MyPop3ml->dbconn)) {
        die("\ndocument.getElementById('result').value+=unescape('".
            rawurlencode('select database function failed for ['.mysql_error()."]\n").
            "');\n</script>\n");
    }
    // now test if it's a registered user to grant access
    if(isset($global_options['passwdfile'])) {
        if (!isset($global_options['passwdfile'][$_SERVER['PHP_AUTH_USER']])
        || $_SERVER['PHP_AUTH_PW'] != $global_options['passwdfile'][$_SERVER['PHP_AUTH_USER']]) {
            $global_options['username'] = '';
        } else {
            $global_options['username'] = $_SERVER['PHP_AUTH_USER'];
            $global_options['password'] = $global_options['passwdfile'][$global_options['username']];
        }
        if(!strcmp($global_options['username'],'')) {
            $query = "select *,password('".$_SERVER['PHP_AUTH_PW'].
                "') as oldpw from ".$MyPop3ml->subscribers." where emailaddress = '".$_SERVER['PHP_AUTH_USER']."'";
            $result = mysql_query($query,$MyPop3ml->dbconn);
            if(!$row = mysql_fetch_object($result)) {
                die('Sorry, you are not authorized to access this page');
            }
            if(strcmp($row->webpass,$row->oldpw)) {
                die('Sorry; you are not authorized to access this page');
            }
            $MyPop3ml->userRequest = true;
            $global_options['username'] = $_SERVER['PHP_AUTH_USER'];
        }
    }
    // now check if there is a selected message
    if(strlen($_POST['id']) > 0) {
        $query = "select * from ".$MyPop3ml->messages." where id = ".$_POST['id'];
        $result = @mysql_query($query,$MyPop3ml->dbconn);
        if($row = mysql_fetch_object($result)) {
            $vm = new ViewMsg;
            $vm->message=&$row->message;
            $vm->run();
            if(!empty($vm->attachment)) {
                for($i=0; $i < sizeof($vm->attachment); $i++) {
                    $filename = $vm->isoDecode($vm->attachment[$i]['filename']);
                    if(strlen($_POST['attachmentid']) > 0 && !strcmp(rawurldecode($_POST['attachmentid']),$filename)) {
                        $buffer = (preg_match("/base64/i",$vm->attachment[$i]['Content-Transfer-Encoding']) ?
                                        base64_decode($vm->attachment[$i]['buffer']) :
                                        $vm->attachment[$i]['buffer']);
                        header("Content-Disposition: attachment; filename=\"$filename\"");
                        header("Content-Type: application/force-download");
                        header("Content-Type: application/octet-stream");
                        header("Content-Type: application/download");
                        header("Content-Description: File Transfer");
                        header("Content-Length: " . strlen($buffer));
                        flush(); // this doesn't really matter.

                        echo $buffer;
                        flush(); // this is essential for large downloads
                        die();
                    }
                    $MyPop3ml->attachment .= $filename.',';
                }
                $MyPop3ml->attachment = trim($MyPop3ml->attachment,', ');
            }
            $MyPop3ml->message = $row->message;
        }
        $MyPop3ml->body_result = $vm->body_result;
        @mysql_free_result($result);
        if(!strcmp(@$_POST['viewmode'],'hide')) {
            $jscode = "if(parent && parent.popBody)\n\topener=parent.popBody.parent;\n";
            $jscode .= (@$MyPop3ml->attachment ?
                'opener.attachment = unescape(\''.rawurlencode($MyPop3ml->attachment)."');\nvar mode=1;\n" :
                "var mode = 0;\n");
            $jscode .= "var item = opener.document.getElementById('divattachment').style;\n".
                "if(mode) {\n\titem.visibility = 'visible';\n} else {\n\titem.visibility = 'hidden';\n}\nopener.BuildAttachList()\n";
            die("<script>\n$jscode</script>\n".$MyPop3ml->body_result);
        } else {
            die('<html><body><pre>'.htmlentities($MyPop3ml->message).'</pre></body></html>');
        }
    }

    function BuildMlList() {
        global $MyPop3ml, $global_options, $lang;
        $retval = '';
        $user = '';

        if(!empty($MyPop3ml->userRequest) && $MyPop3ml->userRequest === true) {
            $user = $global_options['username'];
            $query = "select listname,sublist,language from ".$MyPop3ml->mltable.
               " where (sublist regexp '(^|\\n)$user(\\n|$)' or digestsublist regexp '(^|\\n)$user(\\n|$)' or allowsublist regexp '(^|\\n)$user(\\n|$)' or modsublist regexp '(^|\\n)$user(\\n|$)') and (denysublist not regexp '(^|\\n)$user(\\n|$)')";
        } else {
            $query = "select listname,language from ".$MyPop3ml->mltable;
        }
        $result = @mysql_query($query,$MyPop3ml->dbconn);
        while($row = mysql_fetch_object($result)) {
            if((strlen($_POST['mlname']) <= 0 && $MyPop3ml->language === false)
            || !strcmp($_POST['mlname'],$row->listname)) {
                $MyPop3ml->dbrow->language = &$row->language;
                $MyPop3ml->language = $lang[strtolower($MyPop3ml->GetText('LANG',false))];
            }
            $retval .= $row->listname.',';
        }
        @mysql_free_result($result);
        return rtrim($retval,' ,');
    }

    function BuildFieldList() {
        global $MyPop3ml, $global_options;
        $retval = '';
        $user = '';

        if(strlen($_POST['mlname']) <= 0) {
            return '';
        }
        $query = "select id, date, mailfrom, subject, message, header from ".
            $MyPop3ml->messages." where listname = '".$_POST['mlname'].
               "' and (state = 'sent' or state = 'sentdigest')";
        if(strlen($_POST['search']) > 0) {
            $search = $_POST['search'];
            $query .= ' and ';
            switch($_POST['searchtype']) {
                case 'subject':
                    $query .= "subject regexp '$search'";
                    break;
                case 'sender':
                    $query .= "mailfrom regexp '$search'";
                    break;
                case 'header':
                    $query .= "header regexp '$search'";
                    break;
                case 'message':
                    $query .= "message regexp '$search'";
                    break;
                case 'date':
                    $query .= "date regexp '$search'";
                    break;
                default:
                    $query = substr($query,0,-5);
                    break;
            }
        }
        if(strlen($_POST['search']) > 0) {
            $query .= " order by '".$_POST['search']."'";
        }
        $result = @mysql_query($query,$MyPop3ml->dbconn);
        while($row = mysql_fetch_object($result)) {
            //$body = substr($row->message,strpos($row->message,"\r\n\r\n")+4,100);
            $retval .= "fieldlist[fieldlist.length] = new Array('".$row->id."','".rawurlencode($row->mailfrom)."','".
            rawurlencode($row->subject).//"','".rawurlencode($body).
                "','".$row->date."');\n";
        }
        @mysql_free_result($result);
        return $retval;
    }

?>
<html>
<head>
<title>VIEWMSG</title>
<style>
.header { text-align: center; background-color: yellow; border-top: none; border-left: none; border-right: solid 1px black; border-bottom: solid 1px black; width: 150px; }
.field { border-top: none; border-left: none; border-right: solid 1px black; border-bottom: solid 1px black; empty-cells: show; }
.row { cursor: pointer; background-color: #ffffff; border: none; }
</style>
</head>
<script language='javascript'>
    var id='<?php echo $_POST['id'];?>';
    var mllist='<?php echo BuildMlList()?>';
    var fieldlist=new Array();
    var sellist='<?php echo $_POST['mlname']; ?>';
    var searchvalue='<?php echo rawurlencode($_POST['search']); ?>';
    var seloption='<?php echo $_POST['searchtype']; ?>';
    //var attachment='<?php echo (!empty($MyPop3ml->attachment) ? rawurlencode($MyPop3ml->attachment) : ''); ?>';
    var attachment='';
    var selattach='<?php echo rawurlencode($_POST['attachments']); ?>';
    var viewmode='<?php echo $_POST['viewmode']; ?>';
    var lastSelItem = null;
<?php echo BuildFieldList()?>

    function SubmitForm() {
        document.main.id.value = '';
        document.main.target='_self';
        document.main.submit();
    }

    function ShowMessage(item,value) {
        id = document.main.id.value = value;
        document.main.target='popBody';
        document.main.submit();
        if(lastSelItem) {
            lastSelItem.style.color = 'black';
        }
        item.style.color = 'red';
        lastSelItem = item;
    }

    function GetAttach() {
        el = document.main.attachments;
        for(var i=0; i < el.options.length; i++) {
            if(el.options[i].selected) {
                break;
            }
        }
        //var pop=window.open('','popDownload','toolbar=no,menubar=no,resizable=yes,top=0,left=0');
        document.main.attachmentid.value=el[i].value;
        document.main.id.value=id;
        document.main.target='popAttach';
        document.main.submit();
        document.main.attachmentid.value='';
        document.main.target='_self';
    }

    function ViewDetail() {
        if(viewmode == 'hide') {
            viewmode = document.main.viewmode.value = 'view';
            document.getElementById('detailtext').innerHTML = '<?php echo $MyPop3ml->language['hide detail'];?>';
        } else {
            viewmode = document.main.viewmode.value = 'hide';
            document.getElementById('detailtext').innerHTML = '<?php echo $MyPop3ml->language['view detail'];?>';
        }
        document.main.target='popBody';
        document.main.submit();
    }

    function BuildAttachList() {
        var select = document.getElementById('attachments');
        var options = select.getElementsByTagName("option");
        for (var i=0; i < options.length; i++) {
            select.removeChild(options[i]);
        }
        if(attachment.length == 0)
            return;
        token=attachment.split(',');
        for(var i=0; i < token.length; i++) {
            op = document.createElement("option");
            op.setAttribute("value",token[i]);
            text = document.createTextNode(token[i]);
            op.appendChild(text);
            select.appendChild(op);
        }
    }

</script>
<body>
<div>
 <form name='main' id='test' method=POST action='<?php echo $_SERVER['PHP_SELF']; ?>'>
  <input type=hidden name='id'>
  <input type=hidden name='attachmentid'>
  <input type=hidden name='attachmentbuffer' value='<?php echo rawurlencode($_POST['attachmentbuffer'])?>'>
  <input type=hidden name='viewmode' value='hide'>
  <table>
   <tr>
    <td style='font-weight: bold'><?php echo $MyPop3ml->language['listname'];?></td>
    <td>
     <select name='mlname' id='mlname' align=left onChange='javascript:SubmitForm()'>&nbsp;
     <script>
         var token=mllist.split(',');
         for(var i=0; i < token.length; i++) {
             document.writeln("<option value='"+token[i]+"' id='"+token[i]+"'>"+token[i]);
         }
     </script>
     </select>
    </td>
    <td style='font-weight: bold'><?php echo $MyPop3ml->language['search'];?>
    </td>
    <td>
     <input style='width: 150px' type=text name='search' id='search'>&nbsp;
    </td>
    <td>
     <select name='searchtype' id='searchtype' align=left>&nbsp;
      <option value='subject' id='subject'><?php echo $MyPop3ml->language['subject'];?>
      <option value='sender' id='sender'><?php echo $MyPop3ml->language['sender'];?>
      <option value='header' id='header'><?php echo $MyPop3ml->language['header'];?>
      <option value='message' id='message'><?php echo $MyPop3ml->language['entire message'];?>
      <option value='date' id='date'><?php echo $MyPop3ml->language['date'];?>
     </select>
    </td>
    <td>
     <input type=submit value='<?php echo $MyPop3ml->language['send'];?>' onClick="SubmitForm()">
     <input type=reset value='<?php echo $MyPop3ml->language['reset'];?>'>
    </td>
   </tr>
  </table>
 </div>
 <div style="float: left; width: 50%; height: 550px">
  <span style='font-weight: bold'><?php echo $MyPop3ml->language['messages'];?></span>
  <div style='float: left; width: 100%; height: 410px; border: 1px solid black; padding: 0px; overflow: scroll' id='body'>
   <table id="tfieldlist" cellpadding=2 cellspacing=0 border=0>
    <tr class='row' style="background-color: magenta; font-weight: bold">
     <td nowrap class='field'>id</td>
     <td nowrap class='field'> <?php echo $MyPop3ml->language['from']?> </td>
     <td nowrap class='field'> <?php echo $MyPop3ml->language['subject'];?> </td>
     <td nowrap class='field'> <?php echo $MyPop3ml->language['date'];?> </td>
    </td>
    <script>
        var colmode = 0;
        var htmlcode = '';
        for(var i = 0; i < fieldlist.length; i++) {
            htmlcode = "<tr onClick=\"ShowMessage(this,'"+
                      fieldlist[i][0]+"')\" class=\"row\" style=\"background-color: ";
            if(colmode == 0) {
                colmode = 1;
                htmlcode+='#ffffff';
            } else {
                colmode = 0;
                htmlcode+='#96b2d5';
            }
            htmlcode+="\">";
            htmlcode+="<td nowrap class='field'>"+(fieldlist[i][0].length > 0 ? unescape(fieldlist[i][0]) : '&nbsp;')+"</td>";
            htmlcode+="<td nowrap class='field'>"+(fieldlist[i][1].length > 0 ? unescape(fieldlist[i][1]) : '&nbsp;')+"</td>";
            htmlcode+="<td nowrap class='field'>"+(fieldlist[i][2].length > 0 ? unescape(fieldlist[i][2]) : '&nbsp;')+"</td>";
            htmlcode+="<td nowrap class='field'>"+(fieldlist[i][3].length > 0 ? unescape(fieldlist[i][3]) : '&nbsp;')+"</td>";
            htmlcode+="</tr>";
            document.writeln(htmlcode);
        }
    </script>
   </table>
  </div>
 </div>
 <div style='float: right; width: 48%; height: 635px'>
  <span style='font-weight: bold'><?php echo $MyPop3ml->language['message'];?></span>
  &nbsp; &nbsp; &nbsp;<a href='javascript:ViewDetail()'><span id='detailtext'><?php echo (!strcmp($_POST['viewmode'],'hide') ? $MyPop3ml->language['view detail'] : $MyPop3ml->language['hide detail']);?></span></a>
  <div style='float: right; width: 100%; height: 410px; border: 1px solid black; padding: 0px;' id='divBody'>
    <iframe name='popBody' id="popBody" width="100%" height="100%" scrolling="yes" frameborder="0">
    </iframe>
  </div>
  <br clear='all'>
  <div id='divattachment' style='width: 100%; height: 100%; visibility: hidden'>
   <table>
    <tr>
     <td>
      <span style='font-weight: bold'><?php echo $MyPop3ml->language['attachments'];?></span>&nbsp;
     </td>
     <td>
      <select name='attachments' id='attachments' align=left>&nbsp;
       <script>
           /*
           var token=unescape(attachment);
           token=token.split(',');
           for(var i=0; i < token.length; i++) {
               document.writeln("<option value='"+escape(token[i])+"' id='"+escape(token[i])+"'>"+token[i]);
           }
           */
       </script>
      </select>
     </td>
     <td>
      <a href='javascript:GetAttach()'>download</a>
     </td>
    </tr>
   </table>
  </div>
<div style='visibility: hidden'>
 <iframe name='popAttach' id="popAttach" width=10px height=10px frameborder=1>
 </iframe>
</div>
 </form>
</div>
<script>
    if(sellist.length > 0) {
        document.getElementById(sellist).selected = true;
    }
    document.getElementById('search').value = unescape(searchvalue);
    if(seloption.length > 0) {
        document.getElementById(seloption).selected = true;
    }
    document.getElementById('popBody').innerHTML = unescape('<?php echo (!empty($MyPop3ml->body_result) ? rawurlencode($MyPop3ml->body_result) : ''); ?>');
    var message='<?php echo (!empty($MyPop3ml->message) ? rawurlencode($MyPop3ml->message) : ''); ?>';
</script>
</body>
</html>
