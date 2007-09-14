<?php
/* This class is part of the XP framework
 *
 * $Id$
 */
 
  uses(
    'unittest.TestCase',
    'util.DateUtil',
    'util.TimeZone'
  );

  /**
   * Test Date utility class
   *
   * @see      xp://util.DateUtil
   * @purpose  Unit Test
   */
  class DateUtilTest extends TestCase {
    protected
      $fixture= NULL;

    /**
     * Sets up this test
     *
     */
    public function setUp() {
      $this->fixture= Date::create(2000, 1, 1, 12, 15, 11, new TimeZone('Europe/Berlin'));
    }

    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addSeconds() {
      $this->assertEquals(
        Date::create(2000, 1, 1, 12, 15, 30, new TimeZone('Europe/Berlin')),
        DateUtil::addSeconds($this->fixture, 19)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addMinutes() {
      $this->assertEquals(
        Date::create(2000, 1, 1, 12, 44, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addMinutes($this->fixture, 29)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addHours() {
      $this->assertEquals(
        Date::create(2000, 1, 1, 13, 15, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addHours($this->fixture, 1)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addDays() {
      $this->assertEquals(
        Date::create(2000, 1, 2, 12, 15, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addDays($this->fixture, 1)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addMonths() {
      $this->assertEquals(
        Date::create(2000, 2, 1, 12, 15, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addMonths($this->fixture, 1)
      );
    }
    
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeSeconds() {
      $this->assertEquals(
        Date::create(2000, 1, 1, 12, 14, 52, new TimeZone('Europe/Berlin')),
        DateUtil::addSeconds($this->fixture, -19)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeMinutes() {
      $this->assertEquals(
        Date::create(2000, 1, 1, 11, 46, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addMinutes($this->fixture, -29)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeHours() {
      $this->assertEquals(
        Date::create(2000, 1, 1, 11, 15, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addHours($this->fixture, -1)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeDays() {
      $this->assertEquals(
        Date::create(1999, 12, 31, 12, 15, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addDays($this->fixture, -1)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeMonths() {
      $this->assertEquals(
        Date::create(1999, 12, 1, 12, 15, 11, new TimeZone('Europe/Berlin')),
        DateUtil::addMonths($this->fixture, -1)
      );
    }
    
    /**
     * Check leap year handling
     *
     */
    #[@test]
    public function testLeapYear() {
      $date= Date::create(2000, 2, 1, 0, 0, 0);
      
      $this->assertEquals(
        Date::create(2000, 3, 1, 0, 0, 0),
        DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        Date::create(2000, 3, 2, 0, 0, 0),
        DateUtil::addDays($date, 30)
      );
    }
    
    /**
     * Check non-leap year handling
     *
     */
    #[@test]
    public function testNonLeapYear() {
      $date= Date::create(1999, 2, 1, 0, 0, 0, new TimeZone('Europe/Berlin'));
      
      $this->assertEquals(
        Date::create(1999, 3, 1, 0, 0, 0, new TimeZone('Europe/Berlin')),
        DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        Date::create(1999, 3, 3, 0, 0, 0, new TimeZone('Europe/Berlin')),
        DateUtil::addDays($date, 30)
      );
    }
    
    /**
     * Tests the compare method
     *
     */
    #[@test]
    public function comparison() {
      $this->assertTrue(DateUtil::compare(new Date('1977-12-14'), new Date('1980-05-28')) < 0, 'a < b') &&
      $this->assertTrue(DateUtil::compare(new Date('1980-05-28'), new Date('1977-12-14')) > 0, 'a > b') &&
      $this->assertTrue(DateUtil::compare(new Date('1980-05-28'), new Date('1980-05-28')) == 0, 'a == b');
    }

    /**
     * Tests using the compare method as a usort() callback
     *
     * @see     php://usort
     */
    #[@test]
    public function sorting() {
      $list= array(
        new Date('1977-12-14'),
        new Date('2002-02-21'),
        new Date('1980-05-28'),
      );
      
      usort($list, array('DateUtil', 'compare'));
      $this->assertEquals(new Date('1977-12-14'), $list[0], 'offset 0') &&
      $this->assertEquals(new Date('1980-05-28'), $list[1], 'offset 1') &&
      $this->assertEquals(new Date('2002-02-21'), $list[2], 'offset 2');
    }
    
    /**
     * Tests the getBeginningOfWeek and
     * getEndOfWeek methods.
     *
     */
    #[@test]
    public function testBeginAndEndOfWeek() {
      $this->assertEquals(
        Date::create(2007, 1, 14, 0, 0, 0),
        DateUtil::getBeginningOfWeek(new Date('2007-1-18'))
      );
      $this->assertEquals(
        Date::create(2007, 1, 20, 23, 59, 59),
        DateUtil::getEndOfWeek(new Date('2007-1-18'))
      );
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testMoveToTimezone() {
      $copy= clone $this->fixture;
      $tz= new TimeZone('Australia/Sydney');
      
      $this->assertEquals($this->fixture, DateUtil::moveToTimeZone($copy, $tz));
    }
    
    /**
     * Test
     *
     */
    #[@test]
    public function testSetTimezone() {
      try {
      $this->assertEquals(
        Date::create(2000, 1, 1, 17, 15, 11),
        DateUtil::setTimeZone($this->fixture, new TimeZone('America/New_York'))
      );
      } catch (IllegalArgumentException $e) {
      
        $e->printStackTrace();
      }
    }
    
    
  }
?>
