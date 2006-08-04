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
   * To use clustering and fail-over, supply a comma-separated list of
   * remote names as follows:
   * <code>
   *   $remote= &Remote::forName('xp://remote1,xp://remote2');
   * </code>
   * 
   * @test     xp://net.xp_framework.unittest.remote.RemoteTest
   * @see      xp://remote.HandlerFactory
   * @purpose  RMI
   */
  class Remote extends Object {
    public
      $_handler       = NULL;

    /**
     * Returns a string representation of this object
     *
     * @access  public
     * @return  string
     */
    public function toString() {
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
    public static function &forName($dsn) {
      static $instances= array();
      
      $pool= &HandlerInstancePool::getInstance();
      $list= explode(',', $dsn);
      shuffle($list);
      foreach ($list as $key) {
        if (isset($instances[$key])) return $instances[$key];

        // No instance yet, so get it
        $url= new URL($key);
        $e= $instance= NULL;
        try {
          $instance= new Remote();
          $instance->_handler= &$pool->acquire($url);
          $instance->_handler && $instance->_handler->initialize($url);
        } catch (RemoteException $e) {
          continue;   // try next
        } catch (Exception $e) {
          $e= new RemoteException($e->getMessage(), $e);
          continue;   // try next
        }

        // Success, cache instance and return
        $instances[$key]= &$instance;
        return $instance;
      }

      // No more active hosts
      throw($e);
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
    public function &lookup($name) {
      return $this->_handler->lookup($name);
    }

    /**
     * Begin a transaction
     *
     * @access  public
     * @param   &remote.UserTransaction tran
     * @return  &remote.UserTransaction
     */
    public function &begin(&$tran) {
      $this->_handler->begin($tran);
      $tran->_handler= &$this->_handler;
      return $tran;
    }
  }
?>
