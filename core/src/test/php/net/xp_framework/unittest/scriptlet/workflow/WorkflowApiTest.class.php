<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
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
  class WorkflowApiTest extends TestCase {
    protected
      $scriptlet  = NULL;
  
    /**
     * Setup method.
     *
     */
    public function setUp() {
      $this->scriptlet= new AbstractXMLScriptlet($this->getClass()->getPackage()->getName());
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
     * Process a request
     *
     * @param   net.xp_framework.unittest.scriptlet.mock.MockRequest
     * @return  net.xp_framework.unittest.scriptlet.mock.MockResponse
     */
    public function process($request) {
      $request->initialize();
      $response= new MockResponse();
      $this->scriptlet->processWorkflow($request, $response);
      return $response;
    }
  
    /**
     * Tests that a state's setup() and process() methods are called.
     *
     */
    #[@test]
    public function setupAndProcessCalled() {
      $request= new MockRequest($this->scriptlet->package, ucfirst($this->name), '{
        public $called= array();
        
        public function setup($request, $response, $context) {
          parent::setup($request, $response, $context);
          $this->called["setup"]= TRUE;
        }

        public function process($request, $response, $context) {
          $this->called["process"]= TRUE;
        }
      }');
      $this->process($request);      
      $this->assertTrue($request->state->called['setup']);
      $this->assertTrue($request->state->called['process']);
    }
    
    /**
     * Tests IllegalAccessException thrown in state setup
     *
     */
    #[@test]
    public function illegalAccessInStateSetup() {
      $request= new MockRequest($this->scriptlet->package, ucfirst($this->name), '{
        public function setup($request, $response, $context) {
          parent::setup($request, $response, $context);
          throw new IllegalAccessException("Access denied");
        }
      }');
      try {
        $this->process($request);
        $this->fail('Expected exception not caught', NULL, 'ScriptletException');
      } catch (ScriptletException $expected) {
        $this->assertEquals(403, $expected->statusCode);
        $this->assertClass($expected->getCause(), 'lang.IllegalAccessException');
      }
    }

    /**
     * Tests IllegalAccessException thrown in state setup
     *
     */
    #[@test]
    public function illegalStateInStateSetup() {
      $request= new MockRequest($this->scriptlet->package, ucfirst($this->name), '{
        public function setup($request, $response, $context) {
          parent::setup($request, $response, $context);
          throw new IllegalStateException("Misconfigured");
        }
      }');
      try {
        $this->process($request);
        $this->fail('Expected exception not caught', NULL, 'ScriptletException');
      } catch (ScriptletException $expected) {
        $this->assertEquals(500, $expected->statusCode);
        $this->assertClass($expected->getCause(), 'lang.IllegalStateException');
      }
    }

    /**
     * Tests IllegalArgumentException thrown in state setup
     *
     */
    #[@test]
    public function illegalArgumentInStateSetup() {
      $request= new MockRequest($this->scriptlet->package, ucfirst($this->name), '{
        public function setup($request, $response, $context) {
          parent::setup($request, $response, $context);
          throw new IllegalArgumentException("Query string format");
        }
      }');
      try {
        $this->process($request);
        $this->fail('Expected exception not caught', NULL, 'ScriptletException');
      } catch (ScriptletException $expected) {
        $this->assertEquals(406, $expected->statusCode);
        $this->assertClass($expected->getCause(), 'lang.IllegalArgumentException');
      }
    }
  }
?>
