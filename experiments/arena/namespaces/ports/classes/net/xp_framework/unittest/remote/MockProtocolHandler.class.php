<?php
/* This class is part of the XP framework
 *
 * $Id: MockProtocolHandler.class.php 9922 2007-04-09 15:31:34Z friebe $ 
 */

  namespace net::xp_framework::unittest::remote;

  ::uses('remote.NameNotFoundException', 'remote.protocol.ProtocolHandler');

  /**
   * Handles the "mock" protocol
   *
   * @see      xp://remote.protocol.XpProtocolHandler
   * @purpose  Protocol Handler
   */
  class MockProtocolHandler extends lang::Object implements remote::protocol::ProtocolHandler {
    public
      $server= array('initialized' => FALSE);

    /**
     * Initialize this protocol handler
     *
     * @param   &peer.URL proxy
     * @throws  io.IOException in case connecting fails
     */
    public function initialize($proxy) {
      if (!$this->server['available']) {
        throw(new io::IOException('Cannot connect to '.$proxy->getHost()));
      }
      if ($this->server['initialized']) {
        throw(new io::IOException('Already initialized'));
      }
      $this->server['initialized']= TRUE;
    }
    
    /**
     * Look up an object by its name
     *
     * @param   string name
     * @param   &lang.Object
     * @throws  remote.NameNotFoundException in case the given name could not be found
     * @throws  remote.RemoteException for any other error
     */
    public function lookup($name) {
      if (!isset($this->server['ctx'][$name])) {
        throw(new remote::NameNotFoundException($name.' not bound'));
      }
      
      return $this->server['ctx'][$name];
    }

    /**
     * Begin a transaction
     *
     * @param   UserTransaction tran
     * @param   bool
     */
    public function begin($tran) {
    }

    /**
     * Rollback a transaction
     *
     * @param   UserTransaction tran
     * @param   bool
     */
    public function rollback($tran) {
    }

    /**
     * Commit a transaction
     *
     * @param   UserTransaction tran
     * @param   bool
     */
    public function commit($tran) {
    }

    /**
     * Invoke a method on a given object id with given method name
     * and given arguments
     *
     * @param   int oid
     * @param   string method
     * @param   mixed[] args
     * @return  &mixed
     */
    public function invoke($oid, $method, $args) {
    }

    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat) {
    }
  } 
?>
