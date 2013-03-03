<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.Context');

  /**
   * Nested Log Context
   *
   * @see http://logging.apache.org/log4j/1.2/apidocs/org/apache/log4j/NDC.html
   */
  class NestedLogContext extends Object implements util·log·Context {
    protected $queue= array();

    /**
     * Push new context information on the queue
     *
     * @param  string info
     * @return void
     */
    public function push($info) {
      $this->queue[]= trim($info);
    }

    /**
     * Pop and remove the last context information from the queue
     *
     * @return string NULL if the current diagnostic queue is empty
     */
    public function pop() {
      return array_pop($this->queue);
    }

    /**
     * Looks at the last context at the top of the queue without removing it
     *
     * @return string
     */
    public function peek() {
      if (0 === ($count= count($this->queue))) return NULL;
      return $this->queue[$count - 1];
    }

    /**
     * Get the current nesting depth of this context
     *
     * @return int
     */
    public function getDepth() {
      return count($this->queue);
    }

    /**
     * Clear any nested context information if any
     *
     * @return void
     */
    public function clear() {
      $this->queue= array();
    }

    /**
     * Formats this log context
     *
     * @return string
     */
    public function format() {
      if (0 === $this->getDepth()) return '';
      return implode(' ', $this->queue);
    }

    /**
     * Creates a string representation of this object
     *
     * @return string
     */
    public function toString() {
      return $this->getClassName().'{'.implode(' > ', $this->queue).'}';
    }
  }
?>
