<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'peer.server.Server',
    'rmi.server.RMIConnectionListener'
  );

  /**
   * RMI server
   *
   * Server code:
   * <code>
   *   uses('rmi.server.RMIServer', 'util.registry.storage.MemoryStorage');
   *   
   *   class Test {
   *     var $value;
   *     function hello() {
   *       $args= func_get_args();
   *       return sizeof($args);
   *     }
   *   }
   *   
   *   $registry= RMIRegistry::setup(new MemoryStorage('HKEY_RMI'));
   *   $registry->register(new Test(), 'rmi.RMIObject');
   * 
   *   $server= new RMIServer();
   *   try(); {
   *     $server->init();
   *     $server->service();
   *     $server->shutdown();
   *   } if (catch('Exception', $e)) {
   *     $e->printStackTrace();
   *     exit(-1);
   *   }
   * </code>
   *
   * @see      xp://rmi.RMIObject
   * @purpose  Base class
   */
  class RMIServer extends Server {
  
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
