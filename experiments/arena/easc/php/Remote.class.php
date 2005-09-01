<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL', 'HandlerFactory', 'RemoteInterfaceMapping');

  /**
   * Entry class for all remote operations
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
   * @see      xp://HandlerFactory
   * @purpose  RMI
   */
  class Remote extends Object {
    var
      $_handler       = NULL;

    /**
     * Static initializer. Sets up serializer.
     *
     * @model   static
     * @access  public
     */
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
      try(); {
        $this->_handler= &HandlerFactory::handlerFor($proxy->getScheme());
      } if (catch('NullPointerException', $e)) {
        return throw($e);
      }
      $this->_handler->initialize($proxy);
    }
    
    /**
     * Retrieve remote instance for 
     *
     * @model   static
     * @access  public
     * @param   string dsn
     * @return  &Remote
     */
    function &forName($dsn) {
      static $instances= array();
      
      if (!isset($instances[$dsn])) {
        try(); {
          $instance= new Remote(new URL($dsn));
        } if (catch('NullPointerException', $e)) {
          return throw($e);
        }
        $instances[$dsn]= &$instance;
      }
      return $instances[$dsn];
    }
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @return  &lang.Object
     */
    function &lookup($name) {
      return $this->_handler->lookup($name);
    }
  }
?>
