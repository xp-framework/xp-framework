<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  /**
   * REST router interface
   *
   */
  interface RestRouter  {
    
    /**
     * Configure router
     * 
     * @param string setup The setup string
     * @param string base The base URL
     */
    public function configure($setup, $base= '');
    
    /**
     * Get routing table
     * 
     * @return webservices.rest.server.routing.RestRouting
     */
    public function getRouting();
    
    /**
     * Return routes for given request and response
     * 
     * @param webservices.rest.server.transport.HttpRequestAdapter request The request
     * @param webservices.rest.server.transport.HttpResponseAdapter response The response
     * @return webservices.rest.server.RestRoute[]
     */
    public function routesFor($request, $response);
    
  }
?>
