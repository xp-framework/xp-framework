<?php
/* This file is part of the XP documentation
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('xml.xp.XMLDecoder', 'util.cmd.ParamString');
  
  $d= &new XMLDecoder(new File('php://stdin'));
  $o= &$d->readObject();
  $d->close();  
  
  var_dump($o);
?>
