<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.NameNotFoundException');

  /**
   * Handles the "mock" protocol
   *
   * @see      xp://remote.protocol.XpProtocolHandler
   * @purpose  Protocol Handler
   */
  class MockProtocolHandler extends Object {
    var
      $ctx= NULL;

    /**
     * Initialize this protocol handler
     *
     * @access  public
     * @param   &peer.URL proxy
     */
    function initialize(&$proxy) {
    
      // Simulate connect failure if specified
      if ($message= $proxy->getParam('failto')) {
        return throw(new IOException($message));
      }
    }
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @param   &lang.Object
     * @throws  remote.NameNotFoundException in case the given name could not be found
     * @throws  remote.RemoteException for any other error
     */
    function &lookup($name) {
      if (!isset($this->ctx[$name])) {
        return throw(new NameNotFoundException($name.' not bound'));
      }
      
      return $this->ctx[$name];
    }

    /**
     * Begin a transaction
     *
     * @access  public
     * @param   UserTransaction tran
     * @param   bool
     */
    function begin(&$tran) {
    }

    /**
     * Rollback a transaction
     *
     * @access  public
     * @param   UserTransaction tran
     * @param   bool
     */
    function rollback(&$tran) {
    }

    /**
     * Commit a transaction
     *
     * @access  public
     * @param   UserTransaction tran
     * @param   bool
     */
    function commit(&$tran) {
    }

    /**
     * Invoke a method on a given object id with given method name
     * and given arguments
     *
     * @access  public
     * @param   int oid
     * @param   string method
     * @param   mixed[] args
     * @return  &mixed
     */
    function &invoke($oid, $method, $args) {
    }

  } implements(__FILE__, 'remote.protocol.ProtocolHandler');
?>
