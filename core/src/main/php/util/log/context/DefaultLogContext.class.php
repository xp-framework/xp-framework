<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('util.log.LogContext');

  /**
   * Default context
   *
   */
  class DefaultLogContext extends LogContext {

    /**
     * Default log context return an empty string
     *
     * @return string
     */
    public function format() {
      return '';
    }
  }
?>