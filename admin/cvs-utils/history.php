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
  $p= &new CSVParser();
  try(); {
    $p->setInputStream(new File('history'));
    $p->setHeaderRecord(array(
      'actiondate', 
      'user', 
      'curdir',
      'special',
      'revs',
      'argument'
    ));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }
  
  $cmp= &Date::fromString($param->exists(1) ? $param->value(1) : '1970-01-01');
  $history= array(
    'M' => array(),     // Committed
    'R' => array(),     // Removed
    'A' => array()      // Added
  );
  while ($r= $p->getNextRecord()) {

    // Only commits, removes and additions
    if (!in_array($r->actiondate{0}, array_keys($history))) continue;
    
    // Only classes in skeleton/
    if ('skeleton' != substr($r->special, 0, 8)) continue;
    if ('.class.php' != substr($r->argument, -10)) continue;

    // Only after date $cmp
    $r->date= &new Date(hexdec(substr($r->actiondate, 1)));
    if ($r->date->isBefore($cmp)) continue;
    
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
