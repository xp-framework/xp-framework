<?php
/* This class is part of the XP framework
 *
 * $Id$
 */

  uses(
    'net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest',
    'scriptlet.xml.workflow.casters.ToDate'
  );
  
  /**
   * Test the ToDate caster
   *
   * @see       xp://net.xp_framework.unittest.scriptlet.workflow.AbstractCasterTest
   * @see       scriptlet.xml.workflow.casters.ToDate
   * @purpose   ToDate test
   */
  class ToDateTest extends AbstractCasterTest {

    /**
     * Return the caster
     *
     * @access  protected
     * @return  &scriptlet.xml.workflow.casters.ParamCaster
     */
    function &caster() {
      return new ToDate();
    }

    /**
     * Test european date format (DD.MM.YYYY)
     *
     * @access  public
     */
    #[@test]
    function europeanDateFormat() {
      $this->assertEquals(new Date('1977-12-14'), $this->castValue('14.12.1977'));
    }

    /**
     * Test US date format (YYYY-MM-DD)
     *
     * @access  public
     */
    #[@test]
    function usDateFormat() {
      $this->assertEquals(new Date('1977-12-14'), $this->castValue('1977-12-14'));
    }

    /**
     * Test empty input
     *
     * @access  public
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    function emptyInput() {
      $this->castValue('');
    }
  }
?>
