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
   *   [namespace]            [package.]HomeState
   *   [namespace]/static     [package.]StaticState
   *   [namespace]/news/view  [package.]ViewNewsState
   * </pre>
   *
   * @see      xp://scriptlet.xml.workflow.routing.StateRoute
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @purpose  Router implementation
   */
  class ClassRouter extends Object implements Router {
    protected
      $scan= '';
  
    /**
     * Constructor
     *
     * @param   string namespace default ''
     */
    public function __construct($namespace= '') {
      $this->scan= ($namespace ? rtrim($namespace, '/').'/' : '').'%[^$]';
    }
  
    /**
     * Route a request
     *
     * @access  public
     * @param   string package
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  scriptlet.xml.workflow.routing.Route
     * @throws  lang.ClassNotFoundException in case the state class cannot be found
     */
    public function route($package, $request, $response, $context) {
      1 == sscanf($request->getStateName(), $this->scan, $className) || $className= 'home';
      return new StateRoute(XPClass::forName($package.implode('', array_map('ucfirst', array_reverse(explode('/', $className)))).'State')->newInstance());
    }
  }
?>
