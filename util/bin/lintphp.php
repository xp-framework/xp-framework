<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('lang.Process');
  
  $source= '';
  while (!feof(STDIN)) {
    $source.= fgets(STDIN);
  }

  // Fork PHP, feed script to it
  $p= &new Process('php -l');
  
  $in= &$p->getInputStream();
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
