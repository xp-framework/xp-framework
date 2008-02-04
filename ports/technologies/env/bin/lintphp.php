<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'lang.Process',
    'lang.System'
  );
  
  $source= '';
  while (!feof(STDIN)) {
    $source.= fgets(STDIN);
  }

  $php_command= '';
  foreach (explode(PATH_SEPARATOR, System::getEnv('PATH')) as $path) {
    if (file_exists($path.DIRECTORY_SEPARATOR.'php')) {
      $php_command= $path.DIRECTORY_SEPARATOR.'php';
      break;
    }
    if (file_exists($path.DIRECTORY_SEPARATOR.'php.exe')) {
      $php_command= $path.DIRECTORY_SEPARATOR.'php.exe';
      break;
    }
  }

  // Fork PHP, feed script to it
  $p= new Process($php_command, array('-l'));
  
  $in= $p->getInputStream();
  $in->write($source);
  $in->close();
  
  $out= &$p->getOutputStream();
  
  while (!$out->eof()) {
    $l= $out->readLine();
    
    if (preg_match('#^(.*) in - on line (\d+)$#', $l, $match)) {
      Console::writeLine($match[2]);
      Console::writeLine($match[1]);
      exit;
    }
  }
?>
