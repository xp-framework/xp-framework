<?php namespace net\xp_framework\unittest\core;

/**
 * Tests the XP Framework's annotations
 *
 * @see   xp://net.xp_framework.unittest.core.AnnotatedClass
 * @see   xp://lang.reflect.Routine
 * @see   xp://lang.reflect.XPClass
 * @see   rfc://0016
 */
class AnnotationTest extends \unittest\TestCase {
  protected $class = null;

  /**
   * Setup method
   */
  public function setUp() {
    $this->class= \lang\XPClass::forName('net.xp_framework.unittest.core.AnnotatedClass');
  }

  #[@test]
  public function setUpMethodHasNoAnnotations() {
    $this->assertFalse($this->getClass()->getMethod('setUp')->hasAnnotations());
  }

  #[@test]
  public function thisMethodHasAnnotations() {
    $this->assertTrue($this->getClass()->getMethod('thisMethodHasAnnotations')->hasAnnotations());
  }

  #[@test]
  public function simpleAnnotationExists() {
    $this->assertTrue($this->class->getMethod('simple')->hasAnnotation('simple'));
  }

  #[@test]
  public function simpleAnnotationValue() {
    $this->assertEquals(NULL, $this->class->getMethod('simple')->getAnnotation('simple'));
  }

  #[@test, @expect('lang.ElementNotFoundException')]
  public function getAnnotationForMethodWithout() {
    $this->getClass()->getMethod('setUp')->getAnnotation('any');
  }

  #[@test]
  public function hasAnnotationForMethodWithout() {
    $this->assertFalse($this->getClass()->getMethod('setUp')->hasAnnotation('any'));
  }
  
  #[@test, @expect('lang.ElementNotFoundException')]
  public function getNonExistantAnnotation() {
    $this->class->getMethod('simple')->getAnnotation('doesnotexist');
  }

  #[@test]
  public function hasNonExistantAnnotation() {
    $this->assertFalse($this->class->getMethod('simple')->hasAnnotation('doesnotexist'));
  }

  #[@test, @values(array('one', 'two', 'three'))]
  public function multipleAnnotationsExist($annotation) {
    $this->assertTrue($this->class->getMethod('multiple')->hasAnnotation($annotation));
  }

  #[@test]
  public function multipleAnnotationsReturnedAsList() {
    $this->assertEquals(
      array('one' => NULL, 'two' => NULL, 'three' => NULL),
      $this->class->getMethod('multiple')->getAnnotations()
    );
  }

  #[@test]
  public function stringAnnotationValue() {
    $this->assertEquals(
      'String value',
      $this->class->getMethod('stringValue')->getAnnotation('strval')
    );
  }

  #[@test]
  public function keyValuePairAnnotationValue() {
    $this->assertEquals(
      array('key' => 'value'),
      $this->class->getMethod('keyValuePair')->getAnnotation('config')
    );
  }

  #[@test]
  public function testMethodHasTestAnnotation() {
    $this->assertTrue($this->class->getMethod('testMethod')->hasAnnotation('test'));
  }

  #[@test]
  public function testMethodHasIgnoreAnnotation() {
    $this->assertTrue($this->class->getMethod('testMethod')->hasAnnotation('ignore'));
  }

  #[@test]
  public function testMethodsLimitAnnotation() {
    $this->assertEquals(
      array('time' => 0.1, 'memory' => 100),
      $this->class->getMethod('testMethod')->getAnnotation('limit')
    );
  }
}
