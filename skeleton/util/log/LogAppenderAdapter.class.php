<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogAppender', 'util.log.Appender');

  /**
   * Backwards-compatibility: Wrap a LogAppender in an Appender
   *
   * @deprecated
   * @see      xp://util.log.LogCategory#addAppender
   * @test     xp://net.xp_framework.unittest.logging.LogCategoryTest
   * @purpose  BC
   */
  class LogAppenderAdapter extends Appender {
    protected $delegate;

    /**
     * Creates a new adapter for a given delegate
     *
     * @param   util.log.LogAppender delegate
     */ 
    public function __construct(LogAppender $delegate) {
      $this->delegate= $delegate;
    }

    /**
     * Append data
     *
     * @param   util.log.LoggingEvent event
     */ 
    public function append(LoggingEvent $event) {
      $this->delegate->append($this->layout->format($event));
    }
    
    /**
     * Finalize this appender. This method is called when the logger
     * is shut down. Does nothing in this default implementation.
     *
     */   
    public function finalize() { 
      $this->delegate->finalize();
    }

    /**
     * Creates a string representation of this object
     *
     * @return  string
     */
    public function toString() {
      return sprintf(
        '%s(*->%s, layout= %s)',
        $this->getClassName(),
        xp::stringOf($this->delegate),
        xp::stringOf($this->layout)
      );
    }
  }
?>
