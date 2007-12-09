<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase');

  /**
   * Tests ref() and deref() core functionality
   *
   * @purpose  Testcase
   */
  class RefTest extends TestCase {

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
    
    /**
     * Tests deref($o) is the same object as $o
     *
     */
    #[@test]
    public function deref() {
      $object= new Object();
      $this->assertReference($object, deref($object));
    }

    /**
     * Tests deref(ref($o)) is the same object as $o
     *
     */
    #[@test]
    public function derefOfRef() {
      $object= new Object();
      $r= ref($object);
      $this->assertReference($object, deref($r));
    }

    /**
     * Tests ObjectReference class
     *
     */
    #[@test]
    public function objectReference() {
      $object= new Object();
      $ref= newinstance('lang.Object', array(ref($object)), '{
        var $object= NULL;
        
        function __construct(&$object) {
          $this->object= &deref($object);
        }
      }');
      
      $this->assertReference($object, $ref->object);
    }
  }
?>
