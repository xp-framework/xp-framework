<?php
/* This file is part of the XP framework's port "Album"
 *
 * $Id: index.php 4695 2005-02-20 00:57:14Z friebe $ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.development');
  uses(
    'name.kiesel.pxl.scriptlet.PxlScriptlet',
    'util.PropertyManager'
  );
  
  // {{{ main
  $pm= &PropertyManager::getInstance();
  $pm->configure('../etc/');

  scriptlet::run(new PxlScriptlet(
    new ClassLoader('name.kiesel.pxl.scriptlet'), 
    '../xsl/'
  ));
  // }}}  
?>
