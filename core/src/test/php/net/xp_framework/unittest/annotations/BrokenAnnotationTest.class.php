<?php namespace net\xp_framework\unittest\annotations;

use lang\XPClass;

/**
 * Tests the XP Framework's annotations
 *
 * @see      rfc://0185
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

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting @/')]
  public function missingAnnotationAfterCommaAndValue() {
    $this->parse('#[@ignore("Test"), ]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting @/')]
  public function missingAnnotationAfterComma() {
    $this->parse('#[@ignore, ]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Expecting @/')]
  public function missingAnnotationAfterSecondComma() {
    $this->parse('#[@ignore, @test, ]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated or malformed string/')]
  public function unterminatedString() {
    $this->parse('#[@ignore("Test)]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated array/')]
  public function unterminatedArray() {
    $this->parse('#[@ignore(array(1]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Unterminated array/')]
  public function unterminatedArrayKey() {
    $this->parse('#[@ignore(name = array(1]');
  }

  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function malformedArray() {
    $this->parse('#[@ignore(array(1 ,, 2))]');
  }


  #[@test, @expect(class= 'lang.ClassFormatException', withMessage= '/Malformed array/')]
  public function malformedArrayKey() {
    $this->parse('#[@ignore(name= array(1 ,, 2))]');
  }
}