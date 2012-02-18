<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
  class HandlerInstancePool extends Object {
    public
      $pool = NULL,
      $cat  = NULL;

    /**
     * Constructor
     *
     */
    protected function __construct() {
      $this->pool= new HashTable();
    }
    
    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      $s= $this->getClassName().'(size= '.$this->pool->size().")@{\n";
      foreach ($this->pool->keys() as $url) {
        $s.= '  '.$url->getURL().' => '.xp::stringOf($this->pool->get($url))."\n";
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
      
      if (!isset($instance)) $instance= new HandlerInstancePool();
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
    public function acquire($key, $initialize= FALSE) {
      $url= new URL($key);
      $key= new URL($url->getScheme().'://'.$url->getHost());
      if ($this->pool->containsKey($key)) {
        $instance= $this->pool->get($key);
      } else {
        sscanf($url->getScheme(), '%[^+]+%s', $type, $option);
        $class= HandlerFactory::handlerFor($type);
        $instance= $this->pool($key, $class->newInstance($option));
      }

      // Add logger
      if (NULL !== ($cat= $url->getParam('log'))) {
        $instance->setTrace(Logger::getInstance()->getCategory($cat));
      }
      // Set timeout, defaults to 60 seconds
      $instance->setTimeout($url->getParam('timeout', 60));

      $initialize && $instance->initialize($url);
      return $instance;
    }
  }
?>
