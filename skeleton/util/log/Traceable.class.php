<?php
/* This class is part of the XP framework's people experiments
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
  class Traceable extends Interface {
  
    /**
     * Set a trace for debugging
     *
     * @access  public
     * @param   &util.log.LogCategory cat
     */
    function setTrace(&$cat) { }
  }
?>
