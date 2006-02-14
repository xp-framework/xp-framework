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
     * Static initializer. Registers the default protocol "xp" with this
     * factory.
     *
     * @model   static
     * @access  public
     */
    function __static() {
      HandlerFactory::protocol('xp', XPClass::forName('XpProtocolHandler'));
    }
    
    /**
     * Register or retrieve protocol handlers for/by a specified type
     *
     * @access  public
     * @param   string type
     * @param   &XPClass handler a class representing a ProtocolHandler
     * @return  &ProtocolHandler
     */
    function &protocol($type, &$handler) {
      static $handlers= array();
      
      if (NULL !== $handler) $handlers[$type]= &$handler;
      if (!isset($handlers[$type])) return xp::null();
      return $handlers[$type];
    }
  
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
      
      $handler= &HandlerFactory::protocol($type, $handler= NULL);
      return $handler->newInstance($option);
    }
  }
?>
