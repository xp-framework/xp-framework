<?php
/* This file is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses(
    'text.parser.CSVParser', 
    'io.File', 
    'util.Date', 
    'util.Calendar', 
    'xml.Tree',
    'util.cmd.ParamString'
  );
  
  // {{{ main
  $param= &new ParamString();
  if (!$param->exists(1)) {
    printf(
      "Creates an XML representation from a CVS history file\n".
      "Usage: %s <history_file> [--since=<date>] [--max=<max>] [--debug]\n\n",
      $param->value(0)
    );
    exit(-1);
  }
  
  $p= &new CSVParser();
  try(); {
    $p->setInputStream(new File($param->value(1)));
    $p->setHeaderRecord(array(
      'actiondate', 
      'user', 
      'curdir',
      'special',
      'revs',
      'argument'
    ));
  } if (catch('Exception', $e)) {
    $e->printStackTrace(STDERR);
    exit(-1);
  }

  $debug= $param->exists('debug');
  $cmp= &Date::fromString($param->exists('since') 
    ? $param->value('since') 
    : '1970-01-01'
  );
  $history= array(
    'M' => array(),     // Committed
    'R' => array(),     // Removed
    'A' => array()      // Added
  );
  while ($r= $p->getNextRecord()) {
    $debug && fputs(STDERR, "Got record %s\n", var_export($r, 1));
    
    // Only commits, removes and additions
    if (!in_array($r->actiondate{0}, array_keys($history))) {
      $debug && fputs(STDERR, "Omitting %s (![M|R|A])\n", $r->actiondate{0});
      continue;
    }
    
    // Only classes in skeleton/
    if ('skeleton' != substr($r->special, 0, 8)) {
      $debug && fputs(STDERR, "Omitting %s (!^skeleton)\n", $r->special);
      continue;
    }
    if ('.class.php' != substr($r->argument, -10)) {
      $debug && fputs(STDERR, "Omitting %s (!.class.php$)\n", $r->argument);
      continue;
    }

    // Only after date $cmp
    $r->date= &new Date(hexdec(substr($r->actiondate, 1)));
    if ($r->date->isBefore($cmp)) {
      $debug && fputs(STDERR, "Omitting %s (before %s)\n", $r->date->toString(), $cmp->toString());
      continue;
    }
    
    // Fill history
    $history[$r->actiondate{0}][$r->special.$r->argument]= array(
      'date'     => $r->date,
      'file'     => $r->argument,
      'dir'      => $r->special,
      'user'     => $r->user,
      'revision' => $r->revs
    );
  }

  $t= &new Tree();
  foreach (array_keys($history) as $k) {
    uasort($history[$k], create_function(
      '$a, $b', 
      'return $a["date"]->compareTo($b["date"]);'
    ));
    $max= $param->exists('max') ? $param->value('max') : sizeof($history[$k]);
    foreach (array_slice($history[$k], 0, $max) as $entry) {
      $n= &$t->addChild(Node::fromArray(array(
        'collection'    => strtr(substr($entry['dir'], 9), DIRECTORY_SEPARATOR, '.'),
        'class'         => substr($entry['file'], 0, -10),
        'revision'      => $entry['revision'],
        'user'          => $entry['user'],
        'date'          => $entry['date']->toString('Y-m-d H:i'),
      ), 'entry'));
      $n->setAttribute('action', $k);
    }
  }

  echo $t->getDeclaration(), "\n", $t->getSource(FALSE);
  // }}}
?>
