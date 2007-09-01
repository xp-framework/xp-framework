<?php
/* This class is part of the XP framework
 *
 * $Id: ToIntegerTest.class.php 8974 2006-12-27 17:29:09Z friebe $
 */

  namespace net::xp_framework::unittest::scriptlet::workflow;

  ::uses(
    'net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest',
    'scriptlet.xml.workflow.casters.ToInteger'
  );
  
  /**
   * Test the ToInteger caster
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
   * @see       scriptlet.xml.workflow.casters.ToInteger
   * @purpose   ToInteger test
   */
  class ToIntegerTest extends AbstractCasterTest {

    /**
     * Return the caster
     *
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    protected function caster() {
      return new scriptlet::xml::workflow::casters::ToInteger();
    }

    /**
     * Test positive and negative numbers
     *
     */
    #[@test]
    public function wholeNumbers() {
      foreach (array('1' => 1, '-1' => -1, '0' => 0) as $input => $expect) {
        $this->assertEquals($expect, $this->castValue($input), $input);
      }
    }

    /**
     * Test empty input
     *
     */
    #[@test]
    public function emptyInput() {
      $this->assertEquals(0, $this->castValue(''));
    }
  }
?>
