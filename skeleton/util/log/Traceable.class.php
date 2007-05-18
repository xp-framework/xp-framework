<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

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
