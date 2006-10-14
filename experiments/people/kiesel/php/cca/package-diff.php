<?php
/* This file is part of the XP framework
 *
 * $Id: pkgls.php 7946 2006-09-21 14:19:34Z kiesel $ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(    
    'io.File',
    'io.TempFile',
    'io.FileUtil',
    'lang.archive.Archive'
  );
  
  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('%s: [-v] <archive1> <archive2>', $p->value(0));
    Console::writeLine('Shows the diffs between two XAR archives.');
    Console::writeLine('   -v  = shows unified diff if files differ.');
    exit();
  }
  
  $from= &new Archive(new File($p->value($p->count- 2)));
  $from->open(ARCHIVE_READ);

  $to= &new Archive(new File($p->value($p->count- 1)));
  $to->open(ARCHIVE_READ);
  
  $seen= array();
  while ($entry= $from->getEntry()) {
    // Remember file as seen...
    $seen[$entry]= TRUE;
    
    if (!$to->contains($entry)) {
      Console::writeLine('D      '.$entry);
      continue;
    }
    
    if (md5($from->extract($entry)) == md5($to->extract($entry))) {
    
      // Same files
      continue;
    }
    
    // Files do exist but differ
    if ($p->exists('verbose', 'v', FALSE)) {
      Console::writeLine('M      '.$entry);
      try(); {
        FileUtil::setContents($f1= &new TempFile(), $from->extract($entry));
        FileUtil::setContents($f2= &new TempFile(), $to->extract($entry));
        $diff= System::exec('diff -u '.$f1->getURI().' '.$f2->getURI(), '2>&1', FALSE);
        Console::writeLine(implode("\n", $diff));
        
        $f1->unlink();
        $f2->unlink();
      } if (catch('SystemException', $e)) {
        Console::writeLine('!--> Could not fork diff command: '.$e->toString());
      } if (catch('IOException', $e)) {
        $e->printStackTrace();
      }
    } else {
      Console::writeLine('M      '.$entry);
    }
  }
  
  while ($entry= $to->getEntry()) {
  
    // Already seen
    if (isset($seen[$entry])) continue;
    
    if (!$from->contains($entry)) {
      Console::writeLine('A      '.$entry);
    }
  }
?>
