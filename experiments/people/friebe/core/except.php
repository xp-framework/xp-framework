<?php
/* This file is part of the XP framework's peoples' experiments
 *
 * $Id$
 */

  require($argv[1].'/lang.base.php');
  
  try(); {
    XPClass::forName('non.existant.Class');
  } if (catch('ClassNotFoundException', $e)) {
    $e->printStackTrace();
  }
?>
