<?php namespace net\xp_framework\unittest\remote;

use remote\NameNotFoundException;
use remote\protocol\ProtocolHandler;


/**
 * Handles the "mock" protocol
 *
 * @see      xp://remote.protocol.XpProtocolHandler
 * @purpose  Protocol Handler
 */
class MockProtocolHandler extends \lang\Object implements ProtocolHandler {
  public
    $server= array('initialized' => false);

  /**
   * Initialize this protocol handler
   *
   * @param   peer.URL proxy
   * @throws  io.IOException in case connecting fails
   */
  public function initialize($proxy) {
    if (!$this->server['available']) {
      throw new \io\IOException('Cannot connect to '.$proxy->getHost());
    }
    if ($this->server['initialized']) {
      throw new \io\IOException('Already initialized');
    }
    $this->server['initialized']= true;
  }
  
  /**
   * Look up an object by its name
   *
   * @param   string name
   * @param   lang.Object
   * @throws  remote.NameNotFoundException in case the given name could not be found
   * @throws  remote.RemoteException for any other error
   */
  public function lookup($name) {
    if (!isset($this->server['ctx'][$name])) {
      throw new NameNotFoundException($name.' not bound', null);
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
   * @return  mixed
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

  /**
   * Set timeout
   *
   * @param double timeout
   */
  public function setTimeout($timeout) {
  }
} 
