<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses('unittest.TestCase', 'net.xp_framework.unittest.core.AnnotatedClass');

  /**
   * Tests the XP Framework's annotations
   *
   * @see      rfc://0016
   * @purpose  Testcase
   */
  class AnnotationTest extends TestCase {
    public
      $class = NULL;

    /**
     * Setup method. .
     *
     */
    public function setUp() {
      $this->class= XPClass::forName('net.xp_framework.unittest.core.AnnotatedClass');
    }

    /**
     * Helper method to return whether a specified annotation exists
     *
     * @param   string method
     * @param   string annotation
     * @return  bool
     */
    protected function annotationExists($method, $annotation) {
      return $this->class->getMethod($method)->hasAnnotation($annotation);
    }

    /**
     * Helper method to get an annotation of a specified method
     *
     * @param   string method
     * @param   string annotation
     * @return  mixed annotation value
     */
    protected function methodAnnotation($method, $annotation) {
      return $this->class->getMethod($method)->getAnnotation($annotation);
    }

    /**
     * Tests this class' methodAnnotation() method has no annotations
     *
     * @see     xp://lang.reflect.Routine#hasAnnotations
     */
    #[@test]
    public function methodAnnotationHasNoAnnotations() {
      $this->assertFalse($this->getClass()->getMethod('methodAnnotation')->hasAnnotations());
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
      $this->assertTrue($this->annotationExists('simple', 'simple'));
    }

    /**
     * Tests getAnnotation() returns NULL for simple annotations without
     * any value.,
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#simple
     */
    #[@test]
    public function simpleAnnotationValue() {
      $this->assertEquals(NULL, $this->methodAnnotation('simple', 'simple'));
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
      $this->methodAnnotation('simple', 'doesnotexist');
    }

    /**
     * Tests getting an annotation for a method which has annotations but
     * not the one we're asking for
     *
     */
    #[@test]
    public function hasNonExistantAnnotation() {
      $this->assertFalse($this->annotationExists('simple', 'doesnotexist'));
    }

    /**
     * Tests method with multiple annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#multiple
     */
    #[@test]
    public function multipleAnnotationsExist() {
      foreach (array('one', 'two', 'three') as $annotation) {
        $this->assertTrue($this->annotationExists('multiple', $annotation), $annotation);
      }
    }

    /**
     * Tests method with multiple annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#multiple
     */
    #[@test]
    public function multipleAnnotationsReturnedAsList() {
      $method= $this->class->getMethod('multiple');
      $this->assertEquals(
        array('one' => NULL, 'two' => NULL, 'three' => NULL),
        $method->getAnnotations()
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
      $this->assertEquals('String value', $this->methodAnnotation('stringValue', 'strval'));
    }

    /**
     * Tests getAnnotation() returns the string associated with the 
     * annotation.
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#keyValuePair
     */
    #[@test]
    public function keyValuePairAnnotationValue() {
      $this->assertEquals(array('key' => 'value'), $this->methodAnnotation('keyValuePair', 'config'));
    }

    /**
     * Tests unittest annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#testMethod
     */
    #[@test]
    public function testMethod() {
      $m= $this->class->getMethod('testMethod');
      $this->assertTrue($m->hasAnnotation('test'));
      $this->assertTrue($m->hasAnnotation('ignore'));
      $this->assertEquals(0.1, $m->getAnnotation('limit', 'time'));
      $this->assertEquals(100, $m->getAnnotation('limit', 'memory'));
      $this->assertEquals(
        array('time' => 0.1, 'memory' => 100), 
        $m->getAnnotation('limit')
      );
    }

    /**
     * Tests getAnnotation() returns the string associated with the 
     * annotation.
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#keyValuePairs
     */
    #[@test]
    public function keyValuePairsAnnotationValue() {
      $this->assertEquals(
        array('key' => 'value', 'times' => 5, 'disabled' => FALSE, 'null' => NULL, 'list' => array(1, 2)), 
        $this->methodAnnotation('keyValuePairs', 'config')
      );
    }

    /**
     * Tests multi-line annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#multiLine
     */
    #[@test]
    public function multiLineAnnotation() {
      $this->assertEquals(array('classes' => array(
        'net.xp_framework.unittest.core.FirstInterceptor',
        'net.xp_framework.unittest.core.SecondInterceptor',
      )), $this->methodAnnotation('multiLine', 'interceptors'));
    }

    /**
     * Tests simple xpath annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#simpleXPath
     */
    #[@test]
    public function simpleXPathAnnotation() {
      $this->assertEquals(array(
        'xpath' => '/parent/child/@attribute'
      ), $this->methodAnnotation('simpleXPath', 'fromXml'));
    }

    /**
     * Tests complex xpath annotations
     *
     * @see     xp://net.xp_framework.unittest.core.AnnotatedClass#complexXPath
     */
    #[@test]
    public function complexXPathAnnotation() {
      $this->assertEquals(array(
        'xpath' => '/parent[@attr="value"]/child[@attr1="val1" and @attr2="val2"'
      ), $this->methodAnnotation('complexXPath', 'fromXml'));
    }
  }
?>
