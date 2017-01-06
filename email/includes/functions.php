<?php
/*
 * @(#) $Header: /var/cvsroot/pop3ml/includes/Attic/functions.php,v 1.5.2.3 2010/01/07 10:31:36 cvs Exp $
 */
    if (!$_SERVER['PHP_AUTH_USER']) {
        header("WWW-Authenticate: Basic realm=\"Pop3ml\"");
        header("HTTP/1.0 401 Unauthorized");
        exit;
    } else {
        if (!isset($global_options['passwdfile'][$_SERVER['PHP_AUTH_USER']])
        || $_SERVER['PHP_AUTH_PW'] != $global_options['passwdfile'][$_SERVER['PHP_AUTH_USER']]) {
            $global_options['username'] = '';
            exit;
        }
    }

    $global_options['username'] = $_SERVER['PHP_AUTH_USER'];
    $global_options['password'] = $global_options['passwdfile'][$global_options['username']];
