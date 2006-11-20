<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest',
    'scriptlet.xml.workflow.casters.ToFloat'
  );
  
  /**
   * Test the ToFloat caster
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
   * @see       scriptlet.xml.workflow.casters.ToFloat
   * @purpose   ToFloat test
   */
  class ToFloatTest extends AbstractCasterTest {

    /**
     * Return the caster
     *
     * @access  protected
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    function &caster() {
      return new ToFloat();
    }

    /**
     * Test whole numbers
     *
     * @access  public
     */
    #[@test]
    function wholeNumbers() {
      foreach (array('1' => 1.0, '-1' => -1.0, '0' => 0.0) as $input => $expect) {
        $this->assertEquals($expect, $this->castValue($input), $input);
      }
    }

    /**
     * Test fractional numbers
     *
     * @access  public
     */
    #[@test]
    function fractionalNumbers() {
      foreach (array('0.5' => 0.5, '-0.5' => -0.5, '.5' => 0.5) as $input => $expect) {
        $this->assertEquals($expect, $this->castValue($input), $input);
      }
    }

    /**
     * Test empty input
     *
     * @access  public
     */
    #[@test]
    function emptyInput() {
      $this->assertEquals(0.0, $this->castValue(''));
    }
  }
?>
