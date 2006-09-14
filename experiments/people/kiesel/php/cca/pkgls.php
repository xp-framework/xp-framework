<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(    
    'io.File',
    'io.cca.Archive'
  );
  
  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  $archive= &new Archive(new File($p->value(1)));
  $archive->open(ARCHIVE_READ);
  
  $cnt= $size= 0;
  Console::writeLine('Archive '.$p->value(1).' contains:');
  while ($entry= $archive->getEntry()) {
    $cnt++;
    $size+= ($len= strlen($archive->extract($entry)));
    Console::writeLinef(' `- %-50s %10s bytes',
      $entry, 
      number_format($len, 0, FALSE, '.')
    );
  }
  
  Console::writeLine(str_repeat('=', 80));
  Console::writeLinef('    %-50s %10s bytes', 
    'Total '.$cnt.' files', 
    number_format($size, 0, FALSE, '.')
  );
?>
