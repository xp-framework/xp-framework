<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */

  uses(
    'unittest.TestCase',
    'text.DateFormat'
  );

  /**
   * TestCase
   *
   * @see      xp://text.DateFormat
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
        '09.01.2009',
        create(new DateFormat('%d.%m.%Y'))->format(new Date('2009-01-09'))
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
     * Test timezone names
     *
     */
    #[@test]
    public function parseDateWithTimeZoneName() {
      $this->assertEquals(
        new Date('2009-12-14 11:45:00', new TimeZone('Europe/Berlin')),
        create(new DateFormat('%Y-%m-%d %H:%M:%S %z'))->parse('2009-12-14 11:45:00 Europe/Berlin')
      );
    }

    /**
     * Test timezone names
     *
     */
    #[@test]
    public function formatDateWithTimeZoneName() {
      $this->assertEquals(
        '2009-12-14 11:45:00 Europe/Berlin',
        create(new DateFormat('%Y-%m-%d %H:%M:%S %z'))->format(new Date('2009-12-14 11:45:00', new TimeZone('Europe/Berlin')))
      );
    }

    /**
     * Test timezone offset
     *
     */
    #[@test]
    public function parseDateWithTimeZoneOffset() {
      $this->assertEquals(
        new Date('2009-12-14 11:45:00-0800'),
        create(new DateFormat('%Y-%m-%d %H:%M:%S%Z'))->parse('2009-12-14 11:45:00-0800')
      );
    }

    /**
     * Test timezone offset
     *
     */
    #[@test]
    public function formatDateWithTimeZoneOffset() {
      $this->assertEquals(
        '2009-12-14 11:45:00-0800',
        create(new DateFormat('%Y-%m-%d %H:%M:%S%Z'))->format(new Date('2009-12-14 11:45:00-0800'))
      );
    }

    /**
     * Test formatting a literal percent sign
     *
     */
    #[@test]
    public function formatLiteralPercent() {
      $this->assertEquals('%', create(new DateFormat('%%'))->format(new Date()));
    }

    /**
     * Test specialized format (07-Mrz-2011)
     *
     */
    #[@test]
    public function parseGermanMonthNamesInInput() {
      $this->assertEquals(
        new Date('2011-03-07'),
        create(new DateFormat('%d-%[month=Jan,Feb,Mrz,Apr,Mai,Jun,Jul,Aug,Sep,Okt,Nov,Dez]-%Y'))->parse('07-Mrz-2011')
      );
    }

    /**
     * Test specialized format (07-Mrz-2011)
     *
     */
    #[@test]
    public function formatGermanMonthNames() {
      $this->assertEquals(
        '07-Mrz-2011',
        create(new DateFormat('%d-%[month=Jan,Feb,Mrz,Apr,Mai,Jun,Jul,Aug,Sep,Okt,Nov,Dez]-%Y'))->format(new Date('2011-03-07'))
      );
    }

    /**
     * Test illegal format token
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function illegalToken() {
      new DateFormat('%^');
    }

    /**
     * Test parsing errors
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function stringTooShort() {
      create(new DateFormat('%Y-%m-%d'))->parse('2004');
    }

    /**
     * Test parsing errors
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function formatMismatch() {
      create(new DateFormat('%Y-%m-%d'))->parse('12.12.2004');
    }


    /**
     * Test parsing errors
     *
     */
    #[@test, @expect('lang.FormatException')]
    public function nonNumericInput() {
      create(new DateFormat('%Y'))->parse('Hello');
    }
  }
?>
