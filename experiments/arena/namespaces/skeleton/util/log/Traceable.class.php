<?php
/* This class is part of the XP framework
 *
 * $Id: Traceable.class.php 10398 2007-05-18 13:55:25Z kiesel $ 
 */

  namespace util::log;

  /**
   * Classes implementing the traceable interface define they can
   * be debugged by passing an util.log.LogCategory object to their
   * setTrace() method.
   *
   * @purpose  Interface
   */
  interface Traceable {
  
    /**
     * Set a trace for debugging
     *
     * @param   util.log.LogCategory cat
     */
    public function setTrace($cat);
  }
?>
