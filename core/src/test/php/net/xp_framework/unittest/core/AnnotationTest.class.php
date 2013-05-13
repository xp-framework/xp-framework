<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'net.xp_framework.unittest.core.AnnotatedClass');

  /**
   * Tests the XP Framework's annotations
   *
   * @see   rfc://0016
   */
  class AnnotationTest extends TestCase {
    protected $class = NULL;

    /**
     * Setup method
     *
     */
    public function setUp() {
      $this->class= XPClass::forName('net.xp_framework.unittest.core.AnnotatedClass');
    }

    /**
     * Tests this class' methodAnnotation() method has no annotations
     *
     * @see     xp://lang.reflect.Routine#hasAnnotations
     */
    #[@test]
    public function setUpMethodHasNoAnnotations() {
      $this->assertFalse($this->getClass()->getMethod('setUp')->hasAnnotations());
    }

    /**
     * Tests this method has annotations
     *
     * @see     xp://lang.reflect.Routine#hasAnnotations
     */
    #[@test]
    public function thisMethodHasAnnotations() {
      $this->assertTrue($this->getClass()->getMethod('thisMethodHasAnnotations')->hasAnnotations());
    }

    /**
     * Tests method with a simple annotation without a value exists
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#simple
     */
    #[@test]
    public function simpleAnnotationExists() {
      $this->assertTrue($this->class->getMethod('simple')->hasAnnotation('simple'));
    }

    /**
     * Tests getAnnotation() returns NULL for simple annotations without
     * any value.,
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#simple
     */
    #[@test]
    public function simpleAnnotationValue() {
      $this->assertEquals(NULL, $this->class->getMethod('simple')->getAnnotation('simple'));
    }

    /**
     * Tests getting an annotation for a method without any annotation.
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function getAnnotationForMethodWithout() {
      $this->getClass()->getMethod('setUp')->getAnnotation('any');
    }

    /**
     * Tests getting an annotation for a method without any annotation.
     *
     */
    #[@test]
    public function hasAnnotationForMethodWithout() {
      $this->assertFalse($this->getClass()->getMethod('setUp')->hasAnnotation('any'));
    }
    
    /**
     * Tests getting an annotation for a method which has annotations but
     * not the one we're asking for
     *
     */
    #[@test, @expect('lang.ElementNotFoundException')]
    public function getNonExistantAnnotation() {
      $this->class->getMethod('simple')->getAnnotation('doesnotexist');
    }

    /**
     * Tests getting an annotation for a method which has annotations but
     * not the one we're asking for
     *
     */
    #[@test]
    public function hasNonExistantAnnotation() {
      $this->assertFalse($this->class->getMethod('simple')->hasAnnotation('doesnotexist'));
    }

    /**
     * Tests method with multiple annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#multiple
     */
    #[@test, @values(array('one', 'two', 'three'))]
    public function multipleAnnotationsExist($annotation) {
      $this->assertTrue($this->class->getMethod('multiple')->hasAnnotation($annotation));
    }

    /**
     * Tests method with multiple annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#multiple
     */
    #[@test]
    public function multipleAnnotationsReturnedAsList() {
      $this->assertEquals(
        array('one' => NULL, 'two' => NULL, 'three' => NULL),
        $this->class->getMethod('multiple')->getAnnotations()
      );
    }

    /**
     * Tests getAnnotation() returns the string associated with the 
     * annotation.
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#stringValue
     */
    #[@test]
    public function stringAnnotationValue() {
      $this->assertEquals(
        'String value',
        $this->class->getMethod('stringValue')->getAnnotation('strval')
      );
    }

    /**
     * Tests getAnnotation() returns the string associated with the 
     * annotation.
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#keyValuePair
     */
    #[@test]
    public function keyValuePairAnnotationValue() {
      $this->assertEquals(
        array('key' => 'value'),
        $this->class->getMethod('keyValuePair')->getAnnotation('config')
      );
    }

    /**
     * Tests unittest annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#testMethod
     */
    #[@test]
    public function testMethodHasTestAnnotation() {
      $this->assertTrue($this->class->getMethod('testMethod')->hasAnnotation('test'));
    }

    /**
     * Tests unittest annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#testMethod
     */
    #[@test]
    public function testMethodHasIgnoreAnnotation() {
      $this->assertTrue($this->class->getMethod('testMethod')->hasAnnotation('ignore'));
    }

    /**
     * Tests unittest annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#testMethod
     */
    #[@test]
    public function testMethodsLimitAnnotation() {
      $this->assertEquals(
        array('time' => 0.1, 'memory' => 100),
        $this->class->getMethod('testMethod')->getAnnotation('limit')
      );
    }
  }
?>
