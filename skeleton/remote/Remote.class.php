<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.URL', 
    'remote.HandlerFactory', 
    'remote.protocol.RemoteInterfaceMapping', 
    'remote.UserTransaction'
  );

  /**
   * Entry class for all remote operations
   *
   * Example:
   * <code>
   *   try(); {
   *     $remote= &Remote::forName('xp://localhost:6448/');
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
   * @see      xp://remote.HandlerFactory
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
      Serializer::exceptionName('invoke/Exception', 'remote.InvocationException');
      Serializer::packageMapping('net.xp_framework.easc.reflect', 'remote.reflect');
    }
    
    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    function toString() {
      return $this->getClassName().'(handler= '.$this->_handler->toString().')';
    }
    
    /**
     * Retrieve remote instance for a given DSN. Invoking this method
     * twice with the same dsn will result in the same instance.
     *
     * @model   static
     * @access  public
     * @param   string dsn
     * @return  &remote.Remote
     * @throws  remote.RemoteException in case of setup failure
     */
    function &forName($dsn) {
      static $instances= array();
      
      if (!isset($instances[$dsn])) {
        $url= &new URL($dsn);
        try(); {
          $self= &new Remote();
          $self->_handler= &HandlerFactory::handlerFor($url->getScheme());
          $self->_handler && $self->_handler->initialize($url);
        } if (catch('RemoteException', $e)) {
          return throw($e);
        } if (catch('Exception', $e)) {
          return throw(new RemoteException($e->getMessage(), $e));
        }
        $instances[$dsn]= &$self;
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

    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &remote.UserTransaction tran
     * @return  &remote.UserTransaction
     */
    function &begin(&$tran) {
      $this->_handler->begin($tran);
      $tran->_handler= &$this->_handler;
      return $tran;
    }
  }
?>
