<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.URL', 
    'remote.HandlerInstancePool', 
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
   * @test     xp://net.xp_framework.unittest.remote.RemoteTest
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
      Serializer::exceptionName('naming/NameNotFound', 'remote.NameNotFoundException');
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
      
      if (isset($instances[$dsn])) return $instances[$dsn];

      $pool= &HandlerInstancePool::getInstance();
      foreach (explode(',', $dsn) as $spec) {
        $url= &new URL($spec);
        $e= $instance= NULL;
        try(); {
          $instance= &new Remote();
          $instance->_handler= &$pool->acquire($url);
          $instance->_handler && $instance->_handler->initialize($url);
        } if (catch('RemoteException', $e)) {
          continue;   // try next
        } if (catch('Exception', $e)) {
          $e= &new RemoteException($e->getMessage(), $e);
          continue;   // try next
        }

        // Success, cache instance and return
        $instances[$dsn]= &$instance;
        return $instance;
      }

      // No more active hosts
      return throw($e);
    }
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @return  &lang.Object
     * @throws  remote.NameNotFoundException in case the given name could not be found
     * @throws  remote.RemoteException for any other error
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
