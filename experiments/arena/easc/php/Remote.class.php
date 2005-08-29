<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL', 'HandlerFactory', 'RemoteInterfaceMapping');

  /**
   * (Insert class' description here)
   *
   * Example:
   * <code>
   *   try(); {
   *     $remote= &Remote::forName('xp://localhost:4448/');
   *     $remote && $calculator= &$remote->lookup($jndiName);
   *   } if (catch('RemoteException', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   *   
   *   Console::writeLine('1 + 1 = ', xp::stringOf($calculator->add(1, 1)));
   *   Console::writeLine('1 - 1 = ', xp::stringOf($calculator->subtract(1, 1)));
   * </code>
   * 
   * @see      reference
   * @purpose  purpose
   */
  class Remote extends Object {
    var
      $_handler       = NULL;

    function __static() {
      Serializer::mapping('I', new RemoteInterfaceMapping());
      Serializer::exceptionName('naming/NameNotFound', 'lang.MethodNotImplementedException');
    }
    
    /**
     * Constructor
     *
     * @access  protected
     * @param   &peer.URL proxy
     */
    function __construct(&$proxy) {
      $this->_handler= &HandlerFactory::handlerFor($proxy->getScheme());
      $this->_handler->initialize($proxy);
    }
    
    /**
     * (Insert method's description here)
     *
     * @model   static
     * @access  
     * @param   
     * @return  
     */
    function &forName($dsn) {
      static $instances= array();
      
      if (!isset($instances[$dsn])) {
        $instances[$dsn]= new Remote(new URL($dsn));
      }
      return $instances[$dsn];
    }
    
    /**
     * (Insert method's description here)
     *
     * @access  
     * @param   
     * @return  
     */
    function &lookup($name) {
      return $this->_handler->lookup($name);
    }
  }
?>
