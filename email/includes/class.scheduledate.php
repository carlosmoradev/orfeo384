<?php 
/*
 * @$Header: /var/cvsroot/scheduledate/class.scheduledate.php,v 1.6 2010/02/06 07:47:56 cvs Exp $
 */
/*
    A simple class to schedule a command by date

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
error_reporting(E_ALL);

/**
 *
    VALID PATTERNS COULD BE (for example):
    "2010/1/6 12:00 "                    // fixed date "2010/01/06 12:00"
    "* * * * * "                         // every year, month, day, hour, minute
    "* * * * 0 "                         // every year, month, day, hour, at '0' minute
    "2009  1-7     *       *       0\n"; // every day and hour of 2009 from january to july; have a little patience, please :)
    "2009  *       *       *       0\n"; // every month, day and hour of 2009; have a little more patience, please :)
    "2009-2010 1 mon,thu,fri 15 51 ";
    "2009-2010 1 2*sun-tue,thu-sat 15 51 ";
    "2009-2010 * *mon,thu 15 51 ";
    "2009 2 3 0 51 ";
    "2009-2010 01,03 3*sun,thu * 51 ";
    "2009-2010 01,03 3*sun 0 51 ";
    "2009-2010 01,03 * 11 51 ";
    "2009-2010 3,12 1,4-7 15 51 ";
    "2009-2010 01-02 10 0-23 51 ";
    "2009-2010 01-02 10 11,23 51 ";
    "2009 3,12 wed-fri 11 51 ";
    "2009 * 1,4-7,9-12 11 51 ";
  *
  * @return true                          existing scheduling pattern and date is greater then now
  * @return false but date is not false   existing scheduling pattern and date is less then now
  * @return false and date is false too   not existing scheduling
  */

class ScheduleDate
{
/* public */
    //var $week = array('dom','lun','mar','mer','gio','ven','sab');
    var $week = array('sun','mon','tue','wed','thu','fri','sat');
    var $dateArray = array('year'=>0,'month'=>0,'day'=>0,'hour'=>0,'minute'=>0);
    var $date = null;
    var $time = null;
    var $firstRun = null;
    var $lastRun = null;
    var $pattern = null;
    var $dayPattern = null;
    var $matches = null;
    var $now = null;

    function BuildDateArray($date) {
        $t = explode(',',strftime("%Y,%m,%d,%H,%M", strtotime($date)));
        $this->dateArray['year'] = $t[0];
        $this->dateArray['month'] = $t[1];
        $this->dateArray['day'] = $t[2];
        $this->dateArray['hour'] = $t[3];
        $this->dateArray['minute'] = $t[4];
    }

    function BuildDate() {
        $this->date = $this->dateArray['year'].'-'.$this->dateArray['month'].'-'.$this->dateArray['day'].' '.
                      $this->dateArray['hour'].':'.$this->dateArray['minute'];
        $this->time = strtotime($this->date);
        return $this->time;
    }

    function Parse(& $pattern, & $matches) {
        preg_match("/([\*0-9]{1,4})(|[\-,].*)[[:blank:]\/\-]+([\*0-9]{1,2})(|[\-,].*)[[:blank:]\/\-]([\*0-9a-z]{1,3}|)(|[\*\-,].*)[[:blank:]\/]([\*0-9]{1,2})(|[\-,].*)[[:blank:]:\-]([\*0-9]{1,2})(|[\-,].*)[[:blank:]:\-\r\n\t$]/i", $pattern."\n", $matches, PREG_OFFSET_CAPTURE);
        if(!$matches || !$matches[0][0]) {
            return false;
        }
        $pattern = $matches[0][0];
        for($i = 2; $i <= 10; $i+=2) {
            if(strlen($matches[$i][0]) > 0) {
                if($matches[$i][0][0] == '-' || $matches[$i][0][0] == ',') {
                    $matches[$i][0] = $matches[$i-1][0].$matches[$i][0];
                }
            } else if(!strcmp($matches[$i-1][0],'*')) {
                $matches[$i][0] = $matches[$i-1][0];
            }
        }
        if(preg_match("/[a-z]/i",$matches[5][0])) {
            if(strlen($matches[6][0]) == 0) {
                $matches[6][0] = $matches[5][0];
            }
            $matches[5][0] = 5;
        } else if(preg_match("/[a-z]/i",$matches[6][0])) {
            if(strlen($matches[5][0]) <= 0) {
                $matches[5][0] = 5;   // if max week days is not specified, set it to max (maybe 6?)
            }
        }
        return true;
    }

    function Explode(& $pattern, $rif, & $field, $needle = ',') {
        $retval = false;

        $list = explode($needle,$pattern);
        $field = false;
        $first = false;
        for($i=0; $i < sizeof($list); $i++) {
             if($pos=strpos($list[$i],'-')) {
                 $from=substr($list[$i],0,$pos);                     // $from=trim(substr($list[$i],0,$pos),"()");
                 if($first === false) $first = $from;
                 $to=substr($list[$i],$pos+1);                       // $to=trim(substr($list[$i],$pos+1),"()");
                 if($rif <= $from) {
                     $field = $from;
                     $retval = true;
                     break;
                 } else if($rif <= $to) {
                     $field = $rif;
                     $retval = true;
                     break;
                 }
             } else if($rif <= $list[$i]) {                          // } else if($rif <= trim($list[$i],"()")) {
                 $field = $list[$i];                                 //     $field = trim($list[$i],"()");
                 $retval = true;
                 break;
             }
             if($first === false) $first = $list[$i];                // if($first === false) $first = trim($list[$i],"()");
        }
        // if no matches, start from first value
        if($field === false)
            $field = $first;
//echo "Exiting [$field]...................\n";
        return $retval;
    }
    
    function GetFirstWeekDay($day) {
        $retval = false;
        $daycounter = array();

        for($i=1; $i <= 31; $i++) {
            $this->dateArray['day'] = $i;
            $d=date("w",strtotime(
                            $this->dateArray['year'].'-'.$this->dateArray['month'].'-'.$i.' '.
                            $this->dateArray['hour'].':'.$this->dateArray['minute']));
            if(!preg_match("/\b".$d."\b/",$this->dayPattern)) {
                continue;
            }
            if(!$daycounter[$d]) {
                $daycounter[$d] = 1;
            } else {
                $daycounter[$d]++;
            }
            if($daycounter[$d] > $this->matches[5][0]) {
                break;
            }
            if($i >= $day) {
                $retval = $i;
                break;
            }
        }
        return $retval;
    }

    function GetLastWeekDay($year,$month,$day,$hour,$minute) {
        $retval = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00');
        $daycounter = array();

        for($i=31; $i >= 28; $i--) {
            if(checkdate(trim($month,' *,-'),$i,trim($year,' *,-'))) {
                break;
            }
        }
        if($i <= $day) {
            $retval=strtotime("$year-$month-$i $hour:$minute:00");
        }
        if($this->dayPattern === null) {
            return $retval;
        }
        $day = $i;    // max month's day
        for($i=1; $i <= $day; $i++) {
            $d=date("w",strtotime("$year-$month-$i $hour:$minute:00"));
            if(preg_match("/\b".$d."\b/",$this->dayPattern)) {
                if(!$daycounter[$d]) {
                    $daycounter[$d] = 1;
                } else {
                    $daycounter[$d]++;
                }
                if($daycounter[$d] > $this->matches[5][0]) {
                    continue;
                }
                $retval=strtotime("$year-$month-$i $hour:$minute:00");
            }
        }
        return $retval;
    }

    function GetLastToken($field, $pattern, $max) {
        if(preg_match("/[a-z]/i",$pattern)) {
            $this->TransformWeek($pattern);
            $pattern = $this->dayPattern;
        }
        if(!strcmp($field,'*')) {
            $pattern = $max;
        }
        //if($pattern && !strcmp($pattern,'*')) {
            //$pattern = $max;
        //} else {
            //$pattern = $field.$pattern;
        //}
        $token = preg_split('/[,-]/',(strlen($pattern) > 0 ? $pattern : $field));
        for($i=sizeof($token)-1; $i >= 0; $i--) {
            if(strlen($token[$i]) > 0) {
                return $token[$i];
            }
        }
    }

    function TransformWeek($pattern) {
        if($this->dayPattern !== null)
            return;
        $token = trim($pattern,'*');
        for($i=0; $i < sizeof($this->week); $i++) {
            $token = str_replace($this->week[$i],$i,$token);
        }
        $token = preg_split('/([,-])/',$token,-1,PREG_SPLIT_DELIM_CAPTURE);
        for($i=0; $i < sizeof($token); $i++) {
            if(strcmp($token[$i],'-')) {
                $this->dayPattern .= $token[$i];
                continue;
            }
            for($x=$token[$i-1]+1; $x < $token[$i+1]; $x++) {
                $this->dayPattern .= ','.$x;
            }
            $this->dayPattern .= ',';
        }
    }

    function CheckPattern (& $pattern, $rif, & $field, $debug = false) {
        if($debug) {
            echo "\n";
            var_dump($pattern,$rif,$field);
        }
        if(!strcmp($pattern,'*')) {
            $field = $rif;
        } else if(strlen($pattern) == 0) {
            return false;
        } else {
            if($this->Explode($pattern,$rif,$field,',') === false) {
                return false;
            }
        }
        return true;
    }

    // minute
    function BuildMinute() {
        if($this->BuildDate() < $this->now) {
            $d=date("i",strtotime($this->date)+60);
            $this->CheckPattern($this->matches[10][0], $d, $this->dateArray['minute']);
//echo " < min(".date("Y-m-d H:i:s",$this->BuildDate())."); ";
        }
    }
    // hour
    function BuildHour() {
        if($this->BuildDate() < $this->now) {
            $d=date("H",strtotime($this->date)+60*60);
            $this->CheckPattern($this->matches[8][0], $d, $this->dateArray['hour']);
//echo " < hour(".date("Y-m-d H:i:s",$this->BuildDate())."); ";
        }
    }
    // day
    function BuildDay() {
        if($this->BuildDate() < $this->now) {
            if(!preg_match("/[a-z]/i",$this->matches[6][0])) {
                $d=date("d",strtotime($this->date)+24*60*60);
                $this->CheckPattern($this->matches[6][0], $d, $this->dateArray['day']);
                if(checkdate(trim($this->dateArray['month'],' *,-'),
                             trim($this->dateArray['day'],' *,-'),
                             trim($this->dateArray['year'],' *,-')) != true) {
                    $this->dateArray['day'] = $d - 1;
                }
            } else {
                $this->TransformWeek($this->matches[6][0]);
                $d=date("w",strtotime($this->date)) + 1;
                if($d > 6)
                    $d = 0;
                $this->CheckPattern($this->dayPattern, $d, $this->dateArray['day']);
                $this->dateArray['day'] = $this->dateArray['day'] - ($d - 1);
                if($this->dateArray['day'] <= 0) {
                    $this->dateArray['day'] = 7 - ($this->dateArray['day'] * -1);
                }
                $newtime = strtotime($this->date)+$this->dateArray['day']*24*60*60;
                $this->dateArray['day']=date("d",$newtime);
                // check if new week day doesn't exceed limit
                if(($this->dateArray['day']=$this->GetFirstWeekDay($this->dateArray['day'])) === false) {
                    $this->dateArray['day'] = 1;
                } else {
                    // if month changes, day is invalid so reset day to '1' and check for first week day after 'year' building
                    if(date('m',$newtime) != $this->dateArray['month']) {
                        $this->dateArray['day'] = 1;
                    }
                }
            }
//echo " < day(".date("Y-m-d H:i:s",$this->BuildDate())."); ";
        }
    }
    // month
    function BuildMonth() {
        if($this->BuildDate() < $this->now) {
            $d=date('m',$this->now)+1;
            if($d > 12) $d = 1;
            $this->CheckPattern($this->matches[4][0], $d, $this->dateArray['month']);
            if(checkdate(trim($this->dateArray['month'],' *,-'),
                         trim($this->dateArray['day'],' *,-'),
                         trim($this->dateArray['year'],' *,-')) != true) {
                $this->dateArray['month'] = '1';
            }
//echo " < mon(".date("Y-m-d H:i:s",$this->BuildDate())."); ";
        }
    }
    // year
    function BuildYear() {
        if($this->BuildDate() < $this->now) {
            $d = date('Y',$this->now)+1;
            if($this->CheckPattern($this->matches[2][0], $d, $this->dateArray['year']) === false) {
                $this->date = false;
            }
//echo " < year(".date("Y-m-d H:i:s",$this->BuildDate())."); ";
        }
    }

    function GetFirstRun($pattern,$now) {
        $this->pattern = $pattern;
        if(!$this->matches) {
            $this->Parse($pattern,$this->matches);
        }
        if($this->CheckPattern($this->matches[2][0], date('Y',$now), $year) !== true) {
            $year = ($this->matches[1][0] ? $this->matches[1][0] : date('Y',$now));
        }
        if($this->CheckPattern($this->matches[4][0], 1, $month) !== true) {
            $month = ($this->matches[3][0] ? $this->matches[3][0] : 1);
        }
        if(!preg_match("/[a-z]/i",$this->matches[6][0])) {
            if($this->CheckPattern($this->matches[6][0], 1, $day) !== true) {
                $day = ($this->matches[5][0] ? $this->matches[5][0] : 1);
            }
        } else {
            $this->dateArray['year'] = $year;
            $this->dateArray['month'] = $month;
            $this->TransformWeek($this->matches[6][0]);
            $day=$this->GetFirstWeekDay(1);
        }
        if($this->CheckPattern($this->matches[8][0], 0, $hour) !== true) {
            $hour = ($this->matches[7][0] ? $this->matches[7][0] : 0);
        }
        if($this->CheckPattern($this->matches[10][0], 0, $minute) !== true) {
            $minute = ($this->matches[9][0] ? $this->matches[9][0] : 0);
        }
        $this->firstRun = strtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00');
        return $this->firstRun;
    }

    function GetLastRun() {
        return $this->GetLastWeekDay(
                   $this->GetLastToken($this->matches[1][0], $this->matches[2][0], 2019),
                   $this->GetLastToken($this->matches[3][0], $this->matches[4][0], 12),
                   $this->GetLastToken($this->matches[5][0], $this->matches[6][0], 31),
                   $this->GetLastToken($this->matches[7][0], $this->matches[8][0], 23),
                   $this->GetLastToken($this->matches[9][0], $this->matches[10][0], 59));
    }

    function Renew($pattern, $date, $timecheck) {
        $retval = false;
        $loop = 365;

        $this->pattern = $pattern;
        //$this->now = $now;
        if(!$this->matches) {
            $this->Parse($this->pattern,$this->matches);
        }
        $this->date = $date;
        $this->BuildDateArray($this->date);
        $this->BuildDate();
        if($this->lastRun === null) {
            $this->lastRun = $this->GetLastRun();
        }
        if($this->lastRun <= $timecheck) {
            $this->date = false;
            $retval = false;
            return $retval;
        }
        while(1) {
            $this->now = strtotime($this->date) + 1; // sum 1 so now is greater then original date
            $this->BuildMinute();
            $this->BuildHour();
            $this->BuildDay();
            $this->BuildMonth();
            $this->BuildYear();
            // check for week day
            if(preg_match("/[a-z]/i",$this->matches[6][0]) && $this->dateArray['day'] == 1) {
                $this->dateArray['day'] = $this->GetFirstWeekDay($this->dateArray['day']);
                $this->BuildDate();
            }
            if($loop-- <= 0) {
                echo nl2br("\nOops! there is an internal error; please send me your pattern at: giu.lucarelli@gmail.com\n\n");
                $this->date = false;
                $retval = false;
                break;
            }
            if(date('Y',$this->time) == 1970) {
                echo "TIME ERROR ".date("Y-m-d D H:i:s",$this->time);
                break;
            }
            if($this->time > $timecheck) {
                $retval = true;
                break;
            }
        }
        if($retval !== false) {
            $retval = $this->time;
        }
        return $retval;
    }
};
