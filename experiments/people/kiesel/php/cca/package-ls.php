<?php
/* This file is part of the XP framework
 *
 * $Id$ 
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
    Console::writeLinef('%s: <archive>', $p->value(0));
    Console::writeLine('Shows the contents of an XP archive');
    exit();
  }
  
  $archive= &new Archive(new File($p->value(1)));
  $archive->open(ARCHIVE_READ);
  
  $cnt= $size= 0;
  Console::writeLine('Archive '.$p->value(1).' contains:');
  while ($entry= $archive->getEntry()) {
    $cnt++;
    $size+= ($len= strlen($archive->extract($entry)));
    Console::writeLinef('%10s %s',
      number_format($len, 0, FALSE, '.'),
      $entry
    );
  }
  
  Console::writeLine(str_repeat('=', 80));
  Console::writeLinef('%10s %s',
    number_format($size, 0, FALSE, '.'),
    'Total '.$cnt.' files'
  );
?>
