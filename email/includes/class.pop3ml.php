<?php
/*
 * @(#) $Header: /var/cvsroot/pop3ml/includes/Attic/class.pop3ml.php,v 1.18.2.125 2010/04/23 06:08:35 cvs Exp $
 */
/*  pop3ml - php Mailing list/Newsletter manager
    Copyright (C) 2009- Giuseppe Lucarelli <giu.lucarelli@gmail.it>

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
/* Debugging / Contributers:
 * Ron Schoellmann
 * Gregor Buchholz
 * Andrea Tempera
 */
//error_reporting(E_ALL & ~E_NOTICE);

require_once(SCRIPTS_DIR_PATH.DS.'smtp.php');
require_once(SCRIPTS_DIR_PATH.DS.'mime_parser.php');
require_once(SCRIPTS_DIR_PATH.DS.'rfc822_addresses.php');
require_once(SCRIPTS_DIR_PATH.DS."pop3.php");
require_once(SCRIPTS_DIR_PATH.DS.'sasl.php');    /* for gmail */
//--------------------------------------------------------------------------------
// if you want a more complex bounce detection or you if have almost one puclic ML use this extra class (refer to README file)
//--------------------------------------------------------------------------------
if(file_exists(SCRIPTS_DIR_PATH.DS."bounce_driver.class"))
    require_once(SCRIPTS_DIR_PATH.DS.'bounce_driver.class');

class MyPop3 extends pop3_class {
    var $decoded;
}

class Pop3Ml
{
/* public */
    var $mltable = '';
    var $messages = '';
    var $listName = '';
    var $listAddress = '';
    var $listHostName = '';
    var $listPort = '';
    var $listTls = '';
    var $listUser = '';
    var $listPopPass = '';
    var $allow = array();
    var $deny = array();
    var $removeAfterPop = '';
    var $moderatedList = '';
    var $modSublist = '';
    var $listOwner = '';
    var $maxMsgSize = '';
    var $headersChange = array();
    var $logHeader = '';
    var $sublist = '';
    var $digestSublist = '';
    var $trailerFile = '';
    var $forwardMailerTo = false;
    var $maxPop3MsgLimit = false;
    var $expireLock = false;
    var $cacheMessages = false;
    var $cachePath = false;
    var $minTimeResendMsg = false;
    var $scheduledTime = false;
    var $sendDigest = false;
    var $digestMaxMsg = false;
    var $smtpServer = array();
/* private */
    var $now = '';
    var $dbconn = '';
    var $dbrow = '';
    var $pop3='';
    var $decoded='';
    var $smtp='';
    var $smtpIndex='';
    var $error='';
    /**
     * debug variables are used by 'test_pop3ml.php' script too.
     * if you are using that script these variables are set automatically from the web form.
     * for normal use instead you have to set manually their values
     */
    var $debugOutput='';
    var $debug=0;
    var $smtpDebug=0;
    /*
     * the following variable is for normal use only. set its value to '1' for pop3 debug
     */
    var $pop3Debug=0;
    var $bodyTextPlain = '';
    // this variable is used to avoid looping thru parent/children sublist
    var $recursiveList = '';
    var $scheduledPattern = false;
    var $substate = false;
    var $sender = false;
    
    function Init() {
        $this->debugOutput = '';
        //-------------------
        // thanks to Ron and Gregor
        if(!in_array("pop3", stream_get_wrappers())) {
            stream_wrapper_register('pop3', 'pop3_stream');  /* Register the pop3 stream handler class */
        }
        $this->pop3 = new pop3_class;
        //-------------------
        if(!$this->mltable) $this->mltable = 'mltable';
        if(!$this->messages) $this->messages = 'messages';
        if(!$this->queue) $this->queue = 'queue';
        if(!$this->subqueue) $this->subqueue = 'subqueue';
        if(!$this->subscribers) $this->subscribers = 'subscribers';
        $query = "select * from $this->mltable where listname = '".$this->listName."'";
        $command = @mysql_query($query,$this->dbconn);
        if(!$result = @mysql_fetch_object($command)) {
            if($this->debug) {
                $this->debugOutput .= "\nERROR: no ML found, quit\n";
            } else {
                echo "no ML found: quit; ";
            }
            return false;
        }
        $this->dbrow = $result;
        $this->listUser=$this->dbrow->listuser;
        $this->listPopPass=$this->dbrow->listpoppass;
        $this->maxMsgSize=$this->dbrow->msgsize;
        $this->removeAfterPop=$this->dbrow->removeafterpop;
        $this->moderatedList=$this->dbrow->moderatedlist;
        $this->listOwner=$this->dbrow->listowneremail;
        $this->listAddress=$this->dbrow->listaddr;
        $this->trailerFile=str_replace("\r",'',$this->dbrow->trailerfile);
        $this->smtpServer=explode("\n",$this->dbrow->smtpserver);
        // for compatibily with previuos release 0.1
        $this->headersChange=str_replace('::',' ',$this->SetText($this->dbrow->headerchange));
        $this->smtpIndex=0;    // use default smtp server
        if(CACHE_MESSAGES == false)
            $this->cacheMessages = false;
        if(CACHE_PATH != false) {
            $this->cachePath = CACHE_PATH;
        }
        if($this->cachePath && strlen($this->cachePath) > 1 && $this->cachePath[strlen($this->cachePath)-1] != '/') {
            $this->cachePath .= '/';
        }
        if($this->minTimeResendMsg != false)
            $this->minTimeResendMsg = MIN_TIME_RESEND_MSG;
        $this->sendDigest = ($this->dbrow->senddigest ? $this->dbrow->senddigest :
                                    SEND_DIGEST);
        if(DIGEST_MAX_MSG == false) {
            $this->digestMaxMsg = $this->dbrow->digestmaxmsg;
        } else {
            $this->digestMaxMsg = DIGEST_MAX_MSG;
        }
        if(!$this->digestMaxMsg)
            $this->digestMaxMsg = 30;
        if(!$this->scheduledTime) {
            $this->scheduledTime = constant('SCHEDULED_TIME');
        }
        return true;
    }

    function InitSublistFields() {
        $this->dbrow->sublist=str_replace(" ",'',trim($this->dbrow->sublist));
        $this->dbrow->modsublist=str_replace(" ",'',trim($this->dbrow->modsublist));
        $this->dbrow->digestsublist=str_replace(" ",'',trim($this->dbrow->digestsublist));
        $this->dbrow->allowsublist=str_replace(" ",'',trim($this->dbrow->allowsublist));
        $this->dbrow->denysublist=str_replace(" ",'',trim($this->dbrow->denysublist));
        $this->sublist=$this->BuildRecursiveList($this->dbrow->sublist,'sublist',false);
        $this->digestSublist=$this->BuildRecursiveList($this->dbrow->digestsublist,'digestsublist',false);
        $this->modSublist=$this->BuildRecursiveList($this->dbrow->modsublist,'modsublist',false);
        // $this->allow/$this->deny are build as array because every address may contain a REGEXP pattern
        // (ie. '.*' '.*@bmsc.it')
        $this->allow=$this->BuildRecursiveList($this->dbrow->allowsublist,'allowsublist',true);
        $this->deny=$this->BuildRecursiveList($this->dbrow->denysublist,'denysublist',true);
        // if 'parentlist' is set append parent subscribers/allow/digestsublist list as allowed addresses
        // and parent denysublist as disabled addresses
        if(strlen($this->dbrow->parentlist) > 0) {
            $arr = explode(',',$this->dbrow->parentlist);
            foreach($arr as $token) {
                 $token=trim($token);
                 $this->allow=array_merge((is_array($this->allow) ? $this->allow : array()),
                     $this->BuildRecursiveList($token,'sublist',true),
                     // enable it you want allow digest subscribers too
                     // $this->BuildRecursiveList($token,'digestsublist',true),
                     $this->BuildRecursiveList($token,'allowsublist',true));
                 $this->deny=array_merge((is_array($this->deny) ? $this->deny : array()),
                     $this->BuildRecursiveList($token,'denysublist',true));
                 $this->modSublist.="\n".$this->BuildRecursiveList($token,'modsublist',false);
            }
        }
    }

    function BuildRecursiveList($list,$listtype,$asarray = false, $depth = 0) {
        $retval = '';
        $sublist = ($asarray == true ? array() : '');

        if($depth == 0) {
            $this->recursiveList = '';
        }
        if(strlen($list) <= 2)
            return $retval;
        $retval = explode("\n",str_replace("\r",'',trim($list,"\n\r ,;")));
        while(list($key,$value) = each($retval)) {
            // check if address is valid or sublist name
            //if(ereg("[ '\*\^\$\"\\\{\[]",$value)) {    // got REGEXP value from address, jump
            if(preg_match("/[ '\*\^\$\"\\\{\[]/",$value)) {    // got REGEXP value from address, jump
                continue;
            }
            if(strpos($value,'@'))
                continue;
            // got child list, check if already read
            if(strstr($this->recursiveList,$value)) {
                array_splice($retval,$key,1);
                continue;
            }
            $this->recursiveList .= $value."\n";
            $query = "select $listtype from $this->mltable where listname = '$value'";
            if(!$command=@mysql_query($query,$this->dbconn))
                continue;
            // TODO: insert ml status check (if ml is disabled do something)
            if(!$result = @mysql_fetch_row($command))
                continue;
            array_splice($retval,$key,1);
            if(strlen($result[0]) > 7) {
                if($asarray == true) {
                    $sublist = array_merge($sublist,
                            $this->BuildRecursiveList(str_replace(" ",'',$result[0]),$listtype,$asarray,++$depth));
                } else {
                    $sublist .= $this->BuildRecursiveList(str_replace(" ",'',$result[0]),$listtype,$asarray,++$depth)."\n";
                }
            }
            @mysql_free_result($result);
        }
        if($asarray == true) {
            return array_merge($retval,$sublist);
        } else {
            return trim(implode("\n",$retval)."\n".$sublist,"\n");
        }
    }

    //------------------------------------------------------------------------------
    // if smtp server doesn't require pop auth, set to '' pop3_auth_port, user, password)
    // host:port:ssl:[user]:[password]:[tls]
    //------------------------------------------------------------------------------
    function SmtpInit() {
        $this->smtp=new smtp_class;

        $this->smtp->localhost="localhost";
        $this->smtp->direct_delivery=0;
        $this->smtp->timeout=10;
        $this->smtp->data_timeout=0;
        $this->smtp->debug=0;
        $this->smtp->html_debug=0;
        $this->smtp->pop3_auth_host='';
        $this->smtp->realm="";
        $this->smtp->workstation="";
        $this->smtp->authentication_mechanism="";
        $state = strpos($this->smtpServer[$this->smtpIndex],"\t");
        $token = explode(($state === false ? ':' : "\t"),trim($this->smtpServer[$this->smtpIndex]));
        $this->smtp->host_name=$token[0];
        $this->smtp->host_port=$token[1];
        $this->smtp->ssl=$token[2];
        $this->smtp->pop3authport='';
        $this->smtp->user='';
        $this->smtp->password='';
        if(@$token[3]) $this->smtp->pop3authport=$token[3];
        if(@$token[4]) $this->smtp->user=$token[4];
        if(@$token[5]) $this->smtp->password=$token[5];
        if(@$token[6]) $this->smtp->start_tls=$token[6];
    }

    function &SetText(&$text, $useraddress = '') {
        $text = str_replace(array("__LISTADDRESS__","__LISTNAME__","__LISTOWNER__","__LISTHELP__"),
                 array($this->listAddress,$this->listName,$this->listOwner,$this->listAddress),$text);
        if(strlen($useraddress) > 5) {
            $text = str_replace("__USERADDRESS__",$useraddress,$text);
        }
        return $text;
    }

    function ArraySplit(&$item) {
        $retval = array();
        $recipientlimit = ($this->dbrow->recipientlimit ? $this->dbrow->recipientlimit : 0);
        if($recipientlimit <= 0) {
            $retval[] = $item;
            return $retval;
        }
        for($i=0, $x=0; $i < sizeof($item); $i++) {
            if($x == 0) {
                $retval[] = array();
                $split = &$retval[sizeof($retval)-1];
            }
            $split[] = $item[$i];
            if(++$x >= $recipientlimit)
                $x = 0;
        }
        return $retval;
    }

    function Stripos (& $haystack, $needle, $offset = 0) {
        if(function_exists('stripos')) {
            return stripos($haystack,$needle,$offset);
        }
        // PHP 4 doesn't define this function
        preg_match('/'.str_replace('/','\/',$needle).'/i',$haystack,$matches,PREG_OFFSET_CAPTURE,1);
        if(!@$matches || !@$matches[0][1]) {
            return false;
        }
        return $matches[0][1];
    }

    function IsoDecode($data) {
        $retval='';

        if(!preg_match('/=\?.*\?([qb])\?.*\?=/im',$data)) {
            return $data;
        }
        $token = preg_split('/=\?/i',$data);
        foreach($token as $tok) {
            if(strlen($tok) <= 0) {
               continue;
            }
            if(!preg_match('/.*\?([qb])\?(.*)\?=(.*)/im',$tok,$matches,PREG_OFFSET_CAPTURE)) {
                $retval.=$tok;
            }
            if(!strcasecmp(@$matches[1][0],'b')) {
                $retval.=base64_decode($matches[2][0]).$matches[3][0];
            } else if(!strcasecmp(@$matches[1][0],'q')) {
                $retval.=str_replace('_',' ',quoted_printable_decode($matches[2][0])).$matches[3][0];
            }
        }
        return $retval;
    }


    function GetSingleBodyPart(&$mailbody,$part) {
        preg_match('/(.*)(\r|)\nContent-Type: '.str_replace('/','\/',$part).'/im',
            $mailbody,$matches,PREG_OFFSET_CAPTURE,1);
        if(@$matches[0][0]) {
            if($pos=strpos(substr($mailbody,$matches[0][1]),"\r\n\r\n")) {
                $pos+=4;
            } else {
                if($pos=strpos(substr($mailbody,$matches[0][1]),"\n\n")) {
                    $pos+=2;
                } else {
                    return false;
                }
            }
            $pos+=$matches[0][1];
            $nextpart=trim(strtok($matches[0][0],"\r\n"));
            // search for last 'NextPart' of this body part
            $last=strpos(substr($mailbody,$pos),$nextpart);
            return substr($mailbody,$pos,$last);
        }
        return false;
    }

    function ImplodeHeaders($headkey,$token) {
        $retval = '';
        if(!is_array($token)) {
            return $retval;
        }
        foreach($token as $key => $val) {
            if(is_array($val)) {
                $retval.= $this->ImplodeHeaders($key,$val);
            } else if(strlen($val) == 0) {
                continue;
            } else {
                $retval.= (strcmp($headkey,'') ? $headkey : $key).' '.$val."\r\n";
            }
        }
        return $retval;
    }

    function GetReturnPath($address) {
        $retval = $address;

        if(!@$this->decoded[0]['ExtractedAddresses'])
            return $retval;
        $headers = & $this->decoded[0]['ExtractedAddresses'];
        if(array_key_exists('return-path:',$headers)) {
            $retval =  $headers['return-path:'][0]['address'];
        } else if(!@empty($headers['reply-to:'])) {
            $retval =  $headers['reply-to:'][0]['address'];
        }
        if(strlen($retval) == 0 || preg_match('/error|bounce|no(-|_|)reply/i',$retval)) {
            echo " got mailer address, no reply; ";
            if($this->debug) {
                $this->debugOutput .= "\nREPLY-TO ADDRESS ERROR: mailer\n";
            }
            return '';
        } else {
            return $retval;
        }
    }


    function SmtpSend($mailto, $mailsubject, $mailfrom, $mailheader, &$mailbody) {
        $this->SmtpInit();

        if($this->smtp->direct_delivery)
        {
            if(!function_exists("GetMXRR"))
            {
                /*
                * If possible specify in this array the address of at least on local
                * DNS that may be queried from your network.
                */
                $_NAMESERVERS=array();
                include(SCRIPTS_DIR_PATH.DS."getmxrr.php");
            }
            /*
            * If GetMXRR function is available but it is not functional, to use
            * the direct delivery mode, you may use a replacement function.
            */
            /*
            else
            {
                $_NAMESERVERS=array();
                if(count($_NAMESERVERS)==0)
                    Unset($_NAMESERVERS);
                include(SCRIPTS_DIR_PATH.DS."rrcompat.php");
                $this->smtp->getmxrr="_getmxrr";
            }
            */
        }

        if($this->smtpDebug) {
            $this->smtp->debug = $this->smtpDebug;
            $this->debugOutput .= "\n\nSTARTING SMTP:\n";
            ob_start();
        } else if($this->debug) {
            // if this variable is set, this page has been request from a subscribers using 'test_pop3ml.php' script
            if(@$this->userRequest) {
                $this->debugOutput .= "\n\n$mailbody";
            } else {
                $this->debugOutput .= "\nRCPT TO:\n".(is_array($mailto) ? implode("\n",$mailto) : $mailto).
                                      "\n\n$mailheader\n$mailbody";
            }
            if($success=$this->smtp->Connect()) {
                $this->smtp->Disconnect();
                $retval = 'OK.';
                $this->debugOutput .= "\n\nSMTP CONNECTION STATUS: passed\n";
                return $retval;
            } else {
                $retval = $success;
                $this->debugOutput .= "\n\nSMTP CONNECTION STATUS: failed\n";
                return $retval;
            }
        }
        // remove all '\r' from header. they will be set from 'smtp.php'
        $mailheader = trim(str_replace("\r",'',$mailheader));
        if($this->smtp->SendMessage($mailfrom,
                (is_array($mailto) ? $mailto : array($mailto)),
                explode("\n",$mailheader), $mailbody)) {
            $retval = "OK.";
        } else {
            $retval = $this->smtp->error;
        }
        if($this->smtpDebug) {
            $this->debugOutput .= "\n\n".ob_get_contents();
            ob_end_clean();
        } else if(!$this->debug) {
            $this->debugOutput .= "\n\n$retval\n\n";
        }
        return $retval;
    }

    function GetText($def,$returndef = false) {
        $retval = false;

        if(!preg_match('/\[\[:'.$def.'=.*/im',$this->dbrow->language,$matches,PREG_OFFSET_CAPTURE)) {
            return ($returndef ? $def : $retval);
        }
        $start = $matches[0][1]+strlen($def)+4;
        if(!$end = strpos(substr($this->dbrow->language,$matches[0][1]),':]]')) {
            if(!$end = strpos(substr($this->dbrow->language,$pos),'[[:')) {
                return ($returndef ? $def : false);
            } else {
                $end -= -4;
            }
        } else {
            $end -= (strlen($def) + 4);
        }
            $retval = substr($this->dbrow->language,$start,$end);
        return $retval;
    }

    function NotifyUser($address,$digest,&$mailsubject,&$mailbody) {
        $mailheader = '';
        $digesttext = '';

        if($digest) {
            if(!$digesttext=$this->GetText('DIGEST MODE')) {
                $digesttext = "Digest Mode";
            }
        }
        $mailbody = $this->SetText($mailbody);
        $mailbody = str_replace("__DIGESTMODE__", $digesttext, $mailbody);

        // change the first header to send bounced emails/user reply to whatever you want
        // (ie. noreply@domain.com)
        $mailheader.="Return-Path: ".$this->listOwner."\r\n";
        $mailheader.="Reply-To: ".$this->listAddress."\r\n";
        $mailheader.="To: ".str_replace("\n",', ',$address)."\r\n";
        $mailheader.="From: ".$this->listAddress."\r\nSubject: $mailsubject\r\n";
        $mailheader.= "Precedence: bulk\r\n";
        $error = $this->SmtpSend($address, $mailsubject, $this->listAddress, $mailheader, $mailbody);
        if($error != 'OK.') {
            $this->NotifyOwner($address,'User notification failed, ml ['.$this->listAddress.']',
                'Notification failure for ml ['.$this->listAddress.'] for address <'.$address."> smtp error [$error]");
        }
    }

    function NotifySubscriptionRequest($address,$digest,$mailbody) {
        if($digest) {
            if(!$digesttext=$this->GetText('DIGEST MODE')) {
                $digesttext = "Digest Mode";
            }
        }
        $mailsubject = $this->GetText('NOTIFY OWNER SUBSCRIBE MOD',true).'['.$this->listAddress."] <$address>";
            $mailbody = str_replace("__DIGESTMODE__", $digesttext, $mailbody);
        if(!$this->NotifyOwner($address,$mailsubject,$mailbody)) {
            return false;
        }
        return true;
    }

    function SendSubscribeConfirmation($address,$command,$digest = false,$msgcode = false, $modrequest = false) {
        //$subscribe = $this->GetText('SUBSCRIBE',true);
        $confirm = $this->GetText('CONFIRM',true);
        $toggle = $this->GetText('TOGGLE',true);

        $id = md5(uniqid(rand(), true));
        //$mailsubject = 'confirm subscribe to '.$this->listAddress;
        $mailsubject = "$confirm $command $this->listAddress";
        $mailbody = "__SUBSCRIBE__";
        $confirmtext = ($modrequest != false ? $modrequest.'.' : '').
            "$confirm.$command.".($digest ? 'digest.' : '').$this->listName.'.'.$id.'.'.$address;

        if(!$mailbody=$this->GetText(($msgcode != false ? $msgcode : 'SUBSCRIBE CONF'))) {
            $mailbody = "Please confirm subscription for <__SUBSCRIBE__> to <__LISTADDRESS__>  __DIGESTMODE__\r\n\r\nThanks.\r\n";
        }
        $mailbody = str_replace("__SUBSCRIBE__", $confirmtext,
        str_replace('__TOGGLE__',$toggle,$this->SetText($mailbody,$address)));

        if(!$this->debug) {
            $query="insert into $this->subqueue values ('".$this->listName.':'.
                ($modrequest != false ? $modrequest.'-' : '').
                "$address','subscription','$id',now(),'');";
            if(!@mysql_query($query,$this->dbconn)) { // there is another request, replace it
                $query="update ".$this->subqueue.
                " set request = 'subscription', keyvalue = '$id' where code = '".$this->listName.':'.
                ($modrequest != false ? $modrequest.'-' : '').
                "$address';";
                @mysql_query($query,$this->dbconn);
            }
        }
        if($modrequest != false) {
            if(!$this->NotifySubscriptionRequest($address,$digest,$mailbody)) {
                return false;
            }
        } else {
            if(!$this->NotifyUser($address,$digest,$mailsubject,$mailbody)) {
                return false;
            }
        }
    }

    function SendWelcomeMessage($address,$digest, $msgcode = false, $password = '') {
        //$mailsubject = 'WELCOME to '.$this->listAddress;
        $mailsubject = $this->GetText('WELCOME',true).$this->listAddress;
        $mailbody  = '';

        if(!$mailbody=$this->GetText(($msgcode == false ? 'SUBSCRIBE WELCOME' : $msgcode))) {
            $mailbody = "The address <__SUBSCRIBE__> has been added to <__LISTADDRESS__> __DIGESTMODE__\r\n\r\nThanks.\r\n";
        }
        $mailbody = str_replace('__SUBSCRIBE__', $address, $mailbody);
        $mailbody = str_replace('__USERPASS__',
            (strlen($password) > 1 ? "[$password]" : $this->GetText('USERPASS SET')), $mailbody);
            
        if(!$this->NotifyUser($address,$digest,$mailsubject,$mailbody)) {
            return false;
        }
    }

    function ToggleAddress($address) {
        $digest = false;
        if($this->debug) {
            return;
        }
        if(preg_match('/(^|\n)'.$address.'(\n|$)/i',$this->dbrow->sublist)) {
            $this->dbrow->sublist=preg_replace("/(^|\n)$address(\n|$)/",'\1',
                trim($this->dbrow->sublist));
            $this->dbrow->digestsublist=trim($this->dbrow->digestsublist)."\n".$address;
            $digest = true;
        } else {
            $this->dbrow->digestsublist=preg_replace("/(^|\n)$address(\n|$)/",'\1',
                trim($this->dbrow->digestsublist));
            $this->dbrow->sublist=trim($this->dbrow->sublist)."\n".$address;
        }
        $query = 'update '.$this->mltable.' set sublist = \''.trim($this->dbrow->sublist).'\', digestsublist = \''.
            trim($this->dbrow->digestsublist).'\' where listname = \''.$this->listName.'\'';
        if($command = @mysql_query($query,$this->dbconn)) {
            echo "toggled address [$address]\n";
            $this->SendWelcomeMessage($address,$digest,'TOGGLED MESSAGE');
        }
    }

    function SetConfirmedAddress($address, $digest = false) {
        $sql = 'sublist';

        if($this->debug) {
            $this->debugOutput .= "\n\nsubscription confirmed\n\n";
            return;
        }
        if($digest) {
            $this->dbrow->digestsublist=trim($this->dbrow->digestsublist)."\n".$address;
            $sublist = &$this->dbrow->digestsublist;
            $sql = 'digestsublist';
        } else {
            $this->dbrow->sublist=trim($this->dbrow->sublist)."\n".$address;
            $sublist = &$this->dbrow->sublist;
            $sql = 'sublist';
        }
        $query = 'update '.$this->mltable.' set '.$sql.' = \''.
            trim($sublist).'\' where listname = \''.$this->listName.'\'';
        if(!$command = @mysql_query($query,$this->dbconn)) {
            return;
        }
        echo "confirmed subscription [$address]\n";
        // check if subscriber is register yet, otherwise write a new record
        $query = 'select count(*) from '.$this->subscribers." where emailaddress = '$address'";
        if($result = @mysql_query($query,$this->dbconn)) {
            $row = @mysql_fetch_row($result);
            if($row[0] <= 0) {
                require_once(CLASSES_DIR_PATH.DS.'class.genpass.php');
                $password = GenPass::CreatePassword(8);
                $query = 'insert into '.$this->subscribers.
                    " (id,emailaddress,state,webpass,rowlock) values (0,'$address','enabled',password('".
                    $password."'),'')";
                @mysql_query($query,$this->dbconn);
                $this->SendWelcomeMessage($address,$digest,'SUBSCRIBE WELCOME',$password);
            } else {
                $this->SendWelcomeMessage($address,$digest);
            }
            @mysql_free_result($result);
            return;
        }
        $this->SendWelcomeMessage($address,$digest);
    }

    function SendSubscriptionError($address,$digest,$errorcode) {
        if(strlen($address) < 7)
            return;
        $mailsubject = $this->GetText('NOTIFY SUBJECT',true).' '.$this->listAddress;
        $mailheader = '';
        $mailbody  = '';

        if(!$mailbody=$this->GetText($errorcode)) {
            $mailbody = "Subscription error for address <__USERADDRESS__>, list <__LISTADDRESS__> __DIGESTMODE__\r\n\r\n";
        }
        $mailbody = $this->SetText($mailbody,$address);
        $this->NotifyUser($address,$digest,$mailsubject,$mailbody);
        echo " sent subscription error message with code [$errorcode];\n";
    }

    /**
     * if you want a more complex 'SUBSCRIBE' text, you can use a regexp instead of a simple word, for example changing
     * the value of 'SUBSCRIBE' text (mltable->language field) to [[:SUBSCRIBE=(\s+|)(i|)(\s+|)subscribe:]]
     * to identify body messages as 'subscribe' request, with text like: "i subscribe of course......" or simply "subscribe"
     */
    function IsSubscription(&$bodytest, $address, & $substate) {
        $toggle = $this->GetText('TOGGLE',true);
        $subscribe = $this->GetText('SUBSCRIBE',true);
        $confirm = $this->GetText('CONFIRM',true);
        $owner = $this->GetText('OWNER',true);

        $digest = preg_match("/\bdigest\b/i",$bodytest);
        if(preg_match("/^$toggle/i",ltrim($bodytest," \n\r\t*<>"))) {
            if($this->debug) $this->debugOutput .= "\nGot command: TOGGLE\n";
            if(!strcmp($substate,'subscribeddigest')                 // both subscriptions
            || (!strstr($substate,'subscribed') && !strstr($substate,'digest'))) {    // no subscription
                $this->SendSubscriptionError($address,$digest,'TOGGLE ERROR STATE');
                return true;
            }
            if(strcasecmp($this->dbrow->confirmsub,'yes')) {
                $this->ToggleAddress($address);
            } else {
                $this->SendSubscribeConfirmation($address,$toggle,false,'TOGGLE CONF');
                echo "toggle [$address]. require confirmation: ";
            }
            return true;
        //} else if(eregi("^".$subscribe,ltrim($bodytest," \n\r\t*<>"))) {
        } else if(preg_match("/^$subscribe/i",ltrim($bodytest," \n\r\t*<>"))) {
            // got "subscribe" command, check if user already exists
            if($this->debug) $this->debugOutput .= "\nGot command: SUBSCRIBE\n";
            // if user exists in digest mode, ignore request
            if($digest && strstr($substate,'digest')) {
                $this->SendSubscriptionError($address,$digest,'SUBSCRIPTION ERROR STATE');
                return true;
            }
            // if user exists in normal mode, need to toggle subscription
            if($digest && !strcmp($substate,'subscribed')) {
                $this->SendSubscribeConfirmation($address,$subscribe,$digest,'SUBSCRIPTION ERROR MODE');
                return true;
            }
            // if user exists in normal mode, ignore request
            if(!$digest && !strcmp($substate,'subscribed')) {
                $this->SendSubscriptionError($address,$digest,'SUBSCRIPTION ERROR STATE');
                return true;
            }
            // if user exists in digest mode, need to toggle subscription
            if(!$digest && strstr($substate,'digest')) {
                $this->SendSubscribeConfirmation($address,$subscribe,$digest,'SUBSCRIPTION ERROR MODE');
                return true;
            }
            /* no user, normal subscription request */
            if(strcasecmp($this->dbrow->confirmsub,'yes')) {
                $this->SetConfirmedAddress($address,$digest);
            } else {
                echo "subscribe [$address]. require confirmation: ";
                $this->SendSubscribeConfirmation($address,$subscribe,$digest);
            }
            return true;
        }
        // if preg failed it's not a subscription request
        if(!preg_match('/(\b'.$owner."\b|).\b$confirm\b.\b($subscribe|$toggle)\b.".$this->listName.
                "\.([A-Za-z0-9]+)\.(.*)/i", "--".$bodytest, $matches,PREG_OFFSET_CAPTURE)) {
            return false;
        }
        if($this->debug) $this->debugOutput .= "\nGot command: SUBSCRIBE CONFIRMATION\n";
        $originaladdress = false;
        if(strlen($matches[1][0]) > 1 && !strcasecmp($owner,$matches[1][0])) {
            if(!$pos = strpos($matches[4][0],'>')) {
                if(!$pos = strpos($matches[4][0],' ')) {
                    return false;
                }
            }
            $originaladdress = rtrim(substr($matches[4][0],0,$pos));
            $query = "select * from ".$this->subqueue." where code = '".
                $this->listName.":$owner-$originaladdress' and request = 'subscription'";
        } else {
            $query = "select * from ".$this->subqueue." where code = '".
                $this->listName.':'.$address."' and request = 'subscription'";
        }
        $command = @mysql_query($query,$this->dbconn);
        if(!$result = @mysql_fetch_object($command)) {
            return true;
        }
        if($matches[3][0] && strcmp($matches[3][0],$result->keyvalue)) {
            $this->SendSubscriptionError($address,$digest,'SUBSCRIPTION CONFIRM ERROR');
            return true;
        }
        $query = "delete from ".$this->subqueue." where code = '".
            $this->listName.':'.
            ($originaladdress != false ? "$owner-$originaladdress" : $address).
            "' and request = 'subscription'";
        if(!$this->debug) {
            $command = @mysql_query($query,$this->dbconn);
        }
        //if(eregi($toggle,$this->decoded[0]['Body'])) {
        if(preg_match("/$toggle/i",$this->decoded[0]['Body'])) {
            $this->ToggleAddress($originaladdress ? $originaladdress : $address);
            return true;
        }
        //if(!strcasecmp($this->dbrow->subscriptionmod,'yes') && !eregi($owner,$matches[0][0])) {
        if(!strcasecmp($this->dbrow->subscriptionmod,'yes') && !preg_match("/$owner/i",$matches[0][0])) {
            echo "subscription confirmed [$address]. require owner confirmation: ";
            $this->SendSubscribeConfirmation($address,$subscribe,$digest,'MOD SUBSCRIBE CONF',$owner);
            return true;
        }
        $this->SetConfirmedAddress($originaladdress ? $originaladdress : $address,$digest);
        return true;
    }

    function SendUnsubscribeConfirmation($address, $digest = false, $msgcode = false) {
        $unsubscribe = $this->GetText('UNSUBSCRIBE',true);
        $confirm = $this->GetText('CONFIRM',true);
        $toggle = $this->GetText('TOGGLE',true);
        $id = md5(uniqid(rand(), true));
        $mailsubject = "$confirm $unsubscribe $this->listAddress";
        $mailbody = "__UNSUBSCRIBE__";
        $confirmtext = "$confirm.$unsubscribe.".($digest ? 'digest.' : '').$this->listName.'.'.$id.'.'.$address;

        if(!$mailbody=$this->GetText(($msgcode != false ? $msgcode : 'UNSUBSCRIBE CONF'))) {
            $mailbody = "Please confirm unsubscription for <__SUBSCRIBE__> to <__LISTADDRESS__>  __DIGESTMODE__\r\n\r\nThanks.\r\n";
        }
        $mailbody = str_replace("__UNSUBSCRIBE__", $confirmtext, 
                str_replace("__TOGGLE__", $toggle, $this->SetText($mailbody,$address)));

        if(!$this->debug) {
            $query="insert into $this->subqueue values ('".$this->listName.":$address','unsubscription','$id',now(),'');";
            if(!@mysql_query($query,$this->dbconn)) { // there is another request, replace it
                $query="update ".$this->subqueue." set request = 'unsubscription', keyvalue = '$id' where code = '".
                $this->listName.":$address';";
                @mysql_query($query,$this->dbconn);
            }
        }
        if(!$this->NotifyUser($address,$digest,$mailsubject,$mailbody)) {
            return false;
        }
    }

    function SendGoodbyeMessage($address,$digest) {
        $mailsubject = $this->GetText('GOODBYE',true).$this->listAddress;
        $mailheader = '';
        $mailbody  = '';

        if(!$mailbody=$this->GetText('UNSUBSCRIBE GOODBYE',true)) {
            $mailbody = "The address <__USERADDRESS__>, has been removed from <__LISTADDRESS__> DIGESTMODE__\r\n\r\nThanks.\r\n";
        }
        $mailbody = $this->SetText($mailbody,$address);
        $this->NotifyUser($address,$digest,$mailsubject,$mailbody);
    }

    function SendUnsubscriptionError($address,$digest,$errorcode) {
        $mailsubject = $this->GetText('NOTIFY SUBJECT',true).' '.$this->listAddress;
        $mailheader = '';
        $mailbody  = '';
        //$digesttext = '';

        if(!$mailbody=$this->GetText($errorcode)) {
            $mailbody = "The address <__USERADDRESS>, is subscribed to list <__LISTADDRESS__> __DIGESTMODE__\r\n\r\nThanks.\r\n";
        }
        $mailbody = str_replace('__TOGGLE__',$this->GetText('TOGGLE',true),$this->SetText($mailbody,$address));
        $this->NotifyUser($address,$digest,$mailsubject,$mailbody);
        echo " sent unsubscription error message;\n";
    }

    function UnsetRegisteredAddress($address, $digest = false) {
        $sql = 'sublist';

        if($this->debug) {
            $this->debugOutput .= "\n\nUnsubscription confirmed\n\n";
            return;
        }
/** DISABLED ***********************************
 * if you want to remove both "unsubscription" requests (normal mode/digest mode), enable rows below
        $this->dbrow->digestsublist=preg_replace("/(^|\n)$address(\n|$)/",'\1',
            trim($this->dbrow->digestsublist));
        $this->dbrow->sublist=preg_replace("/(^|\n)$address(\n|$)/",'\1',trim($this->dbrow->sublist));
        $query = 'update '.$this->mltable.' set sublist= \''.
            trim($this->dbrow->sublist).'\', digestsublist = \''.
            trim($this->dbrow->digestsublist).'\' where listname = \''.$this->listName.'\'';
************************************************/
        if($digest) {
            $this->dbrow->digestsublist=preg_replace("/(^|\n)$address(\n|$)/",'\1',
                trim($this->dbrow->digestsublist));
            $sublist = &$this->dbrow->digestsublist;
            $sql = 'digestsublist';
        } else {
            $this->dbrow->sublist=preg_replace("/(^|\n)$address(\n|$)/",'\1',trim($this->dbrow->sublist));
            $sublist = &$this->dbrow->sublist;
            $sql = 'sublist';
        }
        $query = 'update '.$this->mltable.' set '.$sql.' = \''.
            trim($sublist).'\' where listname = \''.$this->listName.'\'';
        if($command = @mysql_query($query,$this->dbconn)) {
            echo "confirmed unsubscription [$address]\n";
            $this->SendGoodbyeMessage($address,$digest);
        }
    }

    function IsUnsubscription(& $bodytest, $address, & $substate) {
        $error = '';

        $unsubscribe = $this->GetText('UNSUBSCRIBE',true);
        $confirm = $this->GetText('CONFIRM',true);

        //$digest = eregi("digest",preg_replace("/\n|\r|=/",'',$bodytest));
        $digest = preg_match("/\bdigest\b/i",$bodytest);
        //if(eregi("^".$unsubscribe,preg_replace("/\n|\r|=/",'',$bodytest))) {
        if(preg_match("/^$unsubscribe/i",preg_replace("/\n|\r|=/",'',$bodytest))) {
            if($this->debug) $this->debugOutput .= "\nGot command: UNSUBSCRIBE\n";
            // if user exists in normal mode, need to toggle subscription
            if($digest && !strcmp($substate,'subscribed')) {
                $this->SendUnsubscriptionError($address,$digest,'UNSUBSCRIPTION ERROR STATE');
                return true;
            }
            // if user doesn't exist in digest mode, ignore request
            if($digest && !strstr($substate,'digest')) {
                $this->SendUnsubscriptionError($address,$digest,'UNSUBSCRIPTION ERROR STATE');
                return true;
            }
            // if user exists in digest mode, need to toggle subscription
            if(!$digest && strstr($substate,'digest')) {
                $this->SendUnsubscriptionError($address,$digest,'UNSUBSCRIPTION ERROR MODE');
                return true;
            }
            // if user doesn't exist in normal mode, ignore request
            if(!$digest && strcmp($substate,'subscribed')) {
                $this->SendUnsubscriptionError($address,$digest,'UNSUBSCRIPTION ERROR STATE');
                return true;
            }
            // got user, normal unsubscription request
            if(strcasecmp($this->dbrow->confirmunsub,'yes')) {
                $this->UnsetRegisteredAddress($address,$digest);
            } else {
                echo "unsubscribe [$address]. require confirmation\n";
                $this->SendUnsubscribeConfirmation($address,$digest);
            }
            return true;
        }
        $query = "select * from ".$this->subqueue." where code = '".
            $this->listName.':'.$address."' and request = 'unsubscription'";
        $command = @mysql_query($query,$this->dbconn);
        if(!$result = @mysql_fetch_object($command)) {
            return false;
        }
        $error = "[$address] unsubscription request pending, ";
        echo $error;
        $this->debugOutput .= "\n\n".$error;
        //if(!eregi($confirm.'.*'.$unsubscribe.'.*'.$this->listName.'.*'.$result->keyvalue.'.*'.$address,
        if(!preg_match('/'.$confirm.'.*'.$unsubscribe.'.*'.$this->listName.'.*'.$result->keyvalue.'.*'.$address.'/i',
                preg_replace("/\n|\r|=/",'',$this->decoded[0]['Body']))) {
            if($this->debug) $this->debugOutput .= "\nGot unsubscription error: $error\n";
            $this->SendUnsubscriptionError($address,$digest,'UNSUBSCRIPTION PENDING');
            return true;
        }
        if(!$this->debug) {
            $query = "delete from ".$this->subqueue." where code = '".
                $this->listName.':'.$address."' and request = 'unsubscription'";
            $command = @mysql_query($query,$this->dbconn);
        }
        $this->UnsetRegisteredAddress($address,$digest);
        return true;
    }

    function RemoveMessage($message) {
        $this->error = '';
    
        if(!$this->debug) {
            $this->error=$this->pop3->DeleteMessage($message);
        }
        return true;
    }

    function NotifyMessageModeration($address,$scheduledTime) {
        $id = md5(uniqid(rand(), true));
        $mailheader = '';
        $mailsubject = $this->GetText('NOTIFY OWNER MESSAGE MOD',true)."[$this->listAddress] <$address>";
        $confirmtext =
            ($scheduledTime ? str_replace(' ','_',$this->scheduledTime.": $scheduledTime.") : '').
            $this->GetText('CONFIRM',true).'.'.
            $this->GetText('MESSAGE',true).".$this->listName.$id.$address";

        if(!$mailbody=$this->GetText('MOD MESSAGE CONF')) {
            $mailbody = "Please confirm message moderation for <__SUBSCRIBE__> to <__LISTADDRESS__>\r\n\r\nThanks.\r\n";
        }
        $mailbody = str_replace("__MESSAGEID__", $confirmtext, $this->SetText($mailbody));

        echo 'message moderation for ml owner from <'.$address.">;\n";
        $boundary='----NextPart'.md5(date('r', time()));
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/report; boundary=".
            $boundary."\r\n";
        $mailheader.='Subject: '.$mailsubject."\r\n";
        $mailbody ='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            $mailbody."\r\noriginal message:\r\n\r\n";
        $mailbody.='--'.$boundary."\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg.eml\"\r\n\r\n".str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers']))."\r\n".str_replace("'","\\'",$this->decoded[0]['Body'])."\r\n\r\n".'--'.$boundary."--\r\n\r\n";
        $error = $this->SmtpSend(explode("\n",$this->modSublist), $mailsubject, $this->listAddress, $mailheader, $mailbody);
        if($error != 'OK.') {
            echo "owner notify failed [$error]";
            return;
        }
        if($this->debug) {
            return;
        }
        $query = "insert into ".$this->messages.
            " (id,date,state,listname,smtp,mailfrom,subject,message,header,keyvalue,rowlock) values(0,now(),'".
            "pending','$this->listName','$this->smtpIndex','$address','".
            str_replace("'","\\'",$this->IsoDecode($this->decoded[0]['Headers']['subject:']))."','".
            str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers']).
            "\r\n\r\n".$this->decoded[0]['Body'])."','".
            str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers']))
            // enable it if you want the md5 value into header too
            // ."X-OriginalModerationID: $id\r\n'
            ."','$id','')";
        if(!$command = @mysql_query($query,$this->dbconn)) {
            echo "inserting message into table failed; [".mysql_error()."]\n";
        }
    }

    function NotifyOwner($address, $mailsubject = false, $mailbody = '') {
        $id = md5(uniqid(rand(), true));
        $mailheader = '';
        if(!$mailsubject)
            $mailsubject = 'Owner request for ['.$this->listName.'] from <'.$address.'>';

        if(strlen($mailbody) <= 0)
            $mailbody = $mailsubject;
        echo 'message for ml owner from <'.$address.">;\n";
        $boundary='----NextPart'.md5(date('r', time()));
        // change the first header to not send bounced emails to list again and avoid looping
        // i'm using the same domain of pop3 ml account
        // you can also use an empty value for it:
        // Return-Path:
        $mailheader.="Return-Path: no-reply".substr($this->listAddress,strpos($this->listAddress,'@')).
            "\r\nPrecedence: bulk\r\n";
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/report; boundary=".
            $boundary."\r\n";
        $mailheader.='Subject: '.$mailsubject."\r\n";
        $mailbody ='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            $mailbody."\r\n\r\noriginal message:\r\n\r\n";
        $mailbody.='--'.$boundary.
            "\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg.eml\"\r\n\r\n".
            str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers']))."\r\n".
            str_replace("'","\\'",$this->decoded[0]['Body'])."\r\n\r\n".'--'.$boundary."--\r\n\r\n";
        $error = $this->SmtpSend($this->listOwner, $mailsubject, $this->listAddress, $mailheader, $mailbody);
        if($error != 'OK.') {
            echo "owner notify failed [$error]";
            return;
        }
    }

    function NotifyBounce($messageid,$bounceerror) {
        $id = md5(uniqid(rand(), true));
        $mailheader = '';
        $mailbody = '';
        $mailsubject = 'Delivery Status Notification (Failure) for ['.$this->listName.']';

        echo "bouncing for message [$messageid] errors [$bounceerror];\n";
        $query = "select * from $this->messages where id = $messageid";
        if(!$result = @mysql_query($query,$this->dbconn)) {
            return;
        }
        if(!$row = @mysql_fetch_object($result)) {
            return;
        }
        $boundary='----NextPart'.md5(date('r', time()));
        $mailheader.="Return-Path: no-reply".substr($this->listAddress,strpos($this->listAddress,'@')).
            "\r\nPrecedence: bulk\r\n";
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/report; boundary=".
            $boundary."\r\n";
        $mailheader.='Subject: '.$mailsubject."\r\n";
        $mailbody.='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            "bounce error for:\r\n\r\n$bounceerror\r\n\r\n";
        $mailbody.='--'.$boundary.
            "\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg.eml\"\r\n\r\n".
            $row->message."\r\n\r\n";
        $mailbody.='--'.$boundary."--\r\n\r\n";
        $error = $this->SmtpSend($this->listOwner, $mailsubject, $this->listAddress, $mailheader, $mailbody);
        if($error != 'OK.') {
            echo "owner notify failed [$error]";
            return;
        }
        @mysql_free_result($result);
    }

    function BounceMessage($sender,$badaddress,$action='Failed',$status='5.1.1',$diagcode='550 5.1.1 User unknown') {
        $id = md5(uniqid(rand(), true));
        $mailheader = '';
        $mailbody = '';
        $mailsubject = 'Delivery Status Notification (Failure) for ['.$this->listName.']';

        $boundary='----NextPart'.md5(date('r', time()));
        $mailheader.="Return-Path:\r\nPrecedence: bulk\r\n";
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/report; boundary=".
            $boundary."\r\n";
        $mailheader.='Subject: '.$mailsubject."\r\n";
        $mailbody.='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            "bounce error:\r\n\r\nSender: $sender\r\nAddress: $badaddress\r\nAction: $action\r\nStatus: $status\r\nDiagnostic code: $diagcode\r\n\r\n";
        $mailbody.='--'.$boundary.
            "\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg.eml\"\r\n\r\n".
            $this->ImplodeHeaders('',$this->decoded[0]['Headers'])."\r\n\r\n".$this->decoded[0]['Body']."\r\n\r\n";
        $mailbody.='--'.$boundary."--\r\n\r\n";
        $error = $this->SmtpSend($this->listOwner, $mailsubject, $this->listAddress, $mailheader, $mailbody);
        if($error != 'OK.') {
            echo "owner notify failed [$error]";
            return;
        }
        if($this->debug) {
            return;
        }
        /*
        * check if there are queued messages with this badaddress
        */
        $query = "select * from $this->queue where addresses REGEXP '(^|,)$badaddress(,|$)'";
        if(!$result = @mysql_query($query,$this->dbconn)) {
            return;
        }
        while($row = @mysql_fetch_object($result)) {
            /*
            * if there is badaddress only, remove queuerecord and set message state to 'sent'
            */
            if(!strcmp(trim($row->addresses),$badaddress)) { 
                @mysql_query('update '.$this->messageTable.
                    ' set state = \'sent\' where id = '.$row->messageid,$this->dbconn);
                @mysql_query('delete from '.$this->queue.
                    ' where id = '.$row->id,$this->dbconn);
                continue;
            }
            /*
            * there are more then one address, remove it and update queue record
            */
            $row->addresses = preg_replace("/(^|,)$badaddress(,|$)/",'\1',$row->addresses);
            $row->addresses = trim($row->addresses,"\r\n, ");
            @mysql_query('update '.$this->queue." set addresses = '$row->addresses'",$this->dbconn);
        }
        @mysql_free_result($result);
    }

    function CheckBounce() {
        $address = & $this->decoded[0]['ExtractedAddresses']['from:'][0]['address'];
        if(!$address)
            return;
        $mailbody= & $this->decoded[0]['Body'];
        // check for gmail first
        if(preg_match('/(google|gmail)/i',$address)) {
            if($pos=$this->Stripos($mailbody,"Delivery to the following recipient failed permanently:")) {
                $pos+=57;    // text plus "\r\n" or "\n\n"
                if(!$end=strpos(substr($mailbody,$pos+4),"\r\n\r\n")) {
                    if(!$end=strpos(substr($mailbody,$pos+4),"\n\n")) {
                        return;    // no match
                    }
                }
                $badaddress = trim(substr($mailbody,$pos,$end+4));
                $this->BounceMessage($address,$badaddress,'failed','5.1.1','550 5.1.1 User unknown');
                return;
            }
        }
        //$this->header = & $this->decoded[0]
        if(!$pos = $this->stripos($mailbody,'Content-Type: message/delivery-status')) {
            return;
        }
        if($this->decoded[0]['Headers']['content-type:']
        //&& eregi('multipart\/report.*report-type=delivery-status',$this->decoded[0]['Headers']['content-type:'])) {
        && preg_match('/multipart\/report.*report-type=delivery-status/i',$this->decoded[0]['Headers']['content-type:'])) {
            if(preg_match('/Final-Recipient:[[:space:]]+(rfc822;|)[[:space:]]+.*(\r|\n)/i',
                        substr($mailbody,$pos),$matches,PREG_OFFSET_CAPTURE)) {
                $pos = strrpos($matches[0][0]," ");
                $badaddress = trim(substr($matches[0][0],$pos+1));
                $pos = $matches[0][1];
                if(preg_match('/Action:[[:space:]]+.*(\r|\n)/i',
                        substr($mailbody,$pos),$matches,PREG_OFFSET_CAPTURE)) {
                    $action = trim(substr($matches[0][0],strrpos($matches[0][0]," ")+1));
                }
                if(preg_match('/Status:[[:space:]]+.*(\r|\n)/i',
                        substr($mailbody,$pos),$matches,PREG_OFFSET_CAPTURE)) {
                    $status = trim(substr($matches[0][0],strrpos($matches[0][0]," ")+1));
                }
                if(preg_match('/Diagnostic-Code:.*(\r|\n)/i',
                        substr($mailbody,$pos),$matches,PREG_OFFSET_CAPTURE)) {
                    $diagcode = trim(substr($matches[0][0],strlen('Diagnostic-Code: ')));
                }
                if(strlen($badaddress) >= 10) {
                    echo "BOUNCE RESULT [badaddress: $badaddress]; ";
                    $this->BounceMessage($address,$badaddress,
                        ($action ? $action : ''),
                        ($status ? $status : ''),
                        ($diagcode ? $diagcode : ''));
                }
            }
        }
    }

    /**
     * PASSWORD command to set subscribers password for web access to all ML email messages
     * syntax: password NewPassword OldPassword        (to change/set a password value)
     */
    function SetUserPassword($address, $body) {
        $list = null;    // array for messages list
        $get = $this->GetText('GET',true);
        $mailsubject = $this->GetText('NOTIFY SUBJECT', true)." $this->listAddress";
        $mailbody = $this->GetText('GENERIC ERROR',true);

        $cmd = trim(strtok($body," \r\n\t"));
        $newpw = trim(strtok(' '));
        $oldpw = trim(strtok(" \r\n\t"));
        $query = "select *,password('$oldpw') as oldpw from ".$this->subscribers." where emailaddress = '$address'";
        $result = @mysql_query($query,$this->dbconn);
        if(!$row = @mysql_fetch_object($result)) {
            $row->webpass = null;
        }
        $pwmatch = strcmp($row->webpass,$row->oldpw);
        if($pwmatch == 0) {    // match with old password
            if($row->webpass === null) {
                $query = 'insert into '.$this->subscribers.
                    " (id,emailaddress,state,webpass,rowlock) values (0,'$address','enabled',password('".$newpw."'),'')";
            } else {
                $query = 'update '.$this->subscribers." set webpass = password('$newpw') where emailaddress = '$address'";
            }
            if(!$this->debug) {
                $result = @mysql_query($query,$this->dbconn);
            } else {
                $result = true;
            }
            if($result) {
                $mailbody = $this->GetText('USERPASS CHANGED',true)."\n$newpw";
            }
            echo "changed password for [$address]; ";
        } else {
            $mailbody = $this->GetText('USERPASS WRONG',true);
            echo "wrong password for [$address] changing request; ";
        }
        @mysql_free_result($result);
        $this->NotifyUser($address,false,$mailsubject,$mailbody);
    }

    /**
     * GET command:
     * get 1        // to retrieve message 1
     * get 1,2,3        // to retrieve 1,2 and 3 message
     * get 1,2,5-10,11    // to retrieve 1,2,from 5 to 10 and 11 message
     */
    function GetCommand($address, $body) {
        $list = null;    // array for messages list
        $get = $this->GetText('GET',true);

        $body = trim($body,"\n\r ,.<>");
        $bodytest = trim(substr($body,strlen($get)+1),"\n\r ,.<>");

        if(strlen($bodytest) < 1)
            return;
        if(strstr($bodytest,',')) {        // request for specific message(s) ie. 'get 1,4,10'
            $list = explode(',',$bodytest);
        } else {
            $list[] = &$bodytest;
        }
        // retrieve all 'sent' messages and 'queued' owned by request sender
        $query = "select * from $this->messages where (state = 'sent' || (state = 'queued' && mailfrom = '".
            $address."')) and (";
        foreach($list as $request) {
            $token = explode('-',$request);
            $query .=
                (sizeof($token) == 1 ?
                    ('id = '.$token[0])
                :
                    ('(id >= '.$token[0].' and id <= '.$token[1]).')');
            $query .= ' or ';
        }
        if(!$result=@mysql_query(substr($query,0,-4).')',$this->dbconn)) {
            return false;
        }
        $mailsubject = 'Subject: '.$this->listName.': '.$body;
        $mailheader = '';
        $mailbody = '';
        $boundary = '';
        $messagelist = '';

        // foreach($this->headersChange as $hk => $hv) { if(strcmp($hv,'')) { $mailheader.= $hk.': '.$hv."\r\n"; } }
        $mailheader.= "Return-Path: ".$this->listOwner."\r\n";
        $mailheader.= "Precedence: bulk\r\n";
        $boundary='----NextPart'.md5(date('r', time()));
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/digest; boundary=".
            $boundary."\r\n";

        $mailbody.='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            $this->listName." $body:\r\n\r\n";

        echo 'get request response for # '.$body."; ";
        while($row = @mysql_fetch_object($result)) {
            $mailbody.="Id: ".$row->id."\r\nSender: ".$row->mailfrom."\n\nSubject: ".$row->subject."\r\n";
            $messagelist.='--'.$boundary.
                "\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg".$row->id.".
                eml\"\r\n\r\n".$row->message."\r\n\r\n";
            $mailbody.="\r\n\r\n";
        }
        @mysql_free_result($result);
        $mailheader.=$mailsubject;
        $mailbody.=$messagelist.'--'.$boundary."--\r\n\r\n";
        unset($messagelist);
        $error = $this->SmtpSend($address,$mailsubject,$this->listAddress,$mailheader,$mailbody);
        if($error != 'OK.') {
            echo " get-error [$error] for [$mailaddress];";
        }
        return;
    }

    /**
     * @return ''         no scheduled mails
     * @return            schedules mail list
     */
    function GetScheduledMails(& $address) {
        $retval = "\r\n";
        $query = "select id,date,subject from $this->messages where state = 'queued' and time_to_sec(timediff(now(),date)) < 0 order by date";
        if(!$result=@mysql_query($query,$this->dbconn)) {
            return $retval;
        }
        while($row = @mysql_fetch_object($result)) {
            $retval.="Id: ".$row->id." - ".$row->date." - ".$row->subject."\r\n";
        }
        return $retval;
    }

    /**
     * SCHEDDROP command:
     * SCHEDDROP 1        drop scheduled message with id = 1
     */
    function DropScheduledMail($address, $body) {
        $list = null;    // array for messages list
        $command = $this->GetText('SCHEDDROP',true);
        $retval = $body."\r\n\r\n".$this->GetText('SCHEDULED DROP RESULT',true);

        if($this->debug) {
            return $retval." [".$this->GetText('DONE',true)."]\r\n";
        }
        $bodytest = trim($body,"\n\r ,.<>");
        $bodytest = trim(substr($body,strlen($command)+1),"\n\r ,.<>");
        $query = "delete from $this->messages where state = 'queued' and id = $bodytest";
        mysql_query($query,$this->dbconn);
        if(mysql_affected_rows($this->dbconn) <= 0) {
            return $retval."\r\n\r\n".$this->GetText('GENERIC ERROR',true);
        }
        $query = "delete from $this->queue where messageid = $bodytest";
        mysql_query($query,$this->dbconn);
        if(mysql_affected_rows($this->dbconn) <= 0) {
            return $retval."\r\n\r\n".$this->GetText('GENERIC ERROR',true);
        }
        return $retval." [".$this->GetText('DONE',true)."]\r\n";
    }

    // TODO: insert 'LIST' command to list all messages
    function CheckMlCommand($address, & $substate) {
        if(!$bodytest=$this->GetSingleBodyPart($this->decoded[0]['Body'],'text/plain')) {
            $bodytest = $this->decoded[0]['Body'];
        }
        $bodytest = ltrim($bodytest,"\r\n <>");
        //if(eregi("^".$this->GetText('GET',true),$bodytest)) {
        if(preg_match("/^".$this->GetText('GET',true)."/i",$bodytest)) {
            if($this->debug) $this->debugOutput .= "\nGot command: GET\n";
            $this->GetCommand($address,$bodytest);
            return true;
        //} else if(eregi("^".$this->GetText('NOTIFY OWNER',true),$bodytest)) {
        } else if(preg_match("/^".$this->GetText('NOTIFY OWNER',true)."/i",$bodytest)) {
            if($this->debug) $this->debugOutput .= "\nGot command: NOTIFY OWNER\n";
            $this->NotifyOwner($address);
            return true;
        //} else if(eregi("^".$this->GetText('HELP',true),$bodytest)) {
        } else if(preg_match("/^".$this->GetText('HELP',true)."/i",$bodytest)) {
            if($this->debug) $this->debugOutput .= "\nGot command: HELP\n";
            $this->NotifyUser($address,false,
                $this->GetText('HELP SUBJECT',true),
                str_replace('X-Scheduled',$this->scheduledTime,$this->GetText('HELP MESSAGE',true)));
            return true;
        //} else if(eregi("^PASSWORD",$bodytest)) {
        } else if(preg_match("/^PASSWORD/i",$bodytest)) {
            if($this->debug) $this->debugOutput .= "\nGot command: PASSWORD\n";
            $this->SetUserPassword($address,$bodytest);
            return true;
        //} else if(eregi("^".$this->GetText('SCHEDLIST',true),$bodytest)) {
        } else if(preg_match("/^".$this->GetText('SCHEDLIST',true)."/i",$bodytest)) {
            if($this->debug) $this->debugOutput .= "\nGot command: SCHEDLIST\n";
            echo "got command: SCHEDLIST; ";
            if(strcasecmp($substate,'mailer') && strcasecmp($substate,'deny')) {
                $mailsubject = $this->GetText('NOTIFY SUBJECT',true).$this->listAddress;
                $mailbody = $this->GetText('SCHEDULED MAIL LIST',true).$this->GetScheduledMails($address);
                $this->NotifyUser($address,false,$mailsubject,$mailbody);
                return true;
            }
        //} else if(eregi("^".$this->GetText('SCHEDDROP',true),$bodytest)) {
        } else if(preg_match("/^".$this->GetText('SCHEDDROP',true)."/i",$bodytest)) {
            if($this->debug) $this->debugOutput .= "\nGot command: SCHEDDROP\n";
            echo "got command: SCHEDDROP; ";
            if(strcasecmp($substate,'mailer') && strcasecmp($substate,'deny')) {
                $mailsubject = $this->GetText('NOTIFY SUBJECT',true).$this->listAddress;
                $mailbody = $this->DropScheduledMail($address,$bodytest);
                $this->NotifyUser($address,false,$mailsubject,$mailbody);
                return true;
            }
        }
        $bodytest = preg_replace("/\n|\r|=/",'',$bodytest);
        if($this->IsSubscription($bodytest, $address,$substate) == true) {
            return true;
        }
        if($this->IsUnsubscription($bodytest, $address,$substate) == true) {
            return true;
        }
        // no ml command
        $this->bodyTextPlain = & $bodytest;
        return false;
    }

    /**
     *  return values:
     *  'deny' -> no access to ML
     *  'subscribed' -> address exists into 'sublist' field
     *  'digest' -> address exists into 'digestsublist' field
     *  'subscribeddigest' -> address exists into both 'sublist/digestsublist' fields
     *  'allow' -> address exists into 'allow' field
     *  'mailer' -> it's automatic mailer
     *  'moderator' -> moderator or owner address
     *  'public' -> address isn't subscribed but ml is configured as public mailing list
     */
    function CheckSender($address) {
        $retval = 'deny';

        if(!$address || strlen($address) < 7) {    // invalid sender, ignore email
            return $retval;
        }
        if(class_exists('Bouncehandler')) {
            $head = str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers']));
            $body = str_replace("'","\\'",$this->decoded[0]['Body']);
            $multiArray = Bouncehandler::get_the_facts($head."\r\n".$body);
            if(!@empty($multiArray['recipient'])) {    // email bounced, notify owner
                $this->BounceMessage($address,$multiArray[0]['recipient'],
                            $multiArray[0]['action'],
                            $multiArray[0]['status'],
                            $multiArray[0]['status']);
                return 'mailer';
            }
            // make a specific body test
            $head_hash = BounceHandler::parse_head($head);
            $boundary = @$head_hash['Content-type']['boundary'];
            $mime_sections = BounceHandler::parse_body_into_mime_sections($body, $boundary);
            $rpt_hash=BounceHandler::parse_machine_parsable_body_part(@$mime_sections['machine_parsable_body_part']);
            for($i=0; $i<count(@$rpt_hash['per_recipient']); $i++){    
                if(!@empty($rpt_hash['per_recipient'][$i]['Action'])) {
                    $this->BounceMessage($address,
                        implode(", ",$rpt_hash['per_recipient'][$i]['Final-recipient']),
                        $rpt_hash['per_recipient'][$i]['Action'],
                        $rpt_hash['per_recipient'][$i]['Status'],
                        $rpt_hash['per_recipient'][$i]['X-supplementary-info']);
                    return 'mailer';
                }
            }
        } // else if($this->decoded[0]............... to do only once 'bounce check'
        if(preg_match('/(mailer-daemon|majordomo|virus|scanner|automated-response|smtp[.-]gateway|mailadmin|mailmaster|surfcontrol|postmaster|no(-|)reply|nobody|devnull)/i',
                $this->decoded[0]['ExtractedAddresses']['from:'][0]['address'])
        || preg_match('/auto(_|-|)reply/i',@$this->decoded[0]['Headers']['precedence:'])) {
            $this->CheckBounce();
            return 'mailer';
        }
        // be sure it's not an undetected autoresponder (ie. 'From: "Gmail Team" <mail-noreply@gmail.com>')
        if(isset($this->decoded[0]['Headers']['precedence:'])
                //&& eregi('(bulk|junk)',$this->decoded[0]['Headers']['precedence:'])
                && preg_match('/(bulk|junk)/i',$this->decoded[0]['Headers']['precedence:'])
        || preg_match('/no(-|_|)reply/i',
                $this->decoded[0]['ExtractedAddresses']['from:'][0]['address'])) {
            return 'mailer';
        }
        if(!strcmp($address,$this->listOwner)
        //|| eregi("(^|\n)$address(\n|$)",$this->modSublist)) {
        || preg_match("/(^|\n)$address(\n|$)/im",$this->modSublist)) {
            return 'moderator';
        }
        if(is_array($this->allow)) {
            foreach($this->allow as $pattern) {
                //if(eregi($pattern, $address)) {
                if(preg_match("/$pattern/i", $address)) {
                    return 'allow';
                }
            }
        }
        // it's no 'allow/modsublist' address, so if it's a newsletter, quit 
        // (if newsletter, only 'allow'/modsublist' addresses can post)
        if($this->dbrow->mltype != 'm') {
            return 'deny';
        }
        if(is_array($this->deny)) {
            foreach($this->deny as $pattern) {
                //if(eregi($pattern, $address)) {
                if(preg_match("/$pattern/i", $address)) {
                    return 'deny';
                }
            }
        }
        /*
         * check if exists a 'subscribers' record for this sender
         */
        $query = 'select state from '.$this->subscribers." where emailaddress = '$address' and state != 'enabled'";
        if($result = @mysql_query($query,$this->dbconn)) {
            if($row = @mysql_fetch_object($result)) {
                // there is a disabled/suspended 'subscribers' record, don't allow post
                mysql_free_result($result);
                return 'deny';
            }
            mysql_free_result($result);
        }
        if(preg_match('/(^|\n)'.$address.'(\n|$)/i',$this->sublist)) {
            $retval = 'subscribed';
        }
        if(preg_match('/(^|\n)'.$address.'(\n|$)/i',$this->digestSublist)) {
            $retval = (!strcmp($retval,'deny') ? '' : $retval) . 'digest';
        }
        if(strcasecmp($this->dbrow->subscribersonly,'yes') && !strcmp($retval,'deny')) {    // ml is open to all
            $retval = 'public';
        }
        return $retval;
    }

    function ListMessages() {
        $result=$this->pop3->ListMessages("",0);
        if(GetType($result)=="array") {
            for(Reset($result),$message=0;$message<count($result);Next($result),$message++) {
                    echo "Message ",Key($result)," - ",$result[Key($result)]," bytes.\n";
            }
            $result=$this->pop3->ListMessages("",1);
            if(GetType($result)=="array") {
                for(Reset($result),$message=0;$message<count($result);Next($result),$message++) {
                    echo "Message ",Key($result),", Unique ID - \"",$result[Key($result)],"\"\n";
                }
            }
        }
    }

    function CacheMessage() {
        if(!file_exists($this->cachePath)) {
            return;
        }
        if(!file_put_contents($this->cachePath.$this->listName.'-'.date("Ymd-H:i:s").'.eml',
                str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers'])).
                "\r\n".str_replace("'","\\'",$this->decoded[0]['Body']))) {
            return;
        }
        sleep(1);    // to avoid 2 messages with the same path
    }

    function Pop3Start() {

        $state = strpos($this->dbrow->hostname,"\t");
        $token = explode(($state === false ? ':' : "\t"),$this->dbrow->hostname);
        $this->pop3->hostname=$this->listHostName=$token[0];   /* POP 3 server host name                      */
        $this->pop3->port=$this->listPort=$token[1];           /* POP 3 server host port,
                                                                 usually 110 but some servers use other ports
                                                                 Gmail uses 995                              */
        $this->pop3->tls=$this->listTls=$token[2];             /* Establish secure connections using TLS      */
        $this->pop3->realm="";                                /* Authentication realm or domain              */
        $this->pop3->workstation="";                          /* Workstation for NTLM authentication         */
        $this->pop3->authentication_mechanism="USER";         /* SASL authentication mechanism               */
        if($this->pop3Debug)
            $this->pop3->debug=1;                             /* Output debug information                    */
        //$this->pop3->html_debug=1;                          /* Debug information is in HTML                */
        //$this->pop3->join_continuation_header_lines=1;      /* Concatenate headers split in multiple lines */
    }

    /* check for new messages */
    function Pop3Read() {
        $retval = '';
        $apop=0;
        $user=$this->listUser;
        $password=$this->listPopPass;
        $this->error='';
        $messages='';
        $address = '';
        $substate = 'deny';
        $messageid = '';

        if(($this->error=$this->pop3->Open())!="") {
            echo "Opening connection error: $this->error\n";
            return $this->error;
        }
        if(($this->error=$this->pop3->Login($user,$password,$apop))!="") {
            echo "Connection error: $this->error\n";
            return $this->error;
        }
        if(($this->error=$this->pop3->Statistics($messages,$size))=="")
        {
            echo "$messages mess.".($size > 0 ? " total size [$size]" : '');
            if($this->maxPop3MsgLimit && $messages > $this->maxPop3MsgLimit) {
                $messages = $this->maxPop3MsgLimit;
                echo " WARNING, APPLIED LIMIT TO MAX [$messages] MESSAGES.";
            }
            for($i=0; $i < $messages; $i++)
            {
                $error = '';
                $this->pop3->GetConnectionName($connection_name);
                $message=$i + 1;
                $size = $this->pop3->ListMessages($message,0);
                echo "\n # $message, size [$size]: ";
                $message_file='pop3://'.$connection_name.'/'.$message;
                $mime=new mime_parser_class;
                $mime->decode_bodies = 0;
                if($this->maxMsgSize && $size > $this->maxMsgSize) {
                    $mime->decode_bodies = 1;
                    echo " Warning [exceeding].";
                }
                $parameters=array(
                    'File'=>$message_file //, 'SaveBody'=>1,
                );
                $success=$mime->Decode($parameters, $this->decoded);
                if(!$success) {
                    echo 'MIME message decoding error: '.HtmlSpecialChars($mime->error)." .";
                } else {
                    if($this->cacheMessages) {
                        $this->CacheMessage();
                    }
                    if(!strcasecmp($this->removeAfterPop,'yes')) {
                        $this->error.=$this->RemoveMessage($message)."\n";
                    }
                    $address = @$this->decoded[0]['ExtractedAddresses']['from:'][0]['address'];
                    echo " sender [$address]: ";
                    /* enable if you want to log 'messageid'
                     * if(isset($this->decoded[0]['Headers']['message-id:'])) {
                     *     $messageid = $this->decoded[0]['Headers']['message-id:'];
                     *     echo " message-id [$messageid]: ";
                     * }
                     */
                    if(@$this->logSubject && @$this->decoded[0]['Headers']['subject:']) {
                        echo 'subject ['.substr($this->decoded[0]['Headers']['subject:'],0,$this->logSubject).']: ';
                    }
                    if(!$address) {
                        echo ' bad address for ['.$this->GetReturnPath('').'ignoring message (saved into cache); ';
                        $this->CacheMessage();
                        continue;
                    }
                    // now rebuild sublist fields because there could be some changes in this ML
                    // or, eventually, parent/children ML
                    $this->InitSublistFields();
                    $this->substate=$substate=$this->CheckSender($address);
                    if($this->CheckMlCommand($address,$substate) == true) {  // got ML command, jump
                        continue;
                    }
                    echo "[$substate] ";
                    if(strstr($substate,'mailer')) {
                        if(!$this->forwardMailerTo) {
                            echo "dropping message";
                            continue;
                        } else if(!strcasecmp($this->forwardMailerTo,'cache') && !$this->cacheMessages) {
                            echo "storing message";
                            $this->CacheMessage();
                            continue;
                        // check if email address to forward isn't the list address email to avoid message loop
                        } else if(strcasecmp($this->forwardMailerTo,'LISTNAME')
                               && strcasecmp($this->forwardMailerTo, $this->listAddress)) {
                            $mailsubject = "DROPPED MESSAGE FROM MAILER [$address]; ".
                                stripslashes($this->decoded[0]['Headers']['subject:'])."]";
                            $recipient = explode(',',str_replace(' ','',str_replace("\r",'',$this->forwardMailerTo)));
                            if($this->MailFilter($address) != true) {
                                echo "applying MailFilter to message for [$this->forwardMailerTo]";
                            }
                            echo "forwarding message to [$this->forwardMailerTo]: ";
                            $error = $this->SmtpSend($recipient, $mailsubject, $this->listAddress,
                                $this->ImplodeHeaders('',$this->decoded[0]['Headers']), $this->decoded[0]['Body']);
                            if($error != 'OK.') {
                                $this->NotifyOwner($this->forwardMailerTo,
                                    'User notification failed, ml ['.$this->listAddress.']',
                                    'Notification failure for ml ['.$this->listAddress.'] for address <'.
                                         $this->forwardMailerTo."> smtp error [$error]");
                            } else {
                                echo 'sent to ['.sizeof($recipient).'] users. End';
                            }
                            continue;
                        }
                        // if got here it means 'forwardMailerTo' is set to listaddress email, so proceed as normal msg
                        echo "forwarding message to list: ";
                    }
                    if(strstr($substate,'deny')) {
                        echo " sorry, you are not subscribed yet and/or not allowed to post.";
                        $this->SendSubscriptionError(
                            $this->GetReturnPath($address),false,'UNSUBSCRIPTION ERROR STATE');
                        continue;
                    }
                    /* message is ready for delivery, check 'filter' for any kind of rule */
                    if($this->MailFilter($address) == true) {
                        $this->SendMessage($address,$size);
                    }
                }
            }
        }
        echo $this->logcr."\n";
        // moved after this function to ensure closing the connection
        //$this->error=$this->pop3->Close();
        $retval = $this->error;
        return $retval;
    }

    function AddTrailer(&$mailbody) {
        $retval = '';

        if(strlen($this->trailerFile) <= 0) {
            return;
        }
        $content_type = @explode(';',$this->decoded[0]['Headers']['content-type:']);
        $content_type[0] = trim($content_type[0]);
        // if(!strstr($content_type[0],'alternative') && !strstr($content_type[0],'digest')
        // && !strstr($content_type[0],'mixed')) {
        if(!$content_type[0] || strstr($content_type[0],'text/plain')) {
            $mailbody.="\r\n".$this->SetText($this->trailerFile)."\r\n";
            return;
        }
        // no text/plain message. search 'text/plain' first, 'text/html' after
        preg_match('/(.*)(\r|)\nContent-Type: text\/plain/i',
            $mailbody,$matches,PREG_OFFSET_CAPTURE,1);
        if(@$matches[0][0]) {
            $pos=$matches[0][1];
            $pos+=25;    // add 'Content-Type: text/plain' character length
            $nextpart=trim(strtok($matches[0][0],"\r\n"));
            // search for last 'NextPart' of this body part
            $last=strpos(substr($mailbody,$pos),$nextpart);
            $last+=$pos - 2;
            if($mailbody[$last] == "\n" || $mailbody[$last] == "\r") {
                $last-=2;
            }
            $mailbody=substr($mailbody,0,$last)."\r\n".
                $this->SetText($this->trailerFile).
                substr($mailbody,$last)."\r\n";
        }
        preg_match('/(.*)(\r|)\nContent-Type: text\/html/i',
            $mailbody,$matches,PREG_OFFSET_CAPTURE,1);
        if(@$matches[0][0]) {
            $pos=$matches[0][1];
            $pos+=24;    // add 'Content-Type: text/html' character length
            $nextpart=trim(strtok($matches[0][0],"\r\n"));
            // search for last 'NextPart' of this body part
            $last=strpos(substr($mailbody,$pos),$nextpart);
            $last+=$pos - 2;
            preg_match('/(\r|)\n<\/body>(\r|)\n<\/html>(\r|)\n/i',
                substr($mailbody,$pos,$last),$matches,PREG_OFFSET_CAPTURE,1);
            if($matches && @$matches[0][1]) {
                $mailbody=substr($mailbody,0,$pos+$matches[0][1])."\r\n<br>".
                    $this->SetText(str_replace("\n","\r\n<br>",$this->trailerFile)).
                    substr($mailbody,$pos+$matches[0][1]+1)."\r\n";
            }
        }
        return;
    }

    /*
     * @return false       not ready to send
     * @return true        ready to send
     */
    function CheckQueueTime(&$row, &$msgrow) {
        $messagetime=strtotime($msgrow->date);
        $queuetime=strtotime($row->date);
        $timeqm=$queuetime-$messagetime;
        $mult=($timeqm - ($timeqm % (24*60*60))) / (24*60*60) + 1;
        if($this->debug) {
            echo "messagetime [$messagetime] [".date(DATE_RFC822,$messagetime)."]\n";
            echo "queuetime [$queuetime] [".date(DATE_RFC822,$queuetime)."]\n";
            echo "now [$this->now] [".date(DATE_RFC822,$this->now)."]\n";
            echo "timeqm: [".$timeqm."]\n";
            echo "mult: [".$mult."]\n";
            echo "time: [".($this->minTimeResendMsg *$mult)."]\n";
            echo "time passed from last send and message date: [$timeqm]\n";
            echo "time passed from last send and now: [".($this->now-$queuetime)."]\n";
        }
        if($this->now < $queuetime) {    // message has been scheduled and not yet ready to send
            return false;
        }
        if(($this->now - $queuetime) <= ($this->minTimeResendMsg * $mult)) {
            return false;
        }
        return true;
    }
    /**
      * this function looks for scheduling mail pattern into message's header
      */
    function IsScheduledMail(& $queuerow, & $msgrow) {
        if(!file_exists(CLASSES_DIR_PATH.DS.'class.scheduledate.php')) {
            return;
        }
        $pattern = $msgrow->header;
        $date = false;
        $repeat = false;

        if(!$pattern) {
            return false;
        }
        if(!preg_match("/(^|\r|\n)".$this->scheduledTime."(:|) +(.*).*(\r|\n|$)/i",
                trim($pattern),$matches,PREG_OFFSET_CAPTURE)) {
            return false;
        }
        if(!@$matches || !@$matches[3][0]) {
            return false;
        }
        $pattern = $matches[3][0];
        $matches = null;
        require_once(CLASSES_DIR_PATH.DS.'class.scheduledate.php');
        if(ScheduleDate::Parse($pattern,$matches) === false) {
            return false;
        }
        $sd = new ScheduleDate;
        if(($week=$this->GetText('SCHEDULED WEEK',false)) !== false) {
            $sd->week = explode(',',$week);
        }
        if(($date=$sd->Renew($pattern,$queuerow->date,$this->now)) === false) {
            return false;
        }
        if($this->debug) {
            echo "\n # message renewed to [$date]; ";
            return true;
        }
        $date = date("Y-m-d H:i:s",$date);    // $date = $sd->date;
        // date has been renewed, so create new queue/message records
        $query = "insert into ".$this->messages.
            " (id,date,state,listname,smtp,mailfrom,subject,message,header,rowlock) values(0,'$date','queued',".
            "'$msgrow->listname','$msgrow->smtp','$msgrow->mailfrom','$msgrow->subject','$msgrow->message','".
            str_replace($this->scheduledTime.': '.$pattern,$this->scheduledTime.': '.$pattern,$msgrow->header)."','')";
        $command = @mysql_query($query,$this->dbconn);
        $messageid = @mysql_insert_id($this->dbconn);
        $query = "insert into $this->queue (id,date,smtp,listname,messageid,addresses,rowlock) values(0,'$date','".
            "$queuerow->smtp','$queuerow->listname',$messageid,'$queuerow->addresses','')";
        $command = @mysql_query($query,$this->dbconn);
        echo "\n # $messageid renewed to [$date]; ";
        return true;
    }

    function SendFromQueue() {
        $query = "select * from $this->queue where listname = '$this->listName'";
        $result = @mysql_query($query,$this->dbconn);
        while($row = @mysql_fetch_object($result)) {
            // get entire message row
            $messageid = $row->messageid;
            $query = "select * from $this->messages where id = $messageid";
            $msgresult = @mysql_query($query,$this->dbconn);
            if(!$msgrow = @mysql_fetch_object($msgresult)) {
                // there is not a associated message, remove queue record
                $query = "delete from $this->queue where id = $row->id";
                $command = @mysql_query($query,$this->dbconn);
                continue;
            }
            // check if message is expired
            if($this->CheckQueueTime($row,$msgrow) != true) {
                // not enaught time has passed, retry later
                continue;
            }
            // ok, try to resend message
            echo "\n # $messageid sent from queue; ";
            // change smtp server if any
            $this->smtpIndex = $row->smtp + 1;
            if($this->smtpIndex > (sizeof($this->smtpServer) - 1)) {
                $this->smtpIndex = 0;
            }
            $addresses = explode(',', $row->addresses);
            $bounceerror = '';
            $addresserror = '';
            foreach($addresses as $address) {
                // remove error code (if exists) from address
                $address = preg_replace('/^\[.*\]/','',$address);
                $pos=strpos($msgrow->message,"\r\n\r\n");
                if(!$pos) $pos=strpos($msgrow->message,"\n\n");
                $error = $this->SmtpSend($address, $msgrow->subject, $this->listAddress,
                    substr($msgrow->message,0,$pos),
                    substr($msgrow->message,$pos+2));
                if($error != 'OK.') {
                    if($error[0] == '5') {
                        $bounceerror.="[$error]$address\n";
                    } else {
                        echo " error [$error] for [$address];";
                        $addresserror.=((strlen($addresserror) <= 0) ? '': ',').
                            "[$error]$address";
                    }
                }
            }
            // check if it is a scheduled message
            $this->IsScheduledMail($row,$msgrow);
            if(strlen($addresserror) > 1) {
                $query = "update $this->queue set date = now()".
                    ", addresses = '".str_replace("'","\\'",$addresserror).
                    "', smtp = $this->smtpIndex where id = $row->id";
                $command = @mysql_query($query,$this->dbconn);
            } else {
                $query = "update $this->messages set state = 'sent', smtp = '".
                    $this->smtpIndex."' where id = $messageid";
                $command = @mysql_query($query,$this->dbconn);
                $query = "delete from $this->queue where id = $row->id";
                $command = @mysql_query($query,$this->dbconn);
            }
            if(strlen($bounceerror) > 1) {
                $this->NotifyBounce($messageid,$bounceerror);
            }
            @mysql_free_result($msgresult);
            echo "\n ";
        }
        @mysql_free_result($result);
        $this->smtpIndex = 0;
        return true;
    }

    function ApplyNotifyFilterCommand($address, $text) {
        $id = md5(uniqid(rand(), true));
        $mailheader = '';
        $mailsubject = $this->GetText('NOTIFY SUBJECT',true)."[$this->listAddress] <$address>";

        $boundary='----NextPart'.md5(date('r', time()));
        $mailheader.="Return-Path: ".$this->listOwner."\r\nPrecedence: bulk\r\n";
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/report; boundary=".
            $boundary."\r\n";
        $mailheader.='Subject: '.$mailsubject."\r\n";
        $mailbody ='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            $text."\r\n\r\noriginal message:\r\n\r\n";
        $mailbody.='--'.$boundary.
            "\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg.eml\"\r\n\r\n".
            str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers']))."\r\n".
            str_replace("'","\\'",$this->decoded[0]['Body'])."\r\n\r\n".'--'.$boundary."--\r\n\r\n";
        $address = explode(',',str_replace(' ','',str_replace("\r",'',$address)));
        $error = $this->SmtpSend($address, $mailsubject, $this->listAddress, $mailheader, $mailbody);
        if($error != 'OK.') {
            $this->NotifyOwner($address,'User notification failed, ml ['.$this->listAddress.'] for address <'.
                $address.'>');
        }
    }

    function ApplyCommand(& $address, & $header, & $body, $command) {
        echo " matching filter, ";
        $retval = true;
        //$cmd=explode(' ',$command);
        for($i=0; $i < sizeof($command); $i++) {
            $cmd = trim(strtolower(strtok($command[$i],' ')));
            if($this->debug) {
                $this->debugOutput .= "\nMATCHING FILTER: $cmd\n";
            }
            // check if command is commented with `#' to disable it
            if($cmd[0] && $cmd[0] == '#') {
                echo "ignoring filter; ";
                continue;
            }
            switch(strtolower(trim($cmd))) {
                case 'drop':         // catched "drop", don't send message
                    echo "applying [$cmd] filter; ";
                    $retval = false;
                    break;
                case 'owner':        // notify owner
                    $this->NotifyOwner($address,
                        'Filter notification, ml ['.$this->listAddress.'] for address <'.
                            $address.'>',
                        strtok('')."\r\n\r\n");
                    break;
                case 'store':        // store message if CACHE_MESSAGES is not true
                    echo "applying [$cmd] filter; ";
                    if($this->cacheMessages == false) {
                        $this->CacheMessage();
                    }
                    break;
                case 'notify':        // notify user
                    echo "applying [$cmd] filter; ";
                    $this->ApplyNotifyFilterCommand($address,strtok(''));
                    break;
                case 'redirect':    // redirect message
                case 'forward':        // alias of redirect
                    echo "applying [$cmd] filter; ";
                    $recipients = explode(',',str_replace(' ','',strtok('')));
                    $error = $this->SmtpSend($recipients, $mailsubject, $address, $header, $body);
                    if($error != 'OK.') {
                        $this->NotifyOwner($address,"Filter [$cmd] failed, ml [$this->listAddress] for address <$address>");
                    }
                    break;
            }
        }
        return $retval;
    }

    function ApplyFilter(& $address, & $pattern, & $buffer) {
        foreach($pattern as $rgx) {
            if(strlen($rgx) <= 1)
                continue;
            $flag = false;
            if($rgx[0] == '!') {
                $flag = true;    // invert regexp result
                $rgx = substr($rgx,1);
            }
            $stat = @preg_match('/'.str_replace('/','\/',$rgx).'/im',$buffer);
            if((!$stat && $flag == false) || ($stat && $flag == true)) {    // one regexp failed, no match, quit
                return false;
            }
        }
        // every regexp matched, apply command
        return true;
    }

    function MailFilter($address) {
        if(strlen($this->dbrow->mailfilter) < 10)
            return true;
        $header = '';
        $retval = true;
        $body = & $this->decoded[0]['Body'];
        $filter = explode(':0',$this->dbrow->mailfilter);
        foreach($filter as $rule) {
            if(strlen($rule) < 3)
                continue;
            $rule = str_replace("\r",'',$rule);
            $pos = strpos($rule,"\n");
            $flag = substr($rule,0,$pos);
            $rule = substr($rule,$pos);
            $pattern = array();
            $command = array();
            while(strlen($rule)) {
                $token = trim(strtok($rule,"\n"));
                if(strlen($token) > 1) {
                    if($token[0] == '*') {
                        $pattern[] = trim(substr($token,1),"\n\r;,. ");
                    } else {
                        $command[] = trim($token,"\n\r;,. ");
                    }
                }
                $rule = strtok("");
            }
            $status = false;
            if(!strcasecmp($flag,' h') || !strcasecmp($flag,'')) {
                if(!$header) {
                    $header = $this->ImplodeHeaders('',$this->decoded[0]['Headers']);
                }
                if($this->ApplyFilter($address,$pattern,$header) == true) {
                    $status = true;
                }
            }
            if(!strcasecmp($flag,' b') || !strcasecmp($flag,'')) {
                if($this->ApplyFilter($address,$pattern,$body) == true) {
                    $status = true;
                }
            }
            if($status == true) {
                // if there is just one "drop" set retval to false, message won't be sent
                if($this->ApplyCommand($address,$header,$body,$command) == false) {
                    $retval = false;
                }
            }
        }
        return $retval;
    }

    /*
     *  regexp sample:
     *  thanks to Gregor Buchholz
     *
     *  Subject [Bigband-__LISTNAME__] \\1 {/(.*)/i}
     * '/(\[Bigband-__LISTNAME__\]).+\[Bigband-__LISTNAME__\]/','\1'
     */
    function RebuildHeader (& $mailheader) {
        $headers = str_replace("__USERADDRESS__",$this->sender,$this->headersChange);
        $headers=explode("\n",$headers);
        foreach($headers as $token) {
            if(@$token[1] == '/') {   // got regexp, jump (it will be executed later)
                continue;
            }
            $hkey = trim(strtolower(strtok($token,': ')));
            $opt = false;
            if(strlen($hkey) > 1 && ($hkey[0] == '!' || $hkey[0] == '|')) {
                $opt = true;
                $hkey = substr($hkey,1);
            }
            $replace = trim(strtok("{"));
            $pattern = trim(strtok(''),"}\n\r ");
            $header = & $this->decoded[0]['Headers']["$hkey:"];
            /**
             * if first character is '!' add header only if not already set
             */
            if($header && !$opt) {
                if(strlen($replace) <= 0) {    // header key only, no pattern, no value, remove header
                    unset($this->decoded[0]['Headers']["$hkey:"]);
                    continue;
                }
            } else {
                if(strlen($replace) <= 0) {    // no header, but no value, jump
                    continue;
                }
                $this->decoded[0]['Headers']["$hkey:"] = '';
                $header = & $this->decoded[0]['Headers']["$hkey:"];
            }
            if(strlen($pattern) <= 0) {
                $pattern = '/(.*)/';
            }
            $header = preg_replace($pattern,$replace,$header,1);
        }
        // rebuild mail headers
        foreach($this->decoded[0]['Headers'] as $key => $val) {
            if(is_array($val)) {
                foreach($val as $token) {
                    $mailheader .= $key.' '.trim($token,"\n\r ")."\r\n";
                }
            } else if(strlen($val) > 0) {
                $mailheader .= $key.' '.trim($val,"\n\r ")."\r\n";
            }
        }
        // now check for header regexp like '/^X-.*\r\n/im',''
        foreach($headers as $token) {
            if(@$token[1] != '/') {
                continue;
            }
            eval("\$mailheader = preg_replace($token,\$mailheader);");
        }
    }

    /**
     * check if exists extra header 'X-Scheduled' (or initial part of subject) to queue message
     * @return = false        no schedule request, message will not be scheduled
     * @return = true         there is an invalid schedule request, message will not be scheduled and sender alerted by mail
     * @return = 'VALID DATE' there is a valid schedule request, message will be scheduled and sender confirmed by mail
     */
    function CheckScheduledTime(& $sender) {
        $pattern = false;
        $retval = false;
        $repeat = false;

        if(!file_exists(CLASSES_DIR_PATH.DS.'class.scheduledate.php')) {
            return false;
        }
        // think about using 'similar_text' function to avoid malformed request sent to ML
        // if(strcasecmp($this->removeAfterPop,'yes')) {
        //     return false;
        // }
        if(@$this->decoded[0]['Headers'][strtolower($this->scheduledTime).':']) {
            $pattern = trim($this->decoded[0]['Headers'][strtolower($this->scheduledTime).':'],"\"' ");
        } else if(preg_match("/^\[".$this->scheduledTime."(:|) +(.*)\]/i",
                @$this->decoded[0]['Headers']['subject:'],$matches,PREG_OFFSET_CAPTURE)) {
            if(@$matches[2][0]) {
                $pattern = trim($matches[2][0],"\"' ");
            }
            $this->decoded[0]['Headers']['subject:'] =
                ltrim(substr($this->decoded[0]['Headers']['subject:'],strlen($matches[0][0])));
        } else if(preg_match("/^".$this->scheduledTime."(:|) +(.*).*(\r|\n|$)/i",
                ltrim($this->bodyTextPlain,'*'),$matches,PREG_OFFSET_CAPTURE)) {
            if(@$matches[2][0]) {
                $pattern = trim($matches[2][0],"\"' ");
            }
            if(!@$this->decoded[0]['Headers']['content-type:']
            || strcasecmp(substr($this->decoded[0]['Headers']['content-type:'],0,10),'text/plain')) {
                $this->decoded[0]['Body'] =
                    ltrim(substr($this->decoded[0]['Body'],strlen($matches[0][0])));
            }
        } else {
            return false;
        }
        require_once(CLASSES_DIR_PATH.DS.'class.scheduledate.php');
        if(ScheduleDate::Parse($pattern,$matches) !== true) {
            $mailsubject = $this->GetText('NOTIFY SUBJECT',true).' '.$this->listAddress;
            $mailbody = $this->GetText('SCHEDULED MAIL ERROR',true)." MALFORMED DATE [$pattern]".
                "\r\n\r\n[".$this->decoded[0]['Headers']['subject:']."]\r\n";
            $this->NotifyUser($sender,false,$mailsubject,$mailbody);
            return true;
        }
        $sd = new ScheduleDate;
        if(($week=$this->GetText('SCHEDULED WEEK',false)) !== false) {
            $sd->week = explode(',',$week);
        }
        $sd->matches = $matches;
        $this->scheduledPattern = $pattern;
        $retval = date("Y-m-d H:i:s",$sd->GetFirstRun($pattern,$this->now));
        if(!@$this->decoded[0]['Headers'][strtolower($this->scheduledTime).':']) {
            $this->headersChange.="\n".$this->scheduledTime.' '.$pattern;
        }
        return $retval;
    }

    /* send section */
    function SendMessage($sender,$msgSize) {
        $error = '';
        $messageid = -1;
        $mailheader = '';
        $mailbody = '';
        $mailsubject = '';
        $ok = 0;
        $originalSender = false;

        $this->sender = (@$this->decoded[0]['Headers']['from:'] ? $this->decoded[0]['Headers']['from:'] : $sender);
        if(($scheduledTime = $this->CheckScheduledTime($sender)) === true) { // invalid schedule request
            echo "scheduling error; ";
            return;
        }
        $modstate = 'sent';
        if(!strcmp($this->moderatedList,'yes')) {
            $modstate = 'pending';
        }
        if($this->maxMsgSize && $msgSize > $this->maxMsgSize) {
            $mailsubject=$this->GetText('NOTIFY SUBJECT',true).' '.$this->listAddress;
            $mailbody=$this->GetText('EXCEEDING MSG SIZE',true).$this->maxMsgSize."\r\n\r\n".
                "------------------------------------------------\r\n\r\n".$mailsubject."\r\n\r\n";
// TODO: insert original message as attachment
            $this->NotifyUser($sender,false,$mailsubject,$mailbody);
            return;
        }
        $mailbody=&$this->decoded[0]['Body'];
        $mailbody.=$this->AddTrailer($mailbody);
        $mailsender = $this->listAddress; // if smtp require authentication
        $mailsubject = @stripslashes($this->decoded[0]['Headers']['subject:']);
        if(!strcmp($modstate,'pending')) {
            //if(eregi("(^|\n)$sender(\n|$)",$this->modSublist)
            //if(preg_match("/(^|\n)$sender(\n|$)/im",$this->modSublist)
            if(!strcasecmp($this->substate,'moderator')
            && preg_match('/\b'.$this->GetText('CONFIRM',true).'\b.\b('.$this->GetText('MESSAGE',true).')\b.'.$this->listName."\.([A-Za-z0-9]+)\.(.*)/i", '--'.$this->bodyTextPlain, $matches,PREG_OFFSET_CAPTURE)) {
                if(@$matches[3][0]) {
                    $originalSender = trim(strtok($matches[3][0]," >\r\n\t"));
                }
                $query="select * from $this->messages where state = 'pending' && keyvalue = '".
                    $matches[2][0]."'";
                if($result = @mysql_query($query,$this->dbconn)) {
                    if($row = @mysql_fetch_object($result)) {
                        $query = "update $this->messages set state = 'queued' where id = $row->id";
                        if(!$this->debug) {
                            @mysql_query($query,$this->dbconn);
                        }
                        $messageid = $row->id;
                        $parameters=array(
                            'Data'=>$row->message //, 'SaveBody'=>1,
                        );
                        unset($this->decoded);
                        $mime=new mime_parser_class;
                        $mime->decode_bodies = 0;
                        $success=$mime->Decode($parameters, $this->decoded);
                        if(!$success) {
                            echo 'MIME message decoding error: '.HtmlSpecialChars($mime->error)." .";
                            return;
                        }
                        $this->sender = $row->mailfrom;
                        $mailbody=& $this->decoded[0]['Body'];
                        @mysql_free_result($result);
                    } else { // key doesn't match, notify owner
                        $this->NotifyOwner($sender,
                            'Message moderation failed, ml ['.$this->listAddress.'] '.$originalSender.'>');
                        @mysql_free_result($result);
                        return;
                    }
                }
                $modstate = 'sent';
                // now check for scheduled time
                if(preg_match("/\b".$this->scheduledTime.":_(.*)\.".$this->GetText('CONFIRM',true).'\b/im',
                    '--'.$this->bodyTextPlain, $matches,PREG_OFFSET_CAPTURE)) {
                    $this->bodyTextPlain = str_replace('_',' ',@$matches[1][0]);
                    if(($scheduledTime = $this->CheckScheduledTime($sender)) === true) {
                        echo "scheduling error; ";
                        return;
                    }
                }
            } else {
                $this->NotifyMessageModeration($sender,trim($this->scheduledPattern));
                return;
            }
        }
        $this->RebuildHeader($mailheader);
        $addresserror = '';
        $bounceerror = '';
        echo "Sending, ";
        $sublist = explode("\n",$this->sublist);
        if(sizeof($sublist) > 0) {
            // set recipients number per smtp connection
            $recipients = $this->ArraySplit($sublist);
            foreach($recipients as $recipient) {
                // if set 'X-Scheduled' header/subject, mail has to be sent at 'datetime' value, so mark msg as queued
                if($scheduledTime) {
                    echo "schedule request, queued; ";
                    $addresserror.="[scheduled]".implode(",[scheduled]",$recipient).',';
                    continue;
                }
                $error = $this->SmtpSend($recipient, $mailsubject, $mailsender,
                    $mailheader, $mailbody);
                if($error != 'OK.') {
                    echo " sending error [$error];";
                    // if ok is a permanent error, don't queue and notify to owner
                    if($error[0] == '5') {
                        $bounceerror.="[$error]".implode("\n[$error]",$recipient)."\n";
                    } else {
                        $addresserror.="[$error]".implode(",[$error]",$recipient).',';
                    }
                } else {
                    $ok += sizeof($recipient);
                }
            }
            if(strlen($bounceerror) > 1)
                $bounceerror = substr($bounceerror,0,-1);
            if(strlen($addresserror) > 1)
                $addresserror = substr($addresserror,0,-1);
        }
        if(!strcmp($modstate,'sent') && strlen($addresserror) > 1) {
                $modstate = 'queued';
        }
        // ok, insert message into archive
        if($messageid > 0) {
            $query = "update $this->messages set state = '$modstate' where id = $messageid";
        } else {
            $query = "insert into ".$this->messages.
                " (id,date,state,listname,smtp,mailfrom,subject,message,header,rowlock) values(0,".
                ($scheduledTime === false ? 'now()' : "'$scheduledTime'").",'".
                "$modstate','$this->listName','$this->smtpIndex','$sender','".
                str_replace("'","\\'",$this->IsoDecode($mailsubject))."','".
                str_replace("'","\\'",$mailheader)."\r\n".str_replace("'","\\'",$mailbody)."','".
                str_replace("'","\\'",$this->ImplodeHeaders('',$this->decoded[0]['Headers'])).
                "','')";
        }
        if(!$this->debug) {
            $command = @mysql_query($query,$this->dbconn);
            if($messageid == -1) {
                $messageid = @mysql_insert_id($this->dbconn);
            }
            echo "msgid [$messageid]:";
            // insert unsent message into queue only if original pop3 message's been removed
            if(strlen($addresserror) > 1) { // && !strcasecmp($this->removeAfterPop,'yes')) {
                $query = "insert into $this->queue (id,date,smtp,listname,messageid,addresses,rowlock) values(0,".
                    ($scheduledTime === false ? 'now()' : "'$scheduledTime'").",'".
                    "$this->smtpIndex','$this->listName',$messageid,'".
                    str_replace("'","\\'",$addresserror)."','')";
                $command = @mysql_query($query,$this->dbconn);
            }
        }
        // notify user mail has been scheduled
        if($scheduledTime) {
            $mailbody = $this->GetText('SCHEDULED MAIL CONFIRM',true).' '.$scheduledTime."\r\n".
                "\r\n\r\nId [$messageid] [$mailsubject]\r\n";
            $mailsubject = $this->GetText('NOTIFY SUBJECT',true).' '.$this->listAddress;
            $this->NotifyUser(($originalSender !== false ? $originalSender : $sender),false,$mailsubject,$mailbody);
        }
        // check if there are permanent error and send them to owner
        if(strlen($bounceerror) > 1) {
            $this->NotifyBounce($messageid,$bounceerror);
        }
        $this->sender = false;
        echo " sent to [$ok] users. End";
    }

    function CheckDigestByDate() {
        $exptime=$this->sendDigest;
        if(!is_numeric(substr($exptime,0,1))) {
            $daycheck=strtolower(substr($exptime,0,3));
            if(strcasecmp($daycheck,date('D',$this->now))) {
                return false;
            }
            // ok, the day is good, go on
            $exptime=substr($exptime,4);
        }
        if(strpos($exptime,':')) {
            if(strcmp($exptime,date('H:i',$this->now))) {
                return false;
            }
            // there is no constant time, but numeric value ('1' for every hour,'12' for every 12 hours)
        } else {
            $now=(date('H',$this->now) * 60)+date('i',$this->now);
            if((($now % ($this->sendDigest *60)) / ($this->sendDigest *60)) <> 0) {
                return false;
            }
        }
        // ok it's send time
        $query = "select * from $this->messages where state = 'sent' and".
        // if you want to check 'from date' too enable row below
        //    (" date > '".date("Y/m/d H:i:s",$this->now - ($this->sendDigest * 60 * 60)) . "' and") : '').
            " date <= '".date("Y/m/d H:i:s",$this->now)."' order by date limit ".$this->digestMaxMsg;
        if(!$result=@mysql_query($query,$this->dbconn)) {
            return false;
        }
        return $result;
    }

    function SendDigest() {
        $result=null;

        if(strlen($this->digestSublist) < 5) {    // no digesto subscribers, exit from here
            return;
        }
        if(!$result=$this->CheckDigestByDate())
            return;
        $mailsubject = '';
        $mailheader = '';
        $mailbody = '';
        $boundary = '';
        $digestlist = '';
        $maxsize=(DIGEST_MAX_SIZE != false ? DIGEST_MAX_SIZE : $this->dbrow->digestmaxsize);
        if(!$maxsize)
            $maxsize = '64K';
        // foreach($this->headersChange as $hk => $hv) { if(strcmp($hv,'')) { $mailheader.= $hk.': '.$hv."\r\n"; } }
        $mailheader.= "Precedence: bulk\r\n";
        $boundary='----NextPart'.md5(date('r', time()));
        $mailheader.="From: ".$this->listAddress."\r\nMIME-Version: 1.0\r\nContent-Type: multipart/digest; boundary=".
            $boundary."\r\n";

        $mailsubject.='Subject: '.$this->listName." Digest: ";
        $mailbody.='--'.$boundary."\r\nContent-Type: text/plain; charset: us-ascii\r\n\r\n".
            $this->listName." Digest:\r\n\r\n";
        $firstid=null;
        $lastid=null;
        if(!strcasecmp($maxsize[strlen($maxsize)-1],'k')) {
            $maxsize*=1024;
        } else if(!strcasecmp($maxsize[strlen($maxsize)-1],'m')) {
            $maxsize*=1024*1024;
        }
        echo 'digest for msg# '.$row->id."; date: ".$row->date."\n";
        while($row = @mysql_fetch_object($result)) {
            if($firstid==null) {
                $firstid=$row->id;
            }
            $lastid=$row->id;
            echo $row->id."; ";
            $mailbody.="Id: ".$row->id."\r\nSender: ".$row->mailfrom."\n\nSubject: ".$row->subject."\r\n";
            $maxsize -= strlen($row->message);
            if($maxsize <= 0) {    // digest message exceeds max size
                $mailbody.="Warning! Exceding max size, this message can be retrieved with ML command:\r\nget.".
                    $row->id."\r\n";
            } else {
                $digestlist.='--'.$boundary.
                    "\r\nContent-Type: message/rfc822\r\nContent-Disposition: inline; filename=\"msg".$row->id.".
                    eml\"\r\n\r\n".$row->message."\r\n\r\n";
            }
            $mailbody.="\r\n\r\n";
        }
        @mysql_free_result($result);
        if(!$firstid) {        // no messages for digest
            return;
        }
        $mailsubject.=$firstid.'-'.$lastid."\r\n";
        $mailheader.=$mailsubject;
        $mailbody.=$digestlist.'--'.$boundary."--\r\n\r\n";
        unset($digestlist);
        $digestsublist=explode("\n",$this->digestSublist);
        if(sizeof($digestsublist) > 0) {
            foreach($digestsublist as $mailaddress) {
                if(strlen($mailaddress) < 5) continue;
                $mailaddress = stripslashes($mailaddress);
                $error = $this->SmtpSend($mailaddress,$mailsubject,$this->listAddress,$mailheader,$mailbody);
                if($error != 'OK.') {
                    echo " digest-error [$error] for [$mailaddress];";
                    $addresserror.=((strlen($addresserror) <= 0) ? '': ',').$mailaddress;
                } else {
                    $ok++;
                }
            }
        }
        if($ok > 0 && _DIGEST_DATE_MODE != true) {
            $query = "update $this->messages set state = 'sentdigest' where state = 'sent' limit ".
                $this->digestMaxMsg;
            if(!$result=@mysql_query($query,$this->dbconn)) {
                echo " digest-error, messages 'state' not changed;";
            }
        }
    }

    function ValidateLockTime($buffer) {
        if(!strcmp($buffer,''))
            return true;
            $buffer = substr($buffer, strpos($buffer,'-')+1);
            // echo "<!-- diff # ".(strtotime('now') - strtotime(str_replace('-',' ',$buffer)))."-->\n";
            if((strtotime('now') - strtotime(str_replace('-',' ',$buffer))) > ($this->expireLock ? $this->expireLock : EXPIRE_LOCK)) {
                return true;
            }
            return false;
    }

    function LockMl() {
        if($this->ValidateLockTime($this->dbrow->rowlock) != true) {
            return false;
        }
        $query = "update ".$this->mltable." set rowlock = '".
            $_SERVER['REMOTE_ADDR'].'-'.date('Y/m/d-H:i:s')."' where listname = '".$this->listName."'";
        if(!@mysql_query($query,$this->dbconn)) {
            return false;
        }
        return true;
    }
    function UnlockMl() {
        $query = "update ".$this->mltable." set rowlock = '' where listname = '".$this->listName."'";
        if(!@mysql_query($query,$this->dbconn)) {
            return false;
        }
    }

    function Run() {
        /* save start time for some time checks because it could be delayed by big messages, many subscribers...*/
        $this->now = strtotime('now');
        echo $this->logHeader;
        echo '-'.date("Y/m/d H:i:s")." ".$this->listName.": ";
        if($this->Init() != true) {
            echo $this->logfooter;
            return;
        }
        if(!strcmp($this->dbrow->shutdown,'yes')) {
            echo "ML disabled. quit\n";
        } else if($this->LockMl() != true) {
            echo "already locked. quit";
        } else {
            $this->SendFromQueue();
            $this->Pop3Start();
            $this->error=$this->Pop3Read();
            if(strcasecmp($this->pop3->state,"DISCONNECTED"))
                $this->error=$this->pop3->Close();
            $this->SendDigest();
            $this->UnlockMl();
        }
        if($this->debug) {
            echo str_replace("\n","<br>\n",htmlentities($this->debugOutput)."\n");
        }
        echo $this->logfooter;
    }
};

?>
