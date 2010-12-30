<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  $package= 'net.xp_framework.unittest.core.generics';

  uses('net.xp_framework.unittest.core.generics.IDictionary');

  /**
   * Lookup map
   *
   */
  #[@generic(self= 'V', implements= array('lang.Type, V'))]
  abstract class net·xp_framework·unittest·core·generics·AbstractTypeDictionary extends Object implements net·xp_framework·unittest·core·generics·IDictionary {

  }
?>
