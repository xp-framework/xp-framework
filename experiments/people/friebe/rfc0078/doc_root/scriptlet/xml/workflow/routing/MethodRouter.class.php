<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.MethodRoute', 'scriptlet.xml.workflow.routing.Router');

  /**
   * The class router is a router implementation that will route requests
   * to state classes' methods by the following conventions:
   *
   * <pre>
   *   State name             Class name                    
   *   ---------------------- -------------------------------
   *   [namespace]/static     [package.]StaticState::index()
   *   [namespace]/news/view  [package.]NewsState::view()
   * </pre>
   *
   * @see      xp://scriptlet.xml.workflow.routing.MethodRoute
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @purpose  Router implementation
   */
  class MethodRouter extends Object implements Router {
    protected
      $scan= '';
  
    /**
     * Constructor
     *
     * @param   string namespace default ''
     */
    public function __construct($namespace= '') {
      $this->scan= ($namespace ? rtrim($namespace, '/').'/' : '').'%[^/]/%s';
    }
  
    /**
     * Route a request
     *
     * @param   string package
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  scriptlet.xml.workflow.routing.Route
     * @throws  lang.ClassNotFoundException in case the state class cannot be found
     * @throws  lang.IllegalArgumentException in case the operation cannot be found
     * @throws  lang.IllegalAccessException in case the operation is not publicly accessible
     */
    public function route($package, $request, $response, $context) {
      1 == sscanf($request->getStateName(), $this->scan, $name, $method) && $method= 'index';
      $class= XPClass::forName($package.ucfirst($name).'State');
      
      // Check if method exists
      if (!($m= $class->getMethod($method))) {
        throw new IllegalArgumentException($class->getName().' does not support a '.$method.' operation');
      }
      
      // Check if method is public
      if (!Modifiers::isPublic($m->getModifiers())) {
        throw new IllegalAccessException($class->getName().'::'.$method.' is not a public operation');
      }

      return new MethodRoute($class->newInstance(), $m);
    }
  }
?>
