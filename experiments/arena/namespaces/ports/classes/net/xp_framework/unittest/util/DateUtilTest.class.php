<?php
/* This class is part of the XP framework
 *
 * $Id: DateUtilTest.class.php 10648 2007-06-21 16:12:42Z friebe $
 */

  namespace net::xp_framework::unittest::util;
 
  ::uses(
    'unittest.TestCase',
    'util.DateUtil'
  );

  /**
   * Test Date utility class
   *
   * @see      xp://util.DateUtil
   * @purpose  Unit Test
   */
  class DateUtilTest extends unittest::TestCase {
    protected
      $fixture= NULL;

    /**
     * Sets up this test
     *
     */
    public function setUp() {
      $this->fixture= new util::Date(util::Date::mktime(12, 15, 11, 1, 1, 2000));
    }

    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addSeconds() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 15, 30, 1, 1, 2000)),
        util::DateUtil::addSeconds($this->fixture, 19)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addMinutes() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 44, 11, 1, 1, 2000)),
        util::DateUtil::addMinutes($this->fixture, 29)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addHours() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(13, 15, 11, 1, 1, 2000)),
        util::DateUtil::addHours($this->fixture, 1)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addDays() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 15, 11, 1, 2, 2000)),
        util::DateUtil::addDays($this->fixture, 1)
      );
    }
      
    /**
     * Test simple add operations on a usual date.
     *
     */
    #[@test]
    public function addMonths() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 15, 11, 2, 1, 2000)),
        util::DateUtil::addMonths($this->fixture, 1)
      );
    }
    
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeSeconds() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 14, 52, 1, 1, 2000)),
        util::DateUtil::addSeconds($this->fixture, -19)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeMinutes() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(11, 46, 11, 1, 1, 2000)),
        util::DateUtil::addMinutes($this->fixture, -29)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeHours() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(11, 15, 11, 1, 1, 2000)),
        util::DateUtil::addHours($this->fixture, -1)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeDays() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 15, 11, 12, 31, 1999)),
        util::DateUtil::addDays($this->fixture, -1)
      );
    }
      
    /**
     * Test basic add operations with negative values.
     *
     */
    #[@test]
    public function addNegativeMonths() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(12, 15, 11, 12, 1, 1999)),
        util::DateUtil::addMonths($this->fixture, -1)
      );
    }
    
    /**
     * Check leap year handling
     *
     */
    #[@test]
    public function testLeapYear() {
      $date= new util::Date(util::Date::mktime(0, 0, 0, 2, 1, 2000));
      
      $this->assertEquals(
        new util::Date(util::Date::mktime(0, 0, 0, 3, 1, 2000)),
        util::DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        new util::Date(util::Date::mktime(0, 0, 0, 3, 2, 2000)),
        util::DateUtil::addDays($date, 30)
      );
    }
    
    /**
     * Check non-leap year handling
     *
     */
    #[@test]
    public function testNonLeapYear() {
      $date= new util::Date(util::Date::mktime(0, 0, 0, 2, 1, 1999));
      
      $this->assertEquals(
        new util::Date(util::Date::mktime(0, 0, 0, 3, 1, 1999)),
        util::DateUtil::addMonths($date, 1)
      );
      
      $this->assertEquals(
        new util::Date(util::Date::mktime(0, 0, 0, 3, 3, 1999)),
        util::DateUtil::addDays($date, 30)
      );
    }
    
    /**
     * Tests the compare method
     *
     */
    #[@test]
    public function comparison() {
      $this->assertTrue(util::DateUtil::compare(new util::Date('1977-12-14'), new util::Date('1980-05-28')) < 0, 'a < b') &&
      $this->assertTrue(util::DateUtil::compare(new util::Date('1980-05-28'), new util::Date('1977-12-14')) > 0, 'a > b') &&
      $this->assertTrue(util::DateUtil::compare(new util::Date('1980-05-28'), new util::Date('1980-05-28')) == 0, 'a == b');
    }

    /**
     * Tests using the compare method as a usort() callback
     *
     * @see     php://usort
     */
    #[@test]
    public function sorting() {
      $list= array(
        new util::Date('1977-12-14'),
        new util::Date('2002-02-21'),
        new util::Date('1980-05-28'),
      );
      
      usort($list, array('DateUtil', 'compare'));
      $this->assertEquals(new util::Date('1977-12-14'), $list[0], 'offset 0') &&
      $this->assertEquals(new util::Date('1980-05-28'), $list[1], 'offset 1') &&
      $this->assertEquals(new util::Date('2002-02-21'), $list[2], 'offset 2');
    }
    
    /**
     * Tests the getBeginningOfWeek and
     * getEndOfWeek methods.
     *
     */
    #[@test]
    public function testBeginAndEndOfWeek() {
      $this->assertEquals(
        new util::Date(util::Date::mktime(0, 0, 0, 1, 14, 2007)),
        util::DateUtil::getBeginningOfWeek(new util::Date('2007-1-18'))
      );
      $this->assertEquals(
        new util::Date(util::Date::mktime(23, 59, 59, 1, 20, 2007)),
        util::DateUtil::getEndOfWeek(new util::Date('2007-1-18'))
      );
    }
  }
?>
