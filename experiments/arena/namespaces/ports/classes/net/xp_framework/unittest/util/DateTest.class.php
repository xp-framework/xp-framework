<?php
/* This class is part of the XP framework
 *
 * $Id: DateTest.class.php 10827 2007-07-17 15:44:14Z kiesel $ 
 */

  namespace net::xp_framework::unittest::util;
 
  ::uses(
    'unittest.TestCase',
    'util.Date'
  );

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class DateTest extends unittest::TestCase {
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
      $this->nowDate= new util::Date($this->nowTime);
      $this->refDate= util::Date::fromString('1977-12-14 11:55');
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
      return $this->assertEquals($expected, $date->toString(DATE_ATOM), $error);
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
      $this->assertEquals($this->nowDate->format('%c'), strftime('%c', $this->nowTime));
      $this->assertTrue($this->nowDate->isAfter(util::Date::fromString('yesterday')));
      $this->assertTrue($this->nowDate->isBefore(util::Date::fromString('tomorrow')));
    }
    
    /**
     * Test dates before beginning of Unix epoch (sometimes using PHP
     * builtin strtotime, depending on machine) and dates even
     * earlier.
     *
     */
    #[@test]
    public function testPreUnixEpoch() {
      $this->assertDateEquals('1969-12-31T00:00:00+00:00', util::Date::fromString('Dec 31 1969 00:00AM'));
      $this->assertDateEquals('1500-01-01T00:00:00+00:00', util::Date::fromString('Jan 01 1500 00:00AM'));
    }

    /**
     * Test setting of correct hours when date was given trough
     * the AM/PM format. Test against PHP's strtotime() and the
     * homegrown replacement.
     *
     */
    #[@test]
    public function testAnteAndPostMeridiem() {
    
      // Test with default strtotime() implementation
      $this->assertEquals(1, util::Date::fromString('May 28 1980 1:00AM')->getHours(), '1:00AM != 1h');
      $this->assertEquals(0, util::Date::fromString('May 28 1980 12:00AM')->getHours(), '12:00AM != 0h');
      $this->assertEquals(13, util::Date::fromString('May 28 1980 1:00PM')->getHours(), '1:00PM != 13h');
      $this->assertEquals(12, util::Date::fromString('May 28 1980 12:00PM')->getHours(), '12:00PM != 12h');

      // Test with homegrown strtotime-replacement
      $this->assertEquals(1, (int)util::Date::fromString('May 28 1580 1:00AM')->getHours(), '1:00AM != 1h');
      $this->assertEquals(0, (int)util::Date::fromString('May 28 1580 12:00AM')->getHours(), '12:00AM != 0h');
      $this->assertEquals(13, (int)util::Date::fromString('May 28 1580 1:00PM')->getHours(), '1:00PM != 13h');
      $this->assertEquals(12, (int)util::Date::fromString('May 28 1580 12:00PM')->getHours(), '12:00PM != 12h');
    }
    
    /**
     * Test mktime function
     *
     */    
    #[@test]
    public function testMktime() {
      
      // Test with a date before 1971
      $this->assertEquals(util::Date::mktime(0, 0, 0, '08', '02', 1968), -44668800, 'Wrong timestamp');
    }
    
    /**
     * Test date parsing in different formats in
     * pre 1970 epoch.
     *
     * @see     bug://13
     */    
    #[@test]
    public function pre1970() {
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', util::Date::fromString('01.02.1969'));
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', util::Date::fromString('1969-02-01'));
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', util::Date::fromString('1969-02-01 00:00AM'));
    }    
  }
?>
