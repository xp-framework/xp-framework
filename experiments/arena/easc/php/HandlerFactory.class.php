<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('XpProtocolHandler');

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class HandlerFactory extends Object {
  
    function &handlerFor($scheme) {
      switch ($scheme) {
        case 'xp': return new XpProtocolHandler();
        default: return xp::null();
      }
    }
  }
?>
