<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Context');

  /**
   * Mapped Log Context
   *
   * @see http://logging.apache.org/log4j/1.2/apidocs/org/apache/log4j/MDC.html
   */
  class MappedLogContext extends Object implements util·log·Context {
    protected $queue= array();

    /**
     * Put a context value as identified with the key parameter into the current context map
     *
     * @param  string key
     * @param  string info
     * @return void
     */
    public function put($key, $info) {
      $this->queue[$key]= $info;
    }

    /**
     * Check if the specified key exists in the current context map
     *
     * @param  string key
     * @return bool
     */
    public function hasKey($key) {
      return isset($this->queue[$key]);
    }

    /**
     * Get the context information identified by the key parameter
     *
     * @param  string key
     * @return string NULL if the current context map does not contain the specified key
     */
    public function get($key) {
      return isset($this->queue[$key]) ? $this->queue[$key] : NULL;
    }

    /**
     * Clear any nested diagnostic information if any
     *
     * @return void
     */
    public function clear() {
      $this->queue= array();
    }

    /**
     * Remove the the context information identified by the key parameter
     *
     * @param  string key
     * @return void
     */
    public function remove($key) {
      unset($this->queue[$key]);
    }

    /**
     * Formats this logging context
     *
     * @return string
     */
    public function format() {
      if (0 === count($this->queue)) return '';

      $s= array();
      foreach ($this->queue as $key => $info) {
        $s[]= $key.'='.$info;
      }
      return implode(' ', $s);
    }

    /**
     * Creates a string representation of this object
     *
     * @return string
     */
    public function toString() {
      $s= $this->getClassName().'{';
      $s.= 0 === count($this->queue) ? '' : "\n";
      foreach ($this->queue as $key => $info) {
        $s.= '  '.$key.'='.$info."\n";
      }
      $s.= '}';
      return $s;
    }
  }
?>
