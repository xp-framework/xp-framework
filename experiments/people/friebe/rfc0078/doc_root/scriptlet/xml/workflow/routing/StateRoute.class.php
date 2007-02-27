<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.Route');

  /**
   * State route
   *
   * @see      xp://scriptlet.xml.workflow.routing.StaticRouter
   * @see      xp://scriptlet.xml.workflow.routing.ClassRouter
   * @see      xp://scriptlet.xml.workflow.routing.Route
   * @purpose  Route implementation
   */
  class StateRoute extends Object {
    protected
      $state= NULL;

    /**
     * Constructor
     *
     * @param   scriptlet.xml.workflow.AbstractState state
     */
    public function __construct($state) {
      $this->setState($state);
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
     * Dispatch this route
     *
     * @param   scriptlet.xml.XMLScriptletRequest
     * @param   scriptlet.xml.XMLScriptletResponse
     * @param   scriptlet.xml.workflow.Context
     * @return  mixed
     */
    public function dispatch($request, $response, $context) {
      $request->state= $this->state;  // TBD: Do we need this?
      $this->state->setup($request, $response, $context);
      return $this->state->process($request, $response, $context);
    }
  }
?>
