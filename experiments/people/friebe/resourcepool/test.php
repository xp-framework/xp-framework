<?php
/* This file is part of the XP framework's people's experiment
 *
 * $Id$ 
 */
  require('lang.base.php');
  uses('ResourcePool', 'Test', 'rdbms.DriverManager');
  
  // {{{ main
  $pool= &ResourcePool::getInstance();
  $pool->register(
    'xp://env/rdbms/orders', 
    DriverManager::getConnection('mysql://...')
  );
  
  echo '#1 ', xp::stringOf(new Test()), "\n";
  echo '#2 ', xp::stringOf(new Test()), "\n";
  // }}}
?>
