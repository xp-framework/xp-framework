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
     * Return routes for given request and response
     * 
     * @param webservices.rest.transport.HttpRequestAdapter request The request
     * @param webservices.rest.transport.HttpResponseAdapter response The response
     * @return webservices.rest.RestRoute[]
     */
    public function routesFor($request, $response);
    
  }
?>
