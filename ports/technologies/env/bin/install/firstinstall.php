<?php
/* This class is part of the XP framework
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

Usage: php %s installer.conf [-f files|--files=files] [-v|--verbose] [-?|--help]

Example installer.conf:

[target]
user="cgi"
hosts="host1|host2"
base="/home/httpd/"

[release]
name="EVENT"
__
    , $p->value(0));
    exit(-2);
  }
  
  // Check whether to be verbose
  $VERBOSE= $p->exists('verbose');
  Console::writeLine('===> Creating environment for configuration ', $p->value(1), $VERBOSE ? ' (+v)' : '');
  
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

  // Command to prepare checkout directory
  $ssh1= sprintf(<<<__
ssh -A %s@%%s 'cd %3\$s ;
  if [ -d %4\$s ]; then
    echo "----> Project already installed."
    exit 1
  else
    mkdir %4\$s
    chown %2\$s %4\$s
  fi
  '
__
    ,
    $conf->readString('target', 'masteruser', 'root'),
    $conf->readString('target', 'user', 'cgi'),
    rtrim($conf->readString('target', 'base', '/usr/local/lib'), '/'),
    rtrim($rel_dir, '/')
  );

  // Command to actually checkout things...
  $ssh2= sprintf(<<<__
ssh -A %s@%%s 'cd %s/%s &&
  svn co svn://php3.de/xp/%s . 2>&1
  '
__
    ,
    $conf->readString('target', 'user', 'cgi'),
    rtrim($conf->readString('target', 'base', '/usr/local/lib/xp'), '/'),
    rtrim($rel_dir, '/'),
    rtrim($rel_src, '/')
  );
  
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
  
  foreach ($hosts as $host) {
    Console::writeLine('===> Installing for ', $host, ' tag ', $rel_src);
    $cmd= sprintf ($ssh1, $host);
    $VERBOSE && Console::writeLine('---> Executing ', $cmd);
    try(); {
      $p= &new Process($cmd);
      $p->close();
    } if (catch('IOException', $e)) {
      $e->printStackTrace(STDERR);
      exit(-1);
    }

    // Non-zero return value indicates failure, so continue with next server...
    if (0 != $p->exitValue()) {
      Console::writeLine('*** Project already installed at ', $host);
      continue;
    }
    
    $cmd= sprintf ($ssh2, $host);
    $VERBOSE && Console::writeLine('---> Executing ', $cmd);
    try(); {
      $p= &new Process($cmd);
    } if (catch('IOException', $e)) {
      $e->printStackTrace(STDERR);
      exit(-1);
    }
    
    $return= $p->out->readLine();
    switch ($return{0}) {
      case '-':
        Console::writeLine('*** Failed for ', substr($return, 1));
        break;
      
      case '+':
        Console::writeLine('---> Success for ', substr($return, 1));
        break;
    }
    
    while ((FALSE !== ($l= $p->out->readLine())) && !$p->out->eof()) {
      if (!$VERBOSE && ('cvs server: ' == substr($l, 0, 12))) continue;
      Console::writeLine('     >> ', $l);
    }
    
    // Read errors
    while ((FALSE !== ($e= $p->err->readLine())) && !$p->err->eof()) {
      Console::writeLine('     !! ', $e);
    }
    $p->close();
  }
  
  Console::writeLine('===> Done (session ended at ', date('r'), ')');
  // }}}
?>
