<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('io.IOException');

  /**
   * Indicates the file could not be found
   *
   * @see      xp://io.IOException
   */
  class FileNotFoundException extends IOException {
  
    /**
     * Constructor
     *
     * @param   string msg The exception message
     * @param   lang.Throwable cause default NULL
     */
    public function __construct($msg, $cause= NULL) {
      parent::__construct($msg, $cause);
    }
  }
?>
