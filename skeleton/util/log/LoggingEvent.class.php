<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('util.log.LogCategory', 'util.log.LogLevel');

  /**
   * A single log event
   *
   * @test    xp://net.xp_framework.unittest.logging.LoggingEventTest
   */
  class LoggingEvent extends Object {
    protected $category= NULL;
    protected $timestamp= 0;
    protected $processId= 0;
    protected $level= 0;
    protected $arguments= array();
    
    /**
     * Creates a new logging event
     *
     * @param   util.log.LogCategory category
     * @param   int timestamp
     * @param   int processId
     * @param   int level one debug, info, warn or error
     * @param   var[] arguments
     */
    public function __construct($category, $timestamp, $processId, $level, array $arguments) {
      $this->category= $category;
      $this->timestamp= $timestamp;
      $this->processId= $processId;
      $this->level= $level;
      $this->arguments= $arguments;
    }
    
    /**
     * Gets category
     *
     * @return  util.log.LogCategory
     */
    public function getCategory() {
      return $this->category;
    }

    /**
     * Gets timestamp
     *
     * @return  int
     */
    public function getTimestamp() {
      return $this->timestamp;
    }

    /**
     * Gets processId
     *
     * @return  int
     */
    public function getProcessId() {
      return $this->processId;
    }

    /**
     * Gets level
     *
     * @see     xp://util.log.LogLevel
     * @return  int
     */
    public function getLevel() {
      return $this->level;
    }

    /**
     * Gets arguments
     *
     * @return  var[]
     */
    public function getArguments() {
      return $this->arguments;
    }

    /**
     * Creates a string representation
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(%s @ %s, PID %d) {%s}',
        $this->getClassName(),
        LogLevel::named($this->level),
        date('r', $this->timestamp),
        $this->processId,
        xp::stringOf($this->arguments)
      );
    }
  }
?>
