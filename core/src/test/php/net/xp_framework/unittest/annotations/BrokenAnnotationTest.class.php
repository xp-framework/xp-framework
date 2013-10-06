<?php namespace net\xp_framework\unittest\annotations;

use lang\XPClass;

/**
 * Tests the XP Framework's annotations
 *
 * @see   rfc://0185
 * @see   https://github.com/xp-framework/xp-framework/pull/328
 */
class BrokenAnnotationTest extends \unittest\TestCase {

  /**
   * Helper
   *
   * @param   string input
   * @return  [:var]
   */
  protected function parse($input) {
    return XPClass::parseAnnotations($input, $this->getClassName());
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated annotation/')]
  public function no_ending_bracket() {
    XPClass::forName('net.xp_framework.unittest.annotations.NoEndingBracket')->getAnnotations();
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error/')]
  public function missing_ending_bracket_in_key_value_pairs() {
    $this->parse("#[@attribute(key= 'value']");
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error/')]
  public function unterminated_single_quoted_string_literal() {
    $this->parse("#[@attribute(key= 'value)]");
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error/')]
  public function unterminated_double_quoted_string_literal() {
    $this->parse('#[@attribute(key= "value)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting "@"/')]
  public function missing_annotation_after_comma_and_value() {
    $this->parse('#[@ignore("Test"), ]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting "@"/')]
  public function missing_annotation_after_comma() {
    $this->parse('#[@ignore, ]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting "@"/')]
  public function missing_annotation_after_second_comma() {
    $this->parse('#[@ignore, @test, ]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error: Unterminated string/')]
  public function unterminated_dq_string() {
    $this->parse('#[@ignore("Test)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error: Unterminated string/')]
  public function unterminated_sq_string() {
    $this->parse("#[@ignore('Test)]");
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function unterminated_array() {
    $this->parse('#[@ignore(array(1]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function unterminated_array_key() {
    $this->parse('#[@ignore(name = array(1]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function malformed_array() {
    $this->parse('#[@ignore(array(1 ,, 2))]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function malformed_array_inside_key_value_pairs() {
    $this->parse('#[@ignore(name= array(1 ,, 2))]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function malformed_array_no_commas() {
    $this->parse('#[@ignore(array(1 2))]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error: Expecting either "\(", "," or "\]"/')]
  public function annotation_not_separated_by_commas() {
    $this->parse("#[@test @throws('rdbms.SQLConnectException')]");
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Parse error: Expecting either "\(", "," or "\]"/')]
  public function too_many_closing_braces() {
    $this->parse("#[@throws('rdbms.SQLConnectException'))]");
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Undefined constant "editor"/')]
  public function undefined_constant() {
    $this->parse('#[@$editorId: param(editor)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/No such constant "EDITOR" in class/')]
  public function undefined_class_constant() {
    $this->parse('#[@$editorId: param(self::EDITOR)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Class ".+" could not be found/')]
  public function undefined_class_in_new() {
    $this->parse('#[@$editorId: param(new NonExistantClass())]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Class ".+" could not be found/')]
  public function undefined_class_in_constant() {
    $this->parse('#[@$editorId: param(NonExistantClass::CONSTANT)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/No such field "EDITOR" in class/')]
  public function undefined_class_member() {
    $this->parse('#[@$editorId: param(self::$EDITOR)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Cannot access protected static field .+AnnotationParsingTest::\$hidden/')]
  public function class_protected_static_member() {
    $this->parse('#[@value(AnnotationParsingTest::$hidden)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Cannot access private static field .+AnnotationParsingTest::\$internal/')]
  public function class_private_static_member() {
    $this->parse('#[@value(AnnotationParsingTest::$internal)]');
  }
}