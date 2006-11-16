<?php
/* This file is part of the XP framework's RFC #0084
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'DeprecatedRule',
    'ReportFactory',
    'MovedRule',
    'RenamedRule',
    'io.File',
    'io.collections.FileCollection',
    'io.collections.iterate.FilteredIOCollectionIterator',
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

  // {{{ Rule definitions
  $rules= array(
    'gui.gtk'                 => new MovedRule('org.gnome'),
    'org.json'                => new RenamedRule('webservices.json'),
    'xml.xmlrpc'              => new RenamedRule('webservices.xmlrpc'),
    'xml.soap'                => new RenamedRule('webservices.soap'),
    'xml.wddx'                => new RenamedRule('webservices.wddx'),
    'xml.uddi'                => new RenamedRule('webservices.uddi'),
    'xml.xp'                  => new DeprecatedRule(array('xml.meta')),
    'io.cca'                  => new DeprecatedRule(array('lang.archive')),
    'util.profiling.unittest' => new RenamedRule('unittest'),
    'util.archive'            => new MovedRule('org.gnu.tar'),
    'util.adt'                => new DeprecatedRule(array('util.collections')),
    'util.registry'           => new DeprecatedRule(),
    'util.mp3'                => new MovedRule('de.fraunhofer.mp3'),
    'peer.ajp'                => new MovedRule('org.apache.ajp'),
    'peer.cvsclient'          => new MovedRule('org.cvshome'),
    'text.apidoc'             => new DeprecatedRule(array('text.doclet')),
    'text.translator'         => new MovedRule('net.schweikhardt'),
    'net.planet-xp'           => new DeprecatedRule(),
  );
  // }}}
  
  // {{{ main
  $p= &new ParamString();
  if (!$p->exists(1) || $p->exists('help', '?')) {
    Console::writeLine(<<<__
Creates a report for all files in a given directpry

Usage: php report.php <base_directory> [-O output]
__
    );
    exit(1);
  }

  try(); {
    $report= &ReportFactory::factory($p->value('report', 'r', 'text'));
    $scan= &new FileCollection($p->value(1));
    $out= &new File($p->value('output', 'O', 'rfc-0084_'.basename($scan->getURI()).'.report'));
  } if (catch('Exception', $e)) {
    $e->printStackTrace();
    exit(-1);
  }

  Console::writeLine('===> Generating ', $report->getType(), ' report for ', $scan->getURI(), ' to ', $out->getURI());
  for (
    $it= &new FilteredIOCollectionIterator($scan, new ExtensionEqualsFilter('.php'), TRUE);
    $it->hasNext();
  ) {
    performWork($it->next(), $report, $rules);
  }
  
  Console::writeLine();
  Console::writeLine('---> Creating summary');
  $report->summarize($scan, $out, $rules);
  Console::writeLine('===> Done');
  // }}}
?>
