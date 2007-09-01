<?php
/* This class is part of the XP framework
 *
 * $Id: HandlerInstancePool.class.php 10594 2007-06-11 10:04:54Z friebe $ 
 */

  namespace remote;

  uses(
    'remote.HandlerFactory',
    'util.collections.HashTable',
    'peer.URL',
    'util.log.Logger'
  );

  /**
   * Pool of handler instances
   *
   * @see      xp://remote.HandlerFactory
   * @purpose  Pool
   */
  class HandlerInstancePool extends lang::Object {
    public
      $pool = NULL,
      $cat  = NULL;

    /**
     * Constructor
     *
     */
    protected function __construct() {
      $this->pool= new util::collections::HashTable();
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'(size= '.$this->pool->size().")@{\n";
      foreach ($this->pool->keys() as $url) {
        $s.= '  '.$url->getURL().' => '.::xp::stringOf($this->pool->get($url))."\n";
      }
      return $s.'}';
    }

    /**
     * Retrieve the HandlerInstancePool instance
     *
     * @return  remote.HandlerInstancePool
     */
    public static function getInstance() {
      static $instance= NULL;
      
      if (!isset($instance)) $instance= new ();
      return $instance;
    }

    /**
     * Pool a handler instance
     *
     * @param   peer.URL url
     * @param   remote.protocol.ProtocolHandler instance
     * @return  remote.protocol.ProtocolHandler the pooled instance
     */
    public function pool($url, $instance) {
      $this->pool->put($url, $instance);
      return $instance;
    }
  
    /**
     * Acquire a handler instance
     *
     * @param   string key
     * @return  remote.protocol.ProtocolHandler
     * @throws  remote.protocol.UnknownProtocolException
     */
    public function acquire($key, $initialize= ) {
      $url= new peer::URL($key);
      $key= new peer::URL($url->getScheme().'://'.$url->getHost());
      if ($this->pool->containsKey($key)) {
        $instance= $this->pool->get($key);
      } else {
        sscanf($url->getScheme(), '%[^+]+%s', $type, $option);
        $class= HandlerFactory::handlerFor($type);
        $instance= $this->pool($key, $class->newInstance($option));
      }

      // Add logger
      if (NULL !== ($cat= $url->getParam('log'))) {
        $instance->setTrace(util::log::Logger::getInstance()->getCategory($cat));
      }

      $initialize && $instance->initialize($url);
      return $instance;
    }
  }
?>
