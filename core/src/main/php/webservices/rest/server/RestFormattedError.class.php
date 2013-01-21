<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  
  /**
   * Formatted error interface
   *
   */
  interface RestFormattedError {
    
    /**
     * Returns the status that should be set in the response
     * 
     * @return int
     */
    public function getStatus();
  }
?>
