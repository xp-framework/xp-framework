<?php
/* This file is part of the XP documentation
 *
 * $Id$
 */
 
  require('lang.base.php');
  uses('xml.xp.XMLEncoder', 'util.cmd.ParamString');
  
  $d= &new XMLEncoder(new File('php://stdout'));
  $d->writeObject(new ParamString());
  $d->close();  
?>
