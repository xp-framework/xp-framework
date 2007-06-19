<?php
  require('lang.base.php');
  xp::sapi('cli');
  uses('OutOfMemoryError', 'xml.Tree', 'xml.Node');
  
  function watchMemory() {
    static $limit= NULL;
    
    if (NULL === $limit) {
      sscanf(ini_get('memory_limit'), '%d%s', $limit, $modifier);
      switch ($modifier) {
        case 'k': case 'K': $limit*= 1024; break;
        case 'M': case 'M': $limit*= 1048576; break;
      }
      $limit-= 102400;    // Reserve 100k
    }
    
    $usage= memory_get_usage();
    Console::writeLinef('%.3f, %.3f', $limit / 1024, $usage / 1024);
    if ($usage >= $limit) {
      throw new OutOfMemoryError(sprintf(
        'Memory limit exceeded: %.3f k/%.3f k',
        $usage,
        $limit
      ));
    }
  }
  
  register_tick_function('watchMemory');
  declare(ticks= 100);
  
  $tree= new Tree();
  try {
    for ($i= 0; $i < 100000; $i++) {
      $tree->addChild(new Node('node', $i));
    }
  } catch (OutOfMemoryError $e) {
    delete($tree);
    Console::writeLine('*** ', $e->toString());
  }
  Console::writeLinef('%d: %.3f', $i, memory_get_usage() / 1024);
?>
