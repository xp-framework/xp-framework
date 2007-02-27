<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('scriptlet.xml.workflow.routing.Route');

  /**
   * Facade route
   *
   * @see      xp://scriptlet.xml.workflow.routing.FacadeRouter
   * @see      xp://scriptlet.xml.workflow.routing.Route
   * @purpose  Route implementation
   */
  class FacadeRoute extends Object implements Route {
    protected
      $facade    = NULL,
      $operation = '',
      $argument  = NULL;

    /**
     * Constructor
     *
     * @param   scriptlet.xml.workflow.facade.Facade facade
     * @param   string lang.reflect.Method
     * @param   string argument default NULL
     */
    public function __construct($facade, $operation, $argument= NULL) {
      $this->setFacade($facade);
      $this->setOperation($operation);
      $this->setArgument($argument);
    }

    /**
     * Set Facade
     *
     * @access  public
     * @param   scriptlet.xml.workflow.facade.Facade facade
     */
    function setFacade($facade) {
      $this->facade= $facade;
    }

    /**
     * Get Facade
     *
     * @access  public
     * @return  scriptlet.xml.workflow.facade.Facade
     */
    function getFacade() {
      return $this->facade;
    }

    /**
     * Set Operation
     *
     * @access  public
     * @param   lang.reflect.Method operation
     */
    function setOperation($operation) {
      $this->operation= $operation;
    }

    /**
     * Get Operation
     *
     * @access  public
     * @return  lang.reflect.Method
     */
    function getOperation() {
      return $this->operation;
    }

    /**
     * Set Argument
     *
     * @access  public
     * @param   string argument
     */
    function setArgument($argument) {
      $this->argument= $argument;
    }

    /**
     * Get Argument
     *
     * @access  public
     * @return  string
     */
    function getArgument() {
      return $this->argument;
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
      return $this->operation->invoke($this->facade, array($request, $response, $context, $this->argument));
    }
  } 
?>
