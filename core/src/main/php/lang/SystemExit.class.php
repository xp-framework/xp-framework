<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  /**
   * Exit from a running program.
   *
   * <code>
   *   Runtime::halt(0);
   * </code>
   *
   * @test     xp://net.xp_framework.unittest.core.SystemExitTest
   * @see      http://corelib.rubyonrails.org/classes/SystemExit.html
   * @see      http://docs.python.org/library/exceptions.html#exceptions.SystemExit
   * @see      xp://lang.Runtime#halt
   */
  class SystemExit extends Throwable {

    /**
     * Constructor
     *
     * @param   int code
     * @param   string message default NULL
     */
    public function __construct($code, $message= NULL) {
      parent::__construct(NULL === $message ? '' : $message);
      $this->code= (int)$code;
    }
    
    /**
     * Fills in stack trace information. For this class, does not include
     * any information.
     *
     * @return  lang.Throwable this
     */
    public function fillInStackTrace() {
      return $this;
    }
  }
?>
