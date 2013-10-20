<?php namespace net\xp_framework\unittest\core;

/**
 * Tests the <xp> functions
 *
 */
class XpTest extends \unittest\TestCase {

  #[@test]
  public function version() {
    $this->assertEquals(3, sscanf(\xp::version(), '%d.%d.%d', $series, $major, $minor));
  }

  #[@test]
  public function no_error_here() {
    $this->assertNull(\xp::errorAt(__FILE__));
  }

  #[@test]
  public function triggered_error_at_file() {
    trigger_error('Test');
    $this->assertEquals(
      array(__LINE__ - 2 => array('Test' => array('class' => NULL, 'method' => 'trigger_error', 'cnt' => 1))),
      \xp::errorAt(__FILE__)
    );
    \xp::gc();
  }

  #[@test]
  public function triggered_error_at_file_and_line() {
    trigger_error('Test');
    $this->assertEquals(
      array('Test' => array('class' => NULL, 'method' => 'trigger_error', 'cnt' => 1)),
      \xp::errorAt(__FILE__, __LINE__ - 3)
    );
    \xp::gc();
  }

  #[@test]
  public function gc() {
    trigger_error('Test');
    $this->assertEquals(
      array(__FILE__ => array(__LINE__ - 2 => array('Test' => array('class' => NULL, 'method' => 'trigger_error', 'cnt' => 1)))),
      \xp::$errors
    );
    \xp::gc();
    $this->assertEquals(array(), \xp::$errors);
  }

  #[@test]
  public function null() {
    $this->assertEquals('null', get_class(\xp::null()));
  }

  #[@test]
  public function reflect_int() {
    $this->assertEquals('þint', \xp::reflect('int'));
  }

  #[@test]
  public function reflect_double() {
    $this->assertEquals('þdouble', \xp::reflect('double'));
  }

  #[@test]
  public function reflect_string() {
    $this->assertEquals('þstring', \xp::reflect('string'));
  }

  #[@test]
  public function reflect_bool() {
    $this->assertEquals('þbool', \xp::reflect('bool'));
  }

  #[@test]
  public function reflect_var() {
    $this->assertEquals('var', \xp::reflect('var'));
  }

  #[@test]
  public function reflect_int_array() {
    $this->assertEquals('¦þint', \xp::reflect('int[]'));
  }

  #[@test]
  public function reflect_int_map() {
    $this->assertEquals('»þint', \xp::reflect('[:int]'));
  }

  #[@test]
  public function reflect_generic_list_of_int() {
    $this->assertEquals('List··þint', \xp::reflect('List<int>'));
  }

  #[@test]
  public function reflect_object() {
    $this->assertEquals('Object', \xp::reflect('lang.Object'));
  }

  #[@test]
  public function reflect_this() {
    $this->assertEquals(__CLASS__, \xp::reflect($this->getClassName()));
  }
}
