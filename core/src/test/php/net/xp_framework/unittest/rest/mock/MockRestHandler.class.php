<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * Mock REST handler class
   *
   */
  #[@webservice]
  class MockRestHandler extends Object {
    
    /**
     * Some handler function
     * 
     */
    #[@webmethod(method= "GET", path= "/some/thing")]
    public function handle() {
    }
  }
?>
