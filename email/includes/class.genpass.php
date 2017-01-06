<?php 
/*
 * @(#) $Header: /var/cvsroot/pop3ml/includes/Attic/class.genpass.php,v 1.1.2.2 2009/08/27 11:16:10 cvs Exp $
 */
/*
    A simple class to display a mail message from message buffer

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

// from http://wiki.jumba.com.au/wiki/User:Thewebdruid
class GenPass
{
    function CreatePassword($length) {
        $chars = "234567890abcdefghijkmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $i = 0;
        $password = "";
        while ($i <= $length) {
            $password .= $chars{mt_rand(0,strlen($chars))};
            $i++;
        }
        return $password;
    }
};
