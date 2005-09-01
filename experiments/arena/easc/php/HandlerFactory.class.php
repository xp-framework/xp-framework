<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('XpProtocolHandler');

  /**
   * Handler factory implementation
   *
   * @see      xp://XpProtocolHandler
   * @purpose  Factory
   */
  class HandlerFactory extends Object {
  
    /**
     * Retrieve a handler for a given scheme
     *
     * @model   static
     * @access  public
     * @param   string scheme
     * @return  &ProtocolHandler
     */
    function &handlerFor($scheme) {
      sscanf($scheme, '%[^+]+%s', $type, $option);
      switch ($type) {
        case 'xp': return new XpProtocolHandler($option);
        default: return xp::null();
      }
    }
  }
?>
