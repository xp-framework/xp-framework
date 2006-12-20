<?php
/* This file is part of the XP framework's examples
 *
 * $Id$
 */
  require('lang.base.php');
  xp::sapi('cli');
  uses(
    'xml.uddi.UDDIServer', 
    'xml.uddi.FindBusinessesCommand',
    'util.log.Logger', 
    'util.log.ConsoleAppender'
  );
  
  // {{{ main
  $cat= Logger::getInstance()->getCategory();
  $cat->addAppender(new ConsoleAppender());
  
  $c= new UDDIServer(
    'http://test.uddi.microsoft.com/inquire', 
    'https://test.uddi.microsoft.com/publish'
  );
  $c->setTrace($cat);
  try {
    $r= $c->invoke(new FindBusinessesCommand(
      array('%IBM%'), 
      array(SORT_BY_DATE_ASC, SORT_BY_NAME_ASC),
      5
    ));
  } catch (XPException $e) {
    $e->printStackTrace();
    exit(-1);
  }
  
  echo $r->toString()
  // }}}
?>
