<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * REST route interface
   *
   */
  interface RestRoute  {
    
    /**
     * Handle route 
     * 
     * @param var[] args The arguments
     * @return webservices.rest.server.RestRoute[]
     */
    public function process($args= array());
    
  }
?>
