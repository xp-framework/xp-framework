<?php
/* This class is part of the XP framework
 *
 * $Id: DestructorTest.class.php 8971 2006-12-27 15:27:10Z friebe $
 */

  namespace net::xp_framework::unittest::core;

  ::uses(
    'unittest.TestCase',
    'net.xp_framework.unittest.core.Destroyable',
    'net.xp_framework.unittest.core.DestructionCallback'
  );

  /**
   * Tests destructor functionality
   *
   * @purpose  Testcase
   */
  class DestructorTest extends unittest::TestCase implements DestructionCallback {
    public
      $destroyed   = array(),
      $destroyable = NULL;
      
    /**
     * Callback for Destroyable class
     *
     * @param   &lang.Object object
     */
    public function onDestruction($object) {
      $this->destroyed[$object->hashCode()]++;
    }
    
    /**
     * Setup method. Creates the destroyable member and sets its 
     * callback to this test.
     *
     */
    public function setUp() {
      $this->destroyable= new Destroyable();
      $this->destroyable->setCallback($this);
      $this->destroyed[$this->destroyable->hashCode()]= 0;
    }

    /**
     * Tests delete() function calls destructor
     *
     */
    #[@test]
    public function deleteCallsDestructor() {
      $hash= $this->destroyable->hashCode();
      ::delete($this->destroyable);
      $this->assertNull($this->destroyable);
      $this->assertEquals(1, $this->destroyed[$hash]);
    }
    
  } 
?>
