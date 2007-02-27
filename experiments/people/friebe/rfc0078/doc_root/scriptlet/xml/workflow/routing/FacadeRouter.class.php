<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.FacadeRoute', 'scriptlet.xml.workflow.routing.Router');

  /**
   * The facade router is a router implementation that will route requests
   * to facades.
   *
   * <pre>
   *   State name                  Class name           Operation  Argument
   *   --------------------------- -------------------- ---------- --------
   *   [namespace]/news            [package.]NewsFacade list       -
   *   [namespace]/news/list       [package.]NewsFacade list       -
   *   [namespace]/news/list/all   [package.]NewsFacade list       all
   *   [namespace]/news/search     [package.]NewsFacade search     -
   *   [namespace]/news/view       [package.]NewsFacade view       -
   *   [namespace]/news/edit       [package.]NewsFacade edit       -
   *   [namespace]/news/add        [package.]NewsFacade add        -
   *   [namespace]/news/delete     [package.]NewsFacade delete     -
   * </pre>
   *
   * @see      xp://scriptlet.xml.workflow.facade.Facade
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @purpose  Router implementation
   */
  class FacadeRouter extends Object implements Router {
    protected
      $scan= '';
  
    /**
     * Constructor
     *
     * @param   string namespace default ''
     */
    public function __construct($namespace= '') {
      $this->scan= ($namespace ? rtrim($namespace, '/').'/' : '').'%[^/]/%[^/]/%s';
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
      1 == sscanf($request->getStateName(), $this->scan, $name, $op, $arg) && $op= 'list';
      $request->setStatename($name.'/'.$op);
      $class= XPClass::forName($package.ucfirst($name).'Facade');

      // Check if method exists
      if (!($m= $class->getMethod('do'.ucfirst($op)))) {
        throw new IllegalArgumentException($class->getName().' does not support a '.$op.' operation');
      }
      
      // Check if method is public
      if (!Modifiers::isPublic($m->getModifiers())) {
        throw new IllegalAccessException($class->getName().'::'.$op.' is not a public operation');
      }

      return new FacadeRoute(
        $class->newInstance(), 
        $m, 
        $arg
      );
    }
  }
?>
