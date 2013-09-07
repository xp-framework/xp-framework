<?php namespace net\xp_framework\unittest\annotations;

/**
 * Tests the XP Framework's annotation parsing implementation supports
 * the deprecated multi-value syntax for backwards compatibility (BC).
 *
 * @see  xp://lang.XPClass#parseAnnotations
 * @see  https://github.com/xp-framework/xp-framework/issues/313
 * @deprecated
 */
class MultiValueBCTest extends \unittest\TestCase {

  /**
   * Helper
   *
   * @param   string input
   * @return  [:var]
   */
  protected function parse($input) {
    $annotations= \lang\XPClass::parseAnnotations($input, $this->getClassName());
    \xp::gc();
    return $annotations;
  }

  #[@test]
  public function multi_value() {
    $this->assertEquals(
      array(0 => array('xmlmapping' => array('hw_server', 'server')), 1 => array()),
      $this->parse("#[@xmlmapping('hw_server', 'server')]")
    );
  }

  #[@test]
  public function multi_value_without_whitespace() {
    $this->assertEquals(
      array(0 => array('xmlmapping' => array('hw_server', 'server')), 1 => array()),
      $this->parse("#[@xmlmapping('hw_server','server')]")
    );
  }

  #[@test]
  public function multi_value_with_variable_types_backwards_compatibility() {
    $this->assertEquals(
      array(0 => array('xmlmapping' => array('hw_server', TRUE)), 1 => array()),
      $this->parse("#[@xmlmapping('hw_server', TRUE)]")
    );
  }

  #[@test]
  public function parsingContinuesAfterMultiValue() {
    $this->assertEquals(
      array(0 => array('xmlmapping' => array('hw_server', 'server'), 'restricted' => NULL), 1 => array()),
      $this->parse("#[@xmlmapping('hw_server', 'server'), @restricted]")
    );
  }
}
