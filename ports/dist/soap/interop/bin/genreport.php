<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'xml.Tree',
    'io.File',
    'io.FileUtil',
    'util.PropertyManager',
    'util.log.Logger'
  );

  /// {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure(dirname(__FILE__).'/../etc/');
  
  $log= &Logger::getInstance();
  $log->configure($pm->getProperties('log'));

  // Open property file
  $prop= &$pm->getProperties('services');
  
  // Create the tree
  $tree= &new Tree('clients');

  // Generate complete XML file with test results
  $section= $prop->getFirstSection();

  Console::writeLine('===> Generating report file...');
  do {
    try(); {
      $f= &new File(sprintf('log/%s/servicetest.xml', $section));
      
      if (
        $f->exists() && 
        $subtree= &Tree::fromString(FileUtil::getContents($f)) &&
        is('xml.Tree', $subtree)
      ) {
        Console::writeLinef('---> Adding reports for service %s', $section);
        $tree->addChild($subtree->root); 
      }
    } if (catch ('IOException', $e)) {
      
      // Ignore exception
    }
  } while ($section= $prop->getNextSection());
  
  // Store testresult tree
  $reportfile= dirname(__FILE__).'/../log/servicetests.xml';  
  FileUtil::setContents(
    new File($reportfile), 
    $tree->getDeclaration()."\n".$tree->getSource(INDENT_DEFAULT)
  );
  
  Console::writeLine('===> Done. Reportfile saved to ', $reportfile);
  /// }}}
?>
