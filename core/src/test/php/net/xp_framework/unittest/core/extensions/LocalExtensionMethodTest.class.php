<?php namespace net\xp_framework\unittest\core\extensions;

use unittest\TestCase;

/**
 * TestCase
 *
 */
class LocalExtensionMethodTest extends TestCase {

  static function __static() {
    \xp::extensions(__CLASS__, $scope= __CLASS__);
  }

  /**
   * Returns methods annotated with a given annotatoons
   *
   * @param  lang.XPClass $self
   * @param  string $annotation
   * @return lang.reflect.Method[]
   */
  public static function methodsAnnotatedWith(\lang\XPClass $self, $annotation) {
    $name= substr($annotation, 1);
    $r= array();
    foreach ($self->getMethods() as $method) {
      if ($method->hasAnnotation($name)) $r[]= $method;
    }
    return $r;
  }

  #[@test]
  public function invoke_it() {
    $this->assertEquals(
      array($this->getClass()->getMethod(__FUNCTION__)),
      $this->getClass()->methodsAnnotatedWith('@test')
    );
  }
}
