<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Log Context interface
   *
   */
  interface LogContext {

    /**
     * Bind to LogCategory
     *
     * @param   util.log.LogCategory cat
     * @throws  lang.IllegalStateException if already bound
     */
    public function bind(LogCategory $cat);

    /**
     * Leave context; unbinds from LogCategory
     *
     */
    public function leave();

    /**
     * Format for logging
     *
     * @return  string
     */
    public function format();
  }
?>
