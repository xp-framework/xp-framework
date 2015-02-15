<?php namespace net\xp_framework\unittest\scriptlet\workflow;

use scriptlet\xml\workflow\casters\ToUnixLineBreaks;


/**
 * Test the ToUnixLineBreaks caster
 *
 * @see       xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
 * @see       scriptlet.xml.workflow.casters.ToUnixLineBreaks
 * @purpose   ToUnixLineBreaks test
 */
class ToUnixLineBreaksTest extends AbstractCasterTest {

  /**
   * Return the caster
   *
   * @return  scriptlet.xml.workflow.casters.ParamCaster
   */
  protected function caster() {
    return new ToUnixLineBreaks();
  }

  /**
   * Test empty value
   *
   */
  #[@test]
  public function emptyValue() {
    $this->assertEquals("", $this->castValue(""));
  }

  /**
   * Test single unix line break
   *
   */
  #[@test]
  public function singleUnixLineBreak() {
    $this->assertEquals("\n", $this->castValue("\n"));
  }

  /**
   * Test single unix line break
   *
   */
  #[@test]
  public function nonFullWindowsLineBreak() {
    $this->assertEquals("test\rtest", $this->castValue("test\rtest"));
  }

  /**
   * Test double unix line breaks
   *
   */
  #[@test]
  public function doubleUnixLineBreak() {
    $this->assertEquals("\n\n", $this->castValue("\n\n"));
  }

  /**
   * Test single windows line break
   *
   */
  #[@test]
  public function singleWindowsLineBreak() {
    $this->assertEquals("\n", $this->castValue("\r\n"));
  }

  /**
   * Test double windows line breaks
   *
   */
  #[@test]
  public function doubleWindowsLineBreak() {
    $this->assertEquals("\n\n", $this->castValue("\r\n\r\n"));
  }

  /**
   * Test mixed windows and unix line breaks
   *
   */
  #[@test]
  public function mixedLineBreaks() {
    $this->assertEquals("\n\n\n\n", $this->castValue("\r\n\n\r\n\n"));
  }

  /**
   * Test line breaks with text
   *
   */
  #[@test]
  public function lineBreaksWithText() {
    $this->assertEquals("First line\nSecond line\n", $this->castValue("First line\r\nSecond line\r\n"));
  }

}
