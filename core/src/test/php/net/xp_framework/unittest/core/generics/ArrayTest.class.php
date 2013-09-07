<?php namespace net\xp_framework\unittest\core\generics;

/**
 * TestCase for generic behaviour at runtime.
 *
 * @see   xp://net.xp_framework.unittest.core.generics.Lookup
 */
class ArrayTest extends \unittest\TestCase {

  #[@test]
  public function primitiveStringArrayValue() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, string[]>', array(
      'this' => array($this->name)
    ));
    $this->assertEquals(array($this->name), $l->get('this'));
  }

  #[@test]
  public function primitiveStringArrayKey() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string[], unittest.TestCase>');
    $l->put(array('this'), $this);
    $this->assertEquals($this, $l->get(array('this')));
  }

  #[@test, @expect('lang.IllegalArgumentException')]
  public function stringToArrayOfStringInvalid() {
    create('new net.xp_framework.unittest.core.generics.Lookup<string, string[]>')
      ->put('greeting', array('Hello', 'World', '!!!', 1))
    ;
  }

  #[@test]
  public function stringToArrayOfStringMultiple() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string, string[]>', array(
      'colors' => array('red', 'green', 'blue'),
      'names'  => array('PHP', 'Java', 'C#')
    ));
    $this->assertEquals(array('red', 'green', 'blue'), $l->get('colors'));
    $this->assertEquals(array('PHP', 'Java', 'C#'), $l->get('names'));
  }
 
  #[@test]
  public function arrayOfStringToStringMultiple() {
    $l= create('new net.xp_framework.unittest.core.generics.Lookup<string[], string>');
    $l->put(array('red', 'green', 'blue'), 'colors');
    $l->put(array('PHP', 'Java', 'C#'), 'names');
    $this->assertEquals('colors', $l->get(array('red', 'green', 'blue')));
    $this->assertEquals('names', $l->get(array('PHP', 'Java', 'C#')));
  }
}