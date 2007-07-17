<?php
/* This class is part of the XP framework
 *
 * $Id$ 
 */
 
  uses(
    'unittest.TestCase',
    'util.Calendar'
  );

  /**
   * Test framework code
   *
   * @purpose  Unit Test
   */
  class CalendarTest extends TestCase {
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
      $this->assertDateEquals(Calendar::easter(2003), '2003-04-20 00:00:00', 'easter');
    }
    
    /**
     * Test calendar class (first of advent calculation)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarAdvent() {
      $this->assertDateEquals(Calendar::advent(2003), '2003-11-30 00:00:00', 'advent');
    }
    
    /**
     * Test calendar class (DST / daylight savings times)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarDSTBegin() {
      $this->assertDateEquals(Calendar::dstBegin(2003), '2003-03-30 00:00:00', 'dstbegin');
    }

    /**
     * Test calendar class (DST / daylight savings times)
     *
     * @see     xp://util.Calendar
     */
    #[@test]
    public function testCalendarDSTEnd() {
      $this->assertDateEquals(Calendar::dstEnd(2003), '2003-10-26 00:00:00', 'dstend');
    }
  }
?>
