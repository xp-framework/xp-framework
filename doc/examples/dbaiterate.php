<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses('io.dba.DBAFile');
  
  // {{{ main
  $dba= new DBAFile('test.db', DBH_GDBM);
  $dba->open(DBO_TRUNC);
  
  // Insert some values
  $dba->insert('date', date('r', time()));
  $dba->insert('gmdate', gmdate('r', time()));
  $dba->insert('user', get_current_user());
  $dba->insert('zend.logoguid', zend_logo_guid());
  $dba->insert('php.logoguid', php_logo_guid());
  
  // Use the iterator functionality
  for ($i= $dba->iterator(); $i->hasNext(); ) {
    $key= $i->next();
    Console::writeLinef('[%-14s] %s', $key, var_export($dba->fetch($key), 1));
  }
  
  // Close and clean up
  $dba->close();
  unlink($dba->getFilename());
  // }}}
?>
