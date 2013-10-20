<?php namespace net\xp_framework\unittest\core;

/**
 * Tests ref() and deref() core functionality
 *
 * @deprecated ref() and deref() have been deprecated from core
 */
class RefTest extends \unittest\TestCase {

  /**
   * Helper method that asserts to objects are references to each other
   *
   * @param   &lang.Object a
   * @param   &lang.Object b
   * @throws  unittest.AssertionFailedError
   */
  protected function assertReference($a, $b) {
    $this->assertEquals($a->__id, $b->__id);
    $a->__id= 'R:'.$a->__id;
    $this->assertEquals($a->__id, $b->__id);
  }
  
  #[@test]
  public function deref() {
    $object= new \lang\Object();
    $this->assertReference($object, deref($object));
  }

  #[@test]
  public function derefOfRef() {
    $object= new \lang\Object();
    $r= ref($object);
    $this->assertReference($object, deref($r));
  }

  #[@test]
  public function objectReference() {
    $object= new \lang\Object();
    $ref= newinstance('lang.Object', array(ref($object)), '{
      public $object= NULL;
      
      public function __construct(&$object) {
        $this->object= &deref($object);
      }
    }');
    
    $this->assertReference($object, $ref->object);
  }
}
