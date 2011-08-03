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
     * Some handler function with parameters
     * 
     */
    #[@webmethod(verb= "GET", path= "/some/thing/{id}")]
    public function handleWithArgs($id) {
      
    }
    
    /**
     * Some handler function
     * 
     */
    #[@webmethod(verb= "GET", path= "/some/thing")]
    public function handle() {
    }
    
    /**
     * Some handler function
     * 
     */
    #[@webmethod(verb= "GET", path= "/some/injected/thing/{id}", inject= array('webservices.rest.transport.HttpRequestAdapter', 'webservices.rest.transport.HttpResponseAdapter'))]
    public function handlerInject($request, $response, $id) {
    }
  }
?>
