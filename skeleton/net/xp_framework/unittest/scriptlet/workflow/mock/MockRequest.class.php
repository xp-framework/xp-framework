<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'scriptlet.xml.workflow.WorkflowScriptletRequest',
    'scriptlet.xml.workflow.AbstractState'
  );

  /**
   * Mock request object
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.HandlerTest
   * @purpose   Mock object
   */
  class MockRequest extends WorkflowScriptletRequest {

    /**
     * Setup method.
     *
     * @access  public
     * @param   &lang.ClassLoader classloader
     * @param   string functionality
     * @param   string stateName
     * @param   array<string, mixed> params default array()
     */
    function __construct(&$classloader, $functionality, $stateName, $params= array()) {
      static $i= 0;

      parent::__construct($classloader);

      // Generate unique classname and put it into the environment
      // That way, the classloader will already know this class in 
      // WorkflowScriptletRequest::initialize() and be able to load
      // and instantiate it.
      $stateName= 'Mock·'.($i++).$stateName;
      $class= &$classloader->defineClass(
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
