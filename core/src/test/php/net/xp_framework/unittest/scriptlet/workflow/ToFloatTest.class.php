<?php namespace net\xp_framework\unittest\scriptlet\workflow;

use scriptlet\xml\workflow\casters\ToFloat;


/**
 * Test the ToFloat caster
 *
 * @deprecated The ToFloat caster is deprecated
 * @see  xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
 * @see  xp://scriptlet.xml.workflow.casters.ToFloat
 */
class ToFloatTest extends AbstractCasterTest {

  /**
   * Return the caster
   *
   * @return  scriptlet.xml.workflow.casters.ParamCaster
   */
  protected function caster() {
    return new ToFloat();
  }

  /**
   * Test whole numbers
   */
  #[@test, @values(array(array('1', 1.0), array('-1', -1.0), array('0', 0.0)))]
  public function wholeNumbers($input, $expect) {
    $this->assertEquals($expect, $this->castValue($input), $input);
  }

  /**
   * Test fractional numbers
   */
  #[@test, @values(array(array('0.5', 0.5), array('-0.5', -0.5), array('.5', 0.5)))]
  public function fractionalNumbers($input, $expect) {
    $this->assertEquals($expect, $this->castValue($input), $input);
  }

  /**
   * Test empty input
   */
  #[@test]
  public function emptyInput() {
    $this->assertEquals(0.0, $this->castValue(''));
  }
}
