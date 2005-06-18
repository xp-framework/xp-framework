<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('util.Properties', 'lang.Process');
  
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLinef(<<<__
Install script

Usage: php %s installer.conf [-f files|--files=files] [-v|--verbose] [-?|--help] [-F|--force]

Example installer.conf:

[target]
user="cgi"
hosts="host1|host2|production[1..5]"
base="/home/httpd/"

[release]
name="EVENT"
__
    , $p->value(0));
    exit(-2);
  }
  
  // Check whether to be verbose
  $VERBOSE= $p->exists('verbose');
  Console::writeLine('===> Installing for configuration ', $p->value(1), $VERBOSE ? ' (+v)' : '');
  
  // Read configuration
  try(); {
    $conf= &new Properties($p->value(1));
    $conf->reset();
  } if (catch('Exception', $e)) {
    $e->printStackTrace(STDERR);
    exit(-1);
  }
  
  $VERBOSE && Console::writeLine('---> Configured as follows: ', $conf->toString());
  
  // Release modalities:
  $rel= $conf->readString('release', 'name');
  if ($rc= $conf->readString('release', 'candidate', NULL)) {
    $rel_src= 'tags/'.strtoupper($rel).'_RC'.$rc;
    $rel_dir= 'xp.'.strtolower($rel).'-rc'.$rc;;
  } elseif ($sp= $conf->readString('release', 'servicepack', NULL)) {
    $rel_src= 'tags/'.strtoupper($rel).'_SP'.$sp;
    $rel_dir= 'xp.'.strtolower($rel).'-sp'.$sp;
  } elseif ($head= $conf->readString('release', 'head', NULL)) {
    $rel_src= 'trunk';
    $rel_dir= 'xp.'.strtolower($rel).'-head';
  } else {
    $rel_src= 'tags/'.strtoupper($rel).'_RELEASE';
    $rel_dir= 'xp.'.strtolower($rel).'-release';
  }
  
  $ssh= str_replace('//', '/', sprintf(<<<__
ssh -A %s@%%1\$s 'cd %s/%s/ &&
  if ( [ -e .xpinstall.block ] && exit %5\$d ) ; then
    echo "-%%1\$s:"; cat .xpinstall.block
  else
    if [ -e .xpinstall.inf ] ; then 
      cat .xpinstall.inf | sed -e 's/^/!/g' ;
    fi ;
    echo "+%%1\$s:"`pwd`"$ " ;
    svn update '%s' 2>&1 ;
  fi
  '
__
    ,
    $conf->readString('target', 'user', 'cgi'),
    $conf->readString('target', 'base', '/usr/local/lib/'),
    $rel_dir,
    $p->value('files', 'f', ''),
    $p->exists('force', 'F') ? 0xFF : 0x00
  ));

  // Read host list
  $hosts= array();
  foreach ($conf->readArray('target', 'hosts') as $host) {
    if (3 == sscanf($host, '%[^[][%d..%d]', $cluster, $min, $max)) {
      foreach (range($min, $max) as $num) {
        $hosts[]= $cluster.$num;
      }
    } else {
      $hosts[]= $host;
    }
  }

  // Create a thread for each remote host
  for ($offset= 0; $offset < sizeof($hosts); $offset++) {
    $host= $hosts[$offset];
    
    Console::writeLine('===> Installing for '.$host);
    
    $VERBOSE && Console::writeLine('---> Executing '.$ssh);
    
    try(); {
      $p= &new Process(sprintf($ssh, $host));
    } if (catch('IOException', $e)) {
      $e->printStackTrace(STDERR);
      continue;
    }

    $return= $p->out->readLine();
    switch ($return{0}) {
      case '-': 
        Console::writeLine($host, ']  ***  Failed for ', substr($return, 1));
        break;

      case '!':
        Console::writeLine($host, '] +++  Please note: ', substr($return, 1));
        while ('!' == $return{0}) {
          $return= $p->out->readLine();
          Console::writeLine($host, '     >> ', substr($return, 1));
        }
        Console::writeLine($host, '] ---> Success for ', substr($return, 1));
        break;

      case '+':
        Console::writeLine($host, '] ---> Success for ', substr($return, 1));
        break;
    }
    while ((FALSE !== ($l= $p->out->readLine())) && !$p->out->eof()) {

      // Hack to beautify script output
      if (preg_match ('/^cvs server: ([^ ]+) is no longer in the repository$/', $l, $m)) {
        $l= 'D '.$m[1];
      }

      if (!$this->verbose && ('cvs server: ' == substr($l, 0, 12))) continue;
      Console::writeLine($host, ']      >> ', $l);
    }

    // Read errors
    while ((FALSE !== ($e= $p->err->readLine())) && !$p->err->eof()) {
      Console::writeLine($host, ']      !! ', $e);
    }
    $p->close();
  }
  Console::writeLine('===> Done (session ended at ', date('r'), ')');
  // }}}
?>
