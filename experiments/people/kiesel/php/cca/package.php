<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(    
    'io.File',
    'io.Folder',
    'io.cca.Archive'
  );
  
  // {{{ function recurse()
  function recurse(&$archive, $base, $path) {
    $f= &new Folder($path);
    
    while (FALSE !== ($entry= $f->getEntry())) {
      if ('.' == $entry{0}) continue;
      
      $file= &new File($f->getURI().DIRECTORY_SEPARATOR.$entry);
      if (is_dir($file->getURI())) {
        recurse($archive, $base, $file->getURI());
        continue;
      }
      
      if ('.class.php' != substr($file->getURI(), -10)) continue;
      
      // Remove base
      $fqcn= substr($file->getURI(), strlen($base));
      
      // Remove .class.php
      $fqcn= substr($fqcn, 0, -10);
      
      // Translate / into .
      $fqcn= trim(strtr($fqcn, DIRECTORY_SEPARATOR, '.'), '.');
      
      // Add package
      $fqcn= $fqcn;
      
      if ($archive->contains($fqcn)) {
        Console::writeLine('!!! Class '.$fqcn.' already in archive, skipping...');
        continue;
      }
      $archive->add($file, $fqcn);
    }
    
    $f->close();
  }
  // }}}

  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
  
  $archive= &new Archive(new File($p->value('file', 'f', 'php://stdout')));
  $archive->open(ARCHIVE_CREATE);
  
  for ($i= 1; $i < $p->count; $i++) {
    $value= $p->value($i);
    if ('-' == $value{0}) continue;
    
    $start= realpath($value);
    recurse(
      $archive,
      $start,
      $start
    );
  }
  
  $archive->create();
  Console::writeLinef('===> %d classes added', sizeof($archive->_index));
?>
