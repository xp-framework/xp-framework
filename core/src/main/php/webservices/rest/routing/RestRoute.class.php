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
     * @param webservices.rest.transport.HttpRequestAdapter request The request
     * @param webservices.rest.transport.HttpResponseAdapter response The response
     * @param mixed[] args The arguments
     * @return webservices.rest.RestRoute[]
     */
    public function route($request, $response, $args= array());
    
  }
?>
