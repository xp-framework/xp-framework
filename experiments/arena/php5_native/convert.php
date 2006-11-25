<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */

  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'io.File',
    'io.FileUtil',
    'io.Folder',
    'net.xp_framework.tophp5.MigrationDoclet',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.RegexFilter'
  );
  
  // Params
  $p= &new ParamString();
  
  // Display help
  if ($p->exists('help', 'h')) {
    Console::writeLinef('...');
    exit();
  }
 
  $base= realpath($p->value(1));
  $collection= &new FileCollection($base);
  $collection->open();
  
  $iterator= &new FilteredIOCollectionIterator($collection,
    new RegexFilter('#\.class\.php$#'),
    TRUE
  );
  while ($iterator->hasNext()) {
    $element= &$iterator->next();
    
    // Construct classname from that
    $relative= substr($element->getURI(), strlen($base)+ 1);
    $fqcn= strtr(substr($relative, 0, -10), DIRECTORY_SEPARATOR, '.');
    
    // Skip existing files
    $folder= &new Folder(dirname('skeleton2'.DIRECTORY_SEPARATOR.$relative));
    if (!$folder->exists()) {
      Console::write('p');
      $folder->create();
    }
    
    $target= &new File('skeleton2'.DIRECTORY_SEPARATOR.$relative);
    if ($target->exists()) {
      Console::write('s');
      continue;
    }
    
    try(); {
    
      // Kind of hackish, but RootDoc expects a ParamString
      $param= &new ParamString();
      $param->setParams(array(NULL, $fqcn));
      
      $doclet= &new MigrationDoclet();
      RootDoc::start($doclet, $param);
    } if (catch('Exception', $e)) {
      Console::writeLine('*** Could not convert '.$fqcn);
      $e->printStackTrace();
      Console::writeLine();
      xp::gc();
      continue;
    }
    
    FileUtil::setContents($target, $doclet->getOutput());
    Console::write('.');
    xp::gc();
  }
?>
