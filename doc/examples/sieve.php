<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses('peer.sieve.SieveClient', 'io.FileUtil', 'io.File', 'peer.URL');
  
  $p= new ParamString();

  if ($p->count < 2) {
    Console::writeLinef('%s: --host=sieve://user:pass@host/ --mode=get|put [--file=filename]', $p->value(0));
    exit(1);
  }
  
  $url= new URL($p->value('host'));
  
  $s= new SieveClient($url->getHost());
  $s->connect();
  $s->authenticate(
    SIEVE_SASL_PLAIN, 
    $url->getUser(''), 
    $url->getPassword('')
  );
  
  switch ($p->value('mode')) {
    case 'put': {
      try {
        $f= new File($p->value('file'));
        $script= new SieveScript($p->value('file'));
        $script->setCode(FileUtil::getContents($f));
      } if (catch ('IOException', $e)) {
        $e->printStackTrace();
        exit(-1);
      }
      
      $s->putScript($script);
      break;
    }
        
    case 'get':
    default: {
      foreach ($s->getScripts() as $script) {
        try {
          $f= new File($script->getName());
          Console::writeLinef('===> Saving %s in %s', $script->getName(), $f->getURI());
          FileUtil::setContents($f, $script->getCode());
        } if (catch ('IOException', $e)) {
          $e->printStackTrace();
          exit(-1);
        }
      }
      
      break;    
    }
  }
  
  $s->close();
  
?>
