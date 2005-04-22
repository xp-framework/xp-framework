<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * (Insert class' description here)
   *
   * @ext      extension
   * @see      reference
   * @purpose  purpose
   */
  class HandlerFactory extends Object {
  
    /**
     * Factory method
     *
     * @model   static
     * @access  public
     * @param   &peer.URL url
     * @return  &rmi.protocol.ProtocolHandler
     * @throws  lang.IllegalArgumentException
     */
    function &factory(&$url) {
      switch (strtok($url->getScheme(), '+')) {
        case 'xp':
          $handler= 'rmi.protocol.default.DefaultProtocolHandler';
          break;

        default:
          return throw(new IllegalArgumentException('Unknown scheme "'.$url->getScheme().'"'));
      }
      $options= strtok('');
      
      // Load and instantiate class
      try(); {
        $class= &XPClass::forName($handler) &&
        $instance= &$class->newInstance($options);
      } if (catch('ClassNotFoundException', $e)) {
        return throw($e);
      }
      
      // Initialize and return instance
      return $instance;
    }
  }
?>
