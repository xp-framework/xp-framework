<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogContext');

  /**
   * String Log Context
   *
   */
  class StringLogContext extends Object implements LogContext {
    protected
      $message,
      $cat;

    /**
     * Constructor of LogContext
     *
     */
    public function __construct($message) {
      $this->message= $message;
    }

    /**
     * Bind this context to a LogCategory
     *
     * @param   util.log.LogCategory cat
     * @throws  lang.IllegalStateException if already bound
     */
    public function bind(LogCategory $cat) {
      if ($this->cat) {
        throw new IllegalStateException('Can only bind to one LogCategory.');
      }
      
      $this->cat= $cat;
    }

    /**
     * Leave this context; unbinds from LogCategory
     *
     */
    public function leave() {
      if (!$this->cat) return;
      $this->cat->leaveContext($this);
      $this->cat= NULL;
    }

    /**
     * Retrieve message
     *
     * @return  string
     */
    public function format() {
      return $this->message;
    }

    /**
     * Equals method
     *
     * @param   self cmp
     * @return  bool
     */
    public function equals($cmp) {
      if (!$cmp instanceof self) return FALSE;
      return $this->message == $cmp->message;
    }

    /**
     * Retrieve string representation
     *
     * @return  string
     */
    public function toString() {
      return $this->getClassName().'@('.$this->hashCode().') { "'.$this->message.'" }';
    }
  }
?>
