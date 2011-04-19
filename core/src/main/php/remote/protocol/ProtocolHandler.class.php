<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL', 'remote.RemoteException', 'util.log.Traceable');

  /**
   * Protocol handler interface
   *
   * @see      xp://remote.HandlerFactory
   * @purpose  Interface
   */
  interface ProtocolHandler extends Traceable {

    /**
     * Initialize this protocol handler
     *
     * @param   peer.URL proxy
     * @throws  remote.RemoteException
     */
    public function initialize($proxy);
    
    /**
     * Look up an object by its name
     *
     * @param   string name
     * @param   lang.Object
     * @throws  remote.RemoteException
     */
    public function lookup($name);

    /**
     * Invoke a method on a given object id with given method name
     * and given arguments
     *
     * @param   int oid
     * @param   string method
     * @param   var[] args
     * @return  var
     * @throws  remote.RemoteException
     */
    public function invoke($oid, $method, $args);

  }
?>
