<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('peer.URL', 'remote.RemoteException');

  /**
   * Protocol handler interface
   *
   * @see      xp://remote.HandlerFactory
   * @purpose  Interface
   */
  interface ProtocolHandler {

    /**
     * Initialize this protocol handler
     *
     * @access  public
     * @param   &peer.URL proxy
     * @throws  remote.RemoteException
     */
    public function initialize(&$proxy);
    
    /**
     * Look up an object by its name
     *
     * @access  public
     * @param   string name
     * @param   &lang.Object
     * @throws  remote.RemoteException
     */
    public function &lookup($name);

    /**
     * Invoke a method on a given object id with given method name
     * and given arguments
     *
     * @access  public
     * @param   int oid
     * @param   string method
     * @param   mixed[] args
     * @return  &mixed
     * @throws  remote.RemoteException
     */
    public function &invoke($oid, $method, $args);
  }
?>
