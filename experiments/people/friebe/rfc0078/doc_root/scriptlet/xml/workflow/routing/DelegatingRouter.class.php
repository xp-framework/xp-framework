<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.Router');

  /**
   * The class router is a router implementation that will delegate 
   * requests to different routing based on rules.
   *
   * Example (use ClassRouter as default, map static/* to StaticRouter, 
   * news/* to MethodRouter).
   * <code>
   *   new DelegatingRouter(new ClassRouter(), array(
   *     'static' => new StaticRouter(),
   *     'news'   => new MethodRouter()
   *   ));
   * </code>
   *
   * @see      xp://scriptlet.xml.workflow.routing.Router
   * @purpose  Router implementation
   */
  class DelegatingRouter extends Object implements Router {
    protected
      $rules= array();
      
    /**
     * Constructor.
     *
     * @access  public
     * @param   scriptlet.xml.workflow.routing.Router default router
     * @param   array<string, scriptlet.xml.workflow.Router> rules default array()
     */
    public function __construct($default, $rules= array()) {
      $this->rules[0]= $default;
      foreach (array_keys($rules) as $fragment) {
        $this->rules[$fragment]= $rules[$fragment];
      }
    }
  
    /**
     * Route a request
     *
     * @param   string package
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  scriptlet.xml.workflow.routing.Route
     */
    public function route($package, $request, $response, $context) {
      $stateName= $request->getStateName();
      foreach (array_filter(array_keys($this->rules)) as $pattern) {
        if (0 == strncmp($pattern, $stateName, strlen($pattern))) {
          return $this->rules[$pattern]->route($package, $request, $response, $context);
        }
      }
      
      // Apply default route
      return $this->rules[0]->route($package, $request, $response, $context);
    }
  }
?>
