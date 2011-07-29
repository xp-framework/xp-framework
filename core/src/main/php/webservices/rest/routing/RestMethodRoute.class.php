<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('webservices.rest.routing.RestRoute');
  
  /**
   * Routing to method of class
   *
   * @purpose Route
   */
  class RestMethodRoute extends Object implements RestRoute {
    protected $path= NULL;
    protected $method= NULL;
    
    /**
     * Constructor
     * 
     * @param webservices.rest.routing.RestPath path The path
     * @param lang.reflect.Method method The method to route to
     */
    public function __construct($path, $method) {
      $this->path= $path;
      $this->method= $method;
    }
    
    /**
     * Handle route 
     * 
     * @param webservices.rest.transport.HttpRequestAdapter request The request
     * @param webservices.rest.transport.HttpResponseAdapter response The response
     * @return mixed[]
     */
    public function route($request, $response) {
      $args= array();
      foreach ($this->method->getParameters() as $arg) {
        $args[]= $this->path->getParam($arg->getName());
      }
      
      return $this->method->invoke(
        $this->method->getDeclaringClass()->newInstance(),
        $args
      );
    }
  }
?>
