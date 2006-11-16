<?php
/* This file is part of the XP framework's RFC #0084
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'ReportFactory',
    'Rules',
    'io.File',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
    'io.collections.iterate.AnyOfFilter',
    'io.collections.iterate.ExtensionEqualsFilter'
  );
  
  // {{{ void performWork(&io.collections.IOElement e, &Report r, array<string, &Rule> rules) 
  //     Migrates the given element
  function performWork(&$e, &$r, $rules) {
    $f= &new File($e->getURI());
    $f->open(FILE_MODE_READ);
    $l= 1;
    $messages= array();
    while (!$f->eof()) {
      $line= $f->readLine();
      foreach (array_keys($rules) as $package) {
        if (FALSE === ($p= strpos($line, $package))) continue;

        isset($messages[$package]) || $messages[$package]= array();
        $messages[$package][]= array($l, $p, $line);
      }
      $l++;
    }
    $f->close();
    
    if (empty($messages)) {
      Console::write('.');
    } else {
      Console::write('o');
      $r->add($f, $messages);
    }
    delete($f);
  }
  // }}}

  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Creates a report for all files in a given directpry

Usage: 
$ php report.php <basedir> [-O output] [-r report] [-e extensions]

Arguments:
* output defines the file to write the report to
  Default: "./rfc-0084_" + basename(basedir) + ".report"

* report controls the report format. Available: text or html
  Default: text

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
    $report= &ReportFactory::factory($p->value('report', 'r', 'text'));
    $out= &new File($p->value('output', 'O', 'rfc-0084_'.basename($scan->getURI()).'.report'));

    $filters= array();
    foreach (explode(',', $p->value('extensions', 'e', 'php')) as $ext) {
      $filters[]= &new ExtensionEqualsFilter($ext);
    }
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  // Iterate
  Console::writeLine('===> Generating ', $report->getType(), ' report for ', $scan->getURI(), ' to ', $out->getURI());
  for (
    $it= &new FilteredIOCollectionIterator($scan, new AnyOfFilter($filters), TRUE);
    $it->hasNext();
  ) {
    performWork($it->next(), $report, $rules);
  }
  
  Console::writeLine();
  
  if ($report->isEmpty()) {
    Console::writeLine('===> Nothing to be migrated here');
    exit(0);
  }
  
  Console::writeLine('---> Creating summary');
  $report->summarize($scan, $out, $rules);
  Console::writeLine('===> Done');
  // }}}
?>
