<?php
/* This file is part of the XP framework's port "Album"
 *
 * $Id$ 
 */
  require('lang.base.php');
  xp::sapi('scriptlet.production');
  uses(
    'de.thekid.dialog.scriptlet.WebsiteScriptlet',
    'util.PropertyManager'
  );
  
  // {{{ main
  PropertyManager::getInstance()->configure('../etc/');

  scriptlet::run(new WebsiteScriptlet(
    'de.thekid.dialog.scriptlet', 
    '../xsl/'
  ));
  // }}}  
?>
