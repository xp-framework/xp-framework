<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('remote.HandlerFactory', 'util.collections.HashTable');

  /**
   * Pool of handler instances
   *
   * @see      xp://remote.HandlerFactory
   * @purpose  Pool
   */
  class HandlerInstancePool extends HashTable {
    public
      $pool= NULL;

    /**
     * Constructor
     *
     * @access  protected
     */
    public function __construct() {
      $this->pool= &new HashTable();
    }
    
    /**
     * Creates a string representation of this object
     *
     * @access  public
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
     * @model   static
     * @access  public
     * @return  &remote.HandlerInstancePool
     */
    public function &getInstance() {
      static $instance= NULL;
      
      if (!isset($instance)) $instance= new HandlerInstancePool();
      return $instance;
    }

    /**
     * Pool a handler instance
     *
     * @access  public
     * @param   &peer.URL url
     * @param   &remote.protocol.ProtocolHandler instance
     * @return  &remote.protocol.ProtocolHandler the pooled instance
     */
    public function &pool(&$url, &$instance) {
      $this->pool->put($url, $instance);
      return $instance;
    }
  
    /**
     * Acquire a handler instance
     *
     * @access  public
     * @param   &peer.URL url
     * @return  &remote.protocol.ProtocolHandler
     * @throws  remote.protocol.UnknownProtocolException
     */
    public function &acquire(&$url) {
      if ($this->pool->containsKey($url)) return $this->pool->get($url);

      sscanf($url->getScheme(), '%[^+]+%s', $type, $option);
      try {
        $class= &HandlerFactory::handlerFor($type);
      } catch (Exception $e) {
        throw($e);
      }

      $instance= &$this->pool($url, $class->newInstance($option));
      return $instance;
    }
  }
?>
