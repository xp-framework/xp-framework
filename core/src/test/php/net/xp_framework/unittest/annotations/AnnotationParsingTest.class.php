<?php namespace net\xp_framework\unittest\annotations;

/**
 * Tests the XP Framework's annotation parsing implementation
 *
 * @see     rfc://0016
 * @see     xp://lang.XPClass#parseAnnotations
 * @see     http://bugs.xp-framework.net/show_bug.cgi?id=38
 * @see     https://github.com/xp-framework/xp-framework/issues/14
 * @see     https://github.com/xp-framework/xp-framework/pull/56
 * @see     https://gist.github.com/1240769
 */
class AnnotationParsingTest extends \unittest\TestCase {

  /**
   * Helper
   *
   * @param   string input
   * @return  [:var]
   */
  protected function parse($input) {
    return \lang\XPClass::parseAnnotations($input, $this->getClassName());
  }

  #[@test]
  public function no_value() {
    $this->assertEquals(
      array(0 => array('hello' => NULL), 1 => array()),
      $this->parse("#[@hello]")
    );
  }

  #[@test]
  public function sq_string_value() {
    $this->assertEquals(
      array(0 => array('hello' => 'World'), 1 => array()),
      $this->parse("#[@hello('World')]")
    );
  }

  #[@test]
  public function sq_string_value_with_equals_sign() {
    $this->assertEquals(
      array(0 => array('hello' => 'World=Welt'), 1 => array()),
      $this->parse("#[@hello('World=Welt')]")
    );
  }

  #[@test]
  public function sq_string_value_with_at_sign() {
    $this->assertEquals(
      array(0 => array('hello' => '@World'), 1 => array()),
      $this->parse("#[@hello('@World')]")
    );
  }

  #[@test]
  public function sq_string_value_with_annotation() {
    $this->assertEquals(
      array(0 => array('hello' => '@hello("World")'), 1 => array()),
      $this->parse("#[@hello('@hello(\"World\")')]")
    );
  }

  #[@test]
  public function sq_string_value_with_double_quotes() {
    $this->assertEquals(
      array(0 => array('hello' => 'said "he"'), 1 => array()),
      $this->parse("#[@hello('said \"he\"')]")
    );
  }

  #[@test]
  public function sq_string_value_with_escaped_single_quotes() {
    $this->assertEquals(
      array(0 => array('hello' => "said 'he'"), 1 => array()),
      $this->parse("#[@hello('said \'he\'')]")
    );
  }

  #[@test]
  public function dq_string_value() {
    $this->assertEquals(
      array(0 => array('hello' => 'World'), 1 => array()),
      $this->parse('#[@hello("World")]')
    );
  }

  #[@test]
  public function dq_string_value_with_single_quote() {
    $this->assertEquals(
      array(0 => array('hello' => 'Beck\'s'), 1 => array()),
      $this->parse('#[@hello("Beck\'s")]')
    );
  }

  #[@test]
  public function dq_string_value_with_escaped_double_quotes() {
    $this->assertEquals(
      array(0 => array('hello' => 'said "he"'), 1 => array()),
      $this->parse('#[@hello("said \"he\"")]')
    );
  }

  #[@test]
  public function dq_string_value_with_escape_sequence() {
    $this->assertEquals(
      array(0 => array('hello' => "World\n"), 1 => array()),
      $this->parse('#[@hello("World\n")]')
    );
  }

  #[@test]
  public function dq_string_value_with_at_sign() {
    $this->assertEquals(
      array(0 => array('hello' => '@World'), 1 => array()),
      $this->parse('#[@hello("@World")]')
    );
  }

  #[@test]
  public function dq_string_value_with_annotation() {
    $this->assertEquals(
      array(0 => array('hello' => '@hello(\'World\')'), 1 => array()),
      $this->parse('#[@hello("@hello(\'World\')")]')
    );
  }

  #[@test]
  public function int_value() {
    $this->assertEquals(
      array(0 => array('answer' => 42), 1 => array()),
      $this->parse('#[@answer(42)]')
    );
  }

  #[@test]
  public function double_value() {
    $this->assertEquals(
      array(0 => array('version' => 3.5), 1 => array()),
      $this->parse('#[@version(3.5)]')
    );
  }

  #[@test]
  public function multi_value_using_short_array() {
    $this->assertEquals(
      array(0 => array('xmlmapping' => array('hw_server', 'server')), 1 => array()),
      $this->parse("#[@xmlmapping(['hw_server', 'server'])]")
    );
  }

  #[@test]
  public function array_value() {
    $this->assertEquals(
      array(0 => array('versions' => array(3.4, 3.5)), 1 => array()),
      $this->parse('#[@versions(array(3.4, 3.5))]')
    );
  }

  #[@test]
  public function array_value_with_nested_array() {
    $this->assertEquals(
      array(0 => array('versions' => array(array(3))), 1 => array()),
      $this->parse('#[@versions(array(array(3)))]')
    );
  }

  #[@test]
  public function array_value_with_nested_arrays() {
    $this->assertEquals(
      array(0 => array('versions' => array(array(3), array(4))), 1 => array()),
      $this->parse('#[@versions(array(array(3), array(4)))]')
    );
  }

  #[@test]
  public function array_value_with_strings_containing_braces() {
    $this->assertEquals(
      array(0 => array('versions' => array('(3..4]')), 1 => array()),
      $this->parse('#[@versions(array("(3..4]"))]')
    );
  }

  #[@test]
  public function bool_true_value() {
    $this->assertEquals(
      array(0 => array('supported' => true), 1 => array()),
      $this->parse('#[@supported(true)]')
    );
  }

  #[@test]
  public function bool_false_value() {
    $this->assertEquals(
      array(0 => array('supported' => false), 1 => array()),
      $this->parse('#[@supported(false)]')
    );
  }

  #[@test]
  public function key_value_pairs_annotation_value() {
    $this->assertEquals(
      array(0 => array('config' => array('key' => 'value', 'times' => 5, 'disabled' => false, 'null' => null, 'list' => array(1, 2))), 1 => array()), 
      $this->parse("#[@config(key = 'value', times= 5, disabled= false, null = null, list= array(1, 2))]")
    );
  }

  #[@test]
  public function multi_line_annotation() {
    $this->assertEquals(
      array(0 => array('interceptors' => array('classes' => array(
        'net.xp_framework.unittest.core.FirstInterceptor',
        'net.xp_framework.unittest.core.SecondInterceptor',
      ))), 1 => array()),
      $this->parse("
        #[@interceptors(classes= array(
          'net.xp_framework.unittest.core.FirstInterceptor',
          'net.xp_framework.unittest.core.SecondInterceptor',
        ))]
      ")
    );
  }

  #[@test]
  public function simple_XPath_annotation() {
    $this->assertEquals(
      array(0 => array('fromXml' => array('xpath' => '/parent/child/@attribute')), 1 => array()),
      $this->parse("#[@fromXml(xpath= '/parent/child/@attribute')]")
    );
  }

  #[@test]
  public function complex_XPath_annotation() {
    $this->assertEquals(
      array(0 => array('fromXml' => array('xpath' => '/parent[@attr="value"]/child[@attr1="val1" and @attr2="val2"]')), 1 => array()),
      $this->parse("#[@fromXml(xpath= '/parent[@attr=\"value\"]/child[@attr1=\"val1\" and @attr2=\"val2\"]')]")
    );
  }

  #[@test]
  public function string_with_equal_signs() {
    $this->assertEquals(
      array(0 => array('permission' => 'rn=login, rt=config'), 1 => array()),
      $this->parse("#[@permission('rn=login, rt=config')]")
    );
  }

  #[@test]
  public function string_assigned_without_whitespace() {
    $this->assertEquals(
      array(0 => array('arg' => array('name' => 'verbose', 'short' => 'v')), 1 => array()),
      $this->parse("#[@arg(name= 'verbose', short='v')]")
    );
  }

  #[@test]
  public function multiple_values_with_strings_and_equal_signs() {
    $this->assertEquals(
      array(0 => array('permission' => array('names' => array('rn=login, rt=config1', 'rn=login, rt=config2'))), 1 => array()),
      $this->parse("#[@permission(names= array('rn=login, rt=config1', 'rn=login, rt=config2'))]")
    );
  }

  #[@test]
  public function unittest_annotation() {
    $this->assertEquals(
      array(0 => array('test' => NULL, 'ignore' => NULL, 'limit' => array('time' => 0.1, 'memory' => 100)), 1 => array()),
      $this->parse("#[@test, @ignore, @limit(time = 0.1, memory = 100)]")
    );
  }

  #[@test]
  public function overloaded_annotation() {
    $this->assertEquals(
      array(0 => array('overloaded' => array('signatures' => array(array('string'), array('string', 'string')))), 1 => array()),
      $this->parse('#[@overloaded(signatures= array(array("string"), array("string", "string")))]')
    );
  }

  #[@test]
  public function webmethod_with_parameter_annotations() {
    $this->assertEquals(
      array(
        0 => array('webmethod' => array('verb' => 'GET', 'path' => '/greet/{name}')),
        1 => array('$name' => array('path' => NULL), '$greeting' => array('param' => NULL))
      ),
      $this->parse('#[@webmethod(verb= "GET", path= "/greet/{name}"), @$name: path, @$greeting: param]')
    );
  }

  #[@test]
  public function short_array_syntax_as_value() {
    $this->assertEquals(
      array(0 => array('permissions' => array('rn=login, rt=config', 'rn=admin, rt=config')), 1 => array()),
      $this->parse("#[@permissions(['rn=login, rt=config', 'rn=admin, rt=config'])]")
    );
  }

  #[@test]
  public function short_array_syntax_as_key() {
    $this->assertEquals(
      array(0 => array('permissions' => array('names' => array('rn=login, rt=config', 'rn=admin, rt=config'))), 1 => array()),
      $this->parse("#[@permissions(names = ['rn=login, rt=config', 'rn=admin, rt=config'])]")
    );
  }
}
