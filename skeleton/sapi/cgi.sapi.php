<?php
/* This file provides the CLI sapi for the XP framework
 * 
 * $Id$
 */

  // {{{ proto array getallheaders(void)
  //     See php://getallheaders
  if (!function_exists('getallheaders')) { function getallheaders() {
    foreach ($_COOKIE as $k => $v) {
      $cookie= '&'.$k.'='.$v;
    }
    return array(
      'Accept'           => $_ENV['HTTP_ACCEPT'],
      'Accept-Charset'   => $_ENV['HTTP_ACCEPT_CHARSET'],
      'Accept-Encoding'  => $_ENV['HTTP_ACCEPT_ENCODING'],
      'Accept-Language'  => $_ENV['HTTP_ACCEPT_LANGUAGE'],
      'Connection'       => $_ENV['HTTP_KEEP_ALIVE'],
      'Cookie'           => substr($cookie, 1),
      'Host'             => $_ENV['HTTP_HOST'],
      'Referer'          => $_ENV['HTTP_REFERER'],
      'User-Agent'       => $_ENV['HTTP_USER_AGENT'],
    );
  }}
  // }}}
  
  // Get rid of REDIRECT_* variables and copy them to the "global" namespace
  foreach (array('_ENV', '_SERVER') as $var) {
    foreach ($GLOBALS[$var] as $k => $v) {
      if ('REDIRECT_' != substr($k, 0, 9)) continue;
      $GLOBALS[$var][substr($k, 9)]= $v;
      unset($GLOBALS[$var][$k]);
      putenv(substr($k, 9).'='.$v);
    }
  }
?>
