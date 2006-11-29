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
     * @access  protected
     * @param   &lang.Object a
     * @param   &lang.Object b
     * @throws  unittest.AssertionFailedError
     */
    function assertReference(&$a, &$b) {
      $this->assertEquals($a->__id, $b->__id);
      $a->__id= 'R:'.$a->__id;
      $this->assertEquals($a->__id, $b->__id);
    }
    
    /**
     * Tests deref($o) is the same object as $o
     *
     * @access  public
     */
    #[@test]
    function deref() {
      $object= &new Object();
      $this->assertReference($object, deref($object));
    }

    /**
     * Tests deref(ref($o)) is the same object as $o
     *
     * @access  public
     */
    #[@test]
    function derefOfRef() {
      $object= &new Object();
      $this->assertReference($object, deref(ref($object)));
    }

    /**
     * Tests ObjectReference class
     *
     * @access  public
     */
    #[@test]
    function objectReference() {
      $object= &new Object();
      $ref= &newinstance('lang.Object', array(ref($object)), '{
        var $object= NULL;
        
        function __construct(&$object) {
          $this->object= &deref($object);
        }
      }');
      
      $this->assertReference($object, $ref->object);
    }
  }
?>
