<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'util.DateFormat'
  );

  /**
   * TestCase
   *
   * @see      xp://util.DateFormat
   */
  class DateFormatTest extends TestCase {
  
    /**
     * Test US-format (YYYY-MM-DD)
     *
     */
    #[@test]
    public function parseUsFormat() {
      $this->assertEquals(
        new Date('2009-01-01'),
        create(new DateFormat('%Y-%m-%d'))->parse('2009-01-01')
      );
    }

    /**
     * Test US-format (YYYY-MM-DD)
     *
     */
    #[@test]
    public function formatUsFormat() {
      $this->assertEquals(
        '2009-12-14',
        create(new DateFormat('%Y-%m-%d'))->format(new Date('2009-12-14'))
      );
    }

    /**
     * Test US-format (YYYY-MM-DD HH:MM:SS (AM|PM))
     *
     */
    #[@test]
    public function parseUsFormatWithTime() {
      $this->assertEquals(
        new Date('2009-12-14 14:24:36'),
        create(new DateFormat('%Y-%m-%d %I:%M:%S %p'))->parse('2009-12-14 02:24:36 PM')
      );
    }

    /**
     * Test US-format (YYYY-MM-DD HH:MM:SS (AM|PM))
     *
     */
    #[@test]
    public function formatUsFormatWithTime() {
      $this->assertEquals(
        '2009-12-14 02:24:36 PM',
        create(new DateFormat('%Y-%m-%d %I:%M:%S %p'))->format(new Date('2009-12-14 14:24:36'))
      );
    }

    /**
     * Test US-format (YYYY-MM-DD HH:MM:SS (AM|PM))
     *
     */
    #[@test]
    public function parseUsFormatWith24HourTime() {
      $this->assertEquals(
        new Date('2009-12-14 14:00:01'),
        create(new DateFormat('%Y-%m-%d %H:%M:%S'))->parse('2009-12-14 14:00:01')
      );
    }

    /**
     * Test US-format (YYYY-MM-DD HH:MM:SS)
     *
     */
    #[@test]
    public function formatUsFormatWith24HourTime() {
      $this->assertEquals(
        '2009-12-14 14:24:36',
        create(new DateFormat('%Y-%m-%d %H:%M:%S'))->format(new Date('2009-12-14 14:24:36'))
      );
    }

    /**
     * Test EU-format (DD.MM.YYYY)
     *
     */
    #[@test]
    public function parseEuFormat() {
      $this->assertEquals(
        new Date('2009-12-14'),
        create(new DateFormat('%d.%m.%Y'))->parse('14.12.2009')
      );
    }

    /**
     * Test EU-format (DD.MM.YYYY)
     *
     */
    #[@test]
    public function formatEuFormat() {
      $this->assertEquals(
        '14.12.2009',
        create(new DateFormat('%d.%m.%Y'))->format(new Date('2009-12-14'))
      );
    }

    /**
     * Test EU-format (DD.MM.YYYY HH:II:SS)
     *
     */
    #[@test]
    public function parseEuFormatWithTime() {
      $this->assertEquals(
        new Date('2009-12-14 11:45:00'),
        create(new DateFormat('%d.%m.%Y %H:%M:%S'))->parse('14.12.2009 11:45:00')
      );
    }

    /**
     * Test EU-format (DD.MM.YYYY HH:II:SS)
     *
     */
    #[@test]
    public function formatEuFormatWithTime() {
      $this->assertEquals(
        '14.12.2009 11:45:00',
        create(new DateFormat('%d.%m.%Y %H:%M:%S'))->format(new Date('2009-12-14 11:45:00'))
      );
    }
 
    /**
     * Test specialized format (Mo 07-Mrz-2011)
     *
     */
    #[@test]
    public function dayAndMonthNamesInInput() {
      $this->assertEquals(
        new Date('2011-03-07'),
        create(new DateFormat('%* %d-%[month=Jan,Feb,Mrz,Apr,Mai,Jun,Jul,Aug,Sep,Okt,Nov,Dez]-%Y'))->parse('Mo 07-Mrz-2011')
      );
    }
  }
?>
