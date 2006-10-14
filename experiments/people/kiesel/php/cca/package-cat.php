<?php
/* This file is part of the XP framework
 *
 * $Id: pkgls.php 7946 2006-09-21 14:19:34Z kiesel $ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(    
    'io.File',
    'lang.archive.Archive'
  );
  
  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('%s: <archive> <file>', $p->value(0));
    Console::writeLine('Shows the contents of a file in an XP archive');
    exit();
  }
  
  $archive= &new Archive(new File($p->value(1)));
  $archive->open(ARCHIVE_READ);

  if (!$archive->contains($p->value(2))) {
    Console::writeLine($p->value(2).': Not contained in archive.');
    exit(1);
  }
  
  Console::writeLine($archive->extract($p->value(2)));
?>
