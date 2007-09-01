<?php
/* This class is part of the XP framework
 *
 * $Id: CalendarTest.class.php 10827 2007-07-17 15:44:14Z kiesel $ 
 */

  namespace net::xp_framework::unittest::util;
 
  ::uses(
    'unittest.TestCase',
    'util.Calendar'
  );

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class CalendarTest extends unittest::TestCase {
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
     * Test calendar class
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarBasic() {
      $this->assertDateEquals('1977-12-14T00:00:00+00:00', util::Calendar::midnight($this->refDate), 'midnight');
      $this->assertDateEquals('1977-12-01T00:00:00+00:00', util::Calendar::monthBegin($this->refDate), 'monthbegin');
      $this->assertDateEquals('1977-12-31T23:59:59+00:00', util::Calendar::monthEnd($this->refDate), 'monthend');
      $this->assertEquals(50, util::Calendar::week($this->refDate), 'week');
    }
    
    /**
     * Test calendar class (easter day calculation)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarEaster() {
      $this->assertDateEquals('2003-04-20T00:00:00+00:00', util::Calendar::easter(2003), 'easter');
    }
    
    /**
     * Test calendar class (first of advent calculation)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarAdvent() {
      $this->assertDateEquals('2003-11-30T00:00:00+00:00', util::Calendar::advent(2003), 'advent');
    }
    
    /**
     * Test calendar class (DST / daylight savings times)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarDSTBegin() {
      $this->assertDateEquals('2003-03-30T00:00:00+00:00', util::Calendar::dstBegin(2003), 'dstbegin');
    }

    /**
     * Test calendar class (DST / daylight savings times)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarDSTEnd() {
      $this->assertDateEquals('2003-10-26T00:00:00+00:00', util::Calendar::dstEnd(2003), 'dstend');
    }
  }
?>
