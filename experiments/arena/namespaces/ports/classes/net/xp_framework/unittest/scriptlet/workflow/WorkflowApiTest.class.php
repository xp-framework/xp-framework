<?php
/* This class is part of the XP framework
 *
 * $Id: WorkflowApiTest.class.php 8974 2006-12-27 17:29:09Z friebe $
 */

  namespace net::xp_framework::unittest::scriptlet::workflow;

  ::uses(
    'unittest.TestCase',
    'scriptlet.xml.workflow.AbstractXMLScriptlet',
    'scriptlet.xml.workflow.AbstractState',
    'net.xp_framework.unittest.scriptlet.workflow.mock.MockRequest',
    'net.xp_framework.unittest.scriptlet.workflow.mock.MockResponse'
  );
  
  /**
   * Scriptlet/Workflow API test case
   *
   * @see       xp://scriptlet.xml.workflow.AbstractXMLScriptlet
   * @purpose   Unit test
   */
  class WorkflowApiTest extends unittest::TestCase {
  
    /**
     * Setup method.
     *
     */
    public function setUp() {
      $this->scriptlet= new scriptlet::xml::workflow::AbstractXMLScriptlet(lang::ClassLoader::getDefault());
      $this->scriptlet->init();
    }

    /**
     * Teardown method.
     *
     */
    public function tearDown() {
      $this->scriptlet->finalize();
    }
    
    /**
     * Teardown method.
     *
     * @param   &net.xp_framework.unittest.scriptlet.mock.MockRequest
     * @return  &net.xp_framework.unittest.scriptlet.mock.MockResponse
     */
    public function process($request) {
      $request->initialize();
      $response= new net::xp_framework::unittest::scriptlet::workflow::mock::MockResponse();
      $this->scriptlet->processWorkflow($request, $response);
      return $response;
    }
  
    /**
     * Tests that a state's setup() and process() methods are called.
     *
     */
    #[@test]
    public function setupAndProcessCalled() {
      $request= new net::xp_framework::unittest::scriptlet::workflow::mock::MockRequest($this->scriptlet->classloader, '{
        public $called= array();
        
        public function setup($request, $response, $context) {
          parent::setup($request, $response, $context);
          $this->called["setup"]= TRUE;
        }

        public function process($request, $response, $context) {
          parent::process($request, $response, $context);
          $this->called["process"]= TRUE;
        }
      }');
      $this->process($request);      
      $this->assertTrue($request->state->called['setup']);
      $this->assertTrue($request->state->called['process']);
    }
  }
?>
