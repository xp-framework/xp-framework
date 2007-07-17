<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'util.Date',
    'util.Calendar'
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
     * Helper method
     *
     * @param   &util.Date d
     * @param   string str
     * @param   string error default 'datenotequal'
     * @return  bool
     */
    public function assertDateEquals($d, $str, $error= 'datenotequal') {
      return $this->assertEquals($d->format('%Y-%m-%d %H:%M:%S'), $str, $error);
    }
    
    /**
     * Test calendar class
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarBasic() {
      $this->assertDateEquals(Calendar::midnight($this->refDate), '1977-12-14 00:00:00', 'midnight');
      $this->assertDateEquals(Calendar::monthBegin($this->refDate), '1977-12-01 00:00:00', 'monthbegin');
      $this->assertDateEquals(Calendar::monthEnd($this->refDate), '1977-12-31 23:59:59', 'monthend');
      $this->assertEquals(Calendar::week($this->refDate), 50, 'week');
    }
    
    /**
     * Test calendar class (easter day calculation)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarEaster() {
      $easter= Calendar::easter(2003);
      $this->assertDateEquals($easter, '2003-04-20 00:00:00', 'easter');
      return $easter;
    }
    
    /**
     * Test calendar class (first of advent calculation)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarAdvent() {
      $advent= Calendar::advent(2003);
      $this->assertDateEquals($advent, '2003-11-30 00:00:00', 'advent');
      return $advent;
    }
    
    /**
     * Test calendar class (DST / daylight savings times)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarDSTBegin() {
      $begin= Calendar::dstBegin(2003);
      $this->assertDateEquals($begin, '2003-03-30 00:00:00', 'dstbegin');
      return $begin;
    }

    /**
     * Test calendar class (DST / daylight savings times)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarDSTEnd() {
      $end= Calendar::dstEnd(2003);
      $this->assertDateEquals($end, '2003-10-26 00:00:00', 'dstend');
      return $end;
    }
    
    /**
     * Test dates before beginning of Unix epoch (sometimes using PHP
     * builtin strtotime, depending on machine) and dates even
     * earlier.
     *
     */
    #[@test]
    public function testPreUnixEpoch() {
      $date= Date::fromString('Dec 31 1969 00:00AM');
      $this->assertDateEquals($date, '1969-12-31 00:00:00', 'preunix');

      $date= Date::fromString('Jan 01 1500 00:00AM');
      $this->assertDateEquals($date, '1500-01-01 00:00:00', 'midage');
      return $date;
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
      $date= Date::fromString('May 28 1980 1:00AM');
      $this->assertEquals($date->getHours(), 1, '1:00AM != 1h');
      
      $date= Date::fromString('May 28 1980 12:00AM');
      $this->assertEquals($date->getHours(), 0, '12:00AM != 0h');
      
      $date= Date::fromString('May 28 1980 1:00PM');
      $this->assertEquals($date->getHours(), 13, '1:00PM != 13h');

      $date= Date::fromString('May 28 1980 12:00PM');
      $this->assertEquals($date->getHours(), 12, '12:00PM != 12h');

      // Test with homegrown strtotime-replacement
      $date= Date::fromString('May 28 1580 1:00AM');
      $this->assertEquals((int)$date->getHours(), 1, '1:00AM != 1h');
      
      $date= Date::fromString('May 28 1580 12:00AM');
      $this->assertEquals((int)$date->getHours(), 0, '12:00AM != 0h');
      
      $date= Date::fromString('May 28 1580 1:00PM');
      $this->assertEquals((int)$date->getHours(), 13, '1:00PM != 13h');

      $date= Date::fromString('May 28 1580 12:00PM');
      $this->assertEquals((int)$date->getHours(), 12, '12:00PM != 12h');
    }
    
    /**
     * Test mktime function
     *
     */    
    #[@test]
    public function testMktime() {
      
      // Test with a date before 1971
      $stamp= Date::mktime(0, 0, 0, '08', '02', 1968);
      $this->assertEquals($stamp, -44668800, 'Wrong timestamp');
    }
    
    /**
     * Test date parsing in different formats in
     * pre 1970 epoch.
     *
     * @see     bug://13
     */    
    #[@test]
    public function pre1970() {
      $d= Date::fromString('01.02.1969');
      $this->assertDateEquals($d, '1969-02-01 00:00:00');
      
      $d= Date::fromString('1969-02-01');
      $this->assertDateEquals($d, '1969-02-01 00:00:00');
      
      $d= Date::fromString('1969-02-01 00:00AM');
      $this->assertDateEquals($d, '1969-02-01 00:00:00');
    }    
  }
?>
