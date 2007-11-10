<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'util.Date',
    'util.TimeZone'
  );

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class DateTest extends TestCase {
    public
      $nowTime  = 0,
      $nowDate  = NULL,
      $refDate  = NULL;
    
    /**
     * Set up this test
     *
     */
    public function setUp() {
      date_default_timezone_set('GMT');
      
      $this->nowTime= time();
      $this->nowDate= new Date($this->nowTime);
      $this->refDate= Date::fromString('1977-12-14 11:55');
    }
    
    /**
     * Helper method
     *
     * @param   string expected
     * @param   util.Date date
     * @param   string error default 'datenotequal'
     * @return  bool
     */
    public function assertDateEquals($expected, $date, $error= 'datenotequal') {
      return 
        date_format(date_create($expected), 'U') ===
        date_format($date->getHandle(), 'U')
      ;
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function constructorParseWithoutTz() {
      $this->assertEquals(TRUE, new Date('2007-01-01 01:00:00 Europe/Berlin') instanceof Date);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function constructorUnixtimestampWithoutTz() {
      $this->assertDateEquals('2007-08-23T12:35:47+00:00', new Date(1187872547));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function constructorUnixtimestampWithTz() {
      $this->assertDateEquals('2007-08-23T12:35:47+00:00', new Date(1187872547, new TimeZone('Europe/Berlin')));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function constructorParseTz() {
      $date= new Date('2007-01-01 01:00:00 Europe/Berlin');
      $this->assertEquals('Europe/Berlin', $date->getTimeZone()->getName());
      $this->assertDateEquals('2007-01-01T00:00:00+00:00', $date);
      
      $date= new Date('2007-01-01 01:00:00 Europe/Berlin', new TimeZone('Europe/Athens'));
      $this->assertEquals('Europe/Berlin', $date->getTimeZone()->getName());
      $this->assertDateEquals('2007-01-01T00:00:00+00:00', $date);

      $date= new Date('2007-01-01 01:00:00', new TimeZone('Europe/Athens'));
      $this->assertEquals('Europe/Athens', $date->getTimeZone()->getName());
      $this->assertDateEquals('2006-12-31T23:00:00+00:00', $date);
    }
    
    /**
     * Check that a timezone is not reported erroneously if it actually
     * could not be parsed out of a string.
     *
     */
    #[@test]
    public function noDiscreteTimeZone() {
      $date= new Date('2007-11-04 14:32:00+1000');
      var_dump($date->getTimeZone()->getName());
      $this->assertEquals('+1100', $date->getTimeZone()->getOffset());
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function constructorParseNoTz() {
      $date= new Date('2007-01-01 01:00:00', new TimeZone('Europe/Athens'));
      $this->assertEquals('Europe/Athens', $date->getTimeZone()->getName());
      
      $date= new Date('2007-01-01 01:00:00');
      $this->assertEquals('GMT', $date->getTimeZone()->getName());
    }
    
    /**
     * Test date class
     *
     * @see     xp://util.Date
     */
    #[@test]
    public function testDate() {
      $this->assertEquals($this->nowDate->getTime(), $this->nowTime);
      $this->assertEquals($this->nowDate->toString('r'), date('r', $this->nowTime));
      $this->assertTrue($this->nowDate->isAfter(Date::fromString('yesterday')));
      $this->assertTrue($this->nowDate->isBefore(Date::fromString('tomorrow')));
    }
    
    /**
     * Test dates before beginning of Unix epoch.
     */
    #[@test]
    public function preUnixEpoch() {
      $this->assertDateEquals('1969-12-31T00:00:00+00:00', Date::fromString('31.12.1969 00:00 GMT'));
    }

    /**
     * Test dates before the year 1582 are 11 days off.
     *
     * Quoting Wikipedia:
     * The last day of the Julian calendar was Thursday October 4, 1582 and 
     * this was followed by the first day of the Gregorian calendar, Friday 
     * October 15, 1582 (the cycle of weekdays was not affected).
     *
     * @see   http://en.wikipedia.org/wiki/Gregorian_calendar
     */
    #[@test]
    public function pre1582() {
      $this->assertDateEquals('1499-12-21T00:00:00+00:00', Date::fromString('01.01.1500 00:00 GMT'));
    }

    /**
     * Test dates before the year 1752 are 11 days off.
     *
     * Quoting Wikipedia:
     * The Kingdom of Great Britain and thereby the rest of the British 
     * Empire (including the eastern part of what is now the United States) 
     * adopted the Gregorian calendar in 1752 under the provisions of 
     * the Calendar Act 1750; by which time it was necessary to correct 
     * by eleven days (Wednesday, September 2, 1752 being followed by 
     * Thursday, September 14, 1752) to account for February 29, 1700 
     * (Julian). 
     *
     * @see   http://en.wikipedia.org/wiki/Gregorian_calendar
     */
    #[@test]
    public function calendarAct1750() {
      $this->assertDateEquals('1753-01-01T00:00:00+00:00', Date::fromString('01.01.1753 00:00 GMT'));
      $this->assertDateEquals('1751-12-21T00:00:00+00:00', Date::fromString('01.01.1752 00:00 GMT'));
    }

    /**
     * Test setting of correct hours when date was given trough
     * the AM/PM format.
     *
     */
    #[@test]
    public function anteAndPostMeridiem() {
      $this->assertEquals(1, Date::fromString('May 28 1980 1:00AM')->getHours(), '1:00AM != 1h');
      $this->assertEquals(0, Date::fromString('May 28 1980 12:00AM')->getHours(), '12:00AM != 0h');
      $this->assertEquals(13, Date::fromString('May 28 1980 1:00PM')->getHours(), '1:00PM != 13h');
      $this->assertEquals(12, Date::fromString('May 28 1980 12:00PM')->getHours(), '12:00PM != 12h');
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function anteAndPostMeridiemInMidage() {
      $this->assertEquals(1, Date::fromString('May 28 1580 1:00AM')->getHours(), '1:00AM != 1h');
      $this->assertEquals(0, Date::fromString('May 28 1580 12:00AM')->getHours(), '12:00AM != 0h');
      $this->assertEquals(13, Date::fromString('May 28 1580 1:00PM')->getHours(), '1:00PM != 13h');
      $this->assertEquals(12, Date::fromString('May 28 1580 12:00PM')->getHours(), '12:00PM != 12h');
    }
    
    /**
     * Test mktime function
     *
     */    
    #[@test]
    public function dateCreate() {
      
      // Test with a date before 1971
      $this->assertEquals(-44668800, Date::create(1968, 8, 2, 0, 0, 0)->getTime());
    }
    
    /**
     * Test date parsing in different formats in pre 1970 epoch.
     *
     * @see     bug://13
     */    
    #[@test]
    public function pre1970() {
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', Date::fromString('01.02.1969'));
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', Date::fromString('1969-02-01'));
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', Date::fromString('1969-02-01 00:00AM'));
    }
    
    /**
     * Test serialization of util.Date
     *
     */
    #[@test]
    public function serialization() {
      $original= Date::fromString('2007-07-18T09:42:08 Europe/Athens');
      $copy= unserialize(serialize($original));
      $this->assertEquals($original, $copy);
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function timeZoneSerialization() {
      date_default_timezone_set('Europe/Athens');
      $date= new Date('2007-10-20 21:45:33 Europe/Berlin');
      $this->assertEquals('Europe/Berlin', $date->getTimeZone()->getName());
      
      $copy= unserialize(serialize($date));
      var_dump(serialize($date));
      $this->assertEquals('Europe/Berlin', $copy->getTimeZone()->getName());
    }
    

    /**
     * Test serialization of util.Date from old - or legacy -
     * date string representation.
     *
     */
    #[@test]
    public function serializationOfLegacyDates() {
      $serialized= 'O:4:"Date":12:{s:6:"_utime";i:1185310311;s:7:"seconds";i:51;s:7:"minutes";i:51;s:5:"hours";i:22;s:4:"mday";i:24;s:4:"wday";i:2;s:3:"mon";i:7;s:4:"year";i:2007;s:4:"yday";i:204;s:7:"weekday";s:7:"Tuesday";s:5:"month";s:4:"July";s:4:"__id";N;}';

      $date= unserialize($serialized);
      $this->assertDateEquals('2007-07-24T20:51:51+00:00', $date);

      // Only __id may be set, all the other "old" public members
      // should have been removed here
      $this->assertEquals(2, sizeof(get_object_vars($date)));
    }

    /**
     * Test timezone functionality
     *
     */
    #[@test]
    public function handlingOfTimezone() {
      $date= new Date('2007-07-18T09:42:08 Europe/Athens');

      $this->assertEquals('Europe/Athens', $date->getTimeZone()->getName());
      $this->assertEquals(3 * 3600, $date->getTimeZone()->getOffsetInSeconds($date));
    }
    
    /**
     * Test format() tokens
     *
     */
    #[@test]
    public function supportedFormatTokens() {
      $tests= array(
        '%Y' => '1977',
        '%D %T' => '12/14/1977 11:55:00',
        '%C'    => '77',
        '%e'    => '14',
        '%G'    => '1977',
        '%H'    => '11',
        '%I'    => '11',
        '%j'    => '347',
        '%m'    => '12',
        '%M'    => '55',
        '%n'    => "\n",
        '%r'    => '11:55:00am',
        '%R'    => '11:55:00',
        '%S'    => '00',
        '%t'    => "\t",
        '%u'    => '3',
        '%V'    => '50',
        '%W'    => '50',
        '%w'    => '3',
        '%y'    => '77',
        '%Z'    => '+0000',
        '%z'    => '+0000',
        '%%'    => '%'
        
      );
      
      foreach ($tests as $input => $expect) {
        $this->assertEquals($expect, $this->refDate->format($input));
      }
    }
    
    /**
     * Test
     *
     */
    #[@test, @expect('lang.IllegalArgumentException')]
    public function unsupportedFormatToken() {
      $this->refDate->format('%b');
    }
    
    /**
     * Test representation of string is working deterministicly.
     *
     * There currently is a strange problem related to deserialization
     * from timestamp.
     *
     */
    #[@test]
    public function testTimestamp() {
      date_default_timezone_set('Europe/Berlin');
      
      $d1= new Date('1980-05-28 06:30:00 Europe/Berlin');
      $d2= new Date(328336200);
      
      $this->assertEquals($d1, $d2);
      $this->assertEquals($d2, new Date($d2->toString()));
    }
  }
?>
