<?php
/* This file is part of the XP documentation
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('xml.xp.XMLEncoder', 'util.cmd.ParamString');
  
  $p= &new ParamString();
  try(); {
    $name= ClassLoader::loadClass($p->value(1));
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
    exit();
  }
  
  $d= &new XMLEncoder(new File('php://stdout'));
  $d->writeObject(new $name());
  $d->close();  
?>
