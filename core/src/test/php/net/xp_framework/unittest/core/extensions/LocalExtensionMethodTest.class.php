<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses('unittest.TestCase');

  /**
   * TestCase
   *
   */
  class LocalExtensionMethodTest extends TestCase {

    static function __static() {
      xp::extensions(__CLASS__, $scope= __CLASS__);
    }

    public static function methodsAnnotatedWith(XPClass $self, $annotation) {
      $name= substr($annotation, 1);
      $r= array();
      foreach ($self->getMethods() as $method) {
        if ($method->hasAnnotation($name)) $r[]= $method;
      }
      return $r;
    }

    /**
     * Test it
     *
     */
    #[@test]
    public function invoke_it() {
      $this->assertEquals(
        array($this->getClass()->getMethod(__FUNCTION__)),
        $this->getClass()->methodsAnnotatedWith('@test')
      );
    }
  }
?>
