<?php namespace net\xp_framework\unittest\core;

/**
 * Tests destructor functionality
 */
class DestructorTest extends \unittest\TestCase implements DestructionCallback {
  protected $destroyed   = array();
  protected $destroyable = null;
    
  /**
   * Callback for Destroyable class
   *
   * @param   lang.Object object
   */
  public function onDestruction($object) {
    $this->destroyed[$object->hashCode()]++;
  }
  
  /**
   * Setup method. Creates the destroyable member and sets its 
   * callback to this test.
   */
  public function setUp() {
    $this->destroyable= new Destroyable();
    $this->destroyable->setCallback($this);
    $this->destroyed[$this->destroyable->hashCode()]= 0;
  }

  #[@test]
  public function deleteCallsDestructor() {
    $hash= $this->destroyable->hashCode();
    delete($this->destroyable);
    $this->assertNull($this->destroyable);
    $this->assertEquals(1, $this->destroyed[$hash]);
  } 
}
