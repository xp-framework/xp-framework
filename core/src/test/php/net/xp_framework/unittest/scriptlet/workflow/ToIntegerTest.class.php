<?php namespace net\xp_framework\unittest\scriptlet\workflow;

use scriptlet\xml\workflow\casters\ToInteger;


/**
 * Test the ToInteger caster
 *
 * @see  xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
 * @see  xp://scriptlet.xml.workflow.casters.ToInteger
 */
class ToIntegerTest extends AbstractCasterTest {

  /**
   * Return the caster
   *
   * @return  scriptlet.xml.workflow.casters.ParamCaster
   */
  protected function caster() {
    return new ToInteger();
  }

  /**
   * Test positive and negative numbers
   */
  #[@test, @values(array(array('1', 1), array('-1', -1), array('0', 0)))]
  public function wholeNumbers($input, $expect) {
    $this->assertEquals($expect, $this->castValue($input), $input);
  }

  /**
   * Test empty input
   */
  #[@test]
  public function emptyInput() {
    $this->assertEquals(0, $this->castValue(''));
  }
}
