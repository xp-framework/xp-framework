<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
  uses('scriptlet.HttpSession', 'unittest.TestCase');

  /**
   * TestCase for scriptlet.HttpSession class.
   *
   * @purpose   TestCase
   */
  class HttpSessionTest extends TestCase {
    var
      $session  = NULL;
  
    /**
     * Helper method to create the testing session object.
     *
     * @access  protected
     * @return  scriptlet.HttpSession
     */
    function _session() {
      return new HttpSession();
    }
  
    /**
     * Setup testcase environment for next testcase
     *
     * @access  protected
     */
    function setUp() {
      $this->session= $this->_session();
    }
    
    /**
     * Cleanup last testcase run. Invalidate old sessions and
     * remove environment leftovers
     *
     * @access  protected
     */
    function tearDown() {
      if (is('scriptlet.HttpSession', $this->session) && $this->session->isValid()) {
        $this->session->invalidate();
      }
    }
  
    /**
     * Test session creation
     *
     * @access  public
     */
    #[@test]
    function testCreate() {
      $this->session->initialize(NULL);
      $this->assertTrue($this->session->isValid());
    }
    
    /**
     * Test isNew() method
     *
     * @access  public
     */
    #[@test]
    function testNew() {
      $this->session->initialize(NULL);
      $this->assertTrue($this->session->isNew());
    }
    
    /**
     * Test reattaching of sessions
     *
     * @access  public
     */
    #[@test]
    function testReattach() {
      $this->session->initialize();
      
      $copy= &new HttpSession();
      $copy->initialize($this->session->getId());
      $this->assertFalse($copy->isNew());
    }
    
    /**
     * Test invalidating of session
     *
     * @access  public
     */
    #[@test]
    function testInvalidate() {
      $this->session->initialize(NULL);
      $this->assertTrue($this->session->isValid());
      
      $this->session->invalidate();
      $this->assertFalse($this->session->isValid());
    }
    
    /**
     * Test fetching of registered session keys
     *
     * @access  public
     */
    #[@test]
    function testValueNames() {
      $this->session->initialize(NULL);
      $this->session->putValue('foo', $f= 1);
      $this->session->putValue('bar', $f= 2);
      
      $this->assertEquals(
        array('foo', 'bar'),
        $this->session->getValueNames()
      );
    }

    /**
     * Test fetching of registered session keys
     *
     * @access  public
     */
    #[@test]
    function putDoesNotOverwriteValue() {
      $this->session->initialize(NULL);
      $fixture= &new Object();
      $hash= $fixture->hashCode();
      $this->session->putValue('foo', $fixture);
      $this->assertClass($fixture, 'lang.Object') &&
      $this->assertEquals($hash, $fixture->hashCode());
    }
    
    /**
     * Test resetting of sessions
     *
     * @access  public
     */
    #[@test]
    function testReset() {
      $this->session->initialize(NULL);
      $this->session->putValue('foo', $f= NULL);
      $this->assertEquals(1, sizeof($this->session->getValueNames()));
      
      $this->session->reset();
      $this->assertEquals(0, sizeof($this->session->getValueNames()));
    }
    
    /**
     * Test session fixation protection (users may not pass
     * arbitrary names as session ids)
     *
     * @access  public
     */
    #[@test]
    function testIllegalConstruct() {
      $this->assertFalse($this->session->initialize('ILLEGALSESSIONID'));
    }
    
    /**
     * Test access protection on invalid sessions
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalStateException')]
    function testIllegalSessionAccess() {
      $this->session->initialize('ILLEGALSESSIONID');
      $this->session->putValue('foo', $f= 3);
    }
  }

?>
