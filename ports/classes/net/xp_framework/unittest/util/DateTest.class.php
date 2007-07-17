<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'util.Date'
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
      $this->assertTrue($this->nowDate->isAfter(Date::fromString('yesterday')));
      $this->assertTrue($this->nowDate->isBefore(Date::fromString('tomorrow')));
    }
    
    /**
     * Test dates before beginning of Unix epoch (sometimes using PHP
     * builtin strtotime, depending on machine) and dates even
     * earlier.
     *
     */
    #[@test]
    public function testPreUnixEpoch() {
      $this->assertDateEquals('1969-12-31T00:00:00+00:00', Date::fromString('Dec 31 1969 00:00AM'));
      $this->assertDateEquals('1500-01-01T00:00:00+00:00', Date::fromString('Jan 01 1500 00:00AM'));
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
      $this->assertEquals(1, Date::fromString('May 28 1980 1:00AM')->getHours(), '1:00AM != 1h');
      $this->assertEquals(0, Date::fromString('May 28 1980 12:00AM')->getHours(), '12:00AM != 0h');
      $this->assertEquals(13, Date::fromString('May 28 1980 1:00PM')->getHours(), '1:00PM != 13h');
      $this->assertEquals(12, Date::fromString('May 28 1980 12:00PM')->getHours(), '12:00PM != 12h');

      // Test with homegrown strtotime-replacement
      $this->assertEquals(1, (int)Date::fromString('May 28 1580 1:00AM')->getHours(), '1:00AM != 1h');
      $this->assertEquals(0, (int)Date::fromString('May 28 1580 12:00AM')->getHours(), '12:00AM != 0h');
      $this->assertEquals(13, (int)Date::fromString('May 28 1580 1:00PM')->getHours(), '1:00PM != 13h');
      $this->assertEquals(12, (int)Date::fromString('May 28 1580 12:00PM')->getHours(), '12:00PM != 12h');
    }
    
    /**
     * Test mktime function
     *
     */    
    #[@test]
    public function testMktime() {
      
      // Test with a date before 1971
      $this->assertEquals(Date::mktime(0, 0, 0, '08', '02', 1968), -44668800, 'Wrong timestamp');
    }
    
    /**
     * Test date parsing in different formats in
     * pre 1970 epoch.
     *
     * @see     bug://13
     */    
    #[@test]
    public function pre1970() {
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', Date::fromString('01.02.1969'));
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', Date::fromString('1969-02-01'));
      $this->assertDateEquals('1969-02-01T00:00:00+00:00', Date::fromString('1969-02-01 00:00AM'));
    }    
  }
?>
