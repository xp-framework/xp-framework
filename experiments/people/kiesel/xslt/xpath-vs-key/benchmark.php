<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  // Small testscript to do some benchmarking on
  // a stylesheet using xpath expressions and one
  // using the indexed xsl_key attribute.
  require('lang.base.php');
  xp::sapi('cli');
  
  uses (
    'xml.DomXSLProcessor'
  );
  
  function printRUsage() {
    $u= &getrusage();
    Console::writeLinef('  User time: %s.%s', 
      $u['ru_utime.tv_sec'], 
      $u['ru_utime.tv_usec']
    );
    
    Console::writeLinef('System time: %s.%s', 
      $u['ru_stime.tv_sec'], 
      $u['ru_stime.tv_usec']
    );
    unset ($u);
  }
  
  for ($i= 0; $i < 5000; $i++) {
    if ($i % 1000 == 0) {
      printRUsage();
    }
    
    // Process files
    $proc= &new DomXSLProcessor();
    $proc->setXMLFile('test.xml');
    
    // Exchange this to try different approach
    $proc->setXSLFile('test-key.xsl');
    // $proc->setXSLFile('test-nodeset.xsl');
    
    
    try(); {
      $proc->run();
    } if (catch ('TransformerException', $e)) {
      $e->printStackTrace();
      exit (1);
    }
    
    // Console::write ('Output: ', 
    //   var_export($proc->output(), TRUE),
    //   var_export(xp::registry('errors'), TRUE)
    // );
    $proc->__destruct();
    unset ($proc);

    // sleep(1);
  }
  
  Console::writeLine (var_export (getrusage(), TRUE));

?>
