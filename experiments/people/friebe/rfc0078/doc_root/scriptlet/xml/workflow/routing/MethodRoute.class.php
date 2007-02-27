<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.Route');

  /**
   * Method route
   *
   * @see      xp://scriptlet.xml.workflow.routing.MethodRouter
   * @see      xp://scriptlet.xml.workflow.routing.Route
   * @purpose  Route implementation
   */
  class MethodRoute extends Object implements Route {
    protected
      $state  = NULL,
      $method = NULL;

    /**
     * Constructor
     *
     * @param   scriptlet.xml.workflow.AbstractState state
     * @param   lang.reflect.Method method
     */
    public function __construct($state, $method) {
      $this->setState($state);
      $this->setMethod($method);
    }

    /**
     * Set State
     *
     * @access  public
     * @param   scriptlet.xml.workflow.AbstractState state
     */
    function setState($state) {
      $this->state= $state;
    }

    /**
     * Get State
     *
     * @access  public
     * @return  scriptlet.xml.workflow.AbstractState
     */
    function getState() {
      return $this->state;
    }

    /**
     * Set Method
     *
     * @access  public
     * @param   lang.reflect.Method method
     */
    function setMethod($method) {
      $this->method= $method;
    }

    /**
     * Get Method
     *
     * @access  public
     * @return  lang.reflect.Method
     */
    function getMethod() {
      return $this->method;
    }
    
    /**
     * Dispatch this route
     *
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  mixed
     */
    public function dispatch($request, $response, $context) {
      $request->state= $this->state;
      $this->state->setup($request, $response, $context);
      return $this->method->invoke($this->state, array($request, $response, $context));
    }
  } 
?>
