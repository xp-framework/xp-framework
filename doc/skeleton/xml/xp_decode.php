<?php
/* This file is part of the XP documentation
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('xml.xp.XMLDecoder', 'util.cmd.ParamString');
  
  $p= &new ParamString();
  if ($p->exists('help')) {
    printf("Usage: cat xmlfile | php -q %s\n", basename($p->value(0)));
    exit();
  }
  
  $d= &new XMLDecoder(new File('php://stdin'));
  $o= &$d->readObject();
  $d->close();  
  
  var_dump($o);
?>
