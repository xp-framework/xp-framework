<?php
/* This file is part of the XP framework's RFC #0084
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'Rules',
    'io.File',
    'io.FileUtil',
    'text.String',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.ExtensionEqualsFilter'
  );
  
  // {{{ void performWork(&io.collections.IOElement e, array<string, &Rule> rules) 
  //     Migrates the given element
  function performWork(&$e, $rules) {
    $file= &new File($e->getURI());
    $source= &new String(FileUtil::getContents($file));
    
    $results= array();
    $changes= FALSE;
    foreach (array_keys($rules) as $package) {
      $result= &$rules[$package]->applyTo($package, $source);
      if (!$result->changesOccured()) continue;
      
      $changes= TRUE;
      isset($results[$package]) || $results[$package]= array();
      $results[$package][]= &$result;
    }
    
    if (!$changes) return;
    Console::writeLine($file->getURI(), ': ', xp::stringOf($results));
    FileUtil::setContents($file, $source->toString());
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Creates a report for all files in a given directpry

Usage: 
$ php migrate.php <basedir> [-e extensions]

Arguments:
* extensions is a comma-separated list of extension to search for
  Default: php
  
__
    );
    exit(1);
  }

  // Initialize
  try(); {
    $scan= &new FileCollection($p->value(1));
    $rules= &Rules::allRules();
    $filters= array();
    foreach (explode(',', $p->value('extensions', 'e', 'php')) as $ext) {
      $filters[]= &new ExtensionEqualsFilter($ext);
    }
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  // Iterate
  Console::writeLine('===> Migrating ', $scan->getURI());
  for (
    $it= &new FilteredIOCollectionIterator($scan, new AnyOfFilter($filters), TRUE);
    $it->hasNext();
  ) {
    performWork($it->next(), $rules);
  }
  
  Console::writeLine();
  Console::writeLine('===> Done');
  // }}}
?>
