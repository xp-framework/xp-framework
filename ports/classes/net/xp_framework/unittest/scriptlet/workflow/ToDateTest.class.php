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
     * @return  scriptlet.xml.workflow.casters.ParamCaster
     */
    protected function caster() {
      return new ToDate();
    }

    /**
     * Test european date format (DD.MM.YYYY)
     *
     */
    #[@test]
    public function europeanDateFormat() {
      $this->assertEquals(new Date('1977-12-14'), $this->castValue('14.12.1977'));
    }

    /**
     * Test european date format with short year
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function europeanDateFormatShortYear() {
      $this->assertEquals(new Date('2008-04-10'), $this->castValue('10.04.08'));
    }
    
    /**
     * Test european date format with short year but which composisiton
     * is so unambigous that the parser can extract year, month and
     * day values from it.
     *
     */
    #[@test]
    public function europeanDateFormatShortYearButUnambiguous() {
      $this->assertEquals(new Date('1980-05-28'), $this->castValue('28.05.80'));
    }
    
    /**
     * Test US date format (YYYY-MM-DD)
     *
     */
    #[@test]
    public function usDateFormat() {
      $this->assertEquals(new Date('1977-12-14'), $this->castValue('1977-12-14'));
    }

    /**
     * Test empty input
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function emptyInput() {
      $this->castValue('');
    }

    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function daysNotInMonth() {
      $this->castValue('31.11.2009');
    }

    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function brokenAmericanDateFormat() {
      $this->castValue('30/11/2009'); // Should be 11/30
    }
  }
?>
