<?php
/* This class is part of the XP framework
 *
 * $Id: MockRequest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::scriptlet::workflow::mock;

  ::uses(
    'scriptlet.xml.workflow.WorkflowScriptletRequest',
    'scriptlet.xml.workflow.AbstractState'
  );

  /**
   * Mock request object
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.HandlerTest
   * @purpose   Mock object
   */
  class MockRequest extends scriptlet::xml::workflow::WorkflowScriptletRequest {

    /**
     * Setup method.
     *
     * @param   string package
     * @param   string functionality
     * @param   string stateName
     * @param   array<string, mixed> params default array()
     */
    public function __construct($package, $functionality, $stateName, $params= array()) {
      static $i= 0;

      parent::__construct($package);

      // Generate unique classname and put it into the environment
      // That way, the classloader will already know this class in 
      // WorkflowScriptletRequest::initialize() and be able to load
      // and instantiate it.
      $stateName= 'Mock·'.($i++).$stateName;
      $class= $classloader->defineClass(
        'net.xp_framework.unittest.scriptlet.workflow.mock.state.'.$stateName.'State', 
        'class '.$stateName.'State extends AbstractState '.$functionality
      );
      putenv('STATE='.$stateName);

      // Set some defaults
      putenv('PRODUCT=xp');
      putenv('LANGUAGE=en_US');
    }
  }
?>
