<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'util.log.LogContext'
  );

  /**
   * Nested diagnostic context
   *
   * @see http://logging.apache.org/log4j/1.2/apidocs/org/apache/log4j/NDC.html
   */
  class LogContextNDC extends LogContext {
    protected $queue= array();

    /**
     * Push new diagnostic context information on the NDC queue
     *
     * @param  string info
     * @return void
     */
    public function push($info) {
      $this->queue[]= $info;
    }

    /**
     * Pop and remove the last diagnostic context information from the NDC queue
     *
     * @return string NULL if the current diagnostic queue is empty
     */
    public function pop() {
      return array_pop($this->queue);
    }

    /**
     * Looks at the last diagnostic context at the top of this NDC without removing it
     *
     * @return string
     */
    public function peak() {
      if (0 === ($count= count($this->queue))) return NULL;
      return $this->queue[$count - 1];
    }

    /**
     * Get the current nesting depth of this diagnostic context
     *
     * @return int
     */
    public function getDepth() {
      return count($this->queue);
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
     * Formats a NDC logging context
     *
     * @return string
     */
    public function format() {
      if (0 === $this->getDepth()) return '';
      return implode(' ', $this->queue);
    }
  }
?>