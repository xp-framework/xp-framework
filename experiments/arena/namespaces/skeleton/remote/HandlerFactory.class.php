<?php
/* This class is part of the XP framework
 *
 * $Id: HandlerFactory.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote;

  uses(
    'remote.protocol.ProtocolHandler',
    'remote.protocol.UnknownProtocolException',
    'remote.protocol.XpProtocolHandler'
  );

  /**
   * Handler factory implementation. Registers the default protocol "xp" with this
   * factory.
   *
   * @see      xp://remote.protocol.XpProtocolHandler
   * @purpose  Factory
   */
  class HandlerFactory extends lang::Object {
    protected static 
      $instance     = NULL;

    public
      $handlers= array();

    static function __static() {
      self::$instance= new self();
      self::$instance->register('xp', lang::XPClass::forName('remote.protocol.XpProtocolHandler'));
    }

    /**
     * Constructor.
     *
     */
    protected function __construct() {
    }
    
    /**
     * Retrieve the HandlerFactory instance
     * 
     * @return  remote.HandlerFactory
     */
    public static function getInstance() {
      return self::$instance;
    }

    /**
     * Registers protocol handler for a specified type
     *
     * @param   string type
     * @param   lang.XPClass<remote.protocol.ProtocolHandler> handler class
     * @return  lang.XPClass<remote.protocol.ProtocolHandler>
     */
    public function register($type, $handler) {
      $this->handlers[$type]= $handler;
      return $handler;
    }
  
    /**
     * Retrieve a handler for a given scheme
     *
     * @param   string type
     * @return  lang.XPClass<remote.protocol.ProtocolHandler>
     * @throws  remote.protocol.UnknownProtocolException
     */
    public static function handlerFor($type) {
      $self= self::getInstance();
      if (!isset($self->handlers[$type])) {
        throw(new remote::protocol::UnknownProtocolException($type));
      }
      return $self->handlers[$type];
    }
  }
?>
