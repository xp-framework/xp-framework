<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.server.ForkingServer',
    'rmi.server.RMIConnectionListener'
  );

  /**
   * RMI server
   *
   * @see      xp://peer.server.ForkingServer 
   * @see      xp://rmi.server.RMIServer
   * @purpose  Base class
   */
  class ForkingRMIServer extends ForkingServer {
  
    /**
     * Constructor
     *
     * @access  public
     * @param   string host default '127.0.0.1' the address to bind to
     * @param   int port default 1061 the port to bind to
     */
    public function __construct($host= '127.0.0.1', $port= 1061) {
      self::addListener(new RMIConnectionListener());
      parent::__construct($host, $port);
    }
    
    /**
     * Set a logcategory for tracing
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    public function setTrace(&$cat) {
      $this->listeners[0]->setTrace($cat);
    }
  }
?>
