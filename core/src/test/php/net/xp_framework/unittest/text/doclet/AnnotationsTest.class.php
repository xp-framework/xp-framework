<?php namespace net\xp_framework\unittest\text\doclet;



use unittest\TestCase;
use text\doclet\RootDoc;
use text\doclet\ClassDoc;


/**
 * TestCase
 *
 * @see   xp://text.doclet.AnnotatedDoc
 */
class AnnotationsTest extends TestCase {
  protected $fixture = null;

  static function __static() {
    \xp::extensions(__CLASS__, __CLASS__);  // Local extension methods
  }

  /**
   * Sets up test case
   *
   */
  public function setUp() {
    $root= new RootDoc();
    $root->addSourceLoader($this->getClass()->getClassLoader());
    $this->fixture= $root->classNamed($this->getClassName());
  }

  /**
   * Test class annotations
   *
   */
  #[@test]
  public function this_class_does_not_have_annotations() {
    $this->assertEquals(array(), $this->fixture->annotations());
  }

  /**
   * Returns a method for a class by a given name
   *
   * @param   text.doclet.ClassDoc self
   * @param   string name
   * @return  text.doclet.MethodDoc
   * @throws  lang.ElementNotFoundException
   */
  public static function methodNamed(ClassDoc $self, $name) {
    foreach ($self->methods as $method) {
      if ($name === $method->name()) return $method;
    }
    raise('lang.ElementNotFoundException', 'No such method '.$name.' in '.$self->name());
  }

  /**
   * Test method annotations
   *
   */
  #[@test]
  public function this_method_has_a_test_annotation() {
    $annotations= $this->fixture->methodNamed(__FUNCTION__)->annotations();
    $this->assertInstanceOf('text.doclet.AnnotationDoc', $annotations[0]);
    $this->assertEquals('test', $annotations[0]->name());
    $this->assertEquals(null, $annotations[0]->value);
  }

  /**
   * Test method annotations
   *
   */
  #[@test, @limit(time = 10.0)]
  public function this_method_has_a_limit_annotation() {
    $annotations= $this->fixture->methodNamed(__FUNCTION__)->annotations();
    $this->assertInstanceOf('text.doclet.AnnotationDoc', $annotations[1]);
    $this->assertEquals('limit', $annotations[1]->name());
    $this->assertEquals(array('time' => 10.0), $annotations[1]->value);
  }
}
