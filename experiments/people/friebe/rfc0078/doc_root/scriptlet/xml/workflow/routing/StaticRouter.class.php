<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.StateRoute', 'scriptlet.xml.workflow.routing.Router');

  /**
   * The class router is a router implementation that will route requests
   * to state classes by the following conventions:
   *
   * <pre>
   *   State name             Class name                    
   *   ---------------------- ------------------------------
   *   [namespace]/static     [package.]StaticState
   *   [namespace]/news/view  [package.]NewsState
   * </pre>
   *
   * @see      xp://scriptlet.xml.workflow.routing.StateRoute
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @purpose  Router implementation
   */
  class StaticRouter extends Object implements Router {
    protected
      $scan= '';
  
    /**
     * Constructor
     *
     * @param   string namespace default ''
     */
    public function __construct($namespace= '') {
      $this->scan= ($namespace ? rtrim($namespace, '/').'/' : '').'%[^/]';
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
     */
    public function route($package, $request, $response, $context) {
      sscanf($request->getStateName(), $this->scan, $className);
      return new StateRoute(XPClass::forName($package.ucfirst($className).'State')->newInstance());
    }
  }
?>
